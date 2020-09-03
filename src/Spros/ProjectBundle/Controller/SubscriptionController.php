<?php

namespace Spros\ProjectBundle\Controller;

use Metal\NewsletterBundle\Entity\Subscriber;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\Email;

use Spros\ProjectBundle\Entity\DemandSubscription;

class SubscriptionController extends Controller
{
    public function subscribeAction(Request $request)
    {
        $email = $request->request->get('email');

        $errorList = $this->get('validator')->validate($email, new Email(array('strict' => true)));

        if (!$email || count($errorList) > 0) {
            return JsonResponse::create(
                array(
                    'success' => false,
                    'message' => 'Неверно введен адрес электронной почты!',
                )
            );
        }

        $em = $this->getDoctrine()->getManager();

        $demandSubscription = new DemandSubscription();
        $demandSubscription->setEmail($email);

        if ($cityId = $request->request->get('city')) {
            $city = $em->find('MetalTerritorialBundle:City', $cityId);
            if ($city) {
                $demandSubscription->setCity($city);
            }
        }

        if ($categoryId = $request->request->get('category')) {
            $category = $em->find('MetalCategoriesBundle:Category', $categoryId);
            if ($category) {
                $demandSubscription->setCategory($category);
            }
        }

        $demandSubscription->setReferer($request->headers->get('REFERER'));
        $demandSubscription->setIp($request->getClientIp());
        $demandSubscription->setUserAgent($request->headers->get('USER_AGENT'));

        $em->persist($demandSubscription);
        $em->flush();

        $text = sprintf(
            '%s <br/> <a href="%s">%s</a> <br/> %s <br/> <a href="%s">%s</a>',
            'Для подтверждения подписки на товар перейдите по ссылке.',
            $this->generateUrl(
                'SprosProjectBundle:Subscription:confirmation',
                array('code' => $demandSubscription->getConfirmationCode()),
                true
            ),
            'Перейти для подтверждения подписки',
            'Если хотите отписаться перейдите по ссылке.',
            $this->generateUrl(
                'SprosProjectBundle:Subscription:unsubscribe',
                array('code' => $demandSubscription->getConfirmationCode()),
                true
            ),
            'Отписаться'
        );

        $message = \Swift_Message::newInstance()
            ->setSubject('Подтверждение подписки на рассылку')
            ->setFrom($this->container->getParameter('mailer_from_metallspros'))
            ->setTo($email)
            ->setBody($text, 'text/html');

        try {
            $this->get('mailer')->send($message);
        } catch (\Exception $e) {
            $this->get('session')->getFlashBag()->set('error', 'Произошла ошибка. Пожалуйста попробуйте позже.');

            return $this->redirect($this->generateUrl('SprosProjectBundle:Default:index'));
        }

        return JsonResponse::create(
            array(
                'success' => true,
                'message' => 'Вы успешно подписались на рассылку.',
            )
        );
    }

    public function confirmationAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $code = $request->query->get('code');

        $demandSubscriptionRepository = $em->getRepository('SprosProjectBundle:DemandSubscription');
        if ($demandSubscription = $demandSubscriptionRepository->findOneBy(array('confirmationCode' => $code))) {
            $demandSubscription->setConfirmed();

            $this->get('session')->getFlashBag()->set('success', 'Подписка на рассылку успешно подтверждена.');

            $email = $demandSubscription->getEmail();

            $subscriberRepository = $em->getRepository('MetalNewsletterBundle:Subscriber');
            if ($oldSubscriber = $subscriberRepository->findOneBy(array('email' => $email))) {
                $subscriber = $oldSubscriber;
            } else {
                $subscriber = new Subscriber();
                $subscriber->setEmail($email);
            }

            $subscriber->setDemandSubscription($demandSubscription);
            $subscriber->setSubscribedForDemands(true);

            if(!$oldSubscriber) {
                $em->persist($subscriber);
            }

            $em->flush();

        } else {
            $this->get('session')->getFlashBag()->set( 'error', 'Неверный код подтверждения');
        }

        return $this->redirect($this->generateUrl('SprosProjectBundle:Default:index'));
    }

    public function unsubscribeAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $code = $request->query->get('code');

        $demandSubscriptionRepository = $em->getRepository('SprosProjectBundle:DemandSubscription');
        if ($demandSubscription = $demandSubscriptionRepository->findOneBy(array('confirmationCode' => $code))) {
            $demandSubscription->setUnsubscribed();
            $em->flush();

            $this->get('session')->getFlashBag()->set('success', 'Вы отписались');
        } else {
            $this->get('session')->getFlashBag()->set('error', 'Неверный код подтверждения');
        }

        return $this->redirect($this->generateUrl('SprosProjectBundle:Default:index'));
    }
}
