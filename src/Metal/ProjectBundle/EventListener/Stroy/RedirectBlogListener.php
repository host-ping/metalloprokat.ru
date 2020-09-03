<?php

namespace Metal\ProjectBundle\EventListener\Stroy;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

/**
 * @deprecated Этот класс не используется так как поменялась концепция проекта
 *
 */
class RedirectBlogListener
{
    private $blogBaseUrl;

    public function __construct($blogBaseUrl)
    {
        $this->blogBaseUrl = $blogBaseUrl;

    }

    public function onKernelRequestBeforeRouting(GetResponseEvent $event)
    {
        // for preventing recursion on 404 pages
        if ($event->getRequestType() !== HttpKernelInterface::MASTER_REQUEST) {
            return;
        }

        $request = $event->getRequest();

        $redirectPatterns = array(
            '^/netcat/(.*?)',
            '^/profile/(.*?)',
            '^/apartment/(.*?)',
            '^/cottage/(.*?)',
            '^/build/(.*?)',
            '^/users/(.*?)',
            '^/market/(.*?)',
            '^/tags/(.*?)',
            '^/service/(.*?)',
            '^/about/(.*?)'
        );

        foreach ($redirectPatterns as $redirectPattern) {
            if (preg_match("#".$redirectPattern."#ui", $request->getRequestUri(), $redirectTo)) {
                $event->setResponse(new RedirectResponse($this->blogBaseUrl.$request->getRequestUri(), 301));

                return;
            }
        }
    }
}