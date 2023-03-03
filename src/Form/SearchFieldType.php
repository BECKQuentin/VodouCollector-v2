<?php

namespace App\Form;

use App\Data\SearchData;
//use App\Entity\Objects\Metadata\Categories;
use App\Entity\Objects\Metadata\ExpositionLocation;
use App\Entity\Objects\Metadata\Floor;
use App\Entity\Objects\Metadata\Gods;
use App\Entity\Objects\Metadata\Materials;
use App\Entity\Objects\Metadata\Origin;
use App\Entity\Objects\Metadata\Population;
use App\Entity\Objects\Metadata\State;
//use App\Entity\Objects\Metadata\SubCategories;
use App\Entity\Objects\Metadata\Typology;
use App\Entity\Objects\Metadata\VernacularName;
use App\Entity\User\User;
use App\Repository\Objects\Metadata\GodsRepository;
use App\Repository\Objects\Metadata\MaterialsRepository;
use App\Repository\Objects\Metadata\OriginRepository;
use App\Repository\Objects\Metadata\PopulationRepository;
use App\Repository\Objects\Metadata\TypologyRepository;
use App\Repository\Objects\Metadata\VernacularNameRepository;
use App\Repository\User\UserRepository;
use Svg\Tag\Text;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Count;
use Symfony\Component\Validator\Constraints\LessThan;
use Symfony\Component\Validator\Constraints\Type;

class SearchFieldType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $builder
            ->add('q', TextType::class, [
                'label' => false,
                'required' => false,
                'attr' => [
                    'placeholder' => 'Rechercher',
                    'autofocus' => true
                ]
            ])
            ->add('isBasemented', CheckboxType::class, [
                'label' => 'Socle',
                'required' => false,
            ])
            ->add('expositionLocation', EntityType::class, [
                'class'         => ExpositionLocation::class,
                'label'         => false,
                'choice_label'  => 'nameFR',
                'required'      => false,
                'expanded'      => true,
                'multiple'      => true,
            ])
            ->add('typology', EntityType::class, [
                'label'         => 'Typologie',
                'class'         => Typology::class,
                'choice_label'  => 'name',
                'required'      => false,
                'multiple'      => false,
                'query_builder' => function(TypologyRepository $typologyRepository) {
                    return $typologyRepository->createQueryBuilder('t')
                        ->orderBy('t.name', 'ASC');
                },
            ])
            ->add('vernacularName', EntityType::class, [
                'class'         => VernacularName::class,
                'label'         => 'Nom vernaculaire',
                'choice_label'  => 'name',
                'required'      => false,
                'multiple'      => false,
                'query_builder' => function(VernacularNameRepository $vernacularNameRepository) {
                    return $vernacularNameRepository->createQueryBuilder('v')
                        ->orderBy('v.name', 'ASC');
                },
            ])
            ->add('gods', EntityType::class, [
                'class' => Gods::class,
                'label' => 'Divinités',
                'choice_label'  => 'name',
                'required' => false,
                'multiple'  => true,
                'query_builder' => function(GodsRepository $godsRepository) {
                    return $godsRepository->createQueryBuilder('g')
                        ->orderBy('g.name', 'ASC');
                },
                'attr' => ['class' => 'big_checkboxes form-control']
            ])
            ->add('relatedGods', EntityType::class, [
                'class'         => Gods::class,
                'label'         => 'Divinités Associées',
                'choice_label'  => 'name',
                'required'      => false,
                'multiple'      => true,
                'expanded'      => true,
                'query_builder' => function(GodsRepository $godsRepository) {
                    return $godsRepository->createQueryBuilder('g')
                        ->orderBy('g.name', 'ASC');
                },
                'attr' => ['class' => 'big_checkboxes form-control']
            ])
            ->add('population', EntityType::class, [
                'class'         => Population::class,
                'label'         => 'Population',
                'choice_label'  => 'name',
                'required'      => false,
                'multiple'      => true,
                'expanded'      => true,
                'query_builder' => function(PopulationRepository $populationRepository) {
                    return $populationRepository->createQueryBuilder('p')
                        ->orderBy('p.name', 'ASC');
                },
                'attr' => ['class' => 'big_checkboxes form-control']
            ])
            ->add('origin', EntityType::class, [
                'class'         => Origin::class,
                'label'         => 'Origine',
                'choice_label'  => 'name',
                'required'      => false,
                'multiple'      => true,
                'expanded'        => true,
                'query_builder' => function(OriginRepository $originRepository) {
                    return $originRepository->createQueryBuilder('o')
                        ->orderBy('o.name', 'ASC');
                },
                'attr' => ['class' => 'big_checkboxes form-control']
            ])
            ->add('materials', EntityType::class, [
                'class'         => Materials::class,
                'label'         => 'Matériaux',
                'choice_label'  => 'name',
                'required'      => false,
                'multiple'      => true,
                'expanded'      => true,
                'query_builder' => function(MaterialsRepository $materialsRepository) {
                    return $materialsRepository->createQueryBuilder('m')
                        ->orderBy('m.name', 'ASC');
                },
                'attr' => ['class' => 'big_checkboxes form-control']
            ])
            ->add('antequemDatation', TextType::class, [
                'label'         => 'Antequem',
                'required'      => false,
//                'constraints' => [
//                    new Type('numeric', 'Seulement des chiffres'),
//                    new Count(4, 4, 4, null, 'Doit être un année de 4 chiffres'),
//                ]
            ])
            ->add('preciseDatation', TextType::class, [
                'label'         => 'Antequem',
                'required'      => false,
//                'constraints' => [
//                    new Type('numeric', 'Seulement des chiffres'),
//                    new LessThan(5, 4, 'Doit être un année de 4 chiffres'),
//                ]
            ])
            ->add('postequemDatation', TextType::class, [
                'label'         => 'Antequem',
                'required'      => false,
//                'constraints' => [
//                    new Type('numeric', 'Seulement des chiffres'),
//                    new Count(4, 4, 4, null, 'Doit être un année de 4 chiffres'),
//                ]
            ])
            ->add('state', EntityType::class, [
                'label' => 'Etat',
                'required' => false,
                'class' => State::class,
                'choice_label'  => 'name',
                'multiple'  => true,
            ])
            ->add('floor', EntityType::class, [
                'label' => 'Etage',
                'required' => false,
                'class' => Floor::class,
                'choice_label'  => 'name',
                'multiple'  => true,
            ])
            ->add('showcaseCode', TextType::class, [
                'label' => 'Numéro de vitrine',
                'required' => false,
            ])
            ->add('shelf', TextType::class, [
                'label' => 'Etagère',
                'required' => false,
            ])
            //SORT
            ->add('isSortAlpha', CheckboxType::class, [
                'label'         => false,
                'label_attr'    => ['class' => 'fa-solid fa-arrow-down-a-z'],
                'required'      => false,
                'data'          => true,
            ])
            ->add('isSortAlphaReverse', CheckboxType::class, [
                'label'         => false,
                'label_attr'    => ['class' => 'fa-solid fa-arrow-down-z-a'],
                'required'      => false,
            ])
            ->add('isSortNumeric', CheckboxType::class, [
                'label'         => false,
                'label_attr'    => ['class' => 'fa-solid fa-arrow-down-1-9'],
                'required'      => false,
            ])
            ->add('isSortNumericReverse', CheckboxType::class, [
                'label'         => false,
                'label_attr'    => ['class' => 'fa-solid fa-arrow-down-9-1'],
                'required'      => false,
            ])
            ->add('sortDateUpdate', CheckboxType::class, [
                'label'         => false,
                'label_attr'    => ['class' => 'fa-solid fa-clock-rotate-left'],
                'required'      => false,
            ])
//            ->add('updatedBy', EntityType::class, [
//                'label'         => 'Modifié par',
//                'required'      => false,
//                'class'         => User::class,
//                'choice_label'  => 'fullName',
//                'multiple'      => true,
//                'expanded'      => true,
//                'query_builder' => function(UserRepository $userRepository) {
//                    return $userRepository->createQueryBuilder('u')
//                        ->where("u.roles = 'ROLE_ADMIN'")
//                        ->setParameter("role", '%"ROLE_ADMIN"%');
//                },
//                'attr' => ['class' => 'big_checkboxes form-control']
//            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Filtrer',
                'attr' => [
                    'class' => 'btn-vodou my-3'
                ]
            ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SearchData::class,
            'method'    => 'GET',
            'csrf_protection' => false,
        ]);
    }

    public function getBlockPrefix(): string
    {
        return '';
    }
}
