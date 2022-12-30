<?php
namespace App\Gemonos\TagBundle\Form\DataTransformer;

use App\Entity\Objects\Objects;
use App\Repository\Objects\ObjectsRepository;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class ObjectsCodeTransformer implements DataTransformerInterface
{

    public function __construct(
        private ObjectsRepository $objectsRepository,
    ){}

    public function transform($value): string
    {
        $codes = [];
        foreach ($value->getValues() as $object) {
            $codes[] = $object->getCode();
        }
        return implode(',', $codes);
    }

    //Transformation pour ajout en base
    public function reverseTransform($string)
    {
        $codes = array_unique(array_filter(array_map('trim', explode(',', $string))));
        return $this->objectsRepository->findBy([
            'deletedAt' => null,
            'code' => $codes
        ]);
    }
}