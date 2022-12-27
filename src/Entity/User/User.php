<?php

namespace App\Entity\User;

use App\Entity\Libraries\Libraries;
use App\Entity\Objects\Objects;
use App\Entity\Site\Action;
use App\Entity\Site\News;
use App\Repository\User\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Email]
    #[ORM\Column(length: 180, unique: true)]
    private ?string $email = null;

    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 255)]
    private ?string $firstname = null;

    #[ORM\Column(length: 255)]
    private ?string $lastname = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $phone = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $avatar = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\Column]
    private ?bool $isActive = null;

    #[ORM\OneToMany(mappedBy: 'createdBy', targetEntity: Objects::class)]
    private Collection $objects;

    #[ORM\OneToMany(mappedBy: 'createdBy', targetEntity: Libraries::class)]
    private Collection $libraries;

    #[ORM\OneToMany(mappedBy: 'createdBy', targetEntity: News::class, orphanRemoval: true)]
    private Collection $news;

    #[ORM\OneToMany(mappedBy: 'createdBy', targetEntity: Action::class)]
    #[ORM\JoinColumn(onDelete: "SET NULL")]
    private Collection $actions;

    #[ORM\Column]
    private array $bookmark = [];

    #[ORM\OneToMany(mappedBy: 'updatedBy', targetEntity: Objects::class)]
    private Collection $updatedObjects;

    public function __construct()
    {
        if ($this->getCreatedAt() === null) {
            $this->setCreatedAt(new \DateTimeImmutable('now'));
        }
        $this->objects = new ArrayCollection();
        $this->libraries = new ArrayCollection();
        $this->news = new ArrayCollection();
        $this->actions = new ArrayCollection();
        $this->updatedObjects = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getAvatar(): ?string
    {
        return $this->avatar;
    }

    public function setAvatar(?string $avatar): self
    {
        $this->avatar = $avatar;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function isIsActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): self
    {
        $this->isActive = $isActive;

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
            $object->setCreatedBy($this);
        }

        return $this;
    }

    public function removeObject(Objects $object): self
    {
        if ($this->objects->removeElement($object)) {
            // set the owning side to null (unless already changed)
            if ($object->getCreatedBy() === $this) {
                $object->setCreatedBy(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Libraries>
     */
    public function getLibraries(): Collection
    {
        return $this->libraries;
    }

    public function addLibrary(Libraries $library): self
    {
        if (!$this->libraries->contains($library)) {
            $this->libraries->add($library);
            $library->setCreatedBy($this);
        }

        return $this;
    }

    public function removeLibrary(Libraries $library): self
    {
        if ($this->libraries->removeElement($library)) {
            // set the owning side to null (unless already changed)
            if ($library->getCreatedBy() === $this) {
                $library->setCreatedBy(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, News>
     */
    public function getNews(): Collection
    {
        return $this->news;
    }

    public function addNews(News $news): self
    {
        if (!$this->news->contains($news)) {
            $this->news->add($news);
            $news->setCreatedBy($this);
        }

        return $this;
    }

    public function removeNews(News $news): self
    {
        if ($this->news->removeElement($news)) {
            // set the owning side to null (unless already changed)
            if ($news->getCreatedBy() === $this) {
                $news->setCreatedBy(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Action>
     */
    public function getActions(): Collection
    {
        return $this->actions;
    }

    public function addAction(Action $action): self
    {
        if (!$this->actions->contains($action)) {
            $this->actions->add($action);
            $action->setCreatedBy($this);
        }

        return $this;
    }

    public function removeAction(Action $action): self
    {
        if ($this->actions->removeElement($action)) {
            // set the owning side to null (unless already changed)
            if ($action->getCreatedBy() === $this) {
                $action->setCreatedBy(null);
            }
        }

        return $this;
    }

    public function getBookmark(): array
    {
        return $this->bookmark;
    }

    public function setBookmark(array $bookmark): self
    {
        $this->bookmark = $bookmark;

        return $this;
    }

    /**
     * @return Collection<int, Objects>
     */
    public function getUpdatedObjects(): Collection
    {
        return $this->updatedObjects;
    }

    public function addUpdatedObject(Objects $updatedObject): self
    {
        if (!$this->updatedObjects->contains($updatedObject)) {
            $this->updatedObjects->add($updatedObject);
            $updatedObject->setUpdatedBy($this);
        }

        return $this;
    }

    public function removeUpdatedObject(Objects $updatedObject): self
    {
        if ($this->updatedObjects->removeElement($updatedObject)) {
            // set the owning side to null (unless already changed)
            if ($updatedObject->getUpdatedBy() === $this) {
                $updatedObject->setUpdatedBy(null);
            }
        }

        return $this;
    }
}
