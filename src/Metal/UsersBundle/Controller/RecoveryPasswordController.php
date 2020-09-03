<?php

namespace Metal\UsersBundle\Controller;

use Doctrine\ORM\EntityManager;

use Metal\UsersBundle\Entity\User;
use Metal\UsersBundle\Form\RequestRecoverPasswordType;
use Metal\UsersBundle\Form\RecoverPasswordType;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

class RecoveryPasswordController extends Controller
{
    public function recoverAction(Request $request)
    {
        if ($this->isGranted('ROLE_USER')) {
            return new RedirectResponse($request->getUriForPath('/'));
        }

        if ($request->isMethod('GET')) {
            $form = $this->createForm(new RequestRecoverPasswordType());

            return $this->render(
                '@MetalUsers/Security/recover_password.html.twig',
                array('form' => $form->createView())
            );
        }

        $em = $this->getDoctrine()->getManager();
        /* @var $em EntityManager */

        $form = $this->createForm(new RequestRecoverPasswordType());
        $form->handleRequest($request);

        if (!$form->isValid()) {
            $errors = $this->get('metal.project.form_helper')->getFormErrorMessages($form);

            return JsonResponse::create(
                array(
                    'errors' => $errors,
                )
            );
        }

        $email = $form->get('email')->getData();

        $user = $em->getRepository('MetalUsersBundle:User')->findOneBy(array('email' => $email));

        if (!$user) {
            $errors = array($form->get('email')->createView()->vars['full_name'] => array('Пользователь не найден'));

            return JsonResponse::create(array('errors' => $errors));
        }

        $this->get('metal.users.user_service')->recoveryPassword($user);

        return JsonResponse::create(
            array(
                'status' => 'success',
                'message' => 'Вам отправлено письмо с дальнейшими инструкциями по восстановлению пароля.',
            )
        );
    }

    public function confirmationRecoverAction(Request $request)
    {
        if ($this->isGranted('ROLE_USER')) {
            return new RedirectResponse($request->getUriForPath('/'));
        }

        $em = $this->getDoctrine()->getManager();
        /* @var $em EntityManager */
        $code = $request->query->get('code');
        $id = $request->query->get('id');

        $user = $em->getRepository('MetalUsersBundle:User')->find($id);

        if ($user) {
            if ($user->getRecoverCode() == $code) {

                $form = $this->createForm(new RecoverPasswordType(), $user);

                if ($request->isMethod('POST')) {
                    $form->handleRequest($request);

                    if (!$form->isValid()) {

                        if ($request->isXmlHttpRequest()) {
                            $errors = $this->get('metal.project.form_helper')->getFormErrorMessages($form);

                            return JsonResponse::create(
                                array(
                                    'errors' => $errors,
                                )
                            );
                        }

                        return $this->render(
                            '@MetalUsers/Default/recoverPassword.html.twig',
                            array(
                                'form' => $form->createView(),
                            )
                        );

                    }

                    $password = $this->get('security.encoder_factory')->getEncoder($user)->encodePassword(
                        $user->newPassword,
                        $user->getSalt()
                    );

                    $user->setPassword($password);
                    $user->setRecoverCode(null);

                    $em->flush();

                    return JsonResponse::create(array('status' => 'success'));
                }

                return $this->render(
                    '@MetalUsers/Default/recoverPassword.html.twig',
                    array(
                        'form' => $form->createView(),
                    )
                );
            }
            die('Неверный код подтверждения');
        }
        die('Пользователь не найден');
    }
}
