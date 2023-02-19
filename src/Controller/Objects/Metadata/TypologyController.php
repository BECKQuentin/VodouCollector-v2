<?php

namespace App\Controller\Objects\Metadata;


use App\Entity\Objects\Metadata\Typology;
use App\Entity\Site\Action;
use App\Form\Objects\MetaDataFormType;
use App\Repository\Objects\Metadata\TypologyRepository;
use App\Repository\Site\ActionCategoryRepository;
use App\Service\ActionService;
use Doctrine\Persistence\ManagerRegistry;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TypologyController extends AbstractController
{
    const ROUTE         = 'typology';
    const METADATA_NAME = 'Typologie';

    public function __construct(
        private TypologyRepository $typologyRepository,
        private ActionService $actionService,
    ){}

    #[Route('/typology', name: 'typology')]
    #[IsGranted("ROLE_MEMBER", message: "Seules les Membres peuvent faire ça")]
    public function addFloor(Request $request, ManagerRegistry $doctrine): Response
    {
        return $this->viewReturnMetadata(new Typology(), $this->typologyRepository, $request, $doctrine);
    }


    #[Route('/typology-edit/{id}', name: 'typology_edit')]
    #[IsGranted("ROLE_ADMIN", message: "Seules les ADMINS peuvent faire ça")]
    public function edit(Typology $typology, Request $request, ManagerRegistry $doctrine): Response
    {
        return $this->viewReturnMetadata($typology, $this->typologyRepository, $request, $doctrine);
    }


    #[Route('/typology-delete/{id}', name: 'typology_delete')]
    #[IsGranted("ROLE_ADMIN", message: "Seules les ADMINS peuvent faire ça")]
    public function deleteFloor(Typology $typology, Request $request, ManagerRegistry $doctrine): Response
    {
        $this->actionService->addAction(3, self::METADATA_NAME . ' supprimé', null, $this->getUser(), $typology->getName());

        $em = $doctrine->getManager();
        $em->remove($typology);
        $em->flush();

        $this->addFlash('danger', 'Vous avez supprimé '.$typology->getName().' !');
        return $this->redirectToRoute(self::ROUTE);
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
        ]);
    }
}
