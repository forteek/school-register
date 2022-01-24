<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SessionController extends AbstractController
{
    #[Route('/login', name: 'login')]
    public function create(AuthenticationUtils $authenticationUtils): Response
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        if ($error) {
            $this->addFlash('error', $error->getMessage());
        }

        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('session/create.html.twig', [
            'last_username' => $lastUsername,
        ]);
    }
}
