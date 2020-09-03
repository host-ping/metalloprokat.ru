<?php

namespace Metal\CorpsiteBundle\Controller;

use Metal\CorpsiteBundle\Entity\ClientReview;
use Sonata\AdminBundle\Controller\CRUDController;
use Symfony\Component\HttpFoundation\Request;

class ClientReviewAdminController extends CRUDController
{
    public function downloadFileAction(Request $request)
    {
        $id = $request->attributes->get($this->admin->getIdParameter());

        $object = $this->admin->getObject($id);
        /* @var $object ClientReview */

        return $this->get('vich_uploader.download_handler')
            ->downloadObject($object, 'uploadedPhoto', null, true);
    }
}
