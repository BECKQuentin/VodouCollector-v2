<?php

namespace App\Controller\Objects;
use App\Entity\Objects\Metadata\Origin;
use App\Entity\Objects\Metadata\State;
use App\Entity\Objects\Objects;
use App\Repository\Objects\Metadata\FloorRepository;
use App\Repository\Objects\Metadata\OriginRepository;
use App\Repository\Objects\Metadata\StateRepository;
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
        private OriginRepository $originRepository,
        private EntityManagerInterface $manager,
    ){}

    /**
     * @throws \Exception
     */
    #[Route('/objects-import', name:'import_objects')]
    #[IsGranted("ROLE_ADMIN", message: "Seules les Admins peuvent faire ça")]
    public function importObjects(Request $request)
    {

        //Appellation = Title -> Nom vernaculaire (relation)
        //Typologie = Categories -> Typologie (relation)
        //Commentaire = Fonction usage
        //Historique = Parution du musée


        if ( $xlsx = SimpleXLSX::parse('inventaire.xlsx') ) {

            $arrObjects = [];

//            dd($xlsx->rows());
            //Index de chaque colonne selon les props de l'Entité
            $code = 0;
            $title = 0;
            $sizeLength = 0;
            $sizeHigh = 0;
            $sizeDepth = 0;
            $usageFonction = 0;
            $showcaseCode = 0;
            $floor = 0;
            $createdAt = 0;
            $updatedAt = 0;
            $origin = 0;


            foreach ($xlsx->rows() as $keyRow=>$row) {



                //Initialisation des clefs pour chaque intitulé de colonne
                foreach ($row as $keyCol=>$col) {
                    if ($col === 'N° INVENTAIRE') {
                        $code = $keyCol;
                    }
                    if ($col === 'LARGEUR') {
                        $sizeLength = $keyCol;
                    }
                    if ($col === 'HAUTEUR') {
                        $sizeHigh = $keyCol;
                    }
                    if ($col === 'Profondeur') {
                        $sizeDepth = $keyCol;
                    }
                    if ($col === 'Commentaire') {
                        $usageFonction = $keyCol;
                    }
                    if ($col === 'N° Vitrine') {
                        $showcaseCode = $keyCol;
                    }
                    if ($col === 'Etage') {
                        $floor = $keyCol;
                    }
                    if ($col === 'Date de création') {
                        $createdAt = $keyCol;
                    }
                    if ($col === 'Date de modification') {
                        $updatedAt = $keyCol;
                    }
                    if ($col === 'Pays') {
                        $origin = $keyCol;
                    }
                }

                //Parcours puis ajout des autres lignes
                if ($keyRow !== 0) {
                    $object = new Objects();

                    foreach ($row as $keyCol=>$value) {
                        if ($keyCol === $code) {
                            $object->setCode($value);
                        }
                        if ($keyCol === $sizeLength) {
                            $object->setSizeLength(floatval(str_replace(',', '.', str_replace('.', '', $value))));
                        }
                        if ($keyCol === $sizeHigh) {
                            $object->setSizeHigh(floatval(str_replace(',', '.', str_replace('.', '', $value))));
                        }
                        if ($keyCol === $sizeDepth) {
                            $object->setSizeDepth(floatval(str_replace(',', '.', str_replace('.', '', $value))));
                        }
                        if ($keyCol === $usageFonction) {
                            $object->setUsageFonction($value);
                        }
                        if ($keyCol === $showcaseCode) {
                            $object->setShowcaseCode($value);
                            $valueShowcaseCode = explode(',', $value);
                            if(count($valueShowcaseCode) > 1) {
                                $valueShelf = trim($valueShowcaseCode[1]);
                                $object->setShelf($valueShelf);
                                $valueShowcaseCode = trim($valueShowcaseCode[0]);
                                $object->setShowcaseCode($valueShowcaseCode);
                            }
                        }
                        if ($keyCol === $floor) {
                            if ($value === 'RDC') {
                                $object->setFloor($this->floorRepository->find(2));
                            }elseif ($value === 'Mezzanine') {
                                $object->setFloor($this->floorRepository->find(3));
                            } elseif ($value === '1') {
                                $object->setFloor($this->floorRepository->find(4));
                            } elseif ($value === '2') {
                                $object->setFloor($this->floorRepository->find(5));
                            } elseif ($value === '3') {
                                $object->setFloor($this->floorRepository->find(6));
                            } elseif ($value === 'Container') {
                                $object->setFloor($this->floorRepository->find(7));
                            } elseif ($value === 'Arbogast') {
                                $object->setFloor($this->floorRepository->find(8));
                            } elseif ($value === 'Escaliers') {
                                $object->setFloor($this->floorRepository->find(9));
                            } else {
                                $object->setFloor($this->floorRepository->find(1));//???
                            }
                        }
                        if($keyCol === $createdAt) {
                            if ($value) {
                                $newDate = str_replace('/', '-', $value);
                                $newDate = new \DateTimeImmutable($newDate);
                            } else {
                                $newDate = new \DateTimeImmutable();
                            }
                            $object->setCreatedAt($newDate);
                        }
                        if($keyCol === $updatedAt) {
                            if ($value) {
                                $newDate = str_replace('/', '-', $value);
                                $newDate = new \DateTimeImmutable($newDate);
                            } else {
                                $newDate = new \DateTimeImmutable();
                            }
                            $object->setUpdatedAt($newDate);
                        }
                        if($keyCol === $origin) {
                            $arrOriginsName = [];
                            $origins = $this->originRepository->findAll();
                            foreach ($origins as $origin) {
                                $arrOriginsName[] = $origin->getName();
                            }
                            $arr = array_map('trim', explode(',', str_replace([',','/'],',', $value)));
                            foreach ($arr as $a) {
                                if ($a !== "") {
                                    if ($a === 'A déterminer') {
                                        $a = '???';
                                    }
                                    if ( in_array($a, $arrOriginsName)) {
                                        $object->setOrigin($origin);
                                    } else {
                                        $newOrigin = new Origin();
                                        $newOrigin->setName($a);
                                        $this->manager->persist($newOrigin);
                                        $this->manager->flush();
                                        $object->setorigin($newOrigin);
                                    }
                                }
                            }
//                            dd($arr);
//                            if  ()



                        }




                    }
//                    dd($object);
                    $arrObjects[] = $object;
                }
            }
            dd($arrObjects);

//            dd($object);
//            dd($xlsx->rows());










//                foreach ($row as $col) {
//                    if ($col === 'Typologie') {
//                        dump($col);
//                    }
//                }


//                //Isoler la première ligne (Titre des colonnes)
//                if ($key = 0) {
//                    dd($row);
//
//                    if ($value === 'Typologie') {
//
//                    }
//                } else {
//
//                }
//                dd($key);
//            }







        } else {
            echo SimpleXLSX::parseError();
        }

        return $this->render('objects/objects/others/import_objects.html.twig', [
//            'form'      => $form->createView(),
        ]);

    }
}
