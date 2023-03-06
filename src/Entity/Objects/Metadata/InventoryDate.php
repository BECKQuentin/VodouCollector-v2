<?php

namespace App\Entity\Objects\Metadata;

use App\Entity\Objects\Objects;
use App\Repository\Objects\Metadata\InventoryDateRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: InventoryDateRepository::class)]
class InventoryDate
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?\DateTime $inventoriedAt = null;

    #[ORM\ManyToOne(inversedBy: 'inventoriedAt')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Objects $objects = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getInventoriedAt(): ?\DateTime
    {
        return $this->inventoriedAt;
    }

    public function setInventoriedAt(\DateTime $inventoriedAt): self
    {
        $this->inventoriedAt = $inventoriedAt;

        return $this;
    }

    public function getObjects(): ?Objects
    {
        return $this->objects;
    }

    public function setObjects(?Objects $objects): self
    {
        $this->objects = $objects;

        return $this;
    }
}
