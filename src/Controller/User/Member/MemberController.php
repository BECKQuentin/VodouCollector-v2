<?php

namespace App\Controller\User\Member;

use App\Entity\Site\Action;
use App\Entity\User\User;
use App\Form\User\RegistrationFormType;
use App\Repository\Site\ActionCategoryRepository;
use App\Repository\User\UserRepository;
use App\Service\ActionService;
use Doctrine\Persistence\ManagerRegistry;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class MemberController extends AbstractController
{

    public function __construct(
        private ActionService $actionService
    ){}


    #[Route('/profil', name: 'profil')]
    public function index(): Response
    {
//        $user = $this->getUser();

        return $this->render('user/member/profil.html.twig', [

        ]);
    }


    #[Route('/member', name: 'member')]
    #[IsGranted('ROLE_ADMIN', message: 'Seuls les Admins peuvent faire ça !')]
    public function member(UserRepository $userRepository): Response
    {
        $users = $userRepository->findAll();

        return $this->render('user/member/listing.html.twig', [
            'users' => $users,
        ]);
    }


    #[Route('/member-update/{id}', name: 'member_update')]
    #[IsGranted('ROLE_GUEST', message: 'Seuls les Invités peuvent faire ça !')]
    public function memberUpdate(User $userRequest, ActionCategoryRepository $actionCategoryRepository, UserPasswordHasherInterface $passwordHasher, Request $request, ManagerRegistry $doctrine)
    {
        $user = $this->getUser();

        $isAdmin = false;
        if ($this->container->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            $isAdmin = true;
        }

        $form = $this->createForm(RegistrationFormType::class, $userRequest);
        $form->remove('agreeTerms');

        if (!$isAdmin) {
            $form->remove('roles');
        }
        if ($isAdmin && $user !== $userRequest) {
            $form->remove('password');
        } else {
            $form->get('password')->setData('');
        }

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if ($user === $userRequest) {

//                dd($form->get('password')->getData());

                if ($form->get('password')->getData() !== null && (!$passwordHasher->isPasswordValid($userRequest, $form->get('password')->getData())) ) {

                    $plaintextPassword = $form->get('password')->getData();
                    $hashedPassword = $passwordHasher->hashPassword(
                        $userRequest,
                        $plaintextPassword
                    );
                    $user->setPassword($hashedPassword);
                }

                $this->actionService->addAction(1, 'Utilisateur modifié par lui-même', $userRequest, $user);
                $em = $doctrine->getManager();
                $em->persist($userRequest);
                $em->flush();

            } else {
                $this->actionService->addAction(1, 'Utilisateur modifié par Admin', $userRequest, $user);
                $em = $doctrine->getManager();
                $em->persist($userRequest);
                $em->flush();
            }



            $this->addFlash('success', "Les modifications ont bien été sauvegardées !");
            if ($isAdmin) {
                return $this->redirectToRoute('member');
            } else {
                return $this->redirectToRoute('profil', ['id' => $user->getId()]);
            }

        }
        return $this->render('user/member/update.html.twig', [
            'form' => $form->createView(),
            'user' => $userRequest,
        ]);


    }


    #[Route('/member-block/{id}', name: 'member_block')]
    #[IsGranted('ROLE_ADMIN', message: 'Seuls les Admins peuvent faire ça !')]
    public function blockMember(User $user, ManagerRegistry $doctrine, UserRepository $userRepository): Response
    {

        if ($user) {
            if ($user->isIsActive()) {
                $user->setIsActive(false);
                $this->addFlash('success', "Utilisateur bloqué");
                $this->actionService->addAction(1, 'Utilisateur bloqué', $user, $this->getUser());
            } else {
                $user->setIsActive(true);
                $this->addFlash('success', "Utilisateur débloqué !");
                $this->actionService->addAction(1, 'Utilisateur débloqué', $user, $this->getUser());
            }
            $em = $doctrine->getManager();
            $em->persist($user);
            $em->flush();
            return $this->redirectToRoute('member');
        }


        $users = $userRepository->findAll();

        return $this->render('user/member/listing.html.twig', [
            'users' => $users,
        ]);
    }

}