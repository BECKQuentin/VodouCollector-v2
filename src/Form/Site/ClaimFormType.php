<?php

namespace App\Form\Site;

use App\Entity\User\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class ClaimFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $builder
            ->add('section', ChoiceType::class, [
                'label'     => false,
                'choices'   => [
                    'Remarques'         => 'Remarques',
                    'Problèmes / Bugs'  => 'Problèmes / Bugs',
                    'Erreur'            => 'Erreur',
                ],
                'expanded' => false,
                'mapped' => false,
            ])
            ->add('subject', TextType::class, [
                'label'     => 'Sujet *',
                'required'  => true,
            ])
            ->add('description', TextareaType::class, [
                'label'     => 'Texte *',
                'required'  => true,
            ])
            ->add('submit', SubmitType::class, [
                'label'     => 'Envoyer',
                'attr'      => [
                    'class' => 'btn-vodou mt-3'
                ]
            ])
        ;



    }

//    public function configureOptions(OptionsResolver $resolver): void
//    {
//        $resolver->setDefaults([
//            'data_class' => User::class,
//        ]);
//    }
}
