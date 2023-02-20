<?php

namespace App\Form\Objects;

use App\Entity\Libraries\Book;
use App\Entity\Objects\Metadata\ExpositionLocation;
use App\Entity\Objects\Metadata\Floor;
use App\Entity\Objects\Metadata\Gods;
use App\Entity\Objects\Metadata\Materials;
use App\Entity\Libraries\MuseumCatalogue;
use App\Entity\Objects\Metadata\Origin;
use App\Entity\Objects\Metadata\Population;
use App\Entity\Objects\Metadata\State;
use App\Entity\Objects\Metadata\Typology;
use App\Entity\Objects\Metadata\VernacularName;
use App\Entity\Objects\Objects;
use App\Repository\Libraries\MuseumCatalogueRepository;
use App\Repository\Objects\Metadata\GodsRepository;
use App\Repository\Objects\Metadata\MaterialsRepository;
use App\Repository\Objects\Metadata\OriginRepository;
use App\Repository\Objects\Metadata\PopulationRepository;
use App\Repository\Objects\Metadata\TypologyRepository;
use App\Repository\Objects\Metadata\VernacularNameRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;

class ObjectsFormType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $builder
            ->add('code', TextType::class, [
                'label'         => 'Numéro Inventaire *',
                'required'      => true
            ])
            ->add('typology', EntityType::class, [
                'class'         => Typology::class,
                'label'         => 'Typologie *',
                'choice_label'  => 'name',
                'required'      => true,
                'multiple'      => false,
                'query_builder' => function(TypologyRepository $typologyRepository) {
                    return $typologyRepository->createQueryBuilder('t')
                        ->orderBy('t.name', 'ASC');
                },
            ])
            ->add('vernacularName', EntityType::class, [
                'class'         => VernacularName::class,
                'label'         => 'Nom vernaculaire *',
                'choice_label'  => 'name',
                'required'      => true,
                'multiple'      => false,
                'query_builder' => function(VernacularNameRepository $vernacularNameRepository) {
                    return $vernacularNameRepository->createQueryBuilder('v')
                        ->orderBy('v.name', 'ASC');
                },
            ])
            ->add('precisionVernacularName', TextType::class, [
                'label'         => 'Précision Nom Vernaculaire',
                'required'      => false
            ])
            ->add('memo', TextareaType::class, [
                'label'         => 'Mémo pour l\'équipe',
                'required'      => false,
                'attr' => [
                    'class' => 'objects_memo big_textarea'
                ]
            ])
            ->add('gods', EntityType::class, [
                'class'         => Gods::class,
                'label'         => 'Divinité',
                'choice_label'  => 'name',
                'required'      => false,
                'multiple'      => false,
                'query_builder' => function(GodsRepository $godsRepository) {
                    return $godsRepository->createQueryBuilder('g')
                        ->orderBy('g.name', 'ASC');
                },
            ])
//            ->add('categories', EntityType::class, [
//                'class'         => Categories::class,
//                'label'         => 'Categories',
//                'choice_label'  => 'name',
//                'required'      => false,
//                'multiple'      => false,
//            ])->add('subCategories', EntityType::class, [
//                'class'         => SubCategories::class,
//                'label'         => 'Sous-Categories',
//                'choice_label'  => 'name',
//                'required'      => false,
//                'multiple'      => false,
//            ])
            ->add('population', EntityType::class, [
                'class'         => Population::class,
                'label'         => 'Population',
                'choice_label'  => 'name',
                'required'      => false,
                'multiple'      => true,
                'query_builder' => function(PopulationRepository $populationRepository) {
                    return $populationRepository->createQueryBuilder('p')
                        ->orderBy('p.name', 'ASC');
                },
            ])
            ->add('origin', EntityType::class, [
                'class'         => Origin::class,
                'label'         => 'Lieu de création',
                'choice_label'  => 'name',
                'required'      => false,
                'multiple'      => true,
                'query_builder' => function(OriginRepository $originRepository) {
                    return $originRepository->createQueryBuilder('o')
                        ->orderBy('o.name', 'ASC');
                },
            ])

            ->add('relatedGods', EntityType::class, [
                'class'         => Gods::class,
                'label'         => 'Divinités Associées',
                'choice_label'  => 'name',
                'required'      => false,
                'multiple'      => true,
                'query_builder' => function(GodsRepository $godsRepository) {
                    return $godsRepository->createQueryBuilder('g')
                        ->orderBy('g.name', 'ASC');
                },
            ])
            ->add('materials', EntityType::class, [
                'class'         => Materials::class,
                'label'         => 'Matériaux',
                'choice_label'  => 'name',
                'required'      => false,
                'multiple'      => true,
                'query_builder' => function(MaterialsRepository $materialsRepository) {
                    return $materialsRepository->createQueryBuilder('m')
                        ->orderBy('m.name', 'ASC');
                },
            ])
            ->add('documentationCommentary', TextareaType::class, [
                'label' => 'Commentaire de Documentation',
                'required' => false,
            ])
            ->add('museumCatalogue', EntityType::class, [
                'class'         => MuseumCatalogue::class,
                'label'         => 'Publications du Musée',
                'choice_label'  => 'name',
                'required'      => false,
                'multiple'      => true,
                'query_builder' => function(MuseumCatalogueRepository $museumCatalogueRepository) {
                    return $museumCatalogueRepository->createQueryBuilder('m')
                        ->orderBy('m.name', 'ASC');
                },
                'attr' => [
                    'class' => 'big_textarea'
                ]
            ])
            ->add('book', EntityType::class, [
                'class'         => Book::class,
                'label'         => 'Ouvrages',
                'choice_label'  => 'title',
                'required'      => false,
                'multiple'      => true,
                'attr' => [
                    'class' => 'big_textarea'
                ]
            ])
//            ->add('description', TextareaType::class, [
//                'label'         => 'Description',
//                'required'      => false
//            ])
            ->add('era', TextType::class, [
                'label'         => 'Datation de l\' objet',
                'required'      => false
            ])
            ->add('arrivedCollection', DateType::class, [
                'label'         => 'arrivé dans la collection le',
                'widget' => 'single_text',
                'required'      => false
            ])
            ->add('historicDetail', TextareaType::class, [
                'label'         => 'Mode d\' acquisition',
                'required'      => false,
                'attr' => [
                    'class' => 'big_textarea'
                ]
            ])
            ->add('usageFonction', TextareaType::class, [
                'label'         => 'Fonction d\' usage *',
                'required'      => true,
                'attr' => [
                    'class' => 'big_textarea'
                ]
            ])
//            ->add('usageTags', TextType::class, [
//                'label'         => 'Mots clés sur utilisation',
//                'required'      => false
//            ])
            ->add('usageUser', TextType::class, [
                'label'         => 'Utilisateurs (producteur/rice, propriétaire, collectionneur/euse) si connus',
                'required'      => false
            ])

            ->add('weight', NumberType::class, [
                'label'         => 'Poids (g)',
                'required'      => false
            ])
            ->add('sizeHigh', NumberType::class, [
                'label'         => 'H (cm)',
                'required'      => false
            ])
            ->add('sizeLength', NumberType::class, [
                'label'         => 'L (cm)',
                'required'      => false
            ])
            ->add('sizeDepth', IntegerType::class, [
                'label'         => 'P (cm)',
                'required'      => false
            ])
//            ->add('appearanceTags', TextType::class, [
//                'label'         => 'Mots clés sur l\'apparence',
//                'required'      => false
//            ])
            ->add('naturalLanguageDescription', TextAreaType::class, [
                'label'         => 'Description en langage naturel',
                'required'      => true,
                'attr' => [
                    'class' => 'big_textarea'
                ]
            ])
            ->add('inscriptionsEngraving', TextAreaType::class, [
                'label'         => 'Inscriptions et marques',
                'required'      => false,
                'attr' => [
                    'class' => 'big_textarea'
                ]
            ])


            ->add('stateCommentary', TextAreaType::class, [
                'label'         => 'Remarque sur l\' état',
                'required'      => false
            ])
            ->add('state', EntityType::class, [
                'class'         => State::class,
                'label'         => 'Etat',
                'choice_label'  => 'name',
                'required'      => false,
            ])
            ->add('isBasemented', CheckboxType::class, [
                'label'         => 'Socle',
                'required'      => false
            ])
            ->add('basementCommentary', TextareaType::class, [
                'label'         => 'Commentaire de Soclage',
                'required'      => false
            ])
            ->add('expositionLocation', EntityType::class, [
                'class'         => ExpositionLocation::class,
                'label'         => false,
                'choice_label'  => 'nameFR',
                'required'      => true,
                'expanded'      => true,
                'multiple'      => false,
                 'constraints' => [
                    new NotNull(),
                    new NotBlank()
                ],
            ])

            ->add('floor', EntityType::class, [
                'class'         => Floor::class,
                'label'         => 'Etage',
                'choice_label'  => 'name',
                'required'      => true,
            ])
            ->add('showCaseCode', TextType::class, [
                'label'         => 'Numéro de vitrine',
                'required'      => false,
            ])
            ->add('shelf', TextType::class, [
                'label'         => 'Etagère',
                'required'      => false,
            ])
            ->add('insuranceValue', TextType::class, [
                'label'     => 'Valeur d\'assurance',
                'required'  => false,
            ])
            ->add('images', FileType::class, [
                'label'         => false,
                'multiple'      => true,
                'mapped'        => false,
                'required'      => false,
                'attr'          => ['class' => 'btn'],
                'label_attr'    => ['class' => 'CUSTOM_LABEL_CLASS'],
            ])
            ->add('videos', FileType::class, [
                'label'         => false,
                'multiple'      => true,
                'mapped'        => false,
                'required'      => false,
                'attr'          => ['class' => 'btn'],
                'label_attr'    => ['class' => 'CUSTOM_LABEL_CLASS'],
            ])
            ->add('files', FileType::class, [
                'label'         => false,
                'multiple'      => true,
                'mapped'        => false,
                'required'      => false,
                'attr'          => ['class' => 'btn'],
                'label_attr'    => ['class' => 'CUSTOM_LABEL_CLASS'],
            ])
//            ->add('youtube', CollectionType::class, [
//                'entry_type' => Youtube::class,
//                'label'         => false,
//                'required'      => false,
//                'attr'          => ['class' => 'btn'],
//                'label_attr'    => ['class' => 'CUSTOM_LABEL_CLASS'],
//            ])

            ->add('submit', SubmitType::class, [
                'label' => 'Sauvegarder',
                'attr' => [
                    'class' => 'btn-vodou btn-vodou-submit'
                ]
            ])
            ->add('submit2', SubmitType::class, [
                'label' => 'Sauvegarder',
                'attr' => [
                    'class' => 'btn-vodou btn-vodou-submit my-3'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Objects::class,
        ]);
    }
}
