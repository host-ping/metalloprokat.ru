<?php

namespace Metal\ComplaintsBundle\Controller;


use Doctrine\ORM\EntityManager;
use Sonata\AdminBundle\Controller\CRUDController;

class ComplaintAdminController extends CRUDController
{
    public function showAction($id = null)
    {
        $request = $this->get('request_stack')->getCurrentRequest();
        $id = $this->get('request_stack')->getMasterRequest()->get($this->admin->getIdParameter());
        $em = $this->getDoctrine()->getManager();
        /* @var $em EntityManager */

        $object = $this->admin->getObject($id);

        if ($request->isMethod('POST')) {
            if ($request->request->get('process')) {
                $object->setProcessed();
                $this->addFlash('success', 'Жалоба помечена как решенная');
                $em->flush();
            }

            if ($request->request->get('reopen')) {
                $object->setProcessed(false);
                $this->addFlash('sonata_flash_info', 'Жалоба переоткрыта');
                $em->flush();
            }
        }

        return $this->render($this->admin->getTemplate('show'), array(
            'object' => $object,
            'action' => 'show',
            'elements' => $this->admin->getShow()
        ));
    }
}
