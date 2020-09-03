<?php

namespace Metal\UsersBundle\Helper;

use Brouzie\Bundle\HelpersBundle\Helper\HelperAbstract;
use Metal\UsersBundle\Entity\User;

class AutoLoginHelper extends HelperAbstract
{
    public function filterUrl($url, $target)
    {
        $tokenPlaceholder = '{auto_login_token}';

        if (false !== strpos($url, $tokenPlaceholder)) {
            $user = $this->container->get('security.token_storage')->getToken()->getUser();
            /* @var $user User */

            $userAutoLogin = $this->container
                ->get('doctrine.orm.default_entity_manager')
                ->getRepository('MetalUsersBundle:UserAutoLogin')
                ->findOrCreateAutoLogin($user, $target);

            return str_replace($tokenPlaceholder, $userAutoLogin->getToken(), $url);
        }

        return $url;
    }
}
