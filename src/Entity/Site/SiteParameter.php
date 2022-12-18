<?php

namespace App\Entity\Site;

use App\Repository\Site\SiteParameterRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SiteParameterRepository::class)]
class SiteParameter
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column()]
    private ?int $limitActionLog = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLimitActionLog(): ?int
    {
        return $this->limitActionLog;
    }

    public function setLimitActionLog(?int $limitActionLog): self
    {
        $this->limitActionLog = $limitActionLog;

        return $this;
    }
}
