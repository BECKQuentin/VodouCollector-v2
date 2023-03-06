<?php

namespace App\Entity\Objects\Media;

use App\Entity\Objects\Objects;
use App\Repository\Objects\Media\FileRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FileRepository::class)]
class File
{
    const UPLOAD_DIR = 'upload/files/objects/';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $code = null;

    #[ORM\Column(length: 255)]
    private ?string $src = null;

    #[ORM\ManyToOne(inversedBy: 'files')]
    private ?Objects $objects = null;

    public function getPostPublicPath(): string
    {
        return self::UPLOAD_DIR.'/'.$this->getSrc();
    }

    public function getExtension()
    {
        $extension = explode('.', $this->getSrc());
        return end($extension);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getSrc(): ?string
    {
        return $this->src;
    }

    public function setSrc(string $src): self
    {
        $this->src = $src;

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
