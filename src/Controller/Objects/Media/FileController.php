<?php

namespace App\Controller\Objects\Media;

use App\Entity\Objects\Media\File;
use App\Repository\Objects\ObjectsRepository;
use App\Service\ActionService;
use App\Service\UploadService;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class FileController extends AbstractController
{

    public function __construct(
        private ActionService $actionService,
        private EntityManagerInterface $manager,
        private ObjectsRepository $objectsRepository,
        private UploadService $uploadService,
    ){}

//    #[Route('/objects/{id}/file', name: 'objects_files')]
//    #[IsGranted("ROLE_MEMBER", message: "Seules les Membres peuvent faire ça")]
//    public function fileIndex(Objects $objects, Request $request, UploadService $uploadService, FileRepository $filesRepository, ActionCategoryRepository $actionCategoryRepository, ManagerRegistry $doctrine): Response
//    {
//        $form = $this->createForm(MediaFormType::class, $objects);
//        $form->handleRequest($request);
//
//        if ($form->isSubmitted() && $form->isValid()) {
//
//            $files = $form->get('name')->getData();
//            if ($files) {
//                foreach ($files as $file) {
//
//                    if($uploadService->isFile($file)) {
//                        $fileNameCode = $uploadService->createFileName($file, $objects, $filesRepository);
//                        $fileName = $uploadService->upload($file, $objects, $fileNameCode);
//
//                        $fl = new File();
//                        $fl->setSrc($fileName);
//                        $fl->setCode($fileNameCode);
//                        $fl->setObjects($objects);
//                        $objects->addFile($fl);
//
//                        $action = new Action();
//                        $action->setName('Fichier ajouté');
//                        $action->setObject($objects);
//                        $action->setOthersValue($fl->getSrc());
//                        $action->setCreatedBy($this->getUser());
//                        $action->setCategory($actionCategoryRepository->find(2));
//
//                        $em = $doctrine->getManager();
//                        $em->persist($objects);
//                        $em->persist($action);
//                        $em->flush();
//                    } else {
//                        $this->addFlash('danger', 'Ceci n\'est pas un fichier valide');
//                        $this->redirectToRoute('objects_files',
//                            ['id' => $objects->getId()],
//                        );
//                    }
//                }
//            }
//        }
//
//        return $this->render('objects/media/file.html.twig', [
//            'object'    => $objects,
//            'form'      => $form->createView(),
//            'extension' => $objects->getFiles(),
//            'bookmarks'      => $this->getUser()->getBookmark(),
//        ]);
//    }


    #[Route('/file-delete/{id}', name: 'delete_objects_file')]
    #[IsGranted("ROLE_MEMBER", message: "Seules les Membres peuvent faire ça")]
    public function fileDelete(File $file, Request $request) {

        $object = $file->getObjects();

        $this->uploadService->deleteFile($file);

        $this->actionService->addAction(2, 'Image supprimé', $this->objectsRepository->findOneBy(['id' => $object->getId()]), $this->getUser(), $file->getSrc());

        $this->manager->remove($file);
        $this->manager->flush();

        $this->addFlash('success', 'Fichier annexe supprimé avec succès');

        return($this->redirectToRoute('objects',
            ['id' => $object->getId()],
        ));
    }





}
