<?php

namespace App\Entity\Objects;

use App\Entity\Libraries\Book;
use App\Entity\Libraries\MuseumCatalogue;
use App\Entity\Objects\Media\File;
use App\Entity\Objects\Media\Image;
use App\Entity\Objects\Media\Video;
use App\Entity\Objects\Media\Youtube;
use App\Entity\Objects\Metadata\ExpositionLocation;
use App\Entity\Objects\Metadata\Floor;
use App\Entity\Objects\Metadata\Gods;
use App\Entity\Objects\Metadata\Materials;
use App\Entity\Objects\Metadata\Origin;
use App\Entity\Objects\Metadata\Population;
use App\Entity\Objects\Metadata\State;
use App\Entity\Site\Action;
use App\Entity\User\User;
use App\Repository\Objects\ObjectsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Mapping\ClassMetadata;

#[ORM\Entity(repositoryClass: ObjectsRepository::class)]
class Objects
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $code = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\ManyToOne(inversedBy: 'objects')]
    private ?Gods $gods = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $memo = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\ManyToOne(inversedBy: 'objects')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $createdBy = null;

    #[ORM\Column(nullable: true)]
    private ?int $era = null;

    #[ORM\ManyToOne(inversedBy: 'objects')]
    private ?Origin $origin = null;

    #[ORM\ManyToOne(inversedBy: 'objects')]
    private ?Population $population = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $historicDetail = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $usageFonction = null;

//    #[ORM\Column(type: Types::ARRAY, nullable: true)]
//    private array $usageTags = [];

    #[ORM\ManyToMany(targetEntity: Gods::class, inversedBy: 'objectsRelated')]
    private Collection $relatedGods;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $usageUser = null;

    #[ORM\ManyToMany(targetEntity: Materials::class, inversedBy: 'objects')]
    private Collection $materials;

    #[ORM\Column(nullable: true)]
    private ?float $sizeHigh = null;

    #[ORM\Column(nullable: true)]
    private ?float $sizeLength = null;

    #[ORM\Column(nullable: true)]
    private ?float $sizeDepth = null;

    #[ORM\Column(nullable: true)]
    private ?float $weight = null;

//    #[ORM\Column(type: Types::ARRAY, nullable: true)]
//    private array $appearanceTags = [];

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $naturalLanguageDescription = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $inscriptionsEngraving = null;

    #[ORM\ManyToMany(targetEntity: MuseumCatalogue::class, inversedBy: 'objects')]
    private Collection $museumCatalogue;

    #[ORM\ManyToMany(targetEntity: Book::class, inversedBy: 'objects')]
    private Collection $book;

    #[ORM\Column(nullable: true)]
    private ?bool $isBasemented = null;

    #[ORM\ManyToOne(inversedBy: 'objects')]
    #[ORM\JoinColumn(nullable: false)]
    private ?ExpositionLocation $expositionLocation = null;

    #[ORM\ManyToOne(inversedBy: 'objects')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Floor $floor = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $showcaseCode = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $shelf = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $insuranceValue = null;

    #[ORM\ManyToOne(inversedBy: 'objects')]
    private ?State $state = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $stateCommentary = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $arrivedCollection = null;

    #[ORM\OneToMany(mappedBy: 'objects', targetEntity: Image::class, cascade: ["persist"], orphanRemoval: true)]
    private Collection $images;

    #[ORM\OneToMany(mappedBy: 'objects', targetEntity: Video::class, cascade: ["persist"], orphanRemoval: true)]
    private Collection $videos;

    #[ORM\OneToMany(mappedBy: 'objects', targetEntity: File::class, cascade: ["persist"], orphanRemoval: true)]
    private Collection $files;

//    #[ORM\OneToMany(mappedBy: 'objects', targetEntity: Youtube::class, cascade: ["persist"], orphanRemoval: true)]
//    private Collection $youtube;

    #[ORM\OneToMany(mappedBy: 'object', targetEntity: Action::class, cascade: ["persist", "remove"])]
    private Collection $actions;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $updatedAt = null;

    #[ORM\ManyToOne(inversedBy: 'updatedObjects')]
    #[ORM\JoinColumn(onDelete: 'SET NULL')]
    private ?User $updatedBy = null;

    public function __construct()
    {
        $this->setUpdatedAt(new \DateTimeImmutable('now'));
        if ($this->getCreatedAt() === null) {
            $this->setCreatedAt(new \DateTimeImmutable('now'));
        }
        $this->relatedGods = new ArrayCollection();
        $this->materials = new ArrayCollection();
        $this->museumCatalogue = new ArrayCollection();
        $this->book = new ArrayCollection();
        $this->images = new ArrayCollection();
        $this->videos = new ArrayCollection();
        $this->files = new ArrayCollection();
        $this->youtube = new ArrayCollection();
        $this->actions = new ArrayCollection();
    }

    public static function loadValidatorMetadata(ClassMetadata $metadata)
    {
        $metadata->addConstraint(new UniqueEntity([
            'fields' => 'id',
            'errorPath' => 'id',
            'message' => 'Ce code existe déjà !',
        ]));
        $metadata->addConstraint(new UniqueEntity([
            'fields' => 'title',
            'errorPath' => 'title',
            'message' => 'Ce titre existe déjà !',
        ]));
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

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getGods(): ?Gods
    {
        return $this->gods;
    }

    public function setGods(?Gods $gods): self
    {
        $this->gods = $gods;

        return $this;
    }

    public function getMemo(): ?string
    {
        return $this->memo;
    }

    public function setMemo(?string $memo): self
    {
        $this->memo = $memo;

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

    public function getEra(): ?int
    {
        return $this->era;
    }

    public function setEra(?int $era): self
    {
        $this->era = $era;

        return $this;
    }

    public function getOrigin(): ?Origin
    {
        return $this->origin;
    }

    public function setOrigin(?Origin $origin): self
    {
        $this->origin = $origin;

        return $this;
    }

    public function getPopulation(): ?Population
    {
        return $this->population;
    }

    public function setPopulation(?Population $population): self
    {
        $this->population = $population;

        return $this;
    }

    public function getHistoricDetail(): ?string
    {
        return $this->historicDetail;
    }

    public function setHistoricDetail(?string $historicDetail): self
    {
        $this->historicDetail = $historicDetail;

        return $this;
    }

    public function getUsageFonction(): ?string
    {
        return $this->usageFonction;
    }

    public function setUsageFonction(?string $usageFonction): self
    {
        $this->usageFonction = $usageFonction;

        return $this;
    }

//    public function getUsageTags(): array
//    {
//        return $this->usageTags;
//    }
//
//    public function setUsageTags(?array $usageTags): self
//    {
//        $this->usageTags = $usageTags;
//
//        return $this;
//    }

    /**
     * @return Collection<int, Gods>
     */
    public function getRelatedGods(): Collection
    {
        return $this->relatedGods;
    }

    public function addRelatedGod(Gods $relatedGod): self
    {
        if (!$this->relatedGods->contains($relatedGod)) {
            $this->relatedGods->add($relatedGod);
        }

        return $this;
    }

    public function removeRelatedGod(Gods $relatedGod): self
    {
        $this->relatedGods->removeElement($relatedGod);

        return $this;
    }

    public function getUsageUser(): ?string
    {
        return $this->usageUser;
    }

    public function setUsageUser(?string $usageUser): self
    {
        $this->usageUser = $usageUser;

        return $this;
    }

    /**
     * @return Collection<int, Materials>
     */
    public function getMaterials(): Collection
    {
        return $this->materials;
    }

    public function addMaterial(Materials $material): self
    {
        if (!$this->materials->contains($material)) {
            $this->materials->add($material);
        }

        return $this;
    }

    public function removeMaterial(Materials $material): self
    {
        $this->materials->removeElement($material);

        return $this;
    }

    public function getSizeHigh(): ?float
    {
        return $this->sizeHigh;
    }

    public function setSizeHigh(?float $sizeHigh): self
    {
        $this->sizeHigh = $sizeHigh;

        return $this;
    }

    public function getSizeLength(): ?float
    {
        return $this->sizeLength;
    }

    public function setSizeLength(?float $sizeLength): self
    {
        $this->sizeLength = $sizeLength;

        return $this;
    }

    public function getSizeDepth(): ?float
    {
        return $this->sizeDepth;
    }

    public function setSizeDepth(?float $sizeDepth): self
    {
        $this->sizeDepth = $sizeDepth;

        return $this;
    }

    public function getWeight(): ?float
    {
        return $this->weight;
    }

    public function setWeight(?float $weight): self
    {
        $this->weight = $weight;

        return $this;
    }

//    public function getAppearanceTags(): array
//    {
//        return $this->appearanceTags;
//    }
//
//    public function setAppearanceTags(?array $appearanceTags): self
//    {
//        $this->appearanceTags = $appearanceTags;
//
//        return $this;
//    }

    public function getNaturalLanguageDescription(): ?string
    {
        return $this->naturalLanguageDescription;
    }

    public function setNaturalLanguageDescription(?string $naturalLanguageDescription): self
    {
        $this->naturalLanguageDescription = $naturalLanguageDescription;

        return $this;
    }

    public function getInscriptionsEngraving(): ?string
    {
        return $this->inscriptionsEngraving;
    }

    public function setInscriptionsEngraving(?string $inscriptionsEngraving): self
    {
        $this->inscriptionsEngraving = $inscriptionsEngraving;

        return $this;
    }

    /**
     * @return Collection<int, MuseumCatalogue>
     */
    public function getMuseumCatalogue(): Collection
    {
        return $this->museumCatalogue;
    }

    public function addMuseumCatalogue(MuseumCatalogue $museumCatalogue): self
    {
        if (!$this->museumCatalogue->contains($museumCatalogue)) {
            $this->museumCatalogue->add($museumCatalogue);
        }

        return $this;
    }

    public function removeMuseumCatalogue(MuseumCatalogue $museumCatalogue): self
    {
        $this->museumCatalogue->removeElement($museumCatalogue);

        return $this;
    }

    /**
     * @return Collection<int, Book>
     */
    public function getBook(): Collection
    {
        return $this->book;
    }

    public function addBook(Book $book): self
    {
        if (!$this->book->contains($book)) {
            $this->book->add($book);
        }

        return $this;
    }

    public function removeBook(Book $book): self
    {
        $this->book->removeElement($book);

        return $this;
    }

    public function isIsBasemented(): ?bool
    {
        return $this->isBasemented;
    }

    public function setIsBasemented(?bool $isBasemented): self
    {
        $this->isBasemented = $isBasemented;

        return $this;
    }

    public function getExpositionLocation(): ?ExpositionLocation
    {
        return $this->expositionLocation;
    }

    public function setExpositionLocation(?ExpositionLocation $expositionLocation): self
    {
        $this->expositionLocation = $expositionLocation;

        return $this;
    }

    public function getFloor(): ?Floor
    {
        return $this->floor;
    }

    public function setFloor(?Floor $floor): self
    {
        $this->floor = $floor;

        return $this;
    }

    public function getShowcaseCode(): ?string
    {
        return $this->showcaseCode;
    }

    public function setShowcaseCode(?string $showcaseCode): self
    {
        $this->showcaseCode = $showcaseCode;

        return $this;
    }

    public function getShelf(): ?string
    {
        return $this->shelf;
    }

    public function setShelf(?string $shelf): self
    {
        $this->shelf = $shelf;

        return $this;
    }

    public function getInsuranceValue(): ?string
    {
        return $this->insuranceValue;
    }

    public function setInsuranceValue(?string $insuranceValue): self
    {
        $this->insuranceValue = $insuranceValue;

        return $this;
    }

    public function getState(): ?State
    {
        return $this->state;
    }

    public function setState(?State $state): self
    {
        $this->state = $state;

        return $this;
    }

    public function getStateCommentary(): ?string
    {
        return $this->stateCommentary;
    }

    public function setStateCommentary(?string $stateCommentary): self
    {
        $this->stateCommentary = $stateCommentary;

        return $this;
    }

    public function getArrivedCollection(): ?\DateTimeInterface
    {
        return $this->arrivedCollection;
    }

    public function setArrivedCollection(?\DateTimeInterface $arrivedCollection): self
    {
        $this->arrivedCollection = $arrivedCollection;

        return $this;
    }

    /**
     * @return Collection<int, Image>
     */
    public function getImages(): Collection
    {
        return $this->images;
    }

    public function addImage(Image $image): self
    {
        if (!$this->images->contains($image)) {
            $this->images->add($image);
            $image->setObjects($this);
        }

        return $this;
    }

    public function removeImage(Image $image): self
    {
        if ($this->images->removeElement($image)) {
            // set the owning side to null (unless already changed)
            if ($image->getObjects() === $this) {
                $image->setObjects(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Video>
     */
    public function getVideos(): Collection
    {
        return $this->videos;
    }

    public function addVideo(Video $video): self
    {
        if (!$this->videos->contains($video)) {
            $this->videos->add($video);
            $video->setObjects($this);
        }

        return $this;
    }

    public function removeVideo(Video $video): self
    {
        if ($this->videos->removeElement($video)) {
            // set the owning side to null (unless already changed)
            if ($video->getObjects() === $this) {
                $video->setObjects(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, File>
     */
    public function getFiles(): Collection
    {
        return $this->files;
    }

    public function addFile(File $file): self
    {
        if (!$this->files->contains($file)) {
            $this->files->add($file);
            $file->setObjects($this);
        }

        return $this;
    }

    public function removeFile(File $file): self
    {
        if ($this->files->removeElement($file)) {
            // set the owning side to null (unless already changed)
            if ($file->getObjects() === $this) {
                $file->setObjects(null);
            }
        }

        return $this;
    }

//    /**
//     * @return Collection<int, Youtube>
//     */
//    public function getYoutube(): Collection
//    {
//        return $this->youtube;
//    }
//
//    public function addYoutube(Youtube $youtube): self
//    {
//        if (!$this->youtube->contains($youtube)) {
//            $this->youtube->add($youtube);
//            $youtube->setObjects($this);
//        }
//
//        return $this;
//    }
//
//    public function removeYoutube(Youtube $youtube): self
//    {
//        if ($this->youtube->removeElement($youtube)) {
//            // set the owning side to null (unless already changed)
//            if ($youtube->getObjects() === $this) {
//                $youtube->setObjects(null);
//            }
//        }
//
//        return $this;
//    }

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
            $action->setObject($this);
        }

        return $this;
    }

    public function removeAction(Action $action): self
    {
        if ($this->actions->removeElement($action)) {
            // set the owning side to null (unless already changed)
            if ($action->getObject() === $this) {
                $action->setObject(null);
            }
        }

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getUpdatedBy(): ?User
    {
        return $this->updatedBy;
    }

    public function setUpdatedBy(?User $updatedBy): self
    {
        $this->updatedBy = $updatedBy;

        return $this;
    }
}
