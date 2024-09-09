<?php

namespace App\Controller\Account;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Form\PasswordUserType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\ORM\EntityManagerInterface;


class PasswordController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }


    #[Route('/compte/modifier-mot-de-passe', name: 'app_account_modify_pwd')]
    public function index(Request $request, UserPasswordHasherInterface $PasswordHasher): Response
    {
        $user = $this->getUser();

        $form = $this->createForm(PasswordUserType::class, $user, [
            'passwordHasher' => $PasswordHasher
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){

            $this->entityManager->flush();

            $this->addFlash(
                'success',
            "Votre mot de passe a bien été mis à jour");
            
        }

        return $this->render('account/password/index.html.twig', [
            'modifyPwd' => $form->createView()
        ]);
    }
}

?>