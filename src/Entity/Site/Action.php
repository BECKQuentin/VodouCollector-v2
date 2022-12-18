<?php

namespace App\Entity\Site;

use App\Entity\Objects\Objects;
use App\Entity\User\User;
use App\Repository\Site\ActionRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ActionRepository::class)]
class Action
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\ManyToOne(inversedBy: 'actions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?ActionCategory $category = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\ManyToOne(inversedBy: 'actions')]
    private ?User $createdBy = null;

    #[ORM\ManyToOne(inversedBy: 'actions')]
    private ?Objects $object = null;

    #[ORM\ManyToOne(inversedBy: 'actions')]
    private ?User $user = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $othersValue = null;

    public function __construct()
    {
        if ($this->getCreatedAt() === null) {
            $this->setCreatedAt(new \DateTimeImmutable('now'));
        }
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

    public function getCategory(): ?ActionCategory
    {
        return $this->category;
    }

    public function setCategory(?ActionCategory $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getCreatedBy(): ?User
    {
        return $this->createdBy;
    }

    public function setCreatedBy(?User $createdBy): self
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    public function getObject(): ?Objects
    {
        return $this->object;
    }

    public function setObject(?Objects $object): self
    {
        $this->object = $object;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getOthersValue(): ?string
    {
        return $this->othersValue;
    }

    public function setOthersValue(?string $othersValue): self
    {
        $this->othersValue = $othersValue;

        return $this;
    }
}
