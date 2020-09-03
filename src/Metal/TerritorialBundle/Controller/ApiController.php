<?php

namespace Metal\TerritorialBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Symfony\Component\HttpFoundation\JsonResponse;

class ApiController extends Controller
{
    /**
     * @Cache(maxage=86400, smaxage=86400, expires="tomorrow", public=true)
     */
    public function getRegionsAction()
    {
        return JsonResponse::create(
            $this->getDoctrine()->getManager()->getConnection()->fetchAll('SELECT * FROM Classificator_Regions ORDER BY Regions_ID')
        );
    }

    /**
     * @Cache(maxage=86400, smaxage=86400, expires="tomorrow", public=true)
     */
    public function getCitiesAction()
    {
        return JsonResponse::create(
            $this->getDoctrine()->getManager()->getConnection()->fetchAll('SELECT * FROM Classificator_Region ORDER BY Region_ID')
        );
    }
}
