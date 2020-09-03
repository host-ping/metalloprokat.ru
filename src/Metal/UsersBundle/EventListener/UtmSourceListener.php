<?php

namespace Metal\UsersBundle\EventListener;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;

class UtmSourceListener
{
    public function onKernelRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();
        $referer = $request->headers->get('REFERER');

        if ($referer && strpos($referer, 'utm_source') !== false) {
            $request->getSession()->set('registration_referer', $referer);
        }
    }
}
