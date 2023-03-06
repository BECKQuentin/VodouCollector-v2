<?php

namespace App\Controller\Objects\Metadata;


use App\Entity\Objects\Metadata\InventoryDate;
use App\Entity\Objects\Objects;
use App\Form\Objects\InventoriedAtFormType;
use App\Repository\Objects\Metadata\InventoryDateRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;


class InventoriedAtController extends AbstractController
{

    public function __construct(
        private EntityManagerInterface  $manager,
        private InventoryDateRepository $inventoryDateRepository,
    )
    {
    }

    #[Route('objects/{id}/inventoried-at/', name: 'inventoried_at_index')]
    #[IsGranted("ROLE_MEMBER", message: "Seules les Membres peuvent faire ça")]
    public function index(Objects $objects, Request $request): Response
    {
        return $this->render('objects/metadata/inventoried_at/index.html.twig', [
            'inventoryDates' => $this->inventoryDateRepository->findBy(['objects' => $objects->getId()]),
            'object' => $objects,
        ]);
    }

    #[Route('objects/{id}/inventoried-at/create', name: 'inventoried_at_create')]
    #[IsGranted("ROLE_MEMBER", message: "Seules les Membres peuvent faire ça")]
    public function create(Objects $objects, Request $request): Response
    {
        $inventoriedAt = new InventoryDate();
        $inventoriedAtForm = $this->createForm(InventoriedAtFormType::class, $inventoriedAt);

        $inventoriedAtForm->handleRequest($request);

        if ($inventoriedAtForm->isSubmitted() && $inventoriedAtForm->isValid()) {
            $inventoriedAt->setObjects($objects);
            $this->manager->persist($inventoriedAt);
            $this->manager->flush();
        }

        return $this->render('objects/metadata/inventoried_at/form.html.twig', [
            'object' => $objects,
            'inventoriedAtForm' => $inventoriedAtForm->createView(),
        ]);
    }

    #[Route('inventoried-at/remove/{id}', name: 'inventoried_at_remove')]
    #[IsGranted("ROLE_MEMBER", message: "Seules les Membres peuvent faire ça")]
    public function remove(InventoryDate $inventoryDate, Request $request): Response
    {
        $this->manager->remove($inventoryDate);
        $this->manager->flush();

        return $this->json('Date de recolage supprimé avec succès', 200);
    }
}