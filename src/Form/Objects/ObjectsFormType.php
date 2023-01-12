<?php

namespace App\Form\Objects;

use App\Entity\Libraries\Book;
//use App\Entity\Objects\Metadata\Categories;
use App\Entity\Objects\Media\Image;
use App\Entity\Objects\Media\Youtube;
use App\Entity\Objects\Metadata\ExpositionLocation;
use App\Entity\Objects\Metadata\Floor;
use App\Entity\Objects\Metadata\Gods;
use App\Entity\Objects\Metadata\Materials;
use App\Entity\Libraries\MuseumCatalogue;
use App\Entity\Objects\Metadata\Origin;
use App\Entity\Objects\Metadata\Population;
use App\Entity\Objects\Metadata\State;
//use App\Entity\Objects\Metadata\SubCategories;
use App\Entity\Objects\Objects;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
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
            ->add('title', TextType::class, [
                'label'         => 'Nom Vernaculaire *',
                'required'      => true
            ])
            ->add('memo', TextareaType::class, [
                'label'         => 'Mémo',
                'required'      => false,
                'attr' => [
                    'class' => 'objects_memo'
                ]
            ])
            ->add('gods', EntityType::class, [
                'class'         => Gods::class,
                'label'         => 'Divinité',
                'choice_label'  => 'name',
                'required'      => false,
                'multiple'      => false,
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
                'multiple'      => false,
            ])
            ->add('origin', EntityType::class, [
                'class'         => Origin::class,
                'label'         => 'Origine',
                'choice_label'  => 'name',
                'required'      => false,
                'multiple'      => false,
            ])

            ->add('relatedGods', EntityType::class, [
                'class'         => Gods::class,
                'label'         => 'Divinités Associées',
                'choice_label'  => 'name',
                'required'      => false,
                'multiple'      => true,
            ])
            ->add('materials', EntityType::class, [
                'class'         => Materials::class,
                'label'         => 'Materiaux',
                'choice_label'  => 'name',
                'required'      => false,
                'multiple'      => true,
            ])
            ->add('museumCatalogue', EntityType::class, [
                'class'         => MuseumCatalogue::class,
                'label'         => 'Parution du Musée',
                'choice_label'  => 'name',
                'required'      => false,
                'multiple'      => true,
            ])
            ->add('book', EntityType::class, [
                'class'         => Book::class,
                'label'         => 'Ouvrages',
                'choice_label'  => 'title',
                'required'      => false,
                'multiple'      => true,
            ])
//            ->add('description', TextareaType::class, [
//                'label'         => 'Description',
//                'required'      => false
//            ])
            ->add('era', TextType::class, [
                'label'         => 'Datation de l\' objet',
                'required'      => false
            ])
//            ->add('quantity', IntegerType::class, [
//                'label'         => 'Quantité',
//                'required'      => false
//            ])
            ->add('arrivedCollection', DateType::class, [
                'label'         => 'arrivé dans la collection le',
                'widget' => 'single_text',
                'required'      => false
            ])
            ->add('historicDetail', TextareaType::class, [
                'label'         => 'Mode d\' acquisition',
                'required'      => false,
            ])
            ->add('usageFonction', TextareaType::class, [
                'label'         => 'Fonction d\' usage',
                'required'      => false
            ])
//            ->add('usageTags', TextType::class, [
//                'label'         => 'Mots clés sur utilisation',
//                'required'      => false
//            ])
            ->add('usageUser', TextType::class, [
                'label'         => 'Utilisateurs',
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
                'required'      => false,
            ])
            ->add('inscriptionsEngraving', TextAreaType::class, [
                'label'         => 'Inscriptions et Gravures',
                'required'      => false,
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
