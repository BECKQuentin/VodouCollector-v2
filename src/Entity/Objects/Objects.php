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
use App\Entity\Objects\Metadata\InventoryDate;
use App\Entity\Objects\Metadata\Materials;
use App\Entity\Objects\Metadata\Origin;
use App\Entity\Objects\Metadata\Population;
use App\Entity\Objects\Metadata\State;
use App\Entity\Objects\Metadata\Typology;
use App\Entity\Objects\Metadata\VernacularName;
use App\Entity\Objects\SharedBookmarks\SharedBookmarks;
use App\Entity\Site\Action;
use App\Entity\User\User;
use App\Repository\Objects\ObjectsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Mapping\ClassMetadata;

#[UniqueEntity(
    fields: ['code'],
    message: 'Ce code est déjà utilisé',
)]
#[ORM\Entity(repositoryClass: ObjectsRepository::class)]
class Objects
{
    const LABEL_CODE = "N°Inventaire";
    const LABEL_VERNACULAR_NAME = "Nom vernaculaire";
    const LABEL_TYPOLOGY = "Typologie";
    const LABEL_PRECISION_VERNACULAR_NAME = "Précision nom vernaculaire";
    const LABEL_GODS = "Divinités";
    const LABEL_RELATED_GODS = "Divinités associées";
    const LABEL_ORIGIN = "Origine";
    const LABEL_POPULATION = "Population";
    const LABEL_HISTORICAL_DETAIL = "Mode d'acquisition";
    const LABEL_USAGE_FONCTION = "Fonction d'usage";
    const LABEL_USAGE_USER = "Utilisateurs";
    const LABEL_NATURAL_LANGUAGE_DESCRIPTION = "Description en langage naturel";
    const LABEL_INSCRIPTIONS_ENGRAVINGS = "Inscriptions et marques";
    const LABEL_MATERIALS = "Matériaux";
    const LABEL_DOCUMENTATION_COMMENTARY = "Commentaire de documentation";
    const LABEL_MUSEUM_CATALOGUE = "Publication du musée";
    const LABEL_BOOKS = "Ouvrages";
    const LABEL_STATE = "État";
    const LABEL_STATE_COMMENTARY = "Remarque sur l' état";
    const LABEL_WEIGHT = "Poids";
    const LABEL_SIZE_HIGH = "Hauteur";
    const LABEL_SIZE_LENGTH = "Longueur";
    const LABEL_SIZE_DEPTH = "Profondeur";
    const LABEL_IS_BASEMENTED = "Avec socle";
    const LABEL_BASEMENT_COMMENTARY = "Commentaire de Soclage";
    const LABEL_EXPOSITION_LOCATION = "Lieu d'exposition";
    const LABEL_FLOOR = "Etage";
    const LABEL_SHOWCASE_CODE = "N° de vitrine";
    const LABEL_SHELF = "N° étagère";
    const LABEL_ARRIVED_COLLECTION = "Date d'acquisition";
    const LABEL_CREATED_AT = "Fiche créee le";
    const LABEL_CREATED_BY = "Fiche créee par";
    const LABEL_INVENTORIED_AT = "Recolé le";

    //Labels qui peuvent être diffusé publiquement(extraction csv, excel, pdf) => Aucune donnée sensible(insuranceValue)
    const OBJ_PUBLIC_LABELS = [self::LABEL_CODE, self::LABEL_VERNACULAR_NAME, self::LABEL_TYPOLOGY, self::LABEL_PRECISION_VERNACULAR_NAME, self::LABEL_GODS, self::LABEL_RELATED_GODS, self::LABEL_ORIGIN,
        self::LABEL_POPULATION, self::LABEL_HISTORICAL_DETAIL, self::LABEL_USAGE_FONCTION, self::LABEL_USAGE_USER, self::LABEL_NATURAL_LANGUAGE_DESCRIPTION, self::LABEL_INSCRIPTIONS_ENGRAVINGS, self::LABEL_MATERIALS, self::LABEL_DOCUMENTATION_COMMENTARY,
        self::LABEL_MUSEUM_CATALOGUE, self::LABEL_BOOKS, self::LABEL_STATE, self::LABEL_STATE_COMMENTARY, self::LABEL_WEIGHT, self::LABEL_SIZE_HIGH, self::LABEL_SIZE_LENGTH, self::LABEL_SIZE_DEPTH, self::LABEL_IS_BASEMENTED, self::LABEL_BASEMENT_COMMENTARY,
        self::LABEL_EXPOSITION_LOCATION, self::LABEL_FLOOR, self::LABEL_SHOWCASE_CODE, self::LABEL_SHELF, self::LABEL_ARRIVED_COLLECTION, self::LABEL_CREATED_AT, self::LABEL_CREATED_BY, self::LABEL_INVENTORIED_AT];


    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[NotBlank]
    #[ORM\Column(length: 255)]
    private ?string $code = null;

    #[ORM\ManyToOne(inversedBy: 'objects')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Typology $typology = null;

    #[ORM\ManyToOne(inversedBy: 'objects')]
    #[ORM\JoinColumn(nullable: false)]
    private ?VernacularName $vernacularName = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $precisionVernacularName = null;

    #[ORM\ManyToOne(inversedBy: 'objects')]
    private ?Gods $gods = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $memo = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\ManyToOne(inversedBy: 'objects')]
    #[ORM\JoinColumn(nullable: true, onDelete: "SET NULL")]
    private ?User $createdBy = null;

    #[ORM\Column(nullable: true)]
    private ?int $antequemDatation = null;

    #[ORM\Column(nullable: true)]
    private ?int $preciseDatation = null;

    #[ORM\Column(nullable: true)]
    private ?int $postquemDatation = null;

    #[ORM\ManyToMany(targetEntity: Origin::class, inversedBy: 'objects', cascade: ["persist", "remove"])]
    private Collection $origin;

    #[ORM\ManyToMany(targetEntity: Population::class, inversedBy: 'objects', cascade: ["persist", "remove"])]
    private Collection $population;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $historicDetail = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $usageFonction = null;

//    #[ORM\Column(type: Types::ARRAY, nullable: true)]
//    private array $usageTags = [];

    #[ORM\ManyToMany(targetEntity: Gods::class, inversedBy: 'objectsRelated', cascade: ["persist", "remove"])]
    private Collection $relatedGods;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $usageUser = null;

    #[ORM\ManyToMany(targetEntity: Materials::class, inversedBy: 'objects', cascade: ["persist", "remove"])]
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

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $documentationCommentary = null;

    #[ORM\ManyToMany(targetEntity: MuseumCatalogue::class, inversedBy: 'objects')]
    private Collection $museumCatalogue;

    #[ORM\ManyToMany(targetEntity: Book::class, inversedBy: 'objects')]
    private Collection $book;

    #[ORM\Column(nullable: true)]
    private ?bool $isBasemented = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $basementCommentary = null;

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

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $stateCommentary = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $arrivedCollection = null;

    #[ORM\OneToMany(mappedBy: 'objects', targetEntity: InventoryDate::class, cascade: ["persist", "remove"], orphanRemoval: true)]
    private Collection $inventoriedAt;

    #[ORM\OneToMany(mappedBy: 'objects', targetEntity: Image::class, cascade: ["persist"], orphanRemoval: true)]
    private Collection $images;

    #[ORM\OneToMany(mappedBy: 'objects', targetEntity: Video::class, cascade: ["persist"], orphanRemoval: true)]
    private Collection $videos;

    #[ORM\OneToMany(mappedBy: 'objects', targetEntity: File::class, cascade: ["persist"], orphanRemoval: true)]
    private Collection $files;

    #[ORM\OneToMany(mappedBy: 'objects', targetEntity: Youtube::class, cascade: ["persist"], orphanRemoval: true)]
    private Collection $youtube;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $youtubeLink = null;

    #[ORM\OneToMany(mappedBy: 'object', targetEntity: Action::class)]
    #[ORM\JoinColumn(onDelete: "SET NULL")]
    private Collection $actions;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $updatedAt = null;

    #[ORM\ManyToOne(inversedBy: 'updatedObjects')]
    #[ORM\JoinColumn(onDelete: 'SET NULL')]
    private ?User $updatedBy = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $deletedAt = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(onDelete: 'SET NULL')]
    private ?User $deletedBy = null;

    #[ORM\ManyToMany(targetEntity: SharedBookmarks::class, mappedBy: 'objects')]
    private Collection $sharedBookmarks;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $expositionRemarks = null;

    public function __construct()
    {
        $this->setUpdatedAt(new \DateTimeImmutable('now'));
        if ($this->getCreatedAt() === null) {
            $this->setCreatedAt(new \DateTimeImmutable('now'));
        }
        $this->relatedGods = new ArrayCollection();
        $this->origin = new ArrayCollection();
        $this->population = new ArrayCollection();
        $this->materials = new ArrayCollection();
        $this->museumCatalogue = new ArrayCollection();
        $this->book = new ArrayCollection();
        $this->images = new ArrayCollection();
        $this->videos = new ArrayCollection();
        $this->files = new ArrayCollection();
        $this->youtube = new ArrayCollection();
        $this->actions = new ArrayCollection();
        $this->sharedBookmarks = new ArrayCollection();
        $this->inventoriedAt = new ArrayCollection();
    }

    public static function loadValidatorMetadata(ClassMetadata $metadata)
    {
        $metadata->addConstraint(new UniqueEntity([
            'fields' => 'id',
            'errorPath' => 'id',
            'message' => 'Ce code existe déjà !',
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

    public function getTypology(): ?Typology
    {
        return $this->typology;
    }

    public function setTypology(?Typology $typology): self
    {
        $this->typology = $typology;

        return $this;
    }

    public function getVernacularName(): ?VernacularName
    {
        return $this->vernacularName;
    }

    public function setVernacularName(?VernacularName $vernacularName): self
    {
        $this->vernacularName = $vernacularName;

        return $this;
    }

    public function getPrecisionVernacularName(): ?string
    {
        return $this->precisionVernacularName;
    }

    public function setPrecisionVernacularName(?string $precisionVernacularName): self
    {
        $this->precisionVernacularName = $precisionVernacularName;

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

    public function getAntequemDatation(): ?int
    {
        return $this->antequemDatation;
    }

    public function setAntequemDatation(int $antequemDatation): self
    {
        $this->antequemDatation = $antequemDatation;

        return $this;
    }

    public function getPreciseDatation(): ?int
    {
        return $this->preciseDatation;
    }

    public function setPreciseDatation(?int $preciseDatation): self
    {
        $this->preciseDatation = $preciseDatation;

        return $this;
    }

    public function getPostquemDatation(): ?int
    {
        return $this->postquemDatation;
    }

    public function setPostquemDatation(?int $postquemDatation): self
    {
        $this->postquemDatation = $postquemDatation;

        return $this;
    }

    /**
     * @return Collection<int, Origin>
     */
    public function getOrigin(): Collection
    {
        return $this->origin;
    }

    public function addOrigin(Origin $origin): self
    {
        if (!$this->origin->contains($origin)) {
            $this->origin->add($origin);
        }

        return $this;
    }

    public function removeOrigin(Origin $origin): self
    {
        $this->origin->removeElement($origin);

        return $this;
    }

    /**
     * @return Collection<int, Population>
     */
    public function getPopulation(): Collection
    {
        return $this->population;
    }

    public function addPopulation(Population $population): self
    {
        if (!$this->population->contains($population)) {
            $this->population->add($population);
        }

        return $this;
    }

    public function removePopulation(Population $population): self
    {
        $this->population->removeElement($population);

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

    public function getDocumentationCommentary(): ?string
    {
        return $this->documentationCommentary;
    }

    public function setDocumentationCommentary(?string $documentationCommentary): self
    {
        $this->documentationCommentary = $documentationCommentary;

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

    public function getBasementCommentary(): ?string
    {
        return $this->basementCommentary;
    }

    public function setBasementCommentary(?string $basementCommentary): self
    {
        $this->basementCommentary = $basementCommentary;

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
     * @return Collection<int, InventoryDate>
     */
    public function getInventoriedAt(): Collection
    {
        return $this->inventoriedAt;
    }

    public function addInventoriedAt(InventoryDate $inventoriedAt): self
    {
        if (!$this->inventoriedAt->contains($inventoriedAt)) {
            $this->inventoriedAt->add($inventoriedAt);
            $inventoriedAt->setObjects($this);
        }

        return $this;
    }

    public function removeInventoriedAt(InventoryDate $inventoriedAt): self
    {
        if ($this->inventoriedAt->removeElement($inventoriedAt)) {
            // set the owning side to null (unless already changed)
            if ($inventoriedAt->getObjects() === $this) {
                $inventoriedAt->setObjects(null);
            }
        }

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

    public function getYoutubeLink(): ?string
    {
        return $this->youtubeLink;
    }

    public function setYoutubeLink(?string $youtubeLink): self
    {
        $this->youtubeLink = $youtubeLink;

        return $this;
    }

    /**
     * @return Collection<int, Youtube>
     */
    public function getYoutube(): Collection
    {
        return $this->youtube;
    }
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

    public function getDeletedAt(): ?\DateTimeImmutable
    {
        return $this->deletedAt;
    }

    public function setDeletedAt(?\DateTimeImmutable $deletedAt): self
    {
        $this->deletedAt = $deletedAt;

        return $this;
    }

    public function getDeletedBy(): ?User
    {
        return $this->deletedBy;
    }

    public function setDeletedBy(?User $deletedBy): self
    {
        $this->deletedBy = $deletedBy;

        return $this;
    }

    /**
     * @return Collection<int, SharedBookmarks>
     */
    public function getSharedBookmarks(): Collection
    {
        return $this->sharedBookmarks;
    }

    public function addSharedBookmark(SharedBookmarks $sharedBookmark): self
    {
        if (!$this->sharedBookmarks->contains($sharedBookmark)) {
            $this->sharedBookmarks->add($sharedBookmark);
            $sharedBookmark->addObject($this);
        }

        return $this;
    }

    public function removeSharedBookmark(SharedBookmarks $sharedBookmark): self
    {
        if ($this->sharedBookmarks->removeElement($sharedBookmark)) {
            $sharedBookmark->removeObject($this);
        }

        return $this;
    }

    public function getExpositionRemarks(): ?string
    {
        return $this->expositionRemarks;
    }

    public function setExpositionRemarks(?string $expositionRemarks): self
    {
        $this->expositionRemarks = $expositionRemarks;

        return $this;
    }

}
