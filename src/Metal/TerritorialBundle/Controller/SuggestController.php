<?php

namespace Metal\TerritorialBundle\Controller;

use Doctrine\ORM\EntityManager;
use Metal\TerritorialBundle\Entity\Country;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class SuggestController extends Controller
{
    /**
     * @Cache(maxage=86400, smaxage=86400, expires="tomorrow", public=true)
     */
    public function getCitiesAction(Request $request, Country $country, $use_country = true, $filter_cities = true)
    {
        $q = $request->query->get('q');
        $countryId = $request->query->get('country_id');
        $em = $this->getDoctrine()->getManager();
        $cityRepository = $em->getRepository('MetalTerritorialBundle:City');
        $qb = $cityRepository->createQueryBuilder('c');
        $qb->andWhere('LOWER(c.title) LIKE :q')
            ->setParameter('q', '%'.mb_strtolower($q).'%');

        $qb
            ->join('c.region', 'r')
            ->select('c.id as id, c.title as title, r.title as parent_title');

        if ($filter_cities) {
            if ($countryId) {
                $qb->andWhere('c.country = :country')
                    ->setParameter('country', $countryId);
            } elseif ($use_country) {
                $qb->andWhere('c.country = :country')
                    ->setParameter('country', $country);
            } else {
                $qb->andWhere('c.country IN (:countriesIds)')
                    ->setParameter('countriesIds', Country::getEnabledCountriesIds());
            }
        }

        $cities = $qb->addOrderBy('c.title', 'ASC')
                     ->getQuery()
                     ->getArrayResult();

        return JsonResponse::create($cities);
    }

    /**
     * @Cache(maxage=86400, smaxage=86400, expires="tomorrow", public=true)
     */
    public function getRegionsAction(Request $request, Country $country, $use_country = true, $filter_regions = true)
    {
        $q = $request->query->get('q');
        $countryId = $request->query->get('country_id');
        $em = $this->getDoctrine()->getManager();

        $qb = $em->getRepository('MetalTerritorialBundle:Region')
            ->createQueryBuilder('r')
            ->andWhere('LOWER(r.title) LIKE :q')
            ->setParameter('q', '%'.mb_strtolower($q).'%')
            ->select('r.id AS id, r.title AS title');

        if ($filter_regions) {
            if ($countryId) {
                $qb->andWhere('r.country = :country')
                    ->setParameter('country', $countryId);
            } elseif ($use_country) {
                $qb->andWhere('r.country = :country')
                    ->setParameter('country', $country);
            } else {
                $qb->andWhere('r.country IN (:countriesIds)')
                    ->setParameter('countriesIds', Country::getEnabledCountriesIds());
            }
        }

        $regions = $qb->addOrderBy('r.title', 'ASC')
            ->getQuery()
            ->getArrayResult();

        return JsonResponse::create($regions);
    }


    /**
     * @Cache(maxage=86400, smaxage=86400, expires="tomorrow", public=true)
     */
    public function getTerritoriesByLevelsAction(Country $country)
    {
        $em = $this->getDoctrine()->getManager();
        /* @var $em EntityManager */

        $territories = $em->getRepository('MetalTerritorialBundle:TerritorialStructure')->createQueryBuilder('ts')
            ->select('ts.id')
            ->addSelect('ts.parentId')
            ->addSelect('ts.title')
            ->andWhere('ts.country = :country')
            ->setParameter('country', $country)
            ->getQuery()
            ->getResult();

        $territoriesHierarchy = array();
        foreach ($territories as $territory) {
            $territoriesHierarchy[(int)$territory['parentId']][] = $territory;
        }

        $territories = array();
        $callback = function($items, $depth = 1) use (&$callback, &$territories, $territoriesHierarchy) {
            foreach ($items as $item) {
                $item['depth'] = $depth;
                $territories[] = $item;
                if (isset($territoriesHierarchy[$item['id']])) {
                    $callback($territoriesHierarchy[$item['id']], $depth + 1);
                }
            }
        };
        $callback($territoriesHierarchy[0]);

        return JsonResponse::create($territories);
    }
}
