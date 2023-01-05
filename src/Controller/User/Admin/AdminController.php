<?php

namespace App\Controller\User\Admin;

use App\Entity\Site\Action;
use App\Entity\User\User;
use App\Form\User\RegistrationFormType;
use App\Repository\Site\ActionCategoryRepository;
use App\Repository\Site\ActionRepository;
use App\Repository\User\UserRepository;
use App\Service\ActionService;
use Doctrine\Persistence\ManagerRegistry;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    public function __construct(
        private ActionService $actionService
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
    public function memberDeleteByAdmin(User $user, Request $request, UserRepository $userRepository, ManagerRegistry $doctrine)
    {

        $this->actionService->addAction(1, 'Utilisateur supprimé', $user, $user);

        foreach ($user->getActions() as $action) {
            $action->setUser(null);
            $action->setCreatedBy(null);
        }


        $em = $doctrine->getManager();
        $em->remove($user);
        $em->flush();

        $this->addFlash('success', 'Vous avez supprimé '.$user->getEmail().' !');
        return $this->redirectToRoute('member');
    }


}