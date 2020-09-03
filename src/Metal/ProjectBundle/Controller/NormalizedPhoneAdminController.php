<?php

namespace Metal\ProjectBundle\Controller;

use Sonata\AdminBundle\Controller\CRUDController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

class NormalizedPhoneAdminController extends CRUDController
{
    public function listAction()
    {
        $filterParameters = $this->admin->getFilterParameters();

        if (!isset($filterParameters['phone']) || !$filterParameters['phone']['value']) {
            $this->addFlash('sonata_flash_error', 'Сначала нужно ввести номер телефона для поиска.');

            return new RedirectResponse(
                $this->container->get('router')->generate('admin_metal_project_normalizedphone_search_by_phone')
            );
        }

        return parent::listAction();
    }

    public function searchByPhoneAction(Request $request)
    {
        if (!$request->isMethod('post')) {
            return $this->render(
                'MetalProjectBundle:Admin/NormalizedPhoneAdmin:search_by_phone.html.twig',
                array(
                    'action' => null,
                    'object' => null
                )
            );
        }

        $phone = $request->request->get('search');
        $phone = preg_replace('/\D*/ui', '', $phone);

        if (!$phone) {
            $this->addFlash('sonata_flash_error', 'Введен неправильный номер телефона.');

            return $this->redirect($this->generateUrl('admin_metal_project_normalizedphone_search_by_phone'));
        }

        return $this->redirect(
            $this->generateUrl(
                'admin_metal_project_normalizedphone_list',
                array('filter' => array('phone' => array('value' => $phone)))
            )
        );
    }
}
