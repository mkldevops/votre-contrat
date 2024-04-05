<?php

namespace App\Controller;

use LogicException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser() instanceof UserInterface) {
            return $this->redirectToRoute('app_admin');
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('@EasyAdmin/page/login.html.twig', [
            'error' => $error,
            'last_username' => $lastUsername,
            'page_title' => '<h1>App formation</h1><p class="text-center">Admin login</p>',
            'csrf_token_intention' => 'authenticate',
            'target_path' => $this->generateUrl('app_admin'),
            'username_label' => 'E-mail',
            'password_label' => 'Password',
            'username_parameter' => 'email',
            'password_parameter' => 'password',
            'remember_me_enabled' => true,
            'remember_me_parameter' => '_remember_me',
            'remember_me_checked' => true,
            'remember_me_label' => 'Remember me',
            'translation_domain' => 'messages',
        ]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
