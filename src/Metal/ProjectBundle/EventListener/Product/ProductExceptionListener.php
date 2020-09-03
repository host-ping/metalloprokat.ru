<?php

namespace Metal\ProjectBundle\EventListener\Product;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class ProductExceptionListener
{
    private $conn;

    private $productHost;

    private $pokupaevHost;

    private $router;

    private $hostnamesMap;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct($productHost, $pokupaevHost, $hostnamesMap, UrlGeneratorInterface $router, Registry $doctrine)
    {
        $this->productHost = $productHost;
        $this->pokupaevHost = $pokupaevHost;
        $this->hostnamesMap = $hostnamesMap;
        $this->router = $router;
        $this->em = $doctrine->getManager();
        $this->conn = $this->em->getConnection();
    }

    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        // for preventing recursion on 404 pages
        if ($event->getRequestType() !== HttpKernelInterface::MASTER_REQUEST) {
            return;
        }

        $e = $event->getException();
        $request = $event->getRequest();

        //TODO: подобная проверка не безопасна, нужно подумать как обрабатывать эту часть !$ex instanceof MethodNotAllowedHttpException
        if (!$e instanceof NotFoundHttpException && !$e instanceof MethodNotAllowedHttpException) {
            return;
        }

        $redirectsTo = array(
            'produkt.kz' => 'product.kz'
        );

        foreach ($redirectsTo as $domain => $redirectDomain) {
            if (false !== stripos($request->getHost(), $domain)) {
                $event->setResponse(new RedirectResponse($this->hostnamesMap[$this->productHost]['host_prefix'].'://'.$redirectDomain.$request->getRequestUri(), 301));

                return;
            }
        }

        $isProductHost = false !== strpos($request->getHost(), $this->productHost);
        $isPokupaevHost = false !== strpos($request->getHost(), $this->pokupaevHost);

        if (!$isProductHost && !$isPokupaevHost) {
            return;
        }

        // отключено в связи с пуктом 5 в тикете http://projects.brouzie.com/browse/MET-1428
        if ($isProductHost && false) {
            $redirectsTo = array(
                '/vmainob.asp' => 'dosca.yarmarka.net',
                '/viewob.asp' => 'dosca.yarmarka.net',
                '/vfullob.asp' => 'dosca.yarmarka.net',
                '/debetorList.asp' => 'debetor.yarmarka.net',
                '/debetorShow.asp' => 'debetor.yarmarka.net',
                '/debRules.asp' => 'debetor.yarmarka.net',
                '/debetor.asp' => 'debetor.yarmarka.net',
                '/debetorPaid.asp' => 'debetor.yarmarka.net',
            );

            foreach ($redirectsTo as $from => $domainToRedirect) {
                if (false !== strpos($request->getPathInfo(), $from)) {
                    $event->setResponse(new RedirectResponse('http://'.$domainToRedirect.$request->getRequestUri(), 301));

                    return;
                }
            }
        }

        $productUrls = array(
            '/regmain.asp' => array('MetalUsersBundle:Registration:register', array('city' => null)),
            '/aboutsite.asp' => array('MetalCorpsiteBundle:Default:index', array()),
            '/SFRES.asp' => array('MetalProductsBundle:Products:companies_list', array('subdomain' => 'www')),
            '/vmainob.asp' => array('MetalProjectBundle:Default:index', array()),
            '/main.asp' => array('MetalProjectBundle:Default:index', array()),
            '/topfirm.asp' => array('MetalProductsBundle:Products:companies_list', array('subdomain' => 'www')),
            '/advertall.asp' => array('MetalCorpsiteBundle:Default:announcementOrder', array()),
            '/advertb.asp' => array('MetalCorpsiteBundle:Default:announcementOrder', array()),
            '/srchz.asp' => array('MetalCorpsiteBundle:Default:services', array()),
            '/Vip.asp' => array('MetalCorpsiteBundle:Default:services', array()),
            '/rgs.asp' => array('MetalCorpsiteBundle:Default:services', array()),
            '/help_objava.asp' => array('login', array()),
            '/SprosList.asp' => array('MetalDemandsBundle:Demands:list', array('page' => $request->query->get('idPage')))
        );

        if ($isProductHost && isset($productUrls[$request->getPathInfo()])) {
            $productCategoryId = $request->query->get('SFERA') ?: $request->query->get('VID');
            $q = $event->getRequest()->query->get('SEARCH');

            if ($productCategoryId) {
                $categoryId = $this->conn->fetchColumn('
                SELECT c.metal_category FROM product_category c
                WHERE c.id = :category_id', array('category_id' => $productCategoryId)
                );

                if ($categoryId) {
                    $category = $this->em->find('MetalCategoriesBundle:Category', $categoryId);

                    $event->setResponse(
                        new RedirectResponse(
                            $this->router->generate('MetalProductsBundle:Products:list_category',
                                array(
                                    'category_slug' => $category->getSlugCombined()
                                )
                            ), 301
                        ));

                    return;
                }
                if ($q) {
                    //TODO: нужно поиск выполнить как в /search-suggest/
                }
            }

            list($route, $routeParameters) = $productUrls[$request->getPathInfo()];
            $event->setResponse(new RedirectResponse($this->router->generate($route, $routeParameters), 301));

            return;
        }

        $pokupaevUrls = array(
            '/netcat/modules/auth' => array('login', array()),
            '/demand/add_demand.html' => array('MetalDemandsBundle:Demands:list', array()),
            '/' => array('MetalDemandsBundle:Default:index', array()),
            '/spros/signin/' => array('MetalCorpsiteBundle:Default:contacts', array()),
            '/sign/sign.html' => array('MetalDemandsBundle:Demands:list', array()),
        );

        if ($isPokupaevHost && isset($pokupaevUrls[$request->getPathInfo()])) {
            list($route, $routeParameters) = $pokupaevUrls[$request->getPathInfo()];
            $event->setResponse(new RedirectResponse($this->router->generate($route, $routeParameters), 301));

            return;
        }

        if ($isPokupaevHost) {
            $slugs = array_filter(explode('/', $request->getPathInfo()));

            $matchesDemandId = null;
            $demand = null;
            $slug = $slugs[count($slugs)];
            if (preg_match('/\_(\d+)\.html$/ui', $request->getPathInfo(), $matchesDemandId)) {
                $demand = $this->em->getRepository('MetalDemandsBundle:Demand')->findOneBy(array('oldDemandId' => $matchesDemandId[1]));
                $slug = $slugs[count($slugs) - 1];
            }

            $categoryId = $this->conn->fetchColumn('
                    SELECT c.metal_category FROM pokupaev_category c
                    WHERE c.slug = :slug',
                array(
                    'slug' => $slug
                )
            );

            if ($categoryId) {
                $category = $this->em->find('MetalCategoriesBundle:Category', $categoryId);

                if (!$category) {
                    $event->setResponse(new RedirectResponse($this->router->generate($productUrls['/vmainob.asp'][0], $productUrls['/vmainob.asp'][1]), 301));

                    return;
                }

                if ($demand) {
                    $event->setResponse(
                        new RedirectResponse(
                            $this->router->generate('MetalDemandsBundle:Demand:view',
                                array(
                                    'id' => $demand->getId(),
                                    'subdomain' => 'www',
                                    'category_slug' => $category->getSlugCombined()
                                )), 301));
                } else {
                    $event->setResponse(
                        new RedirectResponse(
                            $this->router->generate('MetalProductsBundle:Products:list_category',
                                array('category_slug' => $category->getSlugCombined())), 301));
                }

                return;
            }
        }

        if ($isPokupaevHost) {
            $event->setResponse(new RedirectResponse($this->router->generate($productUrls['/vmainob.asp'][0], $productUrls['/vmainob.asp'][1]), 301));

            return;
        }
    }
}
