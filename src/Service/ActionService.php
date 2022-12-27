<?php


namespace App\Service;

use App\Entity\Objects\Objects;
use App\Entity\Site\Action;
use App\Entity\Site\ActionCategory;
use App\Entity\User\User;
use App\Repository\Site\ActionCategoryRepository;
use Doctrine\ORM\EntityManagerInterface;

class ActionService
{

    public function __construct(
        private ActionCategoryRepository $actionCategoryRepository,
        private EntityManagerInterface $entityManager,
    ){}


    //Création d'une action et ajout en DB
    //ID CATEGORY [1 => User, 2 => Objects, 3 => Categories]
    public function addAction(int $IdCategory, string $name, User|Objects $items,  User $createdBy, string $othersValue='')
    {
        $action = new Action();
        $action->setCategory($this->actionCategoryRepository->find($IdCategory));
        $action->setName($name);
        if ($items instanceof User )$action->setUser($items);
        if ($items instanceof Objects )$action->setObject($items);
        $action->setCreatedBy($createdBy);
        if ($othersValue) $action->setOthersValue($othersValue);
        $this->entityManager->persist($action);
        $this->entityManager->flush();
    }
}