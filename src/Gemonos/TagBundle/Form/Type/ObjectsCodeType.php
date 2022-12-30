<?php
namespace App\Gemonos\TagBundle\Form\Type;

use App\Gemonos\TagBundle\Form\DataTransformer\ObjectsCodeTransformer;
use App\Repository\Objects\ObjectsRepository;
use Symfony\Bridge\Doctrine\Form\DataTransformer\CollectionToArrayTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ObjectsCodeType extends AbstractType {


    public function __construct(
        private ObjectsRepository $objectsRepository
    ){}

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
//            ->addModelTransformer(new CollectionToArrayTransformer(), true)
            ->addModelTransformer(new ObjectsCodeTransformer( $this->objectsRepository));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefault('attr', [
            'class' => 'tag-input'
        ]);
        $resolver->setDefault('required', false);
    }

    public function getParent (): string {
        return TextType::class;
    }

}