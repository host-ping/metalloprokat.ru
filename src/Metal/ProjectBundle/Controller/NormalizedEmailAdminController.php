<?php

namespace Metal\ProjectBundle\Controller;

use Sonata\AdminBundle\Controller\CRUDController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

class NormalizedEmailAdminController extends CRUDController
{
    public function listAction()
    {
        $filterParameters = $this->admin->getFilterParameters();

        if (!isset($filterParameters['email']) || !$filterParameters['email']['value']) {
            $this->addFlash('sonata_flash_error', 'Сначала нужно ввести email для поиска.');

            return new RedirectResponse(
                $this->container->get('router')->generate('admin_metal_project_normalizedemail_search_by_email')
            );
        }

        return parent::listAction();
    }

    public function searchByEmailAction(Request $request)
    {
        if (!$request->isMethod('post')) {
            return $this->render(
                'MetalProjectBundle:Admin/NormalizedEmailAdmin:search_by_email.html.twig',
                array(
                    'action' => null,
                    'object' => null
                )
            );
        }

        $email = $request->request->get('search');

        if (!$email) {
            $this->addFlash('sonata_flash_error', 'Не введен email.');

            return $this->redirect($this->generateUrl('admin_metal_project_normalizedphone_search_by_phone'));
        }

        return $this->redirect(
            $this->generateUrl(
                'admin_metal_project_normalizedemail_list',
                array('filter' => array('email' => array('value' => $email)))
            )
        );
    }
}