<?php

namespace App\Controller\User\Admin;

use App\Entity\Site\Action;
use App\Entity\User\User;
use App\Form\ConfirmationFormType;
use App\Form\User\RegistrationFormType;
use App\Repository\Site\ActionCategoryRepository;
use App\Repository\Site\ActionRepository;
use App\Repository\User\UserRepository;
use App\Service\ActionService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    public function __construct(
        private ActionService $actionService,
        private EntityManagerInterface $manager,
    ){}

//    #[Route('/a/member-update/{id}', name: 'admin_member_update')]
//    #[IsGranted("ROLE_ADMIN", message: "Seules les ADMINS peuvent faire ça")]
//    public function memberUpdateByAdmin(Request $request, UserRepository $userRepository, ManagerRegistry $doctrine)
//    {
//        $id = $request->get('id');
//        $userToUpdate = $userRepository->findOneBy(['id' => $id]);
//        $form = $this->createForm(RegistrationFormType::class, $userToUpdate);
//        $form->remove('submit');
//        $form->handleRequest($request);
//
//        if ($form->isSubmitted() && $form->isValid()) {
//
//            $em = $doctrine->getManager();
//            $em->persist($userToUpdate);
//            $em->flush();
//
//            $this->addFlash('success', "Les modifications ont bien été sauvegardées !");
//            return $this->redirectToRoute('member');
//        }
//
//        return $this->render('user/member/update.html.twig', [
//            'form' => $form->createView(),
//            'user' => $userToUpdate
//        ]);
//    }


    #[Route('/a/member-delete/{id}', name: 'admin_member_delete')]
    #[IsGranted("ROLE_ADMIN", message: "Seules les ADMINS peuvent faire ça")]
    public function memberDeleteByAdmin(User $user, Request $request, PaginatorInterface $paginator)
    {

        $actualUser = $this->getUser();

//        // Empecher la supression de son compte
//        if ( $user == $actualUser) {
//            $this->addFlash('danger', "Impossible de supprimer son propre compte, contactez l'Admin pour le supprimer !");
//            return $this->redirectToRoute('profil');
//        }

        $objects = $user->getObjects();
        if ($objects->count() > 0) {
            $confirmForm = $this->createForm(ConfirmationFormType::class);

            $confirmForm->handleRequest($request);
            if ($confirmForm->isSubmitted() && $confirmForm->isValid()) {
                if ($confirmForm->get('confirm')->isClicked()) {
                    foreach ($objects as $object) {
                        $object->setCreatedBy(null);
                        return $this->redirectToRoute('profil');
                    }
                    $this->manager->remove($user);
                    $this->manager->flush();
                    $this->addFlash('success', 'Utilisateur supprimée avec succès.');
                    $this->actionService->addAction(1, $this->getUser() . ' supprimé', null, $actualUser);
                }
                return $this->redirectToRoute('profil');
            }

            $objPaginate = $paginator->paginate(
                $user->getObjects(),
                $request->get('page', 1),
                25
            );

            // Afficher le formulaire de confirmation
            return $this->render('user/member/deleteUserConfirmationForm.html.twig', [
                'user' => $user,
                'objects' => $objPaginate,
                'confirmForm' => $confirmForm->createView(),
            ]);
        }


        // Si l'entité n'est pas associée à une entité Objet, la supprimer directement
        $this->manager->remove($user);
        $this->manager->flush();
        $this->addFlash('success', 'Vous avez supprimé '.$user->getFirstname(). ' ' . $user->getLastname().' !');
        return $this->redirectToRoute('profil');

//        foreach ($user->getActions() as $action) {
//            $action->setUser(null);
//            $action->setCreatedBy(null);
//        }
//
//        foreach ($user->getObjects() as $object) {
//            $object->setCreatedBy(null);
//        }
//
//        $this->manager->remove($user);
//        $this->manager->flush();
//
//        $this->actionService->addAction(1, 'Utilisateur supprimé', $user, $user);
//        $this->addFlash('success', 'Vous avez supprimé '.$user->getEmail().' !');
//
//        return $this->redirectToRoute('member');
    }



}