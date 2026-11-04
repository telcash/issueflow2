<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\HttpFoundation\Response;

final class SecurityController extends AbstractController
{
    /**
     * El POST será interceptado por el FormLoginAuthenticator, no tengo
     * que comprobar manualmente ni email ni contraseña.
     * El GET renderiza la pantalla de login (formulario de login)
     */
    #[Route('/login', name: 'app_login', methods: ['GET', 'POST'])]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // Si ya hay un usuario autenticado, no tiene sentido mostrar el formulario de login
        if ($this->getUser() !== null) {
            return $this->redirectToRoute('app_ticket_index');
        }

        // Renderizamos el formulario de login
        return $this->render('security/login.html.twig', [
            // Symfony conserva el último identificador enviado para repoblarlo
            'last_username' => $authenticationUtils->getLastUsername(),

            // Si el intentó falló, el autenticador deja aquó la excepción
            'error' => $authenticationUtils->getLastAuthenticationError(),
        ]);
    }
}