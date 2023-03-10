<?php

namespace App\Controller\User\Security;



use App\Gemonos\GemonosCaptchaBundle\src\Entity\Captcha;
use App\Repository\User\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{

    #[Route('/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils, UserRepository $userRepository, Request $request): Response
    {
         if ($this->getUser()) {
             return $this->redirectToRoute('home');
         }


//        $captcha = new Captcha();

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('user/security/login.html.twig', [
//            'captcha'       => $captcha,
//            'randCaptchaImg'    => $captcha->getRandImage(),
            'last_username'     => $lastUsername,
            'error'             => $error
        ]);
    }


    #[Route('/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
