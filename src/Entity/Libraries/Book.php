<?php

namespace App\Entity\Libraries;

use App\Entity\Objects\Objects;
use App\Repository\Libraries\BookRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BookRepository::class)]
class Book
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\ManyToMany(targetEntity: Objects::class, mappedBy: 'book')]
    private Collection $objects;

    #[ORM\ManyToMany(targetEntity: Libraries::class, mappedBy: 'book')]
    private Collection $libraries;

    public function __construct()
    {
        $this->objects = new ArrayCollection();
        $this->libraries = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setName(string $title): self
    {
        $this->title = $title;

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
            $object->addBook($this);
        }

        return $this;
    }

    public function removeObject(Objects $object): self
    {
        if ($this->objects->removeElement($object)) {
            $object->removeBook($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Libraries>
     */
    public function getLibrairies(): Collection
    {
        return $this->libraries;
    }

    public function addLibrary(Libraries $library): self
    {
        if (!$this->libraries->contains($library)) {
            $this->libraries->add($library);
            $library->addBook($this);
        }

        return $this;
    }

    public function removeLibrary(Libraries $library): self
    {
        if ($this->libraries->removeElement($library)) {
            $library->removeBook($this);
        }

        return $this;
    }
}
