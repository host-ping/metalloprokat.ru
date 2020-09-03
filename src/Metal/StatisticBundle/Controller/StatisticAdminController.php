<?php

namespace Metal\StatisticBundle\Controller;

use Sonata\AdminBundle\Controller\CoreController;
use Symfony\Component\HttpFoundation\Request;

class StatisticAdminController extends CoreController
{
    public function viewAction(Request $request)
    {
        return $this->render(
            'MetalStatisticBundle:Admin:view.html.twig',
            array(
                'base_template' => $this->getBaseTemplate(),
                'admin_pool' => $this->container->get('sonata.admin.pool'),
                'blocks' => $this->container->getParameter('sonata.admin.configuration.dashboard_blocks'),
            )
        );
    }
}
