<?php

namespace Metal\ProjectBundle\Pagerfanta\EventListener;

use Pagerfanta\Exception\NotValidCurrentPageException;
use Pagerfanta\Exception\OutOfRangeCurrentPageException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class HandleOutOfRangeCurrentPage implements EventSubscriberInterface
{
    private $router;

    public function __construct(UrlGeneratorInterface $router)
    {
        $this->router = $router;
    }

    /**
     * @param GetResponseForExceptionEvent $event
     */
    public function onException(GetResponseForExceptionEvent $event)
    {
        $rootException = $exception = $event->getException();

        do {
            if ($exception instanceof OutOfRangeCurrentPageException) {
                preg_match('/"(\d+)"$/', $exception->getMessage(), $matches);
                $maxPage = $matches[1];
                $request = $event->getRequest();
                $url = $this->router->generate(
                    $request->attributes->get('_route'),
                    array_merge(
                        $request->attributes->get('_route_params'),
                        $request->query->all(),
                        array('page' => $maxPage)
                    ),
                    true
                );

                $event->setResponse(RedirectResponse::create($url, 301));

                return;
            }

            if ($exception instanceof NotValidCurrentPageException) {
                $event->setException(new NotFoundHttpException('Page Not Found', $rootException));
                return;
            }
        } while ($exception = $exception->getPrevious());
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::EXCEPTION => array('onException', 512)
        );
    }
}
