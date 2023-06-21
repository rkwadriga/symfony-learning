<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class AuthController extends AbstractController
{
    #[Route('/auth/login', name: 'app_auth_login')]
    public function login(AuthenticationUtils $authUtils): Response
    {
        $lastUsername = $authUtils->getLastUsername();
        $lasAuthError = $authUtils->getLastAuthenticationError();

        return $this->render('auth/login.html.twig', [
            'lastUsername' => $lastUsername,
            'lasAuthError' => $lasAuthError,
        ]);
    }

    #[Route('/auth/logout', name: 'app_auth_logout')]
    public function logout(): Response
    {
        return new Response();
    }
}
