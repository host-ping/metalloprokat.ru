<?php

namespace Metal\ProjectBundle\Controller;

use EWZ\Bundle\RecaptchaBundle\Validator\Constraints as Recaptcha;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

class CaptchaController extends Controller
{
    public function captchaAction(Request $request)
    {
        $form = $this->createFormBuilder()
            ->add(
                'recaptcha',
                'ewz_recaptcha',
                array(
                    'constraints' => array(
                        new Recaptcha\IsTrue(),
                    )
                )
            )
            ->getForm();

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $request->getSession()->set('recaptcha_successfully_validated', true);

                return RedirectResponse::create($request->getUri());
            }
        }

        return $this->render('@MetalProject/Captcha/captcha.html.twig', array('form' => $form->createView()));
    }
}
