<?php

namespace App\Controller;

use App\Classe\Mail;
use App\Repository\UserRepository;
use App\Form\ForgotPasswordFormType;
use App\Form\ResetPasswordFormType;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class ForgotPasswordController extends AbstractController
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    #[Route('/mot-de-passe-oublie', name: 'app_password')]
    public function index(Request $request, UserRepository $userRepository): Response
    {

        $form = $this->createForm(ForgotPasswordFormType::class);

        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {

            $email = $form->get('email')->getData();
            $user = $userRepository->findOneByEmail($email);


            $this->addFlash('success', "Si votre adresse email existe, vous recevrez un mail pour réinitialiser votre mot de passe, checkez vos spams si vous ne recevez rien.");


            if ($user) {

                $token = bin2hex(random_bytes(15));
                $user->setToken($token);

                $date = new DateTime();

                $date->modify('+10 minutes');

                $user->setTokenExpireAt($date);

                $this->em->flush();

                $mail = new Mail();
                $vars = [
                    'link' => $this->generateUrl('app_password_update', ['token' => $token], UrlGeneratorInterface::ABSOLUTE_URL),
                ];
                $mail->send($user->getEmail(), $user->getFirstname().' '.$user->getLastname(), "Modification de votre mot de passe", "forgotpassword.html", $vars);
            }
        }

        return $this->render('password/index.html.twig', [
            'forgotPasswordForm' => $form->createView(),
        ]);
    }


    #[Route('/mot-de-passe/reset/{token}', name: 'app_password_update')]
    public function update(Request $request, UserRepository $userRepository, $token): Response
    {
        if (!$token) {
            return $this->redirectToRoute('app_password');
        }

        $user = $userRepository->findOneByToken($token);
        
        $now = new DateTime();

        if (!$user || $now > $user->getTokenExpireAt()) {
            return $this->redirectToRoute('app_password');
        }
    
        $form = $this->createForm(ResetPasswordFormType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setToken(null);
            $user->setTokenExpireAt(null);
            
            $this->em->flush();
            $this->addFlash(
                'success',
                "Votre mot de passe est correctement mis à jour."
            );
            return $this->redirectToRoute('app_login');
        }

        return $this->render('password/reset.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
