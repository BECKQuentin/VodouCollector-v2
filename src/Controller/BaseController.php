<?php

namespace App\Controller;

use App\Data\SearchData;
use App\Form\SearchFieldType;
use App\Repository\Libraries\BookRepository;
use App\Repository\Libraries\LibrariesRepository;
use App\Repository\Objects\ObjectsRepository;
use App\Repository\Site\NewsRepository;
use App\Repository\User\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Role\RoleHierarchyInterface;

class BaseController extends AbstractController
{


    /**
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Doctrine\ORM\NoResultException
     */
    #[Route('/', name: 'home')]
    public function home(ObjectsRepository $objectsRepository, RoleHierarchyInterface $roleHierarchy, LibrariesRepository $librariesRepository, NewsRepository $newsRepository, BookRepository $bookRepository, UserRepository $userRepository): Response
    {

        $mostViewedObjects = $objectsRepository->findAllNoDeleted();
        $countObjects = $objectsRepository->countObjects();

        //TODO: Factorisez ceci avec le nombres de lignes dans location
        $countIsRent = $objectsRepository->countWhereIsLocated(1);
        $countIsExposedTempObjects = $objectsRepository->countWhereIsLocated(2);
        $countIsExposedPermObjects = $objectsRepository->countWhereIsLocated(3);
        $countIsExposedStock = $objectsRepository->countWhereIsLocated(4);

        $countToFix = $objectsRepository->countToFix();

        //Recupére le premier role (TODO: A changer si choix de plusieurs roles distinct en BDD)
        $news = $newsRepository->findActualNews($this->getUser()->getRoles()[0]);

        return $this->render('home/home.html.twig', [
            'mostViewedObjects' => $mostViewedObjects,
            'news' => $news,
            'countObjects' => $countObjects,
            'countIsRent' => $countIsRent,
            'countIsExposedTempObjects' => $countIsExposedTempObjects,
            'countIsExposedPermObjects' => $countIsExposedPermObjects,
            'countIsExposedStock' => $countIsExposedStock,
            'countToFix' => $countToFix,
            'libraries' => $librariesRepository->findAll(),
            'books' => $bookRepository->findAll(),
            'users' => $userRepository->findAll(),
            'bookmarks'      => $this->getUser()->getBookmark(),
        ]);
    }


    public function header($routeName, Request $request)
    {
        return $this->render('base/header.html.twig', [
            'route_name' => $routeName,
        ]);
    }


    #[Route('/redirect-user', name: 'redirect_user')]
    public function redirectUser()
    {

        if ($this->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('home');
        }
        elseif ($this->isGranted('ROLE_MEMBER')) {
            return $this->redirectToRoute('home');
        }
        elseif ($this->isGranted('ROLE_GUEST')) {
            return $this->redirectToRoute('home');
        }
        else {
            return $this->redirectToRoute('login');
        }
    }


    #[Route('/mentions-legales', name: 'legal_notice')]
    public function legalNotice()
    {
        return $this->render('base/legal_notice.html.twig', [

        ]);
    }

}
