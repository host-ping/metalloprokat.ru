<?php

namespace Metal\ProjectBundle\EventListener\Metalloprokat;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityNotFoundException;
use Metal\ProductsBundle\Controller\ProductsController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class ControllerListener
{
    private $em;

    private $router;

    public function __construct(EntityManager $em, UrlGeneratorInterface $router)
    {
        $this->em = $em;
        $this->router = $router;
    }

    public function onKernelController(FilterControllerEvent $event)
    {
        $controller = $event->getController();
        $availableActions = array('listAction', 'listCompaniesAction');
        if (is_array($controller) && $controller[0] instanceof ProductsController && in_array($controller[1], $availableActions)) {

            $request = $event->getRequest();
            $queryBag = $request->query;
            $category = $request->attributes->get('category');
            $city = $request->attributes->get('city');

            if ($companyId = $queryBag->get('c_id')) {
                $company = $this->em->getRepository('MetalCompaniesBundle:Company')->find($companyId);

                if (!$company || $company->isDeleted()) {
                    throw new NotFoundHttpException('Object not found');
                }

                if ($category) {
                    $redirectUrl = $this->router->generate('MetalCompaniesBundle:Company:products_category', array(
                        'company_slug' => $company->getSlug(),
                        'category_slug' => $category->getSlugCombined(),
                        'subdomain' => $city ? $city->getSlug() : $company->getCity()->getSlug(),
                        'page' => $queryBag->get('page')
                    ));

                    $event->setController(function() use ($redirectUrl) {
                        return new RedirectResponse($redirectUrl, 301);
                    });

                    return;
                }

                $redirectUrl = $this->router->generate('MetalCompaniesBundle:Company:products', array(
                    'company_slug' => $company->getSlug(),
                    'subdomain' => $city ? $city->getSlug() : $company->getCity()->getSlug(),
                    'page' => $queryBag->get('page')
                ));

                $event->setController(function() use ($redirectUrl) {
                    return new RedirectResponse($redirectUrl, 301);
                });

                return;
            }

            if ($cityId = $queryBag->get('city')) {
                $city = $this->em->getRepository('MetalTerritorialBundle:City')->find($cityId);

                try {
                    //TODO: fix collision
                    $city->getSlugWithFallback();
                } catch (EntityNotFoundException $e) {
                    throw new NotFoundHttpException('City not found.');
                }

                $redirectUrl = $this->router->generate('MetalProductsBundle:Products:products_list', array(
                            'subdomain' => $city->getSlugWithFallback(),
                            'page' => $queryBag->get('page')
                    ));

                $event->setController(function() use ($redirectUrl) {
                    return new RedirectResponse($redirectUrl, 301);
                });

                return;
            }

        }


    }
}
