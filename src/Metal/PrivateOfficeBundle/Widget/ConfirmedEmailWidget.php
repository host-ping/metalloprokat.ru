<?php

namespace Metal\PrivateOfficeBundle\Widget;

use Brouzie\WidgetsBundle\Widget\ConditionallyRenderedWidget;
use Brouzie\WidgetsBundle\Widget\TwigWidget;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class ConfirmedEmailWidget extends TwigWidget implements ConditionallyRenderedWidget, ContainerAwareInterface
{
    use ContainerAwareTrait;

    public function shouldBeRendered()
    {
        $checker = $this->container->get('security.authorization_checker');

        return !$checker->isGranted('ROLE_CONFIRMED_EMAIL');
    }
}
