<?php

namespace App\Form\Objects;

use App\Entity\Objects\Objects;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

abstract class AbstractObjectsType extends AbstractType
{

    private function getYears($min, $max='current')
    {
        $years = range($min, ($max === 'current' ? date('Y') : $max));

        return array_combine($years, $years);
    }

    public function addAntequemDatation(FormBuilderInterface $builder): void
    {
        $builder->add('antequemDatation', ChoiceType::class, [
            'label'     => 'Antequem',
            'required'  => false,
            'choices'   => $this->getYears(1700),
        ]);
    }

    public function addPreciseDatation(FormBuilderInterface $builder): void
    {
        $builder->add('preciseDatation', ChoiceType::class, [
            'label'     => 'Année précise',
            'required'  => false,
            'choices'   => $this->getYears(1700),
        ]);
    }

    public function addPostquemDatation(FormBuilderInterface $builder): void
    {
        $builder->add('postquemDatation', ChoiceType::class, [
            'label'     => 'Postquem',
            'required'  => false,
            'choices'   => $this->getYears(1700),
        ]);
    }

    public function addIsBasemented(FormBuilderInterface $builder): void
    {
        $builder->add('isBasemented', CheckboxType::class, [
            'label'         => 'Socle',
            'required'      => false
        ]);
    }

    public function addShowcaseCode(FormBuilderInterface $builder): void
    {
        $builder->add('showcaseCode', TextType::class, [
            'label' => 'Numéro de vitrine',
            'required' => false,
        ]);
    }

    public function addShelf(FormBuilderInterface $builder): void
    {
        $builder->add('shelf', TextType::class, [
            'label'         => 'Etagère',
            'required'      => false,
        ]);
    }



    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Objects::class,
        ]);
    }


}
