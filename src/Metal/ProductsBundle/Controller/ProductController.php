<?php

namespace Metal\ProductsBundle\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Metal\CompaniesBundle\Entity\Company;
use Metal\ProductsBundle\Entity\Product;
use Metal\ProductsBundle\Widget\ProductTabsWidget;
use Metal\ProjectBundle\Entity\ValueObject\SourceTypeProvider;
use Metal\ProjectBundle\Helper\SeoHelper;
use Metal\TerritorialBundle\Entity\City;
use Metal\TerritorialBundle\Entity\Country;
use Metal\TerritorialBundle\Entity\TerritoryInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class ProductController extends Controller
{
    protected $productsPerPage = 20;

    /**
     * @ParamConverter("product", converter="products_converter", class="Metal\ProductsBundle\Entity\Product")
     */
    public function viewAction(Request $request, TerritoryInterface $territory, Product $product, Country $country, City $city = null)
    {
        $tab = $request->query->get('tab');
        $company = $product->getCompany();

        $em = $this->getDoctrine()->getManager();
        /* @var $em EntityManagerInterface */

        $productRepository = $em->getRepository('MetalProductsBundle:Product');
        $enabledProduct = null;
        if ($product->isDeleted() && $product->getCompany()) {
            $enabledProduct = $productRepository->getEnabledDuplicateForProduct($product);
        }

        if ($company) {
            $seoHelper = $this->get('brouzie.helper_factory')->get('MetalProjectBundle:Seo');
            /* @var $seoHelper SeoHelper */

            $cityToRedirect = $seoHelper->getCityForRedirectForProduct($product);
            if ($cityToRedirect && (!$city || $company->getVisibilityStatus() == Company::VISIBILITY_STATUS_NORMAL)) {
                $url = $this->generateUrl(
                    'MetalProductsBundle:Product:view_subdomain',
                    array(
                        'id' => $product->getId(),
                        'subdomain' => $cityToRedirect->getSlugWithFallback()
                    )
                );

                return $this->redirect($url, 301);
            }

            $this->get('brouzie.helper_factory')->get('MetalProductsBundle')->trackProductView($product, SourceTypeProvider::createBySlug('product'));

            $em
                ->getRepository('MetalCompaniesBundle:CompanyCity')
                ->attachCompanyCities(array($company), $territory);

            $em->getRepository('MetalCompaniesBundle:CompanyPhone')
                ->attachPhonesToCompaniesForCurrentTerritory(array($company), $territory);
        }

        $em
            ->getRepository('MetalProductsBundle:Product')
            ->attachNormalizedPrice(array($product), $country);

        if ($request->isXmlHttpRequest()) {
            $availableTabs = array('similar-products-tab');
            $activeTab = 'similar-products-tab';
            if ($tab) {
                $availableTabs = array('category-products-tab');
                $activeTab = 'category-products-tab';
            }

            $productsWidget = $this->get('brouzie_widgets.widget_manager')
                ->createWidget(
                    'MetalProductsBundle:ProductTabs',
                    array(
                        'product' => $product,
                        'city' => $city,
                        'page' => $request->query->get('page', 1),
                        'available_tabs' => $availableTabs,
                        'active_tab' => $activeTab,
                        'per_page' => $this->container->getParameter('project.per_page_for_product_page'),
                    )
                );
            /* @var $productsWidget ProductTabsWidget */

            $products = $productsWidget->getProducts();

            $key = $tab ? 'page.category_products_tab_list' :'page.similar_products_tab_list';
            $title = '';
            if ($company) {
                $title = $this->get('brouzie.helper_factory')->get('MetalProjectBundle:Seo')->getMetaTitleForProductPage($product);
            }

            $response = JsonResponse::create(
                array(
                    'page.title' => $title,
                    $key => $this->renderView(
                        'MetalProductsBundle:Product:products_more.html.twig',
                        array(
                            'pagerfanta' => $tab ? $products['productsByCategory']->pagerfanta : $products['products']->pagerfanta,
                            'city' => $city,
                            'id' => $tab ? 'category-products-tab-more' : 'similar-products-tab-more',
                        )
                    )
                )
            );

            $response->headers->addCacheControlDirective('no-store', true);

            return $response;
        }

        return $this->render(
            'MetalProductsBundle:Product:view.html.twig',
            array(
                'product' => $product,
                'activeTab' => $tab ? 'category-products-tab' : 'similar-products-tab',
                'enabledProduct' => $enabledProduct
            )
        );
    }
}
