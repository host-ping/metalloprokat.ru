<?php

namespace Metal\CatalogBundle\Controller;
use Metal\CatalogBundle\DataFetching\Spec\CatalogProductFilteringSpec;
use Metal\CatalogBundle\DataFetching\Spec\CatalogProductOrderingSpec;
use Metal\CatalogBundle\DataFetching\Spec\CatalogProductsLoadingSpec;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class ProductsController extends Controller
{
    public function listAction(Request $request)
    {
        $page = $request->query->get('page', 1);
        $perPage = 20;

        $specification = CatalogProductFilteringSpec::createFromRequest($request)
            ->loadProductsCountPerBrand(true);

        $loaderOpts = (new CatalogProductsLoadingSpec())->loadProductsCountPerBrand(true);
        $orderBy = (new CatalogProductOrderingSpec())->iterateByBrand();

        $catalogDataFetcher = $this->get('metal.catalog.data_fetcher');
        $catalogEntityLoader = $this->get('metal.catalog.entity_loader');

        $pagerfanta = $catalogDataFetcher->getPagerfantaByCriteria($specification, $orderBy, $perPage, $page);
        $pagerfanta = $catalogEntityLoader->getPagerfantaWithEntities($pagerfanta, $loaderOpts);

        if ($request->isXmlHttpRequest()) {
            $response = JsonResponse::create(
                array(
                    'page.title' => $this->get('brouzie.helper_factory')->get('MetalCatalogBundle:Seo')->getMetaTitleForProductsPage(),
                    'page.catalog_products_list' => $this->renderView(
                        '@MetalCatalog/partial/product_in_products.html.twig',
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
            '@MetalCatalog/Products/list.html.twig',
            array(
                'pagerfanta' => $pagerfanta,
            )
        );
    }
}
