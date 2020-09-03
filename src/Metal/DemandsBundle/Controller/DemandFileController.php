<?php

namespace Metal\DemandsBundle\Controller;

use Metal\DemandsBundle\Entity\DemandFile;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DemandFileController extends Controller
{
    /**
     * @ParamConverter("demandFile", class="MetalDemandsBundle:DemandFile")
     * @Security("has_role('ROLE_ALLOWED_VIEW_DEMAND_CONTACTS') and demandFile.getDemand().getDisplayFileOnSite()")
     */
    public function downloadFileAction(DemandFile $demandFile)
    {
        return $this->createResponse($demandFile);
    }

    /**
     * @ParamConverter("demandFile", class="MetalDemandsBundle:DemandFile")
     */
    public function downloadFileFromCommandAction(DemandFile $demandFile)
    {
        return $this->createResponse($demandFile);
    }

    /**
     * @ParamConverter("demandFile", class="MetalDemandsBundle:DemandFile")
     * @Security("has_role('ROLE_USER') and (demandFile.getDemand().getUser().getId() == user.getId()) and demandFile.getFile().getName()")
     */
    public function downloadFileFromArchiveAction(DemandFile $demandFile)
    {
        return $this->createResponse($demandFile);
    }

    /**
     * @ParamConverter("demandFile", class="MetalDemandsBundle:DemandFile")
     * @Security("has_role('ROLE_SUPPLIER') and (demandFile.getDemand().getCompany().getId() == user.getCompany().getId()) and demandFile.getFile().getName()")
     */
    public function downloadFileFromPrivateAction(DemandFile $demandFile)
    {
        return $this->createResponse($demandFile);
    }

    /**
     * @ParamConverter("demandFile", class="MetalDemandsBundle:DemandFile", options={"id": "file_id"})
     */
    public function downloadFileFromAdminAction(DemandFile $demandFile)
    {
        if (!$this->get('metal.demands.admin.demand')->isGranted('EDIT')) {
            throw $this->createAccessDeniedException();
        }

        return $this->createResponse($demandFile);
    }

    private function createResponse(DemandFile $demandFile)
    {
        return $this->get('vich_uploader.download_handler')
            ->downloadObject($demandFile, 'uploadedFile', null, true);
    }
}
