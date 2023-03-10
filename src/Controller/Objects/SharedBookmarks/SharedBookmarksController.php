<?php

namespace App\Controller\Objects\SharedBookmarks;

use App\Entity\Objects\Objects;
use App\Entity\Objects\SharedBookmarks\SharedBookmarks;
use App\Form\Objects\SharedBookmarksFormType;
use App\Gemonos\TagBundle\Form\Type\ObjectsCodeType;
use App\Repository\Objects\ObjectsRepository;
use App\Repository\Objects\SharedBookmarks\SharedBookmarksRepository;
use App\Service\ActionService;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


#[Route('/shared-bookmarks')]
class SharedBookmarksController extends AbstractController
{

    public function __construct(
        private ActionService $actionService,
        private SharedBookmarksRepository $sharedBookmarksRepository,
        private EntityManagerInterface $entityManager,
        private ObjectsRepository $objectsRepository,
    ){}

    #[Route('', name: 'sharedBookmark_index')]
    #[IsGranted("ROLE_MEMBER", message: "Seules les Membres peuvent faire ça")]
    public function index(Request $request): Response
    {
        //passer l'objet de la page pour voir s'il existe dans le favori ou non
        $object = null;
        if ($request->get('idObject')) $object = $this->objectsRepository->find($request->get('idObject'));

        return $this->render('objects/objects/sharedBookmarks/index.html.twig', [
            'sharedBookmarks'   => $this->sharedBookmarksRepository->findAll(),
            'object'            => $object,
        ]);
    }

    #[Route('/listing', name: 'sharedBookmark_listing')]
    #[IsGranted("ROLE_MEMBER", message: "Seules les Membres peuvent faire ça")]
    public function listing(Request $request): Response
    {
        return $this->render('objects/objects/sharedBookmarks/listing.html.twig', [
            'sharedBookmarks' => $this->sharedBookmarksRepository->findAll(),
        ]);
    }

    #[Route('/create', name: 'sharedBookmark_create')]
    #[IsGranted("ROLE_MEMBER", message: "Seules les Membres peuvent faire ça")]
    public function create(Request $request): Response
    {
        $sharedBookmarks = new SharedBookmarks();
        $sharedBookmarksForm = $this->createForm(SharedBookmarksFormType::class, $sharedBookmarks);
        $sharedBookmarksForm->remove('objects');
        $sharedBookmarksForm->handleRequest($request);

        if ($sharedBookmarksForm->isSubmitted() && $sharedBookmarksForm->isValid()) {

            $this->actionService->addAction(2, 'Favoris partagé crée',null, $this->getUser(), $sharedBookmarks->getName());

            $this->entityManager->persist($sharedBookmarks);
            $this->entityManager->flush();
        }

        return $this->render('objects/objects/sharedBookmarks/form.html.twig', [
            'sharedBookmarksForm' => $sharedBookmarksForm->createView(),
        ]);
    }

    #[Route('/edit/{id}', name: 'sharedBookmark_edit')]
    #[IsGranted("ROLE_MEMBER", message: "Seules les Membres peuvent faire ça")]
    public function edit(SharedBookmarks $sharedBookmarks, Request $request): Response
    {
        $sharedBookmarksForm = $this->createForm(SharedBookmarksFormType::class, $sharedBookmarks);
        $sharedBookmarksForm->handleRequest($request);

        if ($sharedBookmarksForm->isSubmitted() && $sharedBookmarksForm->isValid()) {

            if ($sharedBookmarksForm->get('objects')->getData()) {
                foreach ($sharedBookmarksForm->get('objects')->getData() as $object) {
                    $sharedBookmarks->addObject($object);
                }

                $this->actionService->addAction(2, 'Favoris partagé modifié', null, $this->getUser(), $sharedBookmarks->getName());

                $this->entityManager->persist($sharedBookmarks);
                $this->entityManager->flush();

                $this->addFlash('success', 'Favoris Partagés modifié avec succès !');
                $this->redirectToRoute('sharedBookmark_edit', ['id' => $sharedBookmarks->getId()]);
            } else {
                $this->addFlash('danger', 'Formulaire invalide vérifiez bien que les codes des objets soit correct');
                $this->redirectToRoute('sharedBookmark_edit', ['id' => $sharedBookmarks->getId()]);
            }

        }
            return $this->render('objects/objects/sharedBookmarks/edit.html.twig', [
            'sharedBookmarksForm' => $sharedBookmarksForm->createView(),
            'sharedBookmarks'   => $sharedBookmarks,
            'objects'           => $sharedBookmarks->getObjects(),
        ]);
    }


    //Inscription aux favoris partagés
    //http://127.0.0.1:8000/shared-bookmarks/register/1
    #[Route('/register/{id}', name: 'sharedBookmark_register')]
    #[IsGranted("ROLE_MEMBER", message: "Seules les Membres peuvent faire ça")]
    public function registerSharedBookmark(SharedBookmarks $sharedBookmarks)
    {
        if (!empty($sharedBookmarks)) {
            if (!$sharedBookmarks->getUsers()->contains($this->getUser())) {
                $sharedBookmarks->addUser($this->getUser());
                $json = 'Utilisateur inscrit au favoris partagés';
            } else {
                $sharedBookmarks->removeUser($this->getUser());
                $json = 'Utilisateur désinscrit au favoris partagés';
            }
            $this->entityManager->persist($sharedBookmarks);
            $this->entityManager->flush();
            return $this->json($json, 200);
        } else {
            return $this->json( 'Pas de favoris partagé avec cet ID.',404);
        }
    }

    #[Route('/add-object/{id}/{idObject}', name: 'sharedBookmark_add_object')]
    #[IsGranted("ROLE_MEMBER", message: "Seules les Membres peuvent faire ça")]
    public function addObjectSharedBookmark(SharedBookmarks $sharedBookmarks, int $idObject, ObjectsRepository $objectsRepository)
    {
        $object = $objectsRepository->find($idObject);
        if($object) {
            if (!empty($sharedBookmarks)) {

                if (!$sharedBookmarks->getObjects()->contains($object)) {
                    $sharedBookmarks->addObject($object);
                    $json = 'Objet ajouté au favoris partagés';
                } else {
                    $sharedBookmarks->removeObject($object);
                    $json = 'Objet enlevé du favoris partagés';
                }
                $this->entityManager->persist($sharedBookmarks);
                $this->entityManager->flush();
                return $this->json($json, 200);
            } else {
                return $this->json( 'Pas de favoris partagé avec cet ID.',404);
            }
        } else {
            return $this->json( 'Pas d\Objet avec cet ID',404);
        }

    }
}