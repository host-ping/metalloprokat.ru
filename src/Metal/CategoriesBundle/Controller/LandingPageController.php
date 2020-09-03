<?php

namespace Metal\CategoriesBundle\Controller;

use Metal\CategoriesBundle\Entity\LandingPage;
use Metal\ProductsBundle\DataFetching\Spec\ProductsLoadingSpec;
use Metal\ProductsBundle\DataFetching\Spec\ProductsOrderingSpec;
use Metal\ProjectBundle\Entity\ValueObject\SourceTypeProvider;
use Metal\TerritorialBundle\Entity\City;
use Metal\TerritorialBundle\Entity\Region;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class LandingPageController extends Controller
{
    /**
     * @ParamConverter("landingPage", class="MetalCategoriesBundle:LandingPage", options={"mapping": {"slug": "slug"}})
     */
    public function landingAction(LandingPage $landingPage, Request $request)
    {
        $country = $request->attributes->get('country');
        $region = $request->attributes->get('region');
        $city = $request->attributes->get('city');

        $countryToLoad = $landingPage->getCountry() ?: $country;
        $regionToLoad = $landingPage->getRegion() ?: $region;
        $cityToLoad = $landingPage->getCity() ?: $city;

        if (!$landingPage->isVisibleEverywhere()) {
            $landingCity = $landingPage->getCity();
            if ((!$landingCity && $city) || ($landingCity && !$city) || ($landingCity && $landingCity->getId() !== $city->getId())) {
                throw $this->createNotFoundException('Открыли не в том городе.');
            }

            $landingRegion = $landingPage->getRegion();
            if ((!$landingRegion && $region) || ($landingRegion && !$region) || ($landingRegion && $landingRegion->getId() !== $region->getId())) {
                throw $this->createNotFoundException('Отклыли не в той области.');
            }

            if ($landingPage->getCountry() && $landingPage->getCountry()->getId() !== $country->getId()) {
                throw $this->createNotFoundException('Отклыли не в той стране.');
            }
        } else {
            $countryToLoad = $country;
            $regionToLoad = $region;
            $cityToLoad = $city;
        }

        $territory = $countryToLoad;
        if ($cityToLoad instanceof City) {
            $territory = $cityToLoad;
        } elseif($regionToLoad instanceof Region) {
            $territory = $regionToLoad;
        }

        $page = $request->query->get('page', 1);
        $perPage = 20;
        $specification = $landingPage->getProductFilteringSpec();
        if ($landingPage->isVisibleEverywhere()) {
            $specification->companyCity($cityToLoad);
        }

        $attributesCollection = $landingPage->getAttributesCollection();

        $loaderOpts = (new ProductsLoadingSpec())
            ->preloadDelivery($territory)
            ->preloadPhones($territory)
            ->trackShowing(SourceTypeProvider::PRODUCTS)
            ->normalizePrice($countryToLoad)
        ;

        $orderBy = new ProductsOrderingSpec();
        $orderBy->payedCompanies();
        $orderBy->random($landingPage->getId());

        $dataFetcher = $this->get('metal.products.data_fetcher');
        $productsEntityLoader = $this->get('metal.products.products_entity_loader');

        $productsPagerfanta = $dataFetcher->getPagerfantaByCriteria($specification, $orderBy, $perPage, $page);

        // косим под простую страницу просмотра товаров в категории
        //TODO: постараться избавиться от этих хаков
        $request->attributes->set('category', $landingPage->getCategory());
        $request->attributes->set('attributes_collection', $attributesCollection);
        $request->attributes->set('tab', 'products');
        $request->query->set('cattr', $landingPage->getCompanyAttributes());
        $request->query->set('q', $landingPage->getSearchQuery());
//        $routeParameters = $request->attributes->get('_route_params');
//        $routeParameters['category_slug'] = $landingPage->getCategory()->getUrl($request->attributes->get('attributes_collection')->getUrl());
//        $request->attributes->set('_route_params', $routeParameters);

        if ($request->isXmlHttpRequest()) {
            return $this->render(
                '@MetalProducts/Products/partial/list_products.html.twig',
                array(
                    'pagerfanta' => $productsEntityLoader->getPagerfantaWithEntities($productsPagerfanta, $loaderOpts),
                    'category' => $landingPage->getCategory(),
                )
            );
        }

        $loadedProductsCount = $productsPagerfanta->getNbResults();

        if ($loadedProductsCount < LandingPage::MIN_PRODUCTS_COUNT) {
            throw $this->createNotFoundException(sprintf('Меньше %d товаров на запрос', LandingPage::MIN_PRODUCTS_COUNT));
        }

        $productsViewModel = $productsEntityLoader->getListResultsViewModel($productsPagerfanta, $loaderOpts);

        return $this->render(
            '@MetalCategories/LandingPages/landing_list.html.twig',
            array(
                'productsViewModel' => $productsViewModel,
                'category' => $landingPage->getCategory(),
                'fallbackProductsViewModel' => null,
                'landingPage' => $landingPage,
            )
        );
    }
}
