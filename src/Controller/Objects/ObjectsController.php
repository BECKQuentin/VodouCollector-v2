<?php

namespace App\Controller\Objects;

use App\Data\SearchData;
use App\Entity\Objects\Media\File;
use App\Entity\Objects\Media\Image;
use App\Entity\Objects\Media\Video;
use App\Entity\Objects\Objects;
use App\Form\Objects\ObjectsFormType;
use App\Form\SearchFieldType;
use App\Repository\Objects\Media\FileRepository;
use App\Repository\Objects\Media\ImageRepository;
use App\Repository\Objects\Media\VideoRepository;
use App\Repository\Objects\ObjectsRepository;
use App\Repository\Site\ActionCategoryRepository;
use App\Service\ActionService;
use App\Service\UploadService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Dompdf\Dompdf;
use Dompdf\Options;
use Knp\Component\Pager\PaginatorInterface;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ObjectsController extends AbstractController
{

    public function __construct(
        private ActionService $actionService,
        private EntityManagerInterface $entityManager,
        private ObjectsRepository $objectsRepository,
        private UploadService $uploadService,
        private ImageRepository $imageRepository,
        private VideoRepository $videoRepository,
        private FileRepository $fileRepository
    ){}

    #[Route('/objects/all-code')]
    #[IsGranted("ROLE_MEMBER", message: "Seules les MEMBRES peuvent faire ça")]
    public function allObjectsCode(): \Symfony\Component\HttpFoundation\JsonResponse
    {
        $json = [];
        foreach ($this->objectsRepository->findAllNoDeleted() as $object) {
            $json[] = $object->getCode();
        }
        return $this->json($json);
    }

    #[Route('/objects', name: 'objects_listing')]
    public function listingObjects(PaginatorInterface $paginator, SessionInterface $session, Request $request): Response
    {
        $data = new SearchData();
        $data->page = $request->get('page', 1);

        $searchForm = $this->createForm(SearchFieldType::class, $data);
        if (!$this->container->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            $searchForm->remove('updatedBy');
        }
        $searchForm->handleRequest($request);
        $searchObjects = $this->objectsRepository->searchObjects($data);

        //array des ID de la recherche
        $arrIdSearchObj = [];
        foreach ($searchObjects as $objId) {
            $arrIdSearchObj[] = $objId->getId();
        }
        $idsSearchObjs = implode(',', $arrIdSearchObj);
        if ($idsSearchObjs === '') {
            $idsSearchObjs = 'null';
        }

        //Pagination
        $objPaginate = $paginator->paginate(
            $searchObjects,
            $data->page = $request->get('page', 1),
            10
        );


        return $this->render('objects/objects/listing.html.twig', [
            'bookmarks'      => $this->getUser()->getBookmark(),
            'objects'       => $objPaginate,
            'searchForm'    => $searchForm->createView(),
            'totalItemsCount' => count($searchObjects),
            'idsSearchObjs' => $idsSearchObjs,
        ]);
    }

    #[Route('/objects-add', name: 'objects_add')]
    #[IsGranted("ROLE_MEMBER", message: "Seules les Membre peuvent faire ça")]
    public function create(Request $request)
    {
        return $this->renderEdit(new Objects(), $request);
    }

    #[Route('/objects/{id}', name: 'objects')]
    #[IsGranted("ROLE_GUEST", message: "Seules les Invités peuvent faire ça")]
    public function editObjects(Objects $objects, ImageRepository $imageRepository, FileRepository $fileRepository, VideoRepository $videoRepository, ActionCategoryRepository $actionCategoryRepository,UploadService $uploadService, Request $request, ManagerRegistry $doctrine): Response
    {
        //Vérification du ROLE_MEMBER au moins, les invités ne peuvent que voir l'objet
        if ($this->container->get('security.authorization_checker')->isGranted('ROLE_MEMBER')) {
            return $this->renderEdit($objects, $request);
        } else {
            return $this->render('objects/objects/view.html.twig', [
                'object'    => $objects,
                'bookmarks'      => $this->getUser()->getBookmark(),
            ]);
        }
    }

    private function renderEdit(Objects $objects, Request $request)
    {
        $user = $this->getUser();

        $isAdding = false;
        if  ($objects->getId() == null) {
            $objects = new Objects();
            $isAdding = true;
        } else {
            //Vérification d'Objet existant
            if ($objects->getDeletedAt()) {
                $this->addFlash('danger', $objects->getCode() . ' - ' . $objects->getVernacularName()->getName() . ' n \'existe plus !');
                return $this->redirectToRoute('objects_listing');
            }
        }
        $form = $this->createForm(ObjectsFormType::class, $objects);

        if (!$this->container->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            $form->remove('insuranceValue');
        }

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if  ($isAdding == false) {
                $objects->setUpdatedAt(new \DateTimeImmutable('now'));
                $objects->setUpdatedBy($user);
            } else {
                $objects->setCreatedBy($this->getUser());
            }

            $images = $form->get('images')->getData();
            if ($images) {
                foreach ($images as $image) {

                    if($this->uploadService->isImage($image)) {
                        $fileNameCode = $this->uploadService->createFileName($image, $objects, $this->imageRepository);
                        $fileName = $this->uploadService->upload($image, $objects, $fileNameCode);

                        $img = new Image();
                        $img->setSrc($fileName);
                        $img->setCode($fileNameCode);
                        $img->setObjects($objects);
                        $objects->addImage($img);

                        $this->actionService->addAction(2, 'Image ajouté', $objects, $this->getUser(), $img->getSrc());

                        $this->entityManager->persist($objects);
                        $this->entityManager->flush();
                    } else {
                        $this->addFlash('danger', 'Ceci n\'est pas une image valide');
                        $this->redirectToRoute('objects',
                            ['id' => $objects->getId()]);
                    }
                }
            }

            $files = $form->get('files')->getData();
            if ($files) {
                foreach ($files as $file) {

                    if($this->uploadService->isFile($file)) {
                        $fileNameCode = $this->uploadService->createFileName($file, $objects, $this->fileRepository);
                        $fileName = $this->uploadService->upload($file, $objects, $fileNameCode);

                        $fl = new File();
                        $fl->setSrc($fileName);
                        $fl->setCode($fileNameCode);
                        $fl->setObjects($objects);
                        $objects->addFile($fl);

                        $this->actionService->addAction(2, 'Fichier ajouté', $objects, $this->getUser(), $fl->getSrc());

                        $this->entityManager->persist($objects);
                        $this->entityManager->flush();
                    } else {
                        $this->addFlash('danger', 'Ceci n\'est pas un fichier valide');
                        $this->redirectToRoute('objects',
                            ['id' => $objects->getId()],
                        );
                    }
                }
            }

            $videos = $form->get('videos')->getData();
            if ($videos) {
                foreach ($videos as $video) {

                    if($this->uploadService->isVideo($video)) {
                        $fileNameCode = $this->uploadService->createFileName($video, $objects, $this->videoRepository);
                        $fileName = $this->uploadService->upload($video, $objects, $fileNameCode);

                        $vid = new Video();
                        $vid->setSrc($fileName);
                        $vid->setCode($fileNameCode);
                        $vid->setObjects($objects);
                        $objects->addVideo($vid);

                        $this->actionService->addAction(2, 'Video ajouté', $objects, $this->getUser(), $vid->getSrc());

                        $this->entityManager->persist($objects);
                        $this->entityManager->flush();
                    } else {
                        $this->addFlash('danger', 'Ceci n\'est pas une vidéo valide');
                        $this->redirectToRoute('objects',
                            ['id' => $objects->getId()]);
                    }
                }
            }

//            $urlYoutube = $form->get('youtube')->getData();
//            if ($urlYoutube) {
//                dd($urlYoutube);
//
////                   extraire code youtube de l'url
//                $arrLink = explode('/', $urlYoutube);
//                if ($arrLink[2] == 'www.youtube.com') {
//                    preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $urlYoutube, $match);
//                    $youtube_id = $match[1];
//                    //Flush in BDD
//                    $youtube = new Youtube();
//                    $youtube->setSrc($youtube_id);
//                    $youtube->setCode($objects->getCode());
//                    $youtube->setObjects($objects);
//
//                    $this->actionService->addAction(2, 'Video youtube ajouté', $objects, $this->getUser());
//
//                    $em = $doctrine->getManager();
//                    $em->persist($youtube);
//                    $em->flush();
//
//                } else {
//                    $this->addFlash('danger', 'Ceci n\'est pas une vidéo youtube valide');
//                    $this->redirectToRoute('objects_youtube',
//                        ['id' => $objects->getId()]);
//                }
//
//            }


            if ($isAdding) {
                $objects->setCreatedBy($user);
                $this->actionService->addAction(2, 'Objet crée',$objects, $this->getUser());
            } else {
                $this->actionService->addAction(2, 'Objet modifié', $objects, $this->getUser());
            }

            $this->entityManager->persist($objects);
            $this->entityManager->flush();

            $this->addFlash('success', "Les modifications ont bien été sauvegardées !");
            $this->redirectToRoute('objects', ['id' => $objects->getId()]);
        }

        return $this->render('objects/objects/view.html.twig', [
            'object'    => $objects,
            'bookmarks'      => $this->getUser()->getBookmark(),
//            'extension' => $objects->getFiles(),
            'form'      => $form->createView(),
        ]);
    }



    //SOFT DELETE
    #[Route('/objects-delete/{id}', name: 'objects_delete')]
    #[IsGranted("ROLE_ADMIN", message: "Seules les ADMINS peuvent faire ça")]
    public function deleteObjects(Objects $objects, ActionCategoryRepository $actionCategoryRepository, ManagerRegistry $doctrine): Response
    {

        $objects->setDeletedAt(new \DateTimeImmutable('now'));
        $objects->setDeletedBy($this->getUser());

        $this->actionService->addAction(2, 'Objet supprimé', $objects, $this->getUser());

        $em = $doctrine->getManager();
        $em->persist($objects);
        $em->flush();

        $this->addFlash('success', 'Vous avez supprimé '.$objects->getVernacularName()->getName().' !');
        return $this->redirectToRoute('objects_listing');
    }

    //LISTING des objets supprimés
    #[Route('/deleted-objects', name: 'deleted_objects')]
    #[IsGranted("ROLE_ADMIN", message: "Seules les Admins peuvent faire ça")]
    public function deletedObjects(ObjectsRepository $objectsRepository): Response
    {
        return $this->render('site/monitoring/deleted_objects.html.twig', [
            'objects' => $objectsRepository->findDeletedObjects(),
        ]);
    }

    //RESTAURATION d'un objet supprimé
    #[Route('/deleted-objects/restore/{id}', name: 'restore_deleted_objects')]
    #[IsGranted("ROLE_ADMIN", message: "Seules les Admins peuvent faire ça")]
    public function restoreDeletedObjects(Objects $objects): Response
    {

        $objects->setDeletedAt(null);
        $objects->setDeletedBy(null);

        $this->entityManager->persist($objects);
        $this->entityManager->flush();

        $this->addFlash('success', "Objet restauré !");
        return $this->redirectToRoute('objects', ['id' => $objects->getId()]);
    }

    //REMOVE - Forçage de suppression d'un objet
    #[Route('/deleted-objects/force-delete/{id}', name: 'force_deleted_objects')]
    #[IsGranted("ROLE_ADMIN", message: "Seules les Admins peuvent faire ça")]
    public function forceDeletedObjects(Objects $objects, Request $request): Response
    {
        if (!$objects->getDeletedAt()) {
            $this->addFlash('danger', "Objet encore dans l'inventaire. Supprimé le d'abord.");
            return $this->redirectToRoute($request->getUri());
        }

        //Suppression des images
        $images = $objects->getImages();
        $filesystem = new Filesystem();
        foreach ($images as $image) {
            if ($image) {
                $filesystem->remove($image->getAbsolutePath());
            }
        }
//        //Suppression des Videos
//        $videos = $objects->getVideos();
//        foreach ($videos as $video) {
//            $filesystem->remove($video->getAbsolutePath());
//        }
//        //Suppression des Fichiers
//        $videos = $objects->getVideos();
//        foreach ($videos as $video) {
//            $filesystem->remove($video->getAbsolutePath());
//        }
        $this->entityManager->remove($objects);
        $this->entityManager->flush();

        $this->addFlash('success', "Objet supprimé definitivement !");
        return $this->redirectToRoute('deleted_objects');
    }







    /*Extraction de PDF*/
    #[Route('/objects-extract/pdf/{idsSearchObjs}', name: 'object_extract_pdf')]
    #[IsGranted("ROLE_MEMBER", message: "Seules les ADMINS peuvent faire ça")]
    public function pdfObjects($idsSearchObjs, ObjectsRepository $objectsRepository, Request $request)
    {


        $maxExtract = 300;
        if ($idsSearchObjs !== 'null') {
            $arrIdSearchObj = explode(',', $idsSearchObjs);
            if (count($arrIdSearchObj) > $maxExtract) {
                $this->addFlash('danger', "Trop d'objets à extraire limité à ".$maxExtract." pour le moment! Contactez l'Admin");
                return $this->redirectToRoute('objects_listing');
            }
            if (!$this->isGranted('ROLE_MEMBER') && count($arrIdSearchObj) >= 2) {
                $this->addFlash('danger', "Extraction impossible");
                return $this->redirectToRoute('objects_listing');
            }
        } else {
            $this->addFlash('danger', 'Aucun objet dans votre recherche (extraction impossible)');
            return $this->redirectToRoute('objects_listing');
        }

        $arrPdf = [];
        $arrSearchObjs = [];
        foreach ($arrIdSearchObj as $idObj) {
            $arrSearchObjs[] = $objectsRepository->find($idObj);
        }


        foreach ($arrSearchObjs as $obj) {

            // Configure Dompdf according to your needs
            $pdfOptions = new Options();
            $pdfOptions->set('defaultFont', 'Arial');

            // Instantiate Dompdf with our options
            $dompdf = new Dompdf($pdfOptions);

            $dompdf->setOptions($pdfOptions->setIsRemoteEnabled(true));

            // Retrieve the HTML generated in our twig file
            $html = $this->renderView('objects/objects/others/object_pdf.html.twig', [
                'object' => $obj,
            ]);

            // Load HTML to Dompdf
            $dompdf->loadHtml($html);

            // (Optional) Setup the paper size and orientation 'portrait' or 'portrait'
            $dompdf->setPaper('A4', 'portrait');

            // Render the HTML as PDF
            $dompdf->render();

            // Output the generated PDF to Browser (force download)
            $filename = str_replace([' ', '/'], '-', $obj->getCode() . '-' . strtolower(  $obj->getVernacularName()->getName() . '-' . $obj->getTypology()->getName() ));
            $arrPdf[$filename] = $dompdf->output();
        }

        if (count($arrPdf) > 1) {
            $zip = new \ZipArchive();
            $filename = count($arrPdf) . "-objets.zip";

            if ($zip->open($filename, \ZipArchive::CREATE) !== TRUE) {
                exit("Cannot open <$filename>\n");
            }
            foreach ($arrPdf as $k => $file) {
                $zip->addFromString($k.".pdf", $file);
            }
            $zip->close();

            header("Content-Type: application/zip");
            header("Content-Disposition: attachment; filename=\"$filename\"");
            header("Content-Length: " . filesize($filename));
            readfile($filename);
            exit();
        } else {
            //Forcer le telechargement de l'Objet unique
            $filename = array_key_first($arrPdf) . ".pdf";
            $pdf_content = $arrPdf[array_key_first($arrPdf)];
            header('Content-Type: application/pdf');
            header("Content-Disposition: attachment; filename=\"$filename\"");
            header('Content-Length: ' . strlen($pdf_content));
            echo $pdf_content;
            exit();
        }

    }


    #[Route('/objects-extract/csv/{idsSearchObjs}', name: 'objects_extract_csv', requirements: ['name' => '.+'])]
    #[IsGranted("ROLE_MEMBER", message: "Seules les ADMINS peuvent faire ça")]
    public function extractObjectsCSV($idsSearchObjs, ObjectsRepository $objectsRepository, Request $request): Response
    {
        if ($idsSearchObjs !== 'null') {
            $arrIdSearchObj = explode(',', $idsSearchObjs);
            if (count($arrIdSearchObj) > 2500) {
                $this->addFlash('danger', "Trop d'objets à extraire limité à 2500! Contactez l'Admin");
                return $this->redirectToRoute('objects_listing');
            }
        } else {
            $this->addFlash('danger', 'Aucun objet dans votre recherche (extraction impossible)');
            return $this->redirectToRoute('objects_listing');
        }

        $arrSearchObjs = [];
        foreach ($arrIdSearchObj as $idObj) {
            $arrSearchObjs[] = $objectsRepository->find($idObj);
        }

        $headersCSV = $arrSearchObjs[0]::OBJ_PUBLIC_LABELS;
        $rows[] = implode(';', $headersCSV);

        foreach ($arrSearchObjs as $obj) {

            $data = [
                $obj->getCode() ?? "",
                $obj->getVernacularName()->getName() ?? "",
                $obj->getTypology()->getName() ?? "",
                $obj->getPrecisionVernacularName() ?? "",
                $obj->getGods()->getName() ?? "",
                $this->implodeArrayMap($obj->getRelatedGods()),
                $this->implodeArrayMap($obj->getOrigin()),
                $this->implodeArrayMap($obj->getPopulation()),
                $obj->getHistoricDetail() ?? "",
                $obj->getUsageFonction() ?? "",
                $obj->getUsageUser() ?? "",
                $obj->getNaturalLanguageDescription() ?? "",
                $obj->getInscriptionsEngraving() ?? "",
                $this->implodeArrayMap($obj->getMaterials()),
                $obj->getDocumentationCommentary() ?? "",
                $this->implodeArrayMap($obj->getMuseumCatalogue()),
                $this->implodeArrayMap($obj->getBook()),
                $obj->getState()->getName() ?? "",
                $obj->getStateCommentary() ?? "",
                $obj->getWeight() ?? "",
                $obj->getSizeHigh() ?? "",
                $obj->getSizeLength() ?? "",
                $obj->getSizeDepth() ?? "",
                $obj->isIsBasemented() ? 'Oui' : 'Non',
                $obj->getBasementCommentary() ?? "",
                $obj->getExpositionLocation()->getNameFR() ?? "",
                $obj->getFloor()->getName() ?? "",
                $obj->getShowcaseCode() ?? "",
                $obj->getShelf() ?? "",
                $obj->getArrivedCollection()->format('Y/d/m') ?? "",
                $obj->getCreatedAt()->format('Y/d/m') ?? "",
                $obj->getCreatedBy() ? $obj->getCreatedBy()->getFullName() : "",
                $this->implodeArrayMap($obj->getInventoriedAt()),
            ];


            $newData = [];
            foreach ($data as $d) {
                $newData[] = str_replace(["\n", "\r"], '', $d);
            }
            $rows[] = implode(';', $newData);
        }

        $content = implode("\n", $rows);
        $response = new Response($content);

        $filename = count($arrIdSearchObj) . ' - Objets';
        $dispositionHeader = $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            $filename . '.csv'
        );
        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', $dispositionHeader);

        return $response;
    }


    #[Route('/objects-extract/xls/{idsSearchObjs}', name: 'objects_extract_xls', requirements: ['name' => '.+'])]
    #[IsGranted("ROLE_MEMBER", message: "Seules les ADMINS peuvent faire ça")]
    public function extractObjectsXLS($idsSearchObjs, ObjectsRepository $objectsRepository): Response
    {

        //vérification de la recherche
        if ($idsSearchObjs !== 'null') {
            $arrIdSearchObj = explode(',', $idsSearchObjs);
            if (count($arrIdSearchObj) > 2500) {
                $this->addFlash('danger', "Trop d'objets à extraire, limité à 2500! Contactez l'Admin");
                return $this->redirectToRoute('objects_listing');
            }
        } else {
            $this->addFlash('danger', 'Aucun objet dans votre recherche (extraction impossible)');
            return $this->redirectToRoute('objects_listing');
        }


        $arrSearchObjs = [];
        foreach ($arrIdSearchObj as $idObj) {
            $arrSearchObjs[] = $objectsRepository->find($idObj);
        }

        //création du fichier excel
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle(count($arrIdSearchObj) . ' - Objets');
        $newObj = new Objects();

        $sheet->setCellValue('A1', $newObj::LABEL_CODE);
        $sheet->getColumnDimension('A')->setWidth(8);
        $sheet->setCellValue('B1', $newObj::LABEL_VERNACULAR_NAME);
        $sheet->getColumnDimension('B')->setAutoSize(true);
        $sheet->setCellValue('C1', $newObj::LABEL_TYPOLOGY);
        $sheet->getColumnDimension('C')->setAutoSize(true);
        $sheet->setCellValue('D1', $newObj::LABEL_PRECISION_VERNACULAR_NAME);
        $sheet->getColumnDimension('D')->setAutoSize(true);
        $sheet->setCellValue('E1', $newObj::LABEL_GODS);
        $sheet->getColumnDimension('E')->setAutoSize(true);
        $sheet->setCellValue('F1', $newObj::LABEL_RELATED_GODS);
        $sheet->getColumnDimension('F')->setAutoSize(true);
        $sheet->setCellValue('G1', $newObj::LABEL_ORIGIN);
        $sheet->getColumnDimension('G')->setWidth(11);
        $sheet->setCellValue('H1', $newObj::LABEL_POPULATION);
        $sheet->getColumnDimension('H')->setAutoSize(true);
        $sheet->setCellValue('I1', $newObj::LABEL_HISTORICAL_DETAIL);
        $sheet->getColumnDimension('I')->setAutoSize(true);
        $sheet->setCellValue('J1', $newObj::LABEL_USAGE_FONCTION);
        $sheet->getColumnDimension('J')->setWidth(30);
        $sheet->setCellValue('K1', $newObj::LABEL_USAGE_USER);
        $sheet->getColumnDimension('K')->setAutoSize(true);
        $sheet->setCellValue('L1', $newObj::LABEL_NATURAL_LANGUAGE_DESCRIPTION);
        $sheet->getColumnDimension('L')->setAutoSize(true);
        $sheet->setCellValue('M1', $newObj::LABEL_INSCRIPTIONS_ENGRAVINGS);
        $sheet->getColumnDimension('M')->setAutoSize(true);
        $sheet->setCellValue('N1', $newObj::LABEL_MATERIALS);
        $sheet->getColumnDimension('N')->setWidth(20);
        $sheet->setCellValue('O1', $newObj::LABEL_DOCUMENTATION_COMMENTARY);
        $sheet->getColumnDimension('O')->setAutoSize(true);
        $sheet->setCellValue('P1', $newObj::LABEL_MUSEUM_CATALOGUE);
        $sheet->getColumnDimension('P')->setAutoSize(true);
        $sheet->setCellValue('Q1', $newObj::LABEL_BOOKS);
        $sheet->getColumnDimension('Q')->setAutoSize(true);
        $sheet->setCellValue('R1', $newObj::LABEL_STATE);
        $sheet->getColumnDimension('R')->setAutoSize(true);
        $sheet->setCellValue('S1', $newObj::LABEL_STATE_COMMENTARY);
        $sheet->getColumnDimension('S')->setWidth(20);
        $sheet->setCellValue('T1', $newObj::LABEL_WEIGHT);
        $sheet->getColumnDimension('T')->setAutoSize(true);
        $sheet->setCellValue('U1', $newObj::LABEL_SIZE_HIGH);
        $sheet->getColumnDimension('U')->setAutoSize(true);
        $sheet->setCellValue('V1', $newObj::LABEL_SIZE_LENGTH);
        $sheet->getColumnDimension('V')->setWidth(50);
        $sheet->setCellValue('W1', $newObj::LABEL_SIZE_DEPTH);
        $sheet->getColumnDimension('W')->setAutoSize(true);
        $sheet->setCellValue('X1', $newObj::LABEL_IS_BASEMENTED);
        $sheet->getColumnDimension('X')->setAutoSize(true);
        $sheet->setCellValue('Y1', $newObj::LABEL_BASEMENT_COMMENTARY);
        $sheet->getColumnDimension('Y')->setAutoSize(true);
        $sheet->setCellValue('Z1', $newObj::LABEL_EXPOSITION_LOCATION);
        $sheet->getColumnDimension('Z')->setAutoSize(true);
        $sheet->setCellValue('AA1', $newObj::LABEL_FLOOR);
        $sheet->getColumnDimension('AA')->setAutoSize(true);
        $sheet->setCellValue('AB1', $newObj::LABEL_SHOWCASE_CODE);
        $sheet->getColumnDimension('AB')->setAutoSize(true);
        $sheet->setCellValue('AC1', $newObj::LABEL_SHELF);
        $sheet->getColumnDimension('AC')->setAutoSize(true);
        $sheet->setCellValue('AD1', $newObj::LABEL_ARRIVED_COLLECTION);
        $sheet->getColumnDimension('AD')->setAutoSize(true);
        $sheet->setCellValue('AE1', $newObj::LABEL_CREATED_AT);
        $sheet->getColumnDimension('AE')->setAutoSize(true);
        $sheet->setCellValue('AF1', $newObj::LABEL_CREATED_BY);
        $sheet->getColumnDimension('AF')->setAutoSize(true);
        $sheet->setCellValue('AG1', $newObj::LABEL_INVENTORIED_AT);
        $sheet->getColumnDimension('AG')->setAutoSize(true);



        foreach ($arrSearchObjs as $key => $obj) {

            $key += 2;

            //Application des données dans les cellules
            $sheet->setCellValue('A'.$key, $obj->getCode() ?? "");
            $sheet->setCellValue('B'.$key, $obj->getVernacularName()->getName() ?? "");
            $sheet->setCellValue('C'.$key, $obj->getTypology()->getName() ?? "");
            $sheet->setCellValue('D'.$key, $obj->getPrecisionVernacularName() ?? "");
            $sheet->setCellValue('E'.$key, $obj->getGods()->getName() ?? "");
            $sheet->setCellValue('F'.$key, $this->implodeArrayMap($obj->getRelatedGods()));
            $sheet->setCellValue('G'.$key, $this->implodeArrayMap($obj->getOrigin()));
            $sheet->setCellValue('H'.$key, $this->implodeArrayMap($obj->getPopulation()));
            $sheet->setCellValue('I'.$key, $obj->getHistoricDetail() ?? "");
            $sheet->setCellValue('J'.$key, $obj->getUsageFonction() ?? "");
            $sheet->setCellValue('K'.$key, $obj->getUsageUser() ?? "");
            $sheet->setCellValue('L'.$key, $obj->getNaturalLanguageDescription() ?? "");
            $sheet->setCellValue('M'.$key, $obj->getInscriptionsEngraving() ?? "");
            $sheet->setCellValue('N'.$key, $this->implodeArrayMap($obj->getMaterials()));
            $sheet->setCellValue('O'.$key, $obj->getDocumentationCommentary() ?? "");
            $sheet->setCellValue('P'.$key, $this->implodeArrayMap($obj->getMuseumCatalogue()));
            $sheet->setCellValue('Q'.$key, $this->implodeArrayMap($obj->getBook()));
            $sheet->setCellValue('R'.$key, $obj->getState()->getName() ?? "");
            $sheet->setCellValue('S'.$key, $obj->getStateCommentary() ?? "");
            $sheet->setCellValue('T'.$key, $obj->getWeight() ?? "");
            $sheet->setCellValue('U'.$key, $obj->getSizeHigh() ?? "");
            $sheet->setCellValue('V'.$key, $obj->getSizeLength() ?? "");
            $sheet->setCellValue('W'.$key, $obj->getSizeDepth() ?? "");
            $sheet->setCellValue('X'.$key, $obj->isIsBasemented() ? 'Oui' : 'Non');
            $sheet->setCellValue('Y'.$key, $obj->getBasementCommentary() ?? "");
            $sheet->setCellValue('Z'.$key, $obj->getExpositionLocation()->getNameFR() ?? "");
            $sheet->setCellValue('AA'.$key, $obj->getFloor()->getName() ?? "");
            $sheet->setCellValue('AB'.$key,$obj->getShowcaseCode() ?? "");
            $sheet->setCellValue('AC'.$key,$obj->getShelf() ?? "");
            $sheet->setCellValue('AD'.$key,$obj->getArrivedCollection() ? $obj->getArrivedCollection()->format('Y/d/m') : "");
            $sheet->setCellValue('AE'.$key,$obj->getCreatedAt()->format('Y/d/m') ?? "");
            $sheet->setCellValue('AF'.$key,$obj->getCreatedBy() ? $obj->getCreatedBy()->getFullName() : "");
            $sheet->setCellValue('AG'.$key, $this->implodeArrayMap($obj->getInventoriedAt()));
        }


        $writer = new Xlsx($spreadsheet);

        // Create a Temporary file in the system
        $fileName = count($arrIdSearchObj) . ' - Objets.xlsx';
        $temp_file = tempnam(sys_get_temp_dir(), $fileName);

        // Create the excel file in the tmp directory of the system
        $writer->save($temp_file);

        // Return the excel file as an attachment
        return $this->file($temp_file, $fileName, ResponseHeaderBag::DISPOSITION_INLINE);
    }


    private function implodeArrayMap($field): string {
        $res = [];
        foreach ($field as $f) {
            if (method_exists($f, 'getName')) {
                $res[] = $f->getName();
            } elseif (method_exists($f, 'getInventoriedAt')) {
               $res[] = $f->getInventoriedAt()->format('Y/d/m');
            }
        }
        return implode(',', $res);
    }


}
