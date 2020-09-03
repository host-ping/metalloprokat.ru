<?php

namespace Metal\NewsletterBundle\Helper;

use Brouzie\Bundle\HelpersBundle\Helper\HelperAbstract;

class DefaultHelper extends HelperAbstract
{
    public function generateCode($email, $type)
    {
        $secretString = $this->container->getParameter('unsubscribe_secret');

        return substr(sha1($email.$type.$secretString), 0, 10);
    }

    public function generateUnsubscribeUrl($email, $type)
    {
        return $this->container->get('router')->generate('MetalNewsletterBundle:Subscription:unsubscribe',
            array(
                'email' => $email,
                'type' => $type,
                'code' => $this->generateCode($email, $type)
            ),
            true
        );
    }
}
