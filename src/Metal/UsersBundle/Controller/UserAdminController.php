<?php

namespace Metal\UsersBundle\Controller;

use Metal\UsersBundle\Entity\User;
use Sonata\AdminBundle\Controller\CRUDController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;

class UserAdminController extends CRUDController
{
    public function sendRegistrationEmailAction(Request $request)
    {
        $user = $this->admin->getSubject();
        /* @var $user User */

        $id = $user->getId();

        if (!$request->isMethod('POST')) {
            return $this->render(
                '@MetalUsers/UserAdmin/sendRegistrationEmail.html.twig',
                array('id' => $id, 'action' => 'show')
            );
        }

        $email = $request->get('email');

        $constraintViolations = $this->get('validator')
            ->validate($email, array(new NotBlank(), new Email(array('strict' => true))));

        if (count($constraintViolations)) {
            foreach ($constraintViolations as $error) {
                $this->addFlash('error', $error->getMessage());
            }

            return $this->redirect(
                $this->admin->generateUrl('send_registration_email', array('id' => $id))
            );
        }

        $this->get('metal.users.users_mailer')->notifyOnRegistration($user, $email);

        $this->addFlash('success', 'Email отправлен на адрес '.$email);

        return $this->redirect(
            $this->admin->generateUrl('send_registration_email', array('id' => $id))
        );
    }

    public function sendRecoveryPasswordEmailAction()
    {
        $this->get('metal.users.user_service')->recoveryPassword($this->admin->getSubject());

        $this->addFlash('success', 'Email для восстановления пароля отправлен.');

        return $this->redirect(
            $this->admin->generateUrl('list', array('filter' => $this->admin->getFilterParameters()))
        );
    }

    public function confirmUserEmailAction()
    {
        $user = $this->admin->getSubject();
        /* @var $user User */

        $em = $this->getDoctrine()->getManager();
        /* @var $em EntityManager */

        $user->setIsEmailConfirmed(true);
        $user->setRegistrationCode('');

        $subscriberService = $this->container->get('metal.newsletter.subscriber_service');
        $subscriberService->removeUnnecessarySubscriberForUser($user);
        $subscriberService->createOrUpdateSubscriberForUser($user);
        $em->flush();

        return $this->redirect(
            $this->admin->generateUrl('list', array('filter' => $this->admin->getFilterParameters()))
        );

    }

}
