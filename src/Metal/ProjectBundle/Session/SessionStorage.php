<?php

namespace Metal\ProjectBundle\Session;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorage;

class SessionStorage extends NativeSessionStorage
{
    public function fixCookieDomain(RequestStack $requestStack, array $domainsRegex)
    {
        $request = $requestStack->getCurrentRequest();

        if (!$request instanceof Request) {
            return;
        }

        foreach ($domainsRegex as $regex => $domain) {
            if (preg_match('/'.$regex.'/ui', $request->getHost())) {
                $this->setOptions(array('cookie_domain' => $domain));

                return;
            }
        }
    }
}
