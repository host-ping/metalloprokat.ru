<?php

namespace Metal\ProjectBundle\Helper;

use Brouzie\Bundle\HelpersBundle\Helper\HelperAbstract;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class UrlHelper extends HelperAbstract
{
    private $urls = array();

    public function getRegistrationUrl($redirectTo = null)
    {
        $request = $this->getRequest();
        $city = $request->attributes->get('city');

        if ($redirectTo === true) {
            $redirectTo = $request->getRequestUri();
        }

        return $this->generateUrl('MetalUsersBundle:Registration:register',
            array(
                'city' => $city ? $city->getId() : null,
                '_redirect_to' => $redirectTo
            )
        );
    }

    public function getRegisterAndAddProductsUrl()
    {
        $key = 'register_and_add_products';
        if (isset($this->urls[$key])) {
            return $this->urls[$key];
        }

        $authorizationChecker = $this->container->get('security.authorization_checker');
        $tokenStorage = $this->container->get('security.token_storage');
        if ($tokenStorage->getToken()) {
            if ($authorizationChecker->isGranted('ROLE_SUPPLIER')) {
                return $this->urls[$key] = $this->generateUrl('MetalPrivateOfficeBundle:Products:list');
            }

            if ($authorizationChecker->isGranted('ROLE_USER')) {
                return $this->urls[$key] = $this->generateUrl('MetalPrivateOfficeBundle:CompanyCreation:createCompany');
            }
        }

        $request = $this->getRequest();
        $category = $request->attributes->get('category');

        if ($category) {
            return $this->urls[$key] = $this->generateUrl(
                'MetalUsersBundle:Registration:registerFirstStep',
                array('category_id' => $category->getId())
            );
        }

        $city = $request->attributes->get('city');

        return $this->urls[$key] = $this->generateUrl(
            'MetalUsersBundle:Registration:register',
            array(
                'city' => $city ? $city->getId() : null,
                '_redirect_to' => $this->generateUrl('MetalPrivateOfficeBundle:Products:list'),
            )
        );
    }

    public function getLogoutUrl()
    {
        if (isset($this->urls['logout'])) {
            return $this->urls['logout'];
        }

        $authorizationChecker = $this->container->get('security.authorization_checker');
        $request = $this->getRequest();
        $currentRoute = $request->attributes->get('_route');

        $route = 'logout';
        $routeParameters = array();
        if ($authorizationChecker->isGranted('ROLE_PREVIOUS_ADMIN')) {
            $route = $currentRoute;
            $routeParameters = array_merge(
                (array)$request->attributes->get('_route_params'),
                $request->query->all(),
                array('_switch_user' => '_exit')
            );
        }

        return $this->urls['logout'] = $this->generateUrl($route, $routeParameters);
    }

    public function generateUrl($route, array $parameters = array(), $absolute = UrlGeneratorInterface::ABSOLUTE_PATH)
    {
        $scheme = '';
        if (isset($parameters['_secure'])) {
            $scheme = $parameters['_secure'] && $this->isClientSupportsHttps() ? 'https' : 'http';
            unset($parameters['_secure']);
        }

        $url = $this->container->get('router')->generate($route, $parameters, $absolute);

        return $scheme ? self::replaceScheme($url, $scheme) : $url;
    }

    public function isClientSupportsHttps()
    {
        $request = $this->getRequest();
        if (null === $request) {
            return true;
        }

        $userAgent = $request->headers->get('User-Agent');
        $supportsHttps = !preg_match('/Windows\sNT\s5/i', $userAgent) && !preg_match('/MSIE\s6/i', $userAgent)
            && !preg_match('/Android\s2\.[012]/', $userAgent);

        return $supportsHttps;
    }

    public function convertSchemaRelativeToAbsoluteUrl($schemaRelativeUrl)
    {
        //TODO: добавить проверки на поддержку https, консольную команду и тд
        return self::replaceScheme($schemaRelativeUrl, 'http');
    }

    public function fixSecureScheme($url, $secure)
    {
        $scheme = $secure && $this->isClientSupportsHttps() ? 'https' : 'http';

        return self::replaceScheme($url, $scheme);
    }

    public static function replaceScheme($url, $scheme)
    {
        return preg_replace('/^.*?\/\//', $scheme.'://', $url);
    }
}
