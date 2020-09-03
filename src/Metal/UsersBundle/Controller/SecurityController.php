<?php

namespace Metal\UsersBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class SecurityController extends Controller
{
    public function loginAction(Request $request)
    {
        if ($this->isGranted('ROLE_USER')) {
            return $this->redirect($request->getUriForPath('/'));
        }

        $authenticationUtils = $this->get('security.authentication_utils');

        return $this->render(
            '@MetalUsers/Security/login.html.twig',
            array(
                'last_username' => $authenticationUtils->getLastUsername(),
                'error' => $authenticationUtils->getLastAuthenticationError(),
            )
        );
    }
}
