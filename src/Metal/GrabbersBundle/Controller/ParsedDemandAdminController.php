<?php

namespace Metal\GrabbersBundle\Controller;

use Metal\GrabbersBundle\Entity\ParsedDemand;
use Sonata\AdminBundle\Controller\CRUDController;
use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

class ParsedDemandAdminController extends CRUDController
{
    public function batchActionDelete(ProxyQueryInterface $query)
    {
        $em = $this->getDoctrine()->getManager();

        $parsedDemands = $query->execute();
        /* @var $parsedDemands ParsedDemand[] */

        $i = 0;
        foreach ($parsedDemands as $parsedDemand) {
            if ($parsedDemand->isLockedForDeleting()) {
                continue;
            }
            $em->remove($parsedDemand->getDemand());
            $em->remove($parsedDemand);
            $i++;
        }

        $em->flush();

        $this->addFlash(
            'sonata_flash_success',
            sprintf('Удалено %d заявок из %d.', $i, count($parsedDemands))
        );

        return new RedirectResponse($this->admin->generateUrl(
            'list',
            array('filter' => $this->admin->getFilterParameters())
        ));
    }
}
