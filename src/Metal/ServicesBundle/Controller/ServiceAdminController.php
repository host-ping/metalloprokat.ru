<?php

namespace Metal\ServicesBundle\Controller;

use Sonata\AdminBundle\Controller\CRUDController;
use Symfony\Component\HttpFoundation\RedirectResponse;

class ServiceAdminController extends CRUDController
{
    public function createAction()
    {
        $routeParams = $this->get('request_stack')->getMasterRequest()->attributes->get('_route_params');

        if (!isset($routeParams['id'])) {
            $this->addFlash('sonata_flash_error', 'Сначала нужно выбрать компанию из списка компаний');

            return new RedirectResponse(
                $this->container->get('router')->generate('admin_metal_companies_company_list')
            );
        }

        return parent::createAction();
    }
}
