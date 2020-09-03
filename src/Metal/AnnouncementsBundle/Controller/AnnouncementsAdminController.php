<?php

namespace Metal\AnnouncementsBundle\Controller;

use Metal\AnnouncementsBundle\Entity\Announcement;
use Sonata\AdminBundle\Controller\CRUDController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class AnnouncementsAdminController extends CRUDController
{
    public function downloadFileAction(Request $request)
    {
        $id = $request->attributes->get($this->admin->getIdParameter());

        $object = $this->admin->getObject($id);
        /* @var $object Announcement */

        $response = new BinaryFileResponse($this->container->getParameter('upload_dir').$object->getWebPath());
        $response->setContentDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            $object->getFileOriginalName(),
            $object->getFileOriginalNameSanitized()
        );

        return $response;
    }

}