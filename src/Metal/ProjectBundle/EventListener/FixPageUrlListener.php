<?php

namespace Metal\ProjectBundle\EventListener;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

class FixPageUrlListener
{
    public function onKernelRequestBeforeRouting(GetResponseEvent $event)
    {
        $request = $event->getRequest();
        $queryBag = $request->query;

        if ($queryBag->has('page')) {
            $page = $queryBag->get('page');
            // allow sql profiler
            if ('explain' !== $page && (int)$page < 2) {
                $queryBag->remove('page');
                list($requestUri) = explode('?', $request->getRequestUri());
                $queryString = '';
                if (count($queryBag) > 0) {
                    $queryString = '?'.http_build_query($queryBag->all());
                }
                $event->setResponse(new RedirectResponse($request->getUriForPath($requestUri.$queryString), 301));
            }
        }
    }
}
