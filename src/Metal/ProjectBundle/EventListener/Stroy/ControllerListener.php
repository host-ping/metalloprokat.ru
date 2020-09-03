<?php

namespace Metal\ProjectBundle\EventListener\Stroy;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class ControllerListener
{
    private $baseHost;
    private $router;

    public function __construct($baseHost, UrlGeneratorInterface $router)
    {
        $this->baseHost = $baseHost;
        $this->router = $router;
    }

    public function onKernelController(FilterControllerEvent $event)
    {
        $request = $event->getRequest();
        $attributesBag = $request->attributes;
        $route = $attributesBag->get('_route');

        if (0 !== strpos($route, 'MetalContentBundle') && $route !== 'MetalProjectBundle:Default:index') {
            return;
        }

        $routeParams = array_merge($request->attributes->get('_route_params'), $request->query->all());
        $requireRedirect = false;

        if (preg_match('#(cur[Pp]os)=(\d+)#', $request->getRequestUri(), $matches)) {
            $pageNumber = $matches[2] / 10 + 1;

            unset($routeParams[$matches[1]]);
            $routeParams['page'] = $pageNumber;

            $requireRedirect = true;
        }

        if (isset($routeParams['f_RubricMain'])) {
            unset($routeParams['f_RubricMain']);
            $requireRedirect = true;
        }

        if ($requireRedirect) {
            $redirectUrl = $this->router->generate($route, $routeParams);
            $event->setController(function () use ($redirectUrl) {
                return new RedirectResponse($redirectUrl, 301);
            });

            return;
        }
    }
}
