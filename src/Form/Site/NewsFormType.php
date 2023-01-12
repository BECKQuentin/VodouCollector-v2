<?php

namespace App\Form\Site;

use App\Entity\Site\News;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NewsFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $news = $options['data'];
        $builder
            ->add('title', TextType::class, [
                'required' => true,
                'label' => 'Titre'
            ])
            ->add('description', TextareaType::class, [
                'required' => false,
                'label' => 'Description',
            ])
            ->add('expireAt', DateType::class, [
                'required' => true,
                'label' => 'Date d\'expiration',
                'format' => 'ddMMyyyy',
                'attr'      => [
                    'class' => 'd-flex'
                ],
                'data' => new \DateTime("now +10 day"),
            ])
            ->add('roles', ChoiceType::class, [
                'choices'   => [
                    'Admin' => 'ROLE_ADMIN',
                    'Admin + Membre' => 'ROLE_MEMBER',
                    'Admin + Membre + Invité' => 'ROLE_GUEST',
                ],
                'choice_attr' => function ($choice, $key, $value) use($news) {
                    if ($news->getId() == null) {
                        if ($choice === 'ROLE_ADMIN') {
                            return ['checked' => true];
                        }
                    } elseif($choice === $news->getRoles()[0]) {
                        return ['checked' => true];
                    }
                    return [];
                },
                'expanded' => true,
                'label' => 'Visible pour :',
            ])
            ->add('submit', SubmitType::class, [
                'label'     => 'Valider',
                'attr'      => [
                    'class' => 'btn-vodou my-3'
                ]
            ])
        ;

        //  Data transformer
        $builder->get('roles')
            ->addModelTransformer(new CallbackTransformer(
                function ($rolesArray) {
                    // transform the array to a string
                    if($rolesArray == array()) {
                        return count($rolesArray)? $rolesArray[0]: null;
                    }
                },
                function ($rolesString) {
                    // transform the string back to an array
                    return [$rolesString];
                }
            ));
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => News::class,
        ]);
    }
}
