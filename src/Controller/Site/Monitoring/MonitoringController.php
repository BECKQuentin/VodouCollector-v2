<?php

namespace App\Controller\Site\Monitoring;

use App\Form\Site\ClaimFormType;
use App\Form\Site\SiteParameterFormType;
use App\Repository\Site\ActionRepository;
use App\Repository\Site\SiteParameterRepository;
use App\Service\EmailService;
use Doctrine\Persistence\ManagerRegistry;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;

class MonitoringController extends AbstractController
{


    #[Route('/action-log', name: 'action_log')]
    #[IsGranted("ROLE_ADMIN", message: "Seules les Admins peuvent faire ça")]
    public function actionLog(ActionRepository $actionRepository, SiteParameterRepository $siteParameterRepository, ManagerRegistry $doctrine): Response
    {


        //suppression des anciennes actions
        $actions = $actionRepository->findBy([], ['createdAt' => 'DESC']);
        $site = $siteParameterRepository->find(1);
        if (count($actions) > $site->getLimitActionLog()) {
            foreach (array_slice($actions, $site->getLimitActionLog()) as $actionToRemove) {


                $actionToRemove->getCreatedBy(null);

                $em = $doctrine->getManager();
                $em->remove($actionToRemove);
                $em->flush();
            }
        }

        return $this->render('site/monitoring/action_log.html.twig', [
            'actions' => $actions,
        ]);
    }

    #[Route('/site-parameter', name: 'site_parameter')]
    #[IsGranted("ROLE_ADMIN", message: "Seules les Admins peuvent faire ça")]
    public function siteParameter(SiteParameterRepository $siteParameterRepository, Request $request, ManagerRegistry $doctrine): Response
    {
        $site = $siteParameterRepository->find(1);
        $form = $this->createForm(SiteParameterFormType::class, $site);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em = $doctrine->getManager();
            $em->persist($site);
            $em->flush();
        }

        return $this->render('site/monitoring/site_parameter.html.twig', [
            'form' => $form->createView(),
        ]);
    }



    /**
     * @throws Exception
     */
    #[Route('/claim', name:'claim')]
    public function claim(ClaimFormType $claimFormType, Request $request, EmailService $emailService)
    {

        $form = $this->createForm(ClaimFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $emailService->send([
                    'to'        => 'developer',
                    'subject'   => '[VodouCollector] CLAIM : ' . $form->get('subject')->getData(),
                    'template'  => 'email/site/claim.html.twig',
                    'context'   => [
                        'user'        => $this->getUser(),
                        'subject'     => $form->get('subject')->getData(),
                        'section'     => $form->get('section')->getData(),
                        'description' => $form->get('description')->getData(),
                    ],
                ]
            );

            $this->addFlash('success', "Remarques envoyées. Nous ferons au mieux pour répondre à vos attentes. Merci !");
            return $this->redirectToRoute('claim');
        }
        return $this->render('site/monitoring/claim.html.twig', [
            'form' => $form->createView(),
        ]);
    }




}
