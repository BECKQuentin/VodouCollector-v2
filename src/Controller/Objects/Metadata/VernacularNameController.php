<?php

namespace App\Controller\Objects\Metadata;


use App\Entity\Objects\Metadata\VernacularName;
use App\Form\Objects\MetaDataFormType;
use App\Repository\Objects\Metadata\VernacularNameRepository;
use App\Service\ActionService;
use Doctrine\Persistence\ManagerRegistry;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class VernacularNameController extends AbstractController
{
    const ROUTE         = 'vernacularName';
    const METADATA_NAME = 'Nom Vernaculaire';

    public function __construct(
        private VernacularNameRepository $vernacularNameRepository,
        private ActionService $actionService,
    ){}

    #[Route('/vernacularName', name: 'vernacularName')]
    #[IsGranted("ROLE_MEMBER", message: "Seules les Membres peuvent faire ça")]
    public function add(Request $request, ManagerRegistry $doctrine): Response
    {
        return $this->viewReturnMetadata(new VernacularName(), $this->vernacularNameRepository, $request, $doctrine);
    }


    #[Route('/vernacularName-edit/{id}', name: 'vernacularName_edit')]
    #[IsGranted("ROLE_ADMIN", message: "Seules les ADMINS peuvent faire ça")]
    public function edit(VernacularName $vernacularName, Request $request, ManagerRegistry $doctrine): Response
    {
        return $this->viewReturnMetadata($vernacularName, $this->vernacularNameRepository, $request, $doctrine);
    }


    #[Route('/vernacularName-delete/{id}', name: 'vernacularName_delete')]
    #[IsGranted("ROLE_ADMIN", message: "Seules les ADMINS peuvent faire ça")]
    public function delete(VernacularName $vernacularName, Request $request, ManagerRegistry $doctrine): Response
    {
        $this->actionService->addAction(3, self::METADATA_NAME . ' supprimé', null, $this->getUser(), $vernacularName->getName());

        $em = $doctrine->getManager();
        $em->remove($vernacularName);
        $em->flush();

        $this->addFlash('danger', 'Vous avez supprimé '.$vernacularName->getName().' !');
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
