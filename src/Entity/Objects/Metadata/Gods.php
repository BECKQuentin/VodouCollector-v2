<?php

namespace App\Entity\Objects\Metadata;

use App\Entity\Objects\Objects;
use App\Repository\Objects\Metadata\GodsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;


#[UniqueEntity(
    fields: ['name'],
    message: 'Ce nom est déjà utilisé',
)]
#[ORM\Entity(repositoryClass: GodsRepository::class)]
class Gods
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\OneToMany(mappedBy: 'gods', targetEntity: Objects::class)]
    private Collection $objects;

    #[ORM\ManyToMany(targetEntity: Objects::class, mappedBy: 'relatedGods')]
    private Collection $objectsRelated;

    public function __construct()
    {
        $this->objects = new ArrayCollection();
        $this->objectsRelated = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection<int, Objects>
     */
    public function getObjects(): Collection
    {
        return $this->objects;
    }

    public function addObject(Objects $object): self
    {
        if (!$this->objects->contains($object)) {
            $this->objects->add($object);
            $object->setGods($this);
        }

        return $this;
    }

    public function removeObject(Objects $object): self
    {
        if ($this->objects->removeElement($object)) {
            // set the owning side to null (unless already changed)
            if ($object->getGods() === $this) {
                $object->setGods(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Objects>
     */
    public function getObjectsRelated(): Collection
    {
        return $this->objectsRelated;
    }

    public function addObjectsRelated(Objects $objectsRelated): self
    {
        if (!$this->objectsRelated->contains($objectsRelated)) {
            $this->objectsRelated->add($objectsRelated);
            $objectsRelated->addRelatedGod($this);
        }

        return $this;
    }

    public function removeObjectsRelated(Objects $objectsRelated): self
    {
        if ($this->objectsRelated->removeElement($objectsRelated)) {
            $objectsRelated->removeRelatedGod($this);
        }

        return $this;
    }
}
