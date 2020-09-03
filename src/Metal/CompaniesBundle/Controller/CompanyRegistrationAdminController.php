<?php

namespace Metal\CompaniesBundle\Controller;

use Metal\CompaniesBundle\Entity\CompanyRegistration;
use Sonata\AdminBundle\Controller\CRUDController;
use Symfony\Component\HttpFoundation\Request;

class CompanyRegistrationAdminController extends CRUDController
{
    public function downloadFileAction(Request $request)
    {
        $id = $request->attributes->get($this->admin->getIdParameter());

        $object = $this->admin->getObject($id);
        /* @var $object CompanyRegistration */

        return $this->get('vich_uploader.download_handler')
            ->downloadObject($object, 'uploadedPrice', null, true);
    }
}
