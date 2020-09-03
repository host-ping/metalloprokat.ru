<?php

namespace Metal\CatalogBundle\Controller;

use Metal\CatalogBundle\DataFetching\Spec\CatalogProductFilteringSpec;
use Metal\CatalogBundle\DataFetching\Spec\CatalogProductOrderingSpec;
use Metal\CatalogBundle\DataFetching\Spec\CatalogProductsLoadingSpec;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class BrandsController extends Controller
{
    public function listAction(Request $request)
    {
        $page = $request->query->get('page', 1);
        $perPage = 20;

        $catalogDataFetcher = $this->get('metal.catalog.data_fetcher');
        $catalogEntityLoader = $this->get('metal.catalog.brand.entity_loader');

        $specification = CatalogProductFilteringSpec::createFromRequest($request)
            ->loadBrands(true);

        $loaderOpts = new CatalogProductsLoadingSpec();
        $orderBy = new CatalogProductOrderingSpec();

        $pagerfanta = $catalogDataFetcher->getPagerfantaByCriteria($specification, $orderBy, $perPage, $page);
        $pagerfanta = $catalogEntityLoader->getPagerfantaWithEntities($pagerfanta, $loaderOpts);

        if ($request->isXmlHttpRequest()) {

            $response = JsonResponse::create(
                array(
                    'page.title' => $this->get('brouzie.helper_factory')->get('MetalCatalogBundle:Seo')->getMetaTitleForBrandsPage(),
                    'page.brands_list' => $this->renderView(
                        '@MetalCatalog/partial/brand_in_brands.html.twig',
                        array(
                            'pagerfanta' => $pagerfanta,
                        )
                    )
                )
            );

            $response->headers->addCacheControlDirective('no-store', true);

            return $response;
        }

        return $this->render(
            '@MetalCatalog/Brands/list.html.twig',
            array(
                'pagerfanta' => $pagerfanta,
            )
        );
    }
}
