<?php

namespace Metal\ProjectBundle\EventListener\Product;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

class ProductRequestListener
{
    private $productHost;

    private $pokupaevHost;

    public function __construct($productHost, $pokupaevHost)
    {
        $this->productHost = $productHost;
        $this->pokupaevHost = $pokupaevHost;
    }

    public function onKernelRequestBeforeRouting(GetResponseEvent $event)
    {
        // for preventing recursion on 404 pages
        if (!$event->isMasterRequest()) {
            return;
        }

        $request = $event->getRequest();
        $isProductHost = false !== strpos($request->getHost(), $this->productHost);
        $isPokupaevHost = false !== strpos($request->getHost(), $this->pokupaevHost);

        if (!$isProductHost && !$isPokupaevHost) {
            return;
        }

        if ($isProductHost) {
            if (preg_match('/(\D+)index\.html$/ui', $request->getRequestUri(), $redirectTo)) {
                $event->setResponse(new RedirectResponse($request->getUriForPath($redirectTo[1]), 301));

                return;
            }

            if (preg_match('/(\D+)catalog\.php$/ui', $request->getRequestUri(), $redirectTo)) {
                $event->setResponse(new RedirectResponse($request->getUriForPath(preg_replace('/\.php/','',$redirectTo[0])), 301));

                return;
            }
        }
    }
}
