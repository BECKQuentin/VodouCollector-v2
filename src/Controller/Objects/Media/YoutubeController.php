<?php

namespace App\Controller\Objects\Media;

use App\Entity\Objects\Media\Video;
use App\Entity\Objects\Media\Youtube;
use App\Entity\Objects\Objects;
use App\Entity\Site\Action;
use App\Form\Objects\YoutubeLinkFormType;
use App\Repository\Objects\ObjectsRepository;
use App\Repository\Site\ActionCategoryRepository;
use App\Repository\Objects\Media\YoutubeRepository;
use App\Service\ActionService;
use App\Service\UploadService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class YoutubeController extends AbstractController
{
    public function __construct(
        private ActionService $actionService,
        private EntityManagerInterface $manager,
        private ObjectsRepository $objectsRepository,
        private UploadService $uploadService,
    ){}


//    #[Route('/objects/{id}/youtube', name: 'objects_youtube')]
//    #[IsGranted("ROLE_MEMBER", message: "Seules les Membres peuvent faire ça")]
//    public function youtubeIndex(Objects $objects, Request $request, YoutubeRepository $youtubeRepository, ActionCategoryRepository $actionCategoryRepository, ManagerRegistry $doctrine): Response
//    {
//        $youtube = new Youtube();
//        $form = $this->createForm(YoutubeLinkFormType::class, $youtube);
//        $form->handleRequest($request);
//
//        if ($form->isSubmitted() && $form->isValid()) {
//
//                //extraire code youtube de l'url
//                $url   = $form->getData('src')->getSrc();
//                $arrLink = explode('/', $url);
//                if ($arrLink[2] == 'www.youtube.com') {
//                    preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $url, $match);
//                    $youtube_id = $match[1];
//                    //Flush in BDD
//                    $youtube->setObjects($objects);
//                    $youtube->setSrc($youtube_id);
//                    $youtube->setCode($objects->getCode());
//
//                    $action = new Action();
//                    $action->setName('Video youtube ajouté');
//                    $action->setObject($objects);
//                    $action->setCreatedBy($this->getUser());
//                    $action->setCategory($actionCategoryRepository->find(2));
//
//                    $em = $doctrine->getManager();
//                    $em->persist($youtube);
//                    $em->persist($action);
//                    $em->flush();
//
//                } else {
//                    $this->addFlash('danger', 'Ceci n\'est pas une vidéo youtube valide');
//                    $this->redirectToRoute('objects_youtube',
//                        ['id' => $objects->getId()]);
//                }
//
//                //vider l'input
//                unset($entity);
//                unset($form);
//                $youtube = new Youtube();
//                $form = $this->createForm(YoutubeLinkFormType::class, $youtube);
//
//                return $this->render('objects/media/youtube.html.twig', [
//                    'object'    => $objects,
//                    'bookmarks' => $this->getUser()->getBookmark(),
//                    'form'      => $form->createView(),
//                ]);
//
//        }
//        return $this->render('objects/media/youtube.html.twig', [
//        'object'    => $objects,
//        'bookmarks' => $this->getUser()->getBookmark(),
//        'form'      => $form->createView(),
//        ]);
//
//
//    }


    #[Route('/youtube-delete/{id}', name: 'delete_objects_youtube')]
    #[IsGranted("ROLE_MEMBER", message: "Seules les Membres peuvent faire ça")]
    public function youtubeDelete(Youtube $youtube, Request $request) {

        $object = $youtube->getObjects();

        $this->actionService->addAction(2, 'Lien Youtube supprimé', $this->objectsRepository->findOneBy(['id' => $object->getId()]), $this->getUser(), $youtube->getSrc());

        $this->manager->remove($youtube);
        $this->manager->flush();

        $this->addFlash('success', 'Lien youtube supprimé avec succès');

        return($this->redirectToRoute('objects',
            ['id' => $object->getId()],
        ));
    }





}
