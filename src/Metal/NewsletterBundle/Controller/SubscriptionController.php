<?php

namespace Metal\NewsletterBundle\Controller;

use Metal\NewsletterBundle\Entity\Subscriber;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SubscriptionController extends Controller
{
    public function unsubscribeAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $email = $request->query->get('email');
        $type = $request->query->get('type');
        $code = $request->query->get('code');

        $validCode = $this->get('brouzie.helper_factory')->get('MetalNewsletterBundle')->generateCode($email, $type);

        if ($code !== $validCode) {
            return new Response('Неверный код подтверждения.');
        }

        if ($type === 'notify-old-stroy-user') {
            $user = $em->getRepository('MetalUsersBundle:User')->findOneBy(array('email' => $email));
            $user->setIsEnabled(false);
            $em->flush();

            return new Response('Вы успешно отписались.');
        }

        $subscriber = $em->getRepository('MetalNewsletterBundle:Subscriber')->findOneBy(array('email' => $email));
        /* @var $subscriber Subscriber */

        if (!$subscriber) {
            return new Response('Такого адреса нет в базе.');
        }

        switch ($type) {
            case 'news':
                $subscriber->setSubscribedOnNews(false);
                break;

            case 'demands':
                $subscriber->setSubscribedForDemands(false);
                break;

            case 'index':
                $subscriber->setSubscribedOnIndex(false);
                break;

            case 'recall':
                $subscriber->setSubscribedOnRecallEmails(false);
                break;

            case 'demand-recall':
                $subscriber->setSubscribedOnDemandRecallEmails(false);
                break;

            case 'price-invite-recall':
                $subscriber->setSubscribedOnPriceInviteEmails(false);
                break;

            case 'products-updated':
                $subscriber->setSubscribedOnProductsUpdate(false);
                break;
        }

        $em->flush();

        return new Response('Вы успешно отписались.');
    }
}
