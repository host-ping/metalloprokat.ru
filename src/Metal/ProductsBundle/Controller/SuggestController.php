<?php

namespace Metal\ProductsBundle\Controller;

use Doctrine\ORM\EntityManager;
use Metal\CategoriesBundle\Helper\DefaultHelper;
use Metal\ProductsBundle\DataFetching\Spec\ProductsFilteringSpec;
use Metal\StatisticBundle\Entity\StatsSearch;
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
        $em = $this->getDoctrine()->getManager();
        /* @var $em EntityManager */

        $specification = new ProductsFilteringSpec();
        $territoryDomain = 'www';
        if ($searchTerritory != 'null') {
            list($kind, $territoryId) = explode('-', $searchTerritory);
            if ($kind == 'city') {
                $territory = $em->getRepository('MetalTerritorialBundle:City')->find($territoryId);
                if ($territory) {
                    $territoryDomain = $territory->getSlugWithFallback();
                    $specification->city($territory);
                }
            } elseif ($kind == 'region') {
                $territory = $em->getRepository('MetalTerritorialBundle:Region')->find($territoryId);
                if ($territory) {
                    $territoryDomain = $territory->getSlug();
                    $specification->region($territory);
                }
            }
        } elseif ($city) {
            $territoryDomain = $city->getSlugWithFallback();
            $specification->city($city);
        } elseif ($region) {
            $territoryDomain = $region->getSlug();
            $specification->region($region);
        }

        if (!$specification->regionId && !$specification->cityId) {
            $specification->country($request->attributes->get('country'));
        }

        $categoriesHelper = $this->get('brouzie.helper_factory')->get('MetalCategoriesBundle');
        /* @var $categoriesHelper DefaultHelper */
        $searchSuggestRoute = $this->container->getParameter('project.search_suggest_route');
        $responseItems = array();
        $categoriesDetected = array();

        // search by matching service
        $categoryDetector = $this->get('metal.categories.category_matcher');
        $category = $categoryDetector->getCategoryByTitle($query);
        if ($category && $category->getId() == $categoryDetector::DEFAULT_CATEGORY_ID) {
            $category = null;
        }

        $sphinxy = $this->get('sphinxy.default_connection');
        $match = $sphinxy->getEscaper()->escapeMatch($query);

        if (!$category) {
            $productAttributes = $sphinxy->createQueryBuilder()
                ->select('title, slug, category_title, category_slug_combined')
                ->from('product_attributes')
                ->andWhere('MATCH (:match_title)')
                ->setParameter('match_title', " $match ")
                ->orderBy('title', 'ASC')
                ->getResult()
            ;

            $fulltextSearchEnabled = true;
            if (!count($productAttributes) && $fulltextSearchEnabled) {
                $specification->matchTitle($query);

                $dataFetcher = $this->get('metal.products.data_fetcher');
                $productsCount = $dataFetcher->getItemsCountByCriteria($specification);

                $specification
                    ->loadCompanies(true)
                    ->allowVirtual(true)
                ;

                $companiesCount = $dataFetcher->getItemsCountByCriteria($specification);

                $routeParameters = array(
                    'subdomain' => $territoryDomain,
                    'q' => $query
                );

                $translator = $this->container->get('translator');

                if ($productsCount) {
                    $responseItems[] = array(
                        'title' => $productsCount.' '.$translator->transChoice('products_by_count', $productsCount, array(), 'MetalProductsBundle').' '.'по запросу',
                        'url' => $this->generateUrl(
                            'MetalStatisticBundle:Default:redirectStats',
                            array(
                                'kind' => StatsSearch::SEARCH_BY_BUTTON,
                                'url'=>$this->generateUrl('MetalProductsBundle:Products:products_list', $routeParameters)
                            )
                        ),
                    );
                }

                if ($companiesCount) {
                    $responseItems[] = array(
                        'title' => $companiesCount.' '.$translator->transChoice('from_companies_by_count', $companiesCount, array(), 'MetalCompaniesBundle').' '.'по запросу',
                        'url' => $this->generateUrl(
                            'MetalStatisticBundle:Default:redirectStats',
                            array(
                                'kind' => StatsSearch::SEARCH_BY_BUTTON,
                                'url' => $this->generateUrl('MetalProductsBundle:Products:companies_list', $routeParameters)
                            )
                        )
                    );
                }
            }

            foreach ($productAttributes as $attr) {
                $responseItems[] = array(
                    'type' => 'category',
                    'title' => $attr['category_title'].' '. $attr['title'],
                    'url' => $this->generateUrl(
                        'MetalStatisticBundle:Default:redirectStats',
                        array(
                            'kind' => StatsSearch::SEARCH_BY_SUGGEST,
                            'url' => $this->generateUrl(
                                $searchSuggestRoute,
                                array(
                                    'category_slug' => implode('/', array_filter(array($attr['category_slug_combined'], $attr['slug']))),
                                    'subdomain' => $territoryDomain)
                            )
                        )
                    )

                );
            }
        }

        if ($category) {
            $categoriesDetected[$category->getId()] = $category;
            $attrs = $this->getDoctrine()->getRepository('MetalProductsBundle:ProductParameterValue')->matchAttributesForTitle($category->getId(), $query);

            if ($attrs) {
                //TODO: use more efficient loading (or even do not load again)
                $attributesCollection = $this->getDoctrine()->getRepository('MetalAttributesBundle:AttributeValue')
                    ->loadCollectionBySlugs($category, array_keys($attrs));

                foreach ($attributesCollection->getAttributesValues() as $attributeValue) {
                    $responseItems[] = array(
                        'type' => 'category',
                        'title' => $category->getTitle().' '.$attributeValue->getValue(),
                        'url' => $this->generateUrl(
                            'MetalStatisticBundle:Default:redirectStats',
                            array(
                                'kind' => StatsSearch::SEARCH_BY_SUGGEST,
                                'url' => $this->generateUrl(
                                    $searchSuggestRoute,
                                    array(
                                        'category_slug' => $category->getUrl($attributeValue->getSlug()),
                                        'subdomain' => $territoryDomain,
                                    )
                                ),
                            )
                        ),
                    );
                }
            }

            $responseItems[] = array(
                'type' => 'category',
                'title' => $category->getTitle(),
                'url' => $this->generateUrl(
                    'MetalStatisticBundle:Default:redirectStats',
                    array(
                        'kind' => StatsSearch::SEARCH_BY_SUGGEST,
                        'url' => $this->generateUrl($searchSuggestRoute,
                            array(
                                'category_slug' => $category->getSlugCombined(),
                                'subdomain' => $territoryDomain
                            )
                        )
                    )
                )
            );
        }

        $categories = $sphinxy->createQueryBuilder()
            ->select('id, category_title, slug_combined')
            ->from('categories')
            ->andWhere('MATCH (:match_title)')
            ->setParameter('match_title', " $match ") //FIXME: Добавить тип параметра, сейчас строку "2000" подставляет как число и валится с 500й
            ->getResult()
        ;

        foreach ($categories as $category) {
            if (isset($categoriesDetected[$category['id']])) {
                continue;
            }
            $responseItems[] = array(
                'type' => 'category',
                'title' => $category['category_title'],
                'url' => $this->generateUrl(
                    'MetalStatisticBundle:Default:redirectStats',
                    array(
                        'kind' => StatsSearch::SEARCH_BY_SUGGEST,
                        'url' => $this->generateUrl(
                            $searchSuggestRoute,
                            array(
                                'category_slug' => $category['slug_combined'],
                                'subdomain' => $territoryDomain
                            )
                        )
                    )
                )
            );
        }

        if (!$categoriesDetected) {
            // search by companies
        }

        return JsonResponse::create(array_slice($responseItems, 0, 10));
    }
}
