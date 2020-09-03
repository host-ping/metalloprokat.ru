<?php

namespace Metal\ProjectBundle\Controller;

use Metal\ProjectBundle\Entity\LandingTemplate;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class LandingController extends Controller
{
    /**
     * @ParamConverter("landingTemplate", class="MetalProjectBundle:LandingTemplate", options={"id" = "category_id"}, isOptional=true)
     */
    public function viewAction(LandingTemplate $landingTemplate = null, $simple = false)
    {
        if ($simple) {
            return $this->redirect(
                $this->generateUrl('MetalUsersBundle:Registration:register'));
        }

        return $this->redirect(
            $this->generateUrl('MetalUsersBundle:Registration:registerFirstStep', array(
                'category_id' => $landingTemplate->getCategory()->getId()
            ))
        );
    }
}
