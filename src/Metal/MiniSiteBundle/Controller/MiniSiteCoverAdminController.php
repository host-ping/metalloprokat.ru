<?php

namespace Metal\MiniSiteBundle\Controller;

use Metal\MiniSiteBundle\Entity\MiniSiteCover;
use Sonata\AdminBundle\Controller\CRUDController;
use Symfony\Component\HttpFoundation\Request;

class MiniSiteCoverAdminController extends CRUDController
{
    public function downloadFileAction(Request $request)
    {
        $id = $request->attributes->get($this->admin->getIdParameter());

        $object = $this->admin->getObject($id);
        /* @var $object MiniSiteCover */

        return $this->get('vich_uploader.download_handler')
            ->downloadObject($object, 'uploadedFile', null, true);
    }
}
