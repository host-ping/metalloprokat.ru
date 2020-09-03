<?php

namespace Metal\ServicesBundle\Controller;

use Doctrine\ORM\EntityManager;
use Metal\ServicesBundle\Entity\Payment;
use Metal\UsersBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class RobokassaPaymentController extends Controller
{
    public function resultAction(Request $request)
    {
        $manager = $this->get('jh9.robokassa.manager');
        $payResult = $manager->handleResult($request);

        if ($payResult->isValid()) {
            $em = $this->getDoctrine()->getManager();
            /* @var $em EntityManager */
            $payment = $em->find('MetalServicesBundle:Payment', $payResult->getInvId());

            if (!$payment) {
                throw $this->createNotFoundException(sprintf('Order %d not found', $payResult->getInvId()));
            }

            $payment->setStatus(Payment::STATUS_PAYED);

            $em->flush();

            return new Response(sprintf('OK%d', $payment->getId()));
        }

        return new Response('Not valid', 400);
    }

    public function successAction(Request $request)
    {
        $manager = $this->get('jh9.robokassa.manager');
        $payResult = $manager->handleSuccess($request);

        if (!$payResult->isValid()) {
            return new Response('Not valid', 400);
        }

        $em = $this->getDoctrine()->getManager();
        /* @var $em EntityManager */
        $payment = $em->find('MetalServicesBundle:Payment', $payResult->getInvId());

        if (!$payment) {
            throw $this->createNotFoundException(sprintf('Order %d not found', $payResult->getInvId()));
        }

        $user = $this->getUser();
        /* @var $user User */
        $mailer = $this->get('metal.newsletter.mailer');
        try {
            $mailer->sendMessage(
                '@MetalServices/emails/paid_payment_to_user.html.twig',
                array($user->getEmail() => $user->getFullName()),
                array(
                    'user' => $user,
                    'country' => $user->getCountry(),
                    'payment' => $payment
                )
            );

            if (!$user->isMainUserForCompany()) {
                $director = $user->getCompany()->getCompanyLog()->getCreatedBy();
                $mailer->sendMessage(
                    '@MetalServices/emails/paid_payment_to_director.html.twig',
                    array($director->getEmail() => $director->getFullName()),
                    array(
                        'user' => $user,
                        'country' => $user->getCountry(),
                        'payment' => $payment
                    )
                );

            }

            if ($manager = $user->getCompany()->getManager()) {
                $mailer->sendMessage(
                    '@MetalServices/emails/paid_payment_to_manager.html.twig',
                    array($manager->getEmail() => $manager->getFullName()),
                    array(
                        'user' => $user,
                        'country' => $user->getCountry(),
                        'payment' => $payment
                    )
                );
            }

            foreach ($this->container->getParameter('admin_emails_for_creation_demand_package') as $email) {
                $mailer->sendMessage(
                    '@MetalServices/emails/paid_payment_to_manager.html.twig',
                    $email,
                    array(
                        'user' => $user,
                        'country' => $user->getCountry(),
                        'payment' => $payment,
                        'isDuplicate' => true
                    )
                );
            }

        } catch (\Swift_RfcComplianceException $e) {

        }

        return $this->redirect($this->generateUrl('MetalPrivateOfficeBundle:Details:payment', array('show_flash' => true)));
    }

    public function failAction(Request $request)
    {
        $payResult = $this->get('jh9.robokassa.manager')->handleFail($request);

        return new Response(
            sprintf(
                'Your order with id %d is not paid amount: %s',
                $payResult->getInvId(),
                $payResult->getOutSum()
            )
        );
    }
}
