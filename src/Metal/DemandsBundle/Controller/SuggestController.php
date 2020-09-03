<?php

namespace Metal\DemandsBundle\Controller;

use Metal\DemandsBundle\DataFetching\Spec\DemandFilteringSpec;
use Metal\DemandsBundle\Entity\Demand;
use Metal\DemandsBundle\Helper\DefaultHelper;
use Metal\TerritorialBundle\Entity\City;
use Metal\TerritorialBundle\Entity\Region;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class SuggestController extends Controller
{
    public function searchSuggestAction(Request $request, City $city = null, Region $region = null)
    {
        $query = $request->query->get('query');
        $searchTerritory = $request->query->get('searchTerritory');
        $em = $this->get('doctrine.orm.default_entity_manager');
        $country = $request->attributes->get('country');

        $responseItems = array();
        $categoriesDetected = array();
        $demand = null;

        $demandsHelper = $this->container->get('brouzie.helper_factory')->get('MetalDemandsBundle');
        /* @var $demandsHelper DefaultHelper */

        $territoryDomain = 'www';
        $criteria = new DemandFilteringSpec();
        if ($searchTerritory !== 'null') {
            list($kind, $territoryId) = explode('-', $searchTerritory);
            if ($kind === 'city') {
                $territory = $em->getRepository('MetalTerritorialBundle:City')->find($territoryId);
                if ($territory) {
                    $territoryDomain = $territory->getSlugWithFallback();
                    $criteria->city($territory);
                }
            } elseif ($kind === 'region') {
                $territory = $em->getRepository('MetalTerritorialBundle:Region')->find($territoryId);
                if ($territory) {
                    $territoryDomain = $territory->getSlug();
                    $criteria->region($territory);
                }
            }
        } elseif ($city) {
            $territoryDomain = $city->getSlugWithFallback();
            $criteria->city($city);
        } elseif ($region) {
            $territoryDomain = $region->getSlug();
            $criteria->region($region);
        }

        if (null === $criteria->cityId && null === $criteria->regionId) {
            $criteria->country($country);
        }

        if ($id = Demand::extractDemandNumberFromSearchString($query)) {
            $demand = $em->getRepository('MetalDemandsBundle:Demand')->find($id);

            if ($demand && $demand->getCategory() && $demand->isModerated() && !$demand->isDeleted()) {
                /** @var Demand $demand */
                $cityTitle = $demand->getCity() ? $demand->getCity()->getTitle() : '';
                $responseItems[] = array(
                    'type' => 'demand',
                    'title' => '№'.$demand->getId().' '.$cityTitle,
                    'url' => $demandsHelper->generateDemandUrl($demand),
                );
            }
        }

        // search by matching service
        $categoryDetector = $this->get('metal.categories.category_matcher');
        $category = $categoryDetector->getCategoryByTitle($query);

        if ($category && $category->getId() == $categoryDetector::DEFAULT_CATEGORY_ID) {
            $category = null;
        }

        if ($category) {
            $categoriesDetected[$category->getId()] = $category;

            $responseItems[] = array(
                'type' => 'category',
                'title' => $category->getTitle(),
                'url' => $this->generateUrl(
                    'MetalDemandsBundle:Demands:list_subdomain_category',
                    array('category_slug' => $category->getSlugCombined(), 'subdomain' => $territoryDomain)
                ),
            );
        }

        $sphinxy = $this->get('sphinxy.default_connection');
        $match = $sphinxy->getEscaper()->escapeMatch($query);

        $categories = $sphinxy->createQueryBuilder()
            ->select('id, category_title, slug_combined')
            ->from('categories')
            ->andWhere('MATCH (:match_title)')
            ->setParameter('match_title', " $match ") //FIXME: Добавить тип параметра, сейчас строку "2000" подставляет как число и валится с 500й
            ->getResult()
        ;

        foreach ($categories as $matchCategory) {
            if (isset($categoriesDetected[$matchCategory['id']])) {
                continue;
            }

            $responseItems[] = array(
                'type' => 'category',
                'title' => $matchCategory['category_title'],
                'url' => $this->generateUrl(
                    'MetalDemandsBundle:Demands:list_subdomain_category',
                    array('category_slug' => $matchCategory['slug_combined'], 'subdomain' => $territoryDomain)
                ),
            );
        }

        $fulltextSearchEnabled = true;
        if (!$category && $fulltextSearchEnabled) {
            $criteria->matchTitle($query);
            $demandsDataFetcher = $this->get('metal.demands.data_fetcher');
            $count = $demandsDataFetcher->getItemsCountByCriteria($criteria);

            if ($count) {
                $routeParameters = array(
                    'subdomain' => $territoryDomain,
                    'q' => $query
                );

                $responseItems[] = array(
                    'title' => $this->container->get('translator')
                        ->transChoice('demands_by_request', $count, array(), 'MetalDemandsBundle'),
                    'url' => $this->generateUrl('MetalDemandsBundle:Demands:list_subdomain', $routeParameters),
                );
            }
        }

        return JsonResponse::create(array_slice($responseItems, 0, 10));
    }
}
