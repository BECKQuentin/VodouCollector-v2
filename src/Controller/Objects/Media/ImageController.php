<?php

namespace App\Controller\Objects\Media;

use App\Entity\Objects\Media\Image;
use App\Entity\Site\Action;
use App\Repository\Objects\ObjectsRepository;
use App\Repository\Site\ActionCategoryRepository;
use App\Service\ActionService;
use App\Service\UploadService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ImageController extends AbstractController
{

    public function __construct(
        private ActionService $actionService,
        private EntityManagerInterface $manager,
        private ObjectsRepository $objectsRepository,
    ){}

//    #[Route('/objects/{id}/media', name: 'objects_medias')]
//    #[IsGranted("ROLE_MEMBER", message: "Seules les Membres peuvent faire ça")]
//    public function mediaIndex(Objects $objects, Request $request, UploadService $uploadService, ImageRepository $imagesRepository, ActionCategoryRepository $actionCategoryRepository, ManagerRegistry $doctrine): Response
//    {
//        $form = $this->createForm(MediaFormType::class, $objects);
//        $form->handleRequest($request);
//
//        if ($form->isSubmitted() && $form->isValid()) {
//
//            $images = $form->get('name')->getData();
//
//            if ($images) {
//                foreach ($images as $image) {
//
//                    if($uploadService->isImage($image)) {
//                        $fileNameCode = $uploadService->createFileName($image, $objects, $imagesRepository);
//                        $fileName = $uploadService->upload($image, $objects, $fileNameCode);
//
//                        $img = new Image();
//                        $img->setSrc($fileName);
//                        $img->setCode($fileNameCode);
//                        $img->setObjects($objects);
//                        $objects->addImage($img);
//
//                        $action = new Action();
//                        $action->setName('Image ajouté');
//                        $action->setObject($objects);
//                        $action->setOthersValue($img->getSrc());
//                        $action->setCreatedBy($this->getUser());
//                        $action->setCategory($actionCategoryRepository->find(2));
//
//                        $em = $doctrine->getManager();
//                        $em->persist($objects);
//                        $em->persist($action);
//                        $em->flush();
//                    } else {
//                        $this->addFlash('danger', 'Ceci n\'est pas une image valide');
//                        $this->redirectToRoute('objects_medias',
//                        ['id' => $objects->getId()]);
//                    }
//                }
//            }
//        }
//
//        return $this->render('objects/media/image.html.twig', [
//            'object' => $objects,
//            'bookmarks'      => $this->getUser()->getBookmark(),
//            'form'   => $form->createView(),
//        ]);
//    }


    #[Route('/media-delete/{id}/{object}', name: 'delete_objects_img')]
    #[IsGranted("ROLE_MEMBER", message: "Seules les Membres peuvent faire ça")]
    public function mediaDelete(Image $images, Request $request) {

        $objId = $request->get('object');

        $filesystem = new Filesystem();
        $filesystem->remove($images->getAbsolutePath());

        $this->actionService->addAction(2, 'Image supprimé', $this->objectsRepository->findOneBy(['id' => $objId]), $this->getUser(), $images->getSrc());

        $this->manager->remove($images);
        $this->manager->flush();

        return($this->redirectToRoute('objects',
            ['id' => $objId],
        ));

    }

//    /**
//     * @Route("/media-delete-all/{id}", name="delete_objects_all_img")
//     *
//     */
//    public function mediaDeleteAll(Objects $object, Request $request) {
//
//
//        $em = $this->getDoctrine()->getManager();
//        if($object->getImages()) {
//            foreach ($object->getImages() as $img) {
//                $em->remove($img);
//
//            }
//        }
//        $em->flush();
//
//        return($this->redirectToRoute('objects_medias',
//            ['id' => $object->getId()]
//        ));
//    }

}
