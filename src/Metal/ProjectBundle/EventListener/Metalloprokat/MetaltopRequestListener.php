<?php

namespace Metal\ProjectBundle\EventListener\Metalloprokat;

use Doctrine\ORM\EntityManager;
use Metal\CompaniesBundle\Entity\Company;
use Metal\ProjectBundle\Helper\UrlHelper;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class MetaltopRequestListener
{
    private $em;

    private $metaltopHost;

    private $me1Host;

    private $uploadDir;

    private $router;

    /**
     * @var UrlHelper
     */
    private $urlHelper;

    public function __construct(EntityManager $em, $metaltopHost, $me1Host, $uploadDir, UrlGeneratorInterface $router, ContainerInterface $container)
    {
        $this->em = $em;
        $this->metaltopHost = $metaltopHost;
        $this->me1Host = $me1Host;
        $this->uploadDir = $uploadDir;
        $this->router = $router;
        $this->urlHelper = $container->get('brouzie.helper_factory')->get('MetalProjectBundle:Url');
    }

    public function onKernelRequestBeforeRouting(GetResponseEvent $event)
    {
        // for preventing recursion on 404 pages
        if (!$event->isMasterRequest()) {
            return;
        }

        $request = $event->getRequest();
        $company = $request->attributes->get('company');
        /* @var $company Company */

        $isMe1Host = false !== strpos($request->getHost(), $this->me1Host);
        $isMetaltopHost = false !== strpos($request->getHost(), $this->metaltopHost);

        if (!$isMe1Host && !$isMetaltopHost) {
            return;
        }

        if ($company) {
            if ($company->getDomain() != $request->getHost()) {
                $url = $this->urlHelper->generateUrl('MetalMiniSiteBundle:MiniSite:view', array('domain' => $company->getDomain(), '_secure' => $company->getPackageChecker()->isHttpsAvailable()), true);
                $event->setResponse(new RedirectResponse($url, 301));
            }

            return;
        }

        if ($isMetaltopHost && $request->query->get('id')) {
            $response = new BinaryFileResponse($this->uploadDir.'/other/metaltop.gif');
            $response->setContentDisposition(
                ResponseHeaderBag::DISPOSITION_INLINE,
                'metaltop.gif'
            );

            $event->setResponse($response);

            return;
        }

        if ($isMetaltopHost && $request->query->get('from')) {
            $company = $this->em->getRepository('MetalCompaniesBundle:Company')->find($request->query->get('from'));

            if (!$company) {
                $url = $this->router->generate(
                    'MetalProjectBundle:Default:index', array(), true
                );
            } else {
                $url = $this->urlHelper->generateUrl(
                    'MetalMiniSiteBundle:MiniSite:view', array('domain' => $company->getDomain(), '_secure' => $company->getPackageChecker()->isHttpsAvailable()), true
                );
            }

            $event->setResponse(new RedirectResponse($url, 301));

            return;
        }

        $event->setResponse(new RedirectResponse($this->router->generate('MetalProjectBundle:Default:index'), 301));
    }
}
