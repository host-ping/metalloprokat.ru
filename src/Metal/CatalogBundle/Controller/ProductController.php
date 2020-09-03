<?php

namespace Metal\CatalogBundle\Controller;

use Doctrine\ORM\EntityManager;
use Metal\CatalogBundle\Entity\Product;
use Metal\CatalogBundle\Entity\ProductReview;
use Metal\CatalogBundle\Widget\CatalogProductTabsWidget;
use Metal\CategoriesBundle\Entity\Category;
use Metal\ProjectBundle\Form\ReviewType;
use Metal\TerritorialBundle\Entity\City;
use Metal\TerritorialBundle\Entity\Country;
use Metal\TerritorialBundle\Entity\Region;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class ProductController extends Controller
{
    /**
     * @ParamConverter("product", class="MetalCatalogBundle:Product")
     */
    public function viewAction(Product $product, Request $request, Category $category, City $city = null, Region $region = null, Country $country = null)
    {
        $productCities = $product->getProductCities();
        $productCitiesById = array();
        $productCountriesById = array();
        $productRegionsById = array();
        foreach ($productCities as $productCity) {
            $productCitiesById[$productCity->getCity()->getId()] = $productCity->getCity();
            $productRegionsById[$productCity->getCity()->getRegion()->getId()] = $productCity->getCity()->getRegion();
            $productCountriesById[$productCity->getCity()->getCountry()->getId()] = $productCity->getCity()->getCountry();
        }

        if (
            (($city && !isset($productCitiesById[$city->getId()])) ||
            ($region && !isset($productRegionsById[$region->getId()])) ||
            ($country && !isset($productCountriesById[$country->getId()]))) && reset($productCountriesById)
        ) {
            $url = $this->generateUrl(
                'MetalCatalogBundle:Product:view',
                array(
                    'id' => $product->getId(),
                    'category_slug' => $product->getCategory()->getSlugCombined(),
                    'base_host' => reset($productCountriesById)->getBaseHost()
                )
            );

            return $this->redirect($url, 301);
        }

        if ($category->getId() !== $product->getCategory()->getId()) {
            $url = $this->generateUrl(
                'MetalCatalogBundle:Product:view',
                array(
                    'id' => $product->getId(),
                    'category_slug' => $product->getCategory()->getSlugCombined(),
                    'subdomain' => $city ? $city->getSlugWithFallback() : 'www'
                )
            );

            return $this->redirect($url, 301);
        }

        if ($request->isXmlHttpRequest()) {
            $catalogProductWidget = $this->get('brouzie_widgets.widget_manager')
                ->createWidget(
                    'MetalCatalogBundle:CatalogProductTabs',
                    array(
                        'product' => $product,
                        'page' => $request->query->get('page', 1),
                        'available_tabs' => array('similar-products-tab'),
                        'active_tab' => 'similar-products-tab',
                        'city' => $city,
                        'region' => $region,
                        'country' => $country,
                    )
                );
            /* @var $catalogProductWidget CatalogProductTabsWidget */

            $products = $catalogProductWidget->getProducts();

            $response = JsonResponse::create(
                array(
                    'page.similar_products_tab_list' => $this->renderView(
                        '@MetalCatalog/Product/products_more.html.twig',
                        array(
                            'products' => $products['similarProducts'],
                            'city' => $city,
                            'isPagerfanta' => true,
                            'displayBrand' => true
                        )
                    )
                )
            );

            $response->headers->addCacheControlDirective('no-store', true);

            return $response;
        }

        return $this->render(
            '@MetalCatalog/Product/view.html.twig',
            array(
                'product' => $product
            )
        );
    }

    /**
     * @ParamConverter("product", class="MetalCatalogBundle:Product")
     */
    public function reviewAction(Request $request, Product $product, City $city = null)
    {
        $em = $this->getDoctrine()->getManager();
        /* @var $em EntityManager */

        $user = null;
        if ($this->isGranted('ROLE_USER')) {
            $user = $this->getUser();
        }

        $review = new ProductReview();
        $form = $this->createForm(
            new ReviewType(),
            $review,
            array(
                'is_authenticated' => $user !== null,
                'validation_groups' => array(
                    $user !== null ? 'authenticated' : 'anonymous',
                ),
                'data_class' => ProductReview::class,
            )
        );

        $form->handleRequest($request);

        if (!$form->isValid()) {
            $errors = $this->get('metal.project.form_helper')->getFormErrorMessages($form);

            return JsonResponse::create(array(
                'errors' => $errors,
            ));
        }

        $review->setCity($city);
        $review->setProduct($product);
        $review->setUser($user);
        $review->setCategory($product->getCategory());

        $em->persist($review);
        $em->flush();

        return JsonResponse::create(array(
            'status' => 'success',
        ));
    }
}
