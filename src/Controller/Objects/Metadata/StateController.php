<?php

namespace App\Controller\Objects\Metadata;


use App\Entity\Objects\Metadata\State;
use App\Entity\Site\Action;
use App\Form\ConfirmationFormType;
use App\Form\Objects\MetaDataFormType;
use App\Repository\Objects\Metadata\StateRepository;
use App\Repository\Site\ActionCategoryRepository;
use App\Service\ActionService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StateController extends AbstractController
{
    const ROUTE         = 'state';
    const METADATA_NAME = 'Etat';

    public function __construct(
        private StateRepository $stateRepository,
        private ActionService $actionService,
        private EntityManagerInterface $manager,
        private PaginatorInterface $paginator,
    ){}


    #[Route('/state', name: 'state')]
    #[IsGranted("ROLE_GUEST", message: "Seules les Invités peuvent faire ça")]
    public function addState(StateRepository $stateRepository, Request $request, ManagerRegistry $doctrine): Response
    {
        return $this->viewReturnMetadata(new State(), $stateRepository, $request, $doctrine);
    }


    #[Route('/state-edit/{id}', name: 'state_edit')]
    #[IsGranted("ROLE_ADMIN", message: "Seules les ADMINS peuvent faire ça")]
    public function editState(State $state, StateRepository $stateRepository,Request $request, ManagerRegistry $doctrine): Response
    {
       return $this->viewReturnMetadata($state, $stateRepository, $request, $doctrine);
    }


    #[Route('/state-delete/{id}', name: 'state_delete')]
    #[IsGranted("ROLE_ADMIN", message: "Seules les ADMINS peuvent faire ça")]
    public function deleteState(State $metadata, Request $request): Response
    {
        return $this->deleteMetadata($metadata, $request);
    }

    //////////////* GLOBAL METADATAS (CRU)*///////////////////
    public function viewReturnMetadata($metadata, $metadataRepository, $request, $doctrine)
    {
        $allMetadata = $metadataRepository->findAll();
        $form = $this->createForm(MetaDataFormType::class, $metadata);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->actionService->addAction(3, self::METADATA_NAME . ' ajout/modif', null, $this->getUser(), $metadata->getName());

            $em = $doctrine->getManager();
            $em->persist($metadata);
            $em->flush();

            $this->addFlash('success', "L'article a bien été ajoutée");
            return $this->redirectToRoute(self::ROUTE);
        }

        return $this->render('objects/metadata/metadata.html.twig', [
            'editRoute'     => self::ROUTE.'_edit',
            'deleteRoute'   => self::ROUTE.'_delete',
            'className'     => self::METADATA_NAME,
            'items'         => $allMetadata,
            'form'          => $form->createView(),
            'isNoSort'      => true,
        ]);
    }

    private function deleteMetadata($metadata, $request)
    {
        // Empecher la supression de l'ID 1 soit '???'
        if ( $metadata->getId() === 1) {
            $this->addFlash('danger', "Impossible de supprimer le premier element qui vaut '???' Contacter l'Admin si vous pensez que c'est anormal");
            return $this->redirectToRoute(self::ROUTE);
        }

        $objects = $metadata->getObjects();
        if ($objects->count() > 0) {
            $confirmForm = $this->createForm(ConfirmationFormType::class);

            $confirmForm->handleRequest($request);
            if ($confirmForm->isSubmitted() && $confirmForm->isValid()) {
                if ($confirmForm->get('confirm')->isClicked()) {
                    foreach ($objects as $object) {
                        //mettre celui avec id 1 soit ???
                        if  ($this->stateRepository->find(1)) {
                            $object->setTypology($this->stateRepository->find(1));
                        } else {
                            $this->addFlash('danger', 'Désolé mais ' . self::METADATA_NAME . ' avec l\'ID 1 qui vaut ??? est inexistant vérifier bien en base de données ou contacté l\'Admin.');
                            return $this->redirectToRoute(self::ROUTE);
                        }
                    }

                    $this->manager->remove($metadata);
                    $this->manager->flush();
                    $this->addFlash('success', self::METADATA_NAME . '"' . $metadata->getName() . '"' .' a été supprimée avec succès.');
                    $this->actionService->addAction(3, self::METADATA_NAME . ' supprimé', null, $this->getUser(), $metadata->getName());
                }
                return $this->redirectToRoute(self::ROUTE);
            }
            $objPaginate = $this->paginator->paginate(
                $metadata->getObjects(),
                $request->get('page', 1),
                25
            );

            // Afficher le formulaire de confirmation
            return $this->render('objects/metadata/deleteMetadataConfirmationForm.html.twig', [
                'metadata' => $metadata,
                'objects' => $objPaginate,
                'className'     => self::METADATA_NAME,
                'confirmForm' => $confirmForm->createView(),
            ]);
        }


        // Si l'entité n'est pas associée à une entité Objet, la supprimer directement
        $this->manager->remove($metadata);
        $this->manager->flush();
        $this->addFlash('success', 'Vous avez supprimé '.$metadata->getName().' !');
        return $this->redirectToRoute(self::ROUTE);
    }
}
