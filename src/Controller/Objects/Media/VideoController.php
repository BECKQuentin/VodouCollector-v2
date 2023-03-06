<?php

namespace App\Controller\Objects\Media;

use App\Entity\Objects\Media\Video;
use App\Repository\Objects\ObjectsRepository;
use App\Service\ActionService;
use App\Service\UploadService;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class VideoController extends AbstractController
{

    public function __construct(
        private ActionService $actionService,
        private EntityManagerInterface $manager,
        private ObjectsRepository $objectsRepository,
        private UploadService $uploadService,
    ){}

//    #[Route('/objects/{id}/video', name: 'objects_videos')]
//    #[IsGranted("ROLE_MEMBER", message: "Seules les Membres peuvent faire ça")]
//    public function videoIndex(Objects $objects, Request $request, UploadService $uploadService, VideoRepository $videosRepository, ActionCategoryRepository $actionCategoryRepository, ManagerRegistry $doctrine): Response
//    {
//        $form = $this->createForm(MediaFormType::class, $objects);
//        $form->handleRequest($request);
//
//        if ($form->isSubmitted() && $form->isValid()) {
//
//            $videos = $form->get('name')->getData();
//
//            if ($videos) {
//                foreach ($videos as $video) {
//
//                    if($uploadService->isVideo($video)) {
//                        $fileNameCode = $uploadService->createFileName($video, $objects, $videosRepository);
//                        $fileName = $uploadService->upload($video, $objects, $fileNameCode);
//
//                        $vid = new Video();
//                        $vid->setSrc($fileName);
//                        $vid->setCode($fileNameCode);
//                        $vid->setObjects($objects);
//                        $objects->addVideo($vid);
//
//                        $action = new Action();
//                        $action->setName('Video ajouté');
//                        $action->setObject($objects);
//                        $action->setOthersValue($vid->getSrc());
//                        $action->setCreatedBy($this->getUser());
//                        $action->setCategory($actionCategoryRepository->find(2));
//
//                        $em = $doctrine->getManager();
//                        $em->persist($objects);
//                        $em->persist($action);
//                        $em->flush();
//                    } else {
//                        $this->addFlash('danger', 'Ceci n\'est pas une vidéo valide');
//                        $this->redirectToRoute('objects_files',
//                        ['id' => $objects->getId()]);
//                    }
//                }
//            }
//        }
//
//        return $this->render('objects/media/video.html.twig', [
//            'object'    => $objects,
//            'bookmarks'      => $this->getUser()->getBookmark(),
//            'form'      => $form->createView(),
//        ]);
//    }


    #[Route('/video-delete/{id}', name: 'delete_objects_vid')]
    #[IsGranted("ROLE_MEMBER", message: "Seules les Membres peuvent faire ça")]
    public function videoDelete(Video $video, Request $request) {


        $object = $video->getObjects();

        $this->uploadService->deleteFile($video);

        $this->actionService->addAction(2, 'Image supprimé', $this->objectsRepository->findOneBy(['id' => $object->getId()]), $this->getUser(), $video->getSrc());

        $this->manager->remove($video);
        $this->manager->flush();

        $this->addFlash('success', 'Vidéo supprimé avec succès');


        return($this->redirectToRoute('objects',
            ['id' => $object->getId()],
        ));

    }





}
