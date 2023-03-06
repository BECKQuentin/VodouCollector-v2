<?php

namespace App\Form\Objects;

use App\Entity\Objects\Metadata\InventoryDate;
use App\Entity\Objects\Objects;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InventoriedAtFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('inventoriedAt', DateTimeType::class, [
                'label'         => Objects::LABEL_INVENTORIED_AT,
                'widget'        => 'single_text',
                'required'      => true,
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Ajouter',
                'attr' => [
                    'class' => 'btn-vodou btn-vodou-submit'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => InventoryDate::class,
        ]);
    }
}
