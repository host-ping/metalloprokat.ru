<?php

namespace Brouzie\Bundle\HelpersBundle\Helper;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\HttpFoundation\Request;

abstract class HelperAbstract implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * @param string $name
     *
     * @return HelperAbstract
     */
    protected function getHelper($name)
    {
        return $this->container->get('brouzie.helper_factory')->get($name);
    }

    /**
     * @return Request
     */
    final protected function getRequest()
    {
        return $this->container->get('request_stack')->getMasterRequest();
    }
}
