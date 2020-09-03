<?php

namespace Metal\ProjectBundle\EventListener\Metalloprokat;

use Metal\CategoriesBundle\Entity\Category;
use Metal\DemandsBundle\Entity\Demand;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * @deprecated Этот класс создавался для редиректов с проката на строй для товаров, которые относятся к строительным категориям
 *
 */
class RedirectStroyListener
{
    private $baseHostStroy;
    private $baseHost;
    private $router;

    public function __construct($baseHostStroy, $baseHost, UrlGeneratorInterface $router)
    {
        $this->baseHostStroy = $baseHostStroy;
        $this->baseHost = $baseHost;
        $this->router = $router;
    }

    public function onKernelController(FilterControllerEvent $event)
    {
        // for preventing recursion on 404 pages
        if ($event->getRequestType() !== HttpKernelInterface::MASTER_REQUEST) {
            return;
        }

        //TODO:если у заявки все demad_items переехали на строй, нужен редирект такой заявки на строй
        $request = $event->getRequest();

        $attributesBag = $request->attributes;
        $route = $attributesBag->get('_route');

        if ('MetalProductsBundle:Product:view_subdomain' === $route) {
            $routeParams = array(
                'id' => $attributesBag->get('id'),
                'subdomain' => $attributesBag->get('subdomain'),
                'domain' => $this->baseHostStroy,
            );
            $routeParams = array_merge($routeParams, $request->query->all());
            $redirectUrl = $this->router->generate('MetalProductsBundle:Product:view_subdomain_domain', $routeParams);

            $event->setController(function () use ($redirectUrl) {
                return new RedirectResponse($redirectUrl, 301);
            });

            return;
        } elseif ('MetalMiniSiteBundle:MiniSite:product_view_subdomain' === $route) {
            $routeParams = array(
                'id' => $attributesBag->get('id'),
                'domain' => $attributesBag->get('subdomain').'.'.$this->baseHostStroy,
            );
            $routeParams = array_merge($routeParams, $request->query->all());
            $redirectUrl = $this->router->generate('MetalMiniSiteBundle:MiniSite:product_view', $routeParams);

            $event->setController(function () use ($redirectUrl) {
                return new RedirectResponse($redirectUrl, 301);
            });

            return;
        }

        $category = $attributesBag->get('category');
        /* @var $category Category */

        if (!$category || !$category->getRedirectSlug()) {
            return;
        }

        $redirectUrl = preg_replace(
            '#(https?://(.*?)\.)'.preg_quote($this->baseHost).'(/(.*?))'.preg_quote($category->getSlugCombined()).'(/(.*?))#',
            '$1'.$this->baseHostStroy.'$3'.$category->getRedirectSlug().'$5',
            $request->getUri());

        $event->setController(function () use ($redirectUrl) {
            return new RedirectResponse($redirectUrl, 301);
        });
    }
}
