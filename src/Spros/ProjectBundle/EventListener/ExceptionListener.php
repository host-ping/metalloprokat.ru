<?php

namespace Spros\ProjectBundle\EventListener;

use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class ExceptionListener
{
    private $baseHost;
    private $em;

    public function __construct($baseHost, EntityManager $entityManager)
    {
        $this->baseHost = $baseHost;
        $this->em = $entityManager;
    }

    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $request = $event->getRequest();

        if (false === strpos($request->getHost(), $this->baseHost)) {
            return;
        }

        $ex = $event->getException();
        if (!$ex instanceof NotFoundHttpException) {
            return;
        }

        $uriExplode = array_filter(explode('/', $request->getRequestUri()));

        $categories = $this->em->createQueryBuilder('c')
            ->from('MetalCategoriesBundle:Category', 'c', 'c.slug')
            ->select('c.slugCombined')
            ->addSelect('c.slug')
            ->andWhere('c.slug IN (:uri_explode)')
            ->setParameter('uri_explode', $uriExplode)
            ->getQuery()
            ->getArrayResult();

        $uriReverse = array_reverse($uriExplode);
        $newUri = null;
        foreach ($uriReverse as $key => $uri) {
            if (isset($categories[$uri])) {
                $newUri[$key] = $categories[$uri]['slugCombined'];
                break;
            }
        }

        if (count($newUri)) {
            $index = current(array_keys($newUri));
            foreach ($uriReverse as $key => $uri) {
                if ($key == $index) {
                    break;
                }
                $addUri[] = $uri;
            }

            $addUri[] = $newUri[$index];
            $newUri = implode('/', array_reverse($addUri));

            $event->setResponse(new RedirectResponse($request->getUriForPath('/'.$newUri), 301));

            return;
        }

        $request->getSession()->getFlashBag()->add('error', 'Запрошенная страница не существует');
        $attrs = $request->attributes->all();
        $subRequest = $request->duplicate(array(), null, $attrs);
        $subRequest->attributes->add(array('_controller' => 'SprosProjectBundle:Default:index', 'is_404' => true));
        $subResponse = $event->getKernel()->handle($subRequest, HttpKernelInterface::SUB_REQUEST);
        $event->setResponse($subResponse);
    }
}
