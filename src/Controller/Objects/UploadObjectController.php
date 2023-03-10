<?php

namespace App\Controller\Objects;
use App\Entity\Libraries\MuseumCatalogue;
use App\Entity\Objects\Metadata\Gods;
use App\Entity\Objects\Metadata\InventoryDate;
use App\Entity\Objects\Metadata\Materials;
use App\Entity\Objects\Metadata\Origin;
use App\Entity\Objects\Metadata\Population;
use App\Entity\Objects\Metadata\State;
use App\Entity\Objects\Metadata\Typology;
use App\Entity\Objects\Metadata\VernacularName;
use App\Entity\Objects\Objects;
use App\Repository\Libraries\MuseumCatalogueRepository;
use App\Repository\Objects\Metadata\ExpositionLocationRepository;
use App\Repository\Objects\Metadata\FloorRepository;
use App\Repository\Objects\Metadata\GodsRepository;
use App\Repository\Objects\Metadata\MaterialsRepository;
use App\Repository\Objects\Metadata\OriginRepository;
use App\Repository\Objects\Metadata\PopulationRepository;
use App\Repository\Objects\Metadata\StateRepository;
use App\Repository\Objects\Metadata\TypologyRepository;
use App\Repository\Objects\Metadata\VernacularNameRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Shuchkin\SimpleXLSX;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class UploadObjectController extends AbstractController
{

    public function __construct(
        private FloorRepository $floorRepository,
//        private StateRepository $stateRepository,
        private TypologyRepository $typologyRepository,
        private OriginRepository $originRepository,
        private VernacularNameRepository $vernacularNameRepository,
        private StateRepository $stateRepository,
        private GodsRepository $godsRepository,
        private MaterialsRepository $materialsRepository,
        private PopulationRepository $populationRepository,
        private MuseumCatalogueRepository $museumCatalogueRepository,
        private ExpositionLocationRepository $expositionLocationRepository,
        private EntityManagerInterface $manager,
    ){}

//    /**
//     * @throws \Exception
//     */
//    #[Route('/objects-import', name:'import_objects')]
//    #[IsGranted("ROLE_ADMIN", message: "Seules les Admins peuvent faire ??a")]
//    public function importObjects(Request $request): \Symfony\Component\HttpFoundation\Response
//    {
//
//        $error = [];
//        //Appellation = Title -> Nom vernaculaire (relation)
//        //Typologie = (Categories) -> Typologie (relation)
//        //Commentaire = Fonction usage
//        //Historique = Parution du mus??e
//
//
//        if ( $xlsx = SimpleXLSX::parse('inventaire.xlsx') ) {
//
//            $arrObjects = [];
//
////            dd($xlsx->rows());
//            //Index de chaque colonne selon les props de l'Entit??
//            $code = 0;
//            $title = 0;
//            $sizeLength = 0;
//            $sizeHigh = 0;
//            $sizeDepth = 0;
//            $usageFonction = 0;
//            $idxHistoricDetail = 0;
//            $showcaseCode = 0;
//            $floor = 0;
//            $createdAt = 0;
//            $updatedAt = 0;
//            $idxOrigin = 0;
//            $idxPopulation = 0;
//            $idxTypology = 0;
//            $idxMaterials = 0;
//            $idxInventoryDate = 0;
//            $idxVernacularName = 0;
//            $idxState = 0;
//            $idxRelatedGods = 0;
//            $idxGods = 0;
//            $idxBasement = 0;
//            $idxDocumentationCommentary = 0;
//            $idxMuseumCatalogue = 0;
//            $idxDescription = 0;
//            $idxDatation = 0;
//
//
//
//            foreach ($xlsx->rows() as $keyRow=>$row) {
//
//
//
//                //Initialisation des clefs pour chaque intitul?? de colonne
//                foreach ($row as $keyCol=>$col) {
//                    if ($col === 'N?? INVENTAIRE') {
//                        $code = $keyCol;
//                    }
//                    if ($col === 'LARGEUR') {
//                        $sizeLength = $keyCol;
//                    }
//                    if ($col === 'HAUTEUR') {
//                        $sizeHigh = $keyCol;
//                    }
//                    if ($col === 'Profondeur') {
//                        $sizeDepth = $keyCol;
//                    }
//                    if ($col === 'Commentaire') {
//                        $usageFonction = $keyCol;
//                    }
//                    if ($col === 'Hist. coll.') {
//                        $idxHistoricDetail = $keyCol;
//                    }
//                    if ($col === 'N?? Vitrine') {
//                        $showcaseCode = $keyCol;
//                    }
//                    if ($col === 'Etage') {
//                        $floor = $keyCol;
//                    }
//                    if ($col === 'Date de cr??ation') {
//                        $createdAt = $keyCol;
//                    }
//                    if ($col === 'Date de modification') {
//                        $updatedAt = $keyCol;
//                    }
//                    if ($col === 'Pays') {
//                        $idxOrigin = $keyCol;
//                    }
//                    if ($col === 'Population') {
//                        $idxPopulation = $keyCol;
//                    }
//                    if ($col === 'Typologie') {
//                        $idxTypology = $keyCol;
//                    }
//                    if ($col === 'Mat??riaux') {
//                        $idxMaterials = $keyCol;
//                    }
//                    if ($col === 'Remarque') {
//                        $idxInventoryDate = $keyCol;
//                    }
//                    if ($col === 'APPELLATION') {
//                        $idxVernacularName = $keyCol;
//                    }
//                    if ($col === "Constat d?????tat") {
//                        $idxState = $keyCol;
//                    }
//                    if ($col === "Divinit?? ou force associ??e") {
//                        $idxRelatedGods = $keyCol;
//                    }
//                    if ($col === "Divinit??s") {
//                        $idxGods = $keyCol;
//                    }
//                    if ($col === "Type soclage") {
//                        $idxBasement = $keyCol;
//                    }
//                    if ($col === "Biblio et Expographie") {
//                        $idxDocumentationCommentary = $keyCol;
//                    }
//                    if ($col === "Historique") {
//                        $idxMuseumCatalogue = $keyCol;
//                    }
//                    if ($col === "DESCRIPTION") {
//                        $idxDescription = $keyCol;
//                    }
//                    if ($col === "DATATION") {
//                        $idxDatation = $keyCol;
//                    }
//
//                }
//
//                //Parcours puis ajout des autres lignes
//                if ($keyRow !== 0) {
//
//
//                    $object = new Objects();
//                    $object->setCreatedBy($this->getUser());
//                    $object->setExpositionLocation($this->expositionLocationRepository->find(4));
//
//                    foreach ($row as $keyCol=>$value) {
//
//                        if ($keyCol === $code) {
//                            $object->setCode($value);
//                        }
//                        if ($keyCol === $sizeLength) {
//                            $object->setSizeLength(floatval(str_replace(',', '.', str_replace('.', '', $value))));
//                        }
//                        if ($keyCol === $sizeHigh) {
//                            $object->setSizeHigh(floatval(str_replace(',', '.', str_replace('.', '', $value))));
//                        }
//                        if ($keyCol === $sizeDepth) {
//                            $object->setSizeDepth(floatval(str_replace(',', '.', str_replace('.', '', $value))));
//                        }
//                        if ($keyCol === $usageFonction) {
//                            $object->setUsageFonction($value);
//                        }
//                        if ($keyCol === $idxHistoricDetail) {
//                            $object->setHistoricDetail($value);
//                        }
//                        if ($keyCol === $showcaseCode) {
//                            $object->setShowcaseCode($value);
//                            $valueShowcaseCode = explode(',', $value);
//                            if(count($valueShowcaseCode) > 1) {
//                                $valueShelf = trim($valueShowcaseCode[1]);
//                                $object->setShelf($valueShelf);
//                                $valueShowcaseCode = trim($valueShowcaseCode[0]);
//                                $object->setShowcaseCode($valueShowcaseCode);
//                            }
//                        }
//                        if ($keyCol === $floor) {
//                            $value = trim(strtolower($value));
//                            if ($value === 'rdc') {
//                                $object->setFloor($this->floorRepository->find(2));
//                                $object->setExpositionLocation($this->expositionLocationRepository->find(2));
//                            }elseif ($value === 'mezzanine') {
//                                $object->setFloor($this->floorRepository->find(3));
//                                $object->setExpositionLocation($this->expositionLocationRepository->find(4));
//                            } elseif ($value === '1') {
//                                $object->setFloor($this->floorRepository->find(4));
//                                $object->setExpositionLocation($this->expositionLocationRepository->find(3));
//                            } elseif ($value === '2') {
//                                $object->setFloor($this->floorRepository->find(5));
//                                $object->setExpositionLocation($this->expositionLocationRepository->find(3));
//                            } elseif ($value === '3') {
//                                $object->setFloor($this->floorRepository->find(6));
//                                $object->setExpositionLocation($this->expositionLocationRepository->find(3));
//                            } elseif ($value === 'container') {
//                                $object->setFloor($this->floorRepository->find(7));
//                            } elseif ($value === 'arbogast') {
//                                $object->setFloor($this->floorRepository->find(8));
//                            } elseif ($value === 'escaliers') {
//                                $object->setFloor($this->floorRepository->find(9));
//                                $object->setExpositionLocation($this->expositionLocationRepository->find(3));
//                            } else {
//                                $object->setFloor($this->floorRepository->find(1));//???
//                                $object->setExpositionLocation($this->expositionLocationRepository->find(4));
//                            }
//                        }
//                        if($keyCol === $createdAt) {
//                            if ($value) {
//                                $newDate = str_replace('/', '-', $value);
//                                $newDate = new \DateTimeImmutable($newDate);
//                            } else {
//                                $newDate = new \DateTimeImmutable();
//                            }
//                            $object->setCreatedAt($newDate);
//                        }
//                        if($keyCol === $updatedAt) {
//                            if ($value) {
//                                $newDate = str_replace('/', '-', $value);
//                                $newDate = new \DateTimeImmutable($newDate);
//                            } else {
//                                $newDate = new \DateTimeImmutable();
//                            }
//                            $object->setUpdatedAt($newDate);
//                        }
//
//                        if($keyCol === $idxOrigin) {
//
//                            $origins = $this->originRepository->findAll();
//                            $arr = array_map('trim', explode(',', str_replace([',','/','-','ou'],',', $value)));
//                            foreach ($arr as $a) {
//                                if ($a === 'A d??terminer' || $a === '?' || $a === "") {
//                                    $a = '???';
//                                }
//                                $match = false;
//                                foreach ($origins as $o) {
//                                    if ($o->getName() == $a) {
//                                        $object->addOrigin($o);
//                                        $match = true;
//                                        break; // on sort de la boucle interne car on a trouv?? une correspondance
//                                    }
//                                }
//                                // si on n'a pas trouv?? de correspondance, on cr??e une nouvelle instance de Origin
//                                if (!$match) {
//                                    $newOrigin = new Origin();
//                                    $newOrigin->setName($a);
//                                    $this->manager->persist($newOrigin);
//                                    $this->manager->flush();
//                                    $object->addOrigin($newOrigin);
//                                }
//                            }
//                        }
//
//                        if($keyCol === $idxPopulation) {
//                            $populations = $this->populationRepository->findAll();
//                            $arr = array_map('trim', explode(',', str_replace([',','/','-'],',', $value)));
//                            foreach ($arr as $a) {
//                                if ($a === 'A d??terminer' || $a === '?' || $a === "" || $a === 'autre' || $a == 'autres') {
//                                    $a = '???';
//                                }
//                                $match = false;
//                                foreach ($populations as $p) {
//                                    if ($p->getName() == $a) {
//                                        $object->addPopulation($p);
//                                        $match = true;
//                                        break; // on sort de la boucle interne car on a trouv?? une correspondance
//                                    }
//                                }
//                                // si on n'a pas trouv?? de correspondance, on cr??e une nouvelle instance de Origin
//                                if (!$match) {
//                                    $newPopulation = new Population();
//                                    $newPopulation->setName($a);
//                                    $this->manager->persist($newPopulation);
//                                    $this->manager->flush();
//                                    $object->addPopulation($newPopulation);
//                                }
//                            }
//                        }
//
//                        if($keyCol === $idxTypology) {
//                            $typologies = $this->typologyRepository->findAll();
//                            if ($value === 'A d??terminer' || $value === '?' || $value === "" || $value === 'autre' || $value == 'autres') {
//                                $value = '???';
//                            }
//                            $match = false;
//                            foreach ($typologies as $t) {
//                                if ($t->getName() == $value) {
//                                    $object->setTypology($t);
//                                    $match = true;
//                                    break; // on sort de la boucle interne car on a trouv?? une correspondance
//                                }
//                            }
//                            // si on n'a pas trouv?? de correspondance, on cr??e une nouvelle instance de Origin
//                            if (!$match) {
//                                $newTypology = new Typology();
//                                $newTypology->setName($value);
//                                $this->manager->persist($newTypology);
//                                $this->manager->flush();
//                                $object->setTypology($newTypology);
//                            }
//
//                        }
//
//                        if($keyCol === $idxMaterials) {
//                            $materials = $this->materialsRepository->findAll();
//                            $value = str_replace('.', '', $value);
//                            $arr = array_map('trim', explode(',', str_replace([',','/', ' et '],',', strtolower($value))));
//                            foreach ($arr as $a) {
//                                if ($a === 'A d??terminer' || $a == 'a d??terminer' || $a === '?' || $a === "" || $a === 'autre' || $a == 'autres') {
//                                    $a = '???';
//                                }
//                                $match = false;
//                                foreach ($materials as $m) {
//                                    if ($m->getName() == $a) {
//                                        $object->addMaterial($m);
//                                        $match = true;
//                                        break; // on sort de la boucle interne car on a trouv?? une correspondance
//                                    }
//                                }
//                                // si on n'a pas trouv?? de correspondance, on cr??e une nouvelle instance de Origin
//                                if (!$match) {
//                                    $newMaterial = new Materials();
//                                    $newMaterial->setName($a);
//                                    $this->manager->persist($newMaterial);
//                                    $this->manager->flush();
//                                    $object->addMaterial($newMaterial);
//                                }
//                            }
//                        }
//
//                        if($keyCol === $idxInventoryDate) {
//
//
//                            $monthList = ["janvier", "f??vrier", "mars", "avril", "mai", "juin", "juillet", "ao??t", "septembre", "octobre", "novembre", "d??cembre"];
//
//
//                            //Arriv?? en
//                            if (str_contains($value, 'arriv?? en ')) {
//                                $object->setArrivedCollection(new \DateTimeImmutable('01-09-2019'));
//                            }
//
//
//                            $arr = explode("\n", $value);
//
//                            $dates = array_map('trim', explode('-', str_replace('r??col?? le ', "", strtolower($arr[0]))));
//
//                            $dateTimes = [];
//                            foreach ($dates as $date) {
//                                $arrDate = explode(' ', $date);
//                                if  (count($arrDate) > 1) {
//                                    if (in_array($arrDate[1], $monthList)) {
//                                        $key = array_search($arrDate[1], $monthList);
//                                        $dateTimes[] = new \DateTimeImmutable(str_replace('/', '-', $arrDate[2].'-'.($key+1).'-'.$arrDate[0]));
//                                    }
//                                } else {
//                                    $arrSlashDate = explode('/', $date);
//                                    if (count($arrSlashDate)>2) {
//                                        $dateTimes[] = new \DateTimeImmutable($arrSlashDate[2].'-'.$arrSlashDate[1].'-'.$arrSlashDate[0]);
//                                    }
//                                }
//                            }
//
//                            if ($dateTimes) {
//                                foreach ($dateTimes as $dateTime) {
//                                    $inventoryDate = new InventoryDate();
//                                    $inventoryDate->setInventoriedAt($dateTime);
//                                    $object->addInventoriedAt($inventoryDate);
//                                }
//                            }
//
//                            //Remarks
//                            if(count($arr) > 1) {
//                                $remarks = $arr[1];
//                                $object->setMemo($remarks);
//                            }
//                        }
//
//
//                        if($keyCol === $idxVernacularName) {
//                            $vernacularNames = $this->vernacularNameRepository->findAll();
//                            $value = ucfirst(trim(strtolower($value)));
//                            if ($value === 'a d??terminer' || $value === '?' || $value === "" || $value === 'autre' || $value == 'vodou') {
//                                $value = '???';
//                            }
//                            $match = false;
//                            foreach ($vernacularNames as $v) {
//                                if ($v->getName() == $value) {
//                                    $object->setVernacularName($v);
//                                    $match = true;
//                                    break; // on sort de la boucle interne car on a trouv?? une correspondance
//                                }
//                            }
//                            // si on n'a pas trouv?? de correspondance, on cr??e une nouvelle instance de Origin
//                            if (!$match) {
//                                $newVernacularName = new VernacularName();
//                                $newVernacularName->setName($value);
//                                $this->manager->persist($newVernacularName);
//                                $this->manager->flush();
//                                $object->setVernacularName($newVernacularName);
//                            }
//                        }
//
//
//                        if($keyCol === $idxState) {
//
//                            $value = str_replace('.', ' ', strtolower($value));
//
//                            if (str_contains($value, 'a d??terminer') ) {
//                                $object->setState($this->stateRepository->find(1));
//                                $object->setStateCommentary(str_replace('a d??terminer', '', $value));
//                            } elseif (str_contains($value, 'ok')|| str_contains('correct', $value)) {
//                                $object->setState($this->stateRepository->find(2));
//                                $object->setStateCommentary(str_replace(['ok', 'correct'], '', $value));
//                            } elseif (str_contains($value, 'bon ??tat')) {
////                                dd($value . '-' . $object->getCode());
//                                if (str_contains($value, 'pas en bon')) {
//                                    $object->setState($this->stateRepository->find(1));
//                                    $object->setStateCommentary($value);
//                                } else {
//                                    if (str_contains($value, 'tr??s bon ??tat')) {
//                                        $object->setState($this->stateRepository->find(2));
//                                        $object->setStateCommentary(str_replace(['tr??s bon ??tat'], '', $value));
//                                    } elseif (str_contains($value, 'bon ??tat')) {
//                                        $object->setState($this->stateRepository->find(3));
//                                        $object->setStateCommentary(str_replace(['bon ??tat'], '', $value));
//                                    } elseif (str_contains($value, 'assez bon')) {
//                                        $object->setState($this->stateRepository->find(4));
//                                        $object->setStateCommentary(str_replace(['assez bon', 'assez bon ??tat'], '', $value));
//                                    }
//                                }
//
//                            } elseif (str_contains($value, 'moyen')) {
//                                $object->setState($this->stateRepository->find(4));
//                                $object->setStateCommentary(str_replace(['moyen', '??tat moyen', 'moyen ??tat'], '', $value));
//                            } elseif(str_contains($value,'mauvais')) {
//                                if (str_contains($value, 'tr??s mauvais')) {
//                                    $object->setState($this->stateRepository->find(6));
//                                    $object->setStateCommentary(str_replace(['tr??s mauvais', 'tr??s mauvais ??tat'], '', $value));
//                                } else {
//                                    $object->setState($this->stateRepository->find(5));
//                                    $object->setStateCommentary(str_replace(['mauvais', 'mauvais ??tat'], '', $value));
//                                }
//
//                            } elseif(str_contains($value, 'critique')) {
//                                $object->setState($this->stateRepository->find(6));
//                                $object->setStateCommentary(str_replace(['critique', '??tat critique', 'tr??s critique'], '', $value));
//                            } else {
//                                $object->setState($this->stateRepository->find(1));
//                                $object->setStateCommentary($value);
//                            }
//
//                        }
//
//
//                        if($keyCol === $idxRelatedGods) {
//                            $gods = $this->godsRepository->findAll();
//                            $arr = array_map('trim', explode(',', str_replace([',','/','-'],',', $value)));
//
//                            foreach ($arr as $a) {
//                                if ($a === 'A d??terminer' || $a === '?' || $a === "" || $a === 'autre' || $a == 'autres') {
//                                    $a = '???';
//                                }
//                                $match = false;
//                                foreach ($gods as $g) {
//                                    if ($g->getName() == $a) {
//                                        $object->addRelatedGod($g);
//                                        $match = true;
//                                        break; // on sort de la boucle interne car on a trouv?? une correspondance
//                                    }
//                                }
//                                // si on n'a pas trouv?? de correspondance, on cr??e une nouvelle instance de Origin
//                                if (!$match) {
//                                    $newGods = new Gods();
//                                    $newGods->setName($a);
//                                    $this->manager->persist($newGods);
//                                    $this->manager->flush();
//                                    $object->addRelatedGod($newGods);
//                                }
//                            }
//                        }
//
//                        if($keyCol === $idxGods) {
//                            $gods = $this->godsRepository->findAll();
//                            $arr = array_map('trim', explode(',', str_replace([',','/','-'],',', $value)));
//
//                            foreach ($arr as $a) {
//                                if ($a === 'A d??terminer' || $a === '?' || $a === "" || $a === 'autre' || $a == 'autres') {
//                                    $a = '???';
//                                }
//                                $match = false;
//                                foreach ($gods as $g) {
//                                    if ($g->getName() == $a) {
//                                        $object->setGods($g);
//                                        $match = true;
//                                        break; // on sort de la boucle interne car on a trouv?? une correspondance
//                                    }
//                                }
//                                // si on n'a pas trouv?? de correspondance, on cr??e une nouvelle instance de Origin
//                                if (!$match) {
//                                    $newGods = new Gods();
//                                    $newGods->setName($a);
//                                    $this->manager->persist($newGods);
//                                    $this->manager->flush();
//                                    $object->setGods($newGods);
//                                }
//                            }
//                        }
//
//                        if($keyCol === $idxBasement) {
//                            $object->setBasementCommentary($value);
//                            $value = str_replace('.', ' ', strtolower($value));
//
//                            if (
//                                str_contains($value, 'socle m??tallique') ||
//                                str_contains($value, 'socle en bois') ||
//                                str_contains($value, 'socl??') ||
//                                str_contains($value, 'socle en m??tal') ||
//                                str_contains($value, 'socle noir') ||
//                                str_contains($value, 'socle rectangle')
//                            ) {
//                                $object->setIsBasemented(true);
//                            } else {
//                                $object->setIsBasemented(false);
//                            }
//
//                        }
//
//                        if($keyCol === $idxDocumentationCommentary) {
//                            $object->setDocumentationCommentary($value);
//                        }
//
//                        if($keyCol === $idxMuseumCatalogue) {
//                            $museumCatalogue = $this->museumCatalogueRepository->findAll();
//
//                            $match = false;
//                            foreach ($museumCatalogue as $m) {
//                                if ($m->getName() == $value) {
//                                    $object->addMuseumCatalogue($m);
//                                    $match = true;
//                                    break; // on sort de la boucle interne car on a trouv?? une correspondance
//                                }
//                            }
//                            // si on n'a pas trouv?? de correspondance, on cr??e une nouvelle instance de Origin
//                            if (!$match) {
//                                $newMuseumCatalogue = new MuseumCatalogue();
//                                $newMuseumCatalogue->setName($value);
//                                $this->manager->persist($newMuseumCatalogue);
//                                $this->manager->flush();
//                                $object->addMuseumCatalogue($newMuseumCatalogue);
//                            }
//
//                        }
//
//                        if($keyCol === $idxDescription) {
//                            $object->setNaturalLanguageDescription($value);
//                        }
//
//                        //Datation
//
//                    }
//                    $arrObjects[] = $object;
//                }
//
//
//            }//foreach rows
//
//            foreach ($arrObjects as $obj) {
//                $this->manager->persist($obj);
//            }
//            $this->manager->flush();
//
//
//
//
//
//        } else {
//            echo SimpleXLSX::parseError();
//        }
//
//        return $this->redirectToRoute('objects_listing');
//
//    }
}
