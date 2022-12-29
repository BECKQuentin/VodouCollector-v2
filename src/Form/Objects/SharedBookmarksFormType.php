<?php

namespace App\Form\Objects;

use App\Entity\Objects\Objects;
use App\Entity\Objects\SharedBookmarks\SharedBookmarks;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SharedBookmarksFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom du Groupe de Favoris',
                'required' => true,
            ])
//            ->add('submitSharedBookmarks', SubmitType::class, [
//                'label' => 'Sauvegarder',
//                'attr' => [
//                    'class' => 'btn-vodou btn-vodou-submit'
//                ]
//            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SharedBookmarks::class,
        ]);
    }
}
