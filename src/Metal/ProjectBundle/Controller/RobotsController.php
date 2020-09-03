<?php

namespace Metal\ProjectBundle\Controller;

use Brouzie\Bundle\HelpersBundle\Twig;
use Metal\CompaniesBundle\Entity\Company;
use Metal\ProductsBundle\DataFetching\Spec\ProductsFilteringSpec;
use Metal\ProjectBundle\DataFetching\DataFetcher;
use Metal\ProjectBundle\Helper\UrlHelper;
use Metal\TerritorialBundle\Entity\City;
use Metal\TerritorialBundle\Entity\Country;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class RobotsController extends Controller
{
    public function generateAction(string $section, Request $request, Country $country)
    {
        $templates = [
            'default' => '@MetalProject/Robots/robots.txt.twig',
            'noindex' => '@MetalProject/Robots/noindex.txt.twig',

            'metalspros' => '@MetalProject/Robots/metalspros.txt.twig',
            '8_800' => '@MetalProject/Robots/8_800.txt.twig',

            'metalloprokat' => '@MetalProject/Robots/metalloprokat.txt.twig',
            'product' => '@MetalProject/Robots/product.txt.twig',
            'stroy' => '@MetalProject/Robots/stroy.txt.twig',

            'minisite' => '@MetalProject/Robots/minisite.txt.twig',
        ];

        $isHttpsAbailable = $country->getSecure();
        if ($request->attributes->get('company_on_subdomain')) {
            /** @var Company $company */
            $company = $request->attributes->get('company');
            $isHttpsAbailable = $company->getPackageChecker()->isHttpsAvailable();
            $section = 'minisite';
            if (!$this->getCompanyProductsCount($company) || !$company->getIndexMinisite()) {
                $section = 'noindex';
            }
        }

        if ($request->attributes->get('region')) {
            $section = 'noindex';
        }

        $helperFactory = $this->get('brouzie.helper_factory');
        /** @var UrlHelper $urlHelper */
        $urlHelper = $helperFactory->get('MetalProjectBundle:Url');

        $data = [
            'base_url' => $urlHelper->fixSecureScheme($request->getUriForPath('/'), $isHttpsAbailable),
            'sitemap_url' => $urlHelper->fixSecureScheme($request->getUriForPath('/sitemap.xml'), $isHttpsAbailable),
        ];

        /** @var City $city */
        $city = $request->attributes->get('city');
        if ($city && $city->getRobotsText()) {
            $content = $this->container->get('twig')->createTemplate($city->getRobotsText())->render($data);
        } else {
            $content = $this->renderView($templates[$section] ?? $templates['default'], $data);
        }

        return new Response($content, Response::HTTP_OK, ['Content-Type' => 'text/plain']);
    }

    private function getCompanyProductsCount(Company $company)
    {
        $dataFetcher = $this->container->get('metal.products.data_fetcher');

        $specification = (new ProductsFilteringSpec())->company($company);

        return $dataFetcher->getItemsCountByCriteria($specification, DataFetcher::TTL_INFINITY);
    }
}
