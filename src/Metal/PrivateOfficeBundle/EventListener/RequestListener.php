<?php

namespace Metal\PrivateOfficeBundle\EventListener;

use Brouzie\Bundle\HelpersBundle\Helper\HelperFactory;
use Metal\ProjectBundle\Helper\UrlHelper;
use Metal\TerritorialBundle\Entity\Country;
use Metal\UsersBundle\Entity\User;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;

/**
 * Слушатель личного кабинета, который делает следующие проверки:
 *   - пользователь с компанией должен быть подтвержден директором компании, в противном случае он перенаправляется на страницу "выш аккаунт ожидает подтверждения"
 *   - личный кабинет должен быть открыт на сайте той страны, к которой привязан пользователь
 */
class RequestListener
{
    private $tokenStorage;
    private $authChecker;
    /**
     * @var UrlHelper
     */
    private $urlHelper;
    private $privateOfficeHostname;

    public function __construct(
        TokenStorageInterface $tokenStorage,
        AuthorizationChecker $authChecker,
        HelperFactory $helperFactory,
        $privateOfficeHostname
    ) {
        $this->tokenStorage = $tokenStorage;
        $this->authChecker = $authChecker;
        $this->urlHelper = $helperFactory->get('MetalProjectBundle:Url');
        $this->privateOfficeHostname = $privateOfficeHostname;
    }

    public function onKernelRequestAfterSecurity(GetResponseEvent $event)
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        $request = $event->getRequest();

        $token = $this->tokenStorage->getToken();

        if (!$token || !$token->getUser() instanceof User
            || $request->attributes->get('_route') === 'MetalPrivateOfficeBundle:Default:notApproved'
            || $request->getHost() !== $this->privateOfficeHostname
        ) {
            return;
        }

        if ($this->authChecker->isGranted('ROLE_SUPPLIER') && !$this->authChecker->isGranted('ROLE_APPROVED_USER')) {
            $url = $this->urlHelper->generateUrl(
                'MetalPrivateOfficeBundle:Default:notApproved',
                array('redirect_url' => $request->getRequestUri())
            );
            $event->setResponse(new RedirectResponse($url));
        }

        // не редиректим админов, залогененых под пользователями
        if ($this->authChecker->isGranted('ROLE_PREVIOUS_ADMIN')) {
            return;
        }

        $user = $token->getUser();
        /* @var $user User */

        $userCountry = $user->getCountry();

        $country = $request->attributes->get('country');
        /* @var $country Country */

        if ($country->getId() != $userCountry->getId()
            && in_array($userCountry->getId(), Country::getEnabledCountriesIds())
        ) {
            $url = $this->urlHelper->generateUrl(
                'MetalPrivateOfficeBundle:Default:index_domain',
                array('domain' => $userCountry->getBaseHost(), '_secure' => $userCountry->getSecure())
            );
            $event->setResponse(new RedirectResponse($url));
        }
    }
}
