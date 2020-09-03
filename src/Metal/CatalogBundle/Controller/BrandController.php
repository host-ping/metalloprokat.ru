<?php

namespace Metal\CatalogBundle\Controller;

use Metal\CatalogBundle\Entity\Brand;
use Metal\TerritorialBundle\Entity\City;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class BrandController extends Controller
{
    private static $perPage = 20;

    /**
     * @ParamConverter("brand", class="MetalCatalogBundle:Brand",
     * options={
     *     "repository_method" = "loadBrandBySlug",
     *     "map_method_signature" = true
     * })
     */
    public function viewAction(Request $request, Brand $brand, City $city = null)
    {
        $productsQb = $this->getDoctrine()
            ->getRepository('MetalCatalogBundle:Product')
            ->getProductsByBrandQb($brand, null, $city);

        $pagerfanta = (new Pagerfanta(new DoctrineORMAdapter($productsQb, false)))
            ->setMaxPerPage(static::$perPage)
            ->setCurrentPage($request->query->get('page', 1));

        if ($request->isXmlHttpRequest()) {
            $response = JsonResponse::create(
                array(
                    'page.similar_products_tab_list' => $this->renderView(
                        '@MetalCatalog/partial/products_in_brand.html.twig',
                        array(
                            'pagerfanta' => $pagerfanta,
                            'subdomain' => $city ? $city->getSlugWithFallback() : 'www',
                        )
                    )
                )
            );

            $response->headers->addCacheControlDirective('no-store', true);

            return $response;
        }

        return $this->render(
            '@MetalCatalog/Brand/view.html.twig',
            array(
                'brand' => $brand,
                'pagerfanta' => $pagerfanta
            )
        );
    }
}
