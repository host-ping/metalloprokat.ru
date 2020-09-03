<?php

namespace Metal\ProjectBundle\EventListener;

use Doctrine\ORM\EntityManager;
use Metal\CompaniesBundle\Entity\Company;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Matcher\UrlMatcherInterface;

class ExceptionListener
{
    private $em;
    private $minisiteHostnamesPattern;
    private $matcher;

    public function __construct(EntityManager $entityManager, UrlMatcherInterface $matcher, $minisiteHostnamesPattern)
    {
        $this->em = $entityManager;
        $this->matcher = $matcher;
        $this->minisiteHostnamesPattern = $minisiteHostnamesPattern;
    }

    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $request = $event->getRequest();

        $baseHost = null;
        if (preg_match('/('.$this->minisiteHostnamesPattern.')/ui', $request->getHost(), $matches)) {
            $baseHost = $matches[1];
        }

        if (null === $baseHost) {
            return;
        }

        $ex = $event->getException();
        if (!$ex instanceof MethodNotAllowedHttpException && !$ex instanceof NotFoundHttpException) {
            return;
        }

        // попытка найти в истории слагов компаний слаг и сделать редирект на новый адрес
        $companySlugMatches = null;
        // если не нашло в uri пытаемся найти по сабдомену
        if (!$request->attributes->get('city')) {
            $stringToMatch = $request->getHost();
            preg_match('/('.Company::SLUG_REGEX.')\.'.$baseHost.'/ui', $stringToMatch, $companySlugMatches);
        }

        // проверяю сначала на все товары компании
        if (!$companySlugMatches) {
            $stringToMatch = $request->getRequestUri();
            preg_match('/^\/('.Company::SLUG_REGEX.'+)\//ui', $stringToMatch, $companySlugMatches);
        }

        if ($companySlugMatches && $companyOldSlug = $this->em->getRepository('MetalCompaniesBundle:CompanyOldSlug')->findOneBy(array('oldSlug' => $companySlugMatches[1]))) {
            $redirectTo = preg_replace('/'.preg_quote($companySlugMatches[1]).'/ui',
                $companyOldSlug->getCompany()->getSlug(),
                $request->getScheme().'://'.$request->getHost().$request->getRequestUri()
            );

            $event->setResponse(new RedirectResponse($redirectTo, 301));
            return;
        }

        $pathInfo = $request->getPathInfo();
        if ($pathInfo && substr($pathInfo, -1) === '/') {
            $matchedParameters = null;
            try {
                $matchedParameters = $this->matcher->match(rtrim($pathInfo, '/'));
            } catch (\Exception $e) {

            }

            if ($matchedParameters) {
                if (null !== $qs = $request->getQueryString()) {
                    $qs = '?'.$qs;
                }

                $redirectTo = $request->getUriForPath(rtrim($pathInfo, '/')).$qs;
                $event->setResponse(new RedirectResponse($redirectTo, 301));

                return;
            }
        }

        // http code 405 -> 404
        $headers = $ex->getHeaders();
        if (!empty($headers['Allow']) && $headers['Allow'] === 'FOR_GENERATING_URL_ONLY') {
            $event->setException(new NotFoundHttpException());
        }
    }
}
