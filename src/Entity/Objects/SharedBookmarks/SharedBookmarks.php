<?php

namespace App\Entity\Objects\SharedBookmarks;

use App\Entity\Objects\Objects;
use App\Entity\User\User;
use App\Repository\Objects\SharedBookmarks\SharedBookmarksRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SharedBookmarksRepository::class)]
class SharedBookmarks
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\ManyToMany(targetEntity: Objects::class, inversedBy: 'sharedBookmarks')]
    private Collection $objects;

    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'sharedBookmarks')]
    private Collection $users;

    public function __construct()
    {
        $this->objects = new ArrayCollection();
        $this->users = new ArrayCollection();
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
        }

        return $this;
    }

    public function removeObject(Objects $object): self
    {
        $this->objects->removeElement($object);

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users->add($user);
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        $this->users->removeElement($user);

        return $this;
    }
}
