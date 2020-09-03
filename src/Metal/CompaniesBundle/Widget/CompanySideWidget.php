<?php

namespace Metal\CompaniesBundle\Widget;

use Brouzie\Bundle\WidgetsBundle\Widget\WidgetAbstract;
use Doctrine\ORM\EntityManager;
use Metal\CategoriesBundle\Entity\Category;
use Metal\CompaniesBundle\Entity\Company;
use Metal\ProductsBundle\DataFetching\Elastica\ProductIndex;
use Metal\ProductsBundle\DataFetching\Spec\ProductsFilteringSpec;
use Metal\ProjectBundle\DataFetching\DataFetcher;
use Metal\TerritorialBundle\Entity\City;

class CompanySideWidget extends WidgetAbstract
{
    protected function setDefaultOptions()
    {
        parent::setDefaultOptions();

        $this->optionsResolver
            ->setDefaults(
                [
                    'company' => null,
                    'reviews_count' => 3,
                    'category' => null,
                    'hide_category_link' => false,
                    'hide_all_product_link' => false,
                    'product_external_url' => '',
                ]
            )
            ->setRequired(['company', 'data_for_stats']);
    }

    public function getParametersToRender()
    {
        $company = $this->options['company'];
        /* @var $company Company */
        $category = $this->options['category'];
        /* @var $category Category */
        //TODO: city as option
        $currentCity = $this->getRequest()->attributes->get('city');
        /* @var $currentCity City */
        $contactInfo = $company->getContactInfo();
        $em = $this->getDoctrine();
        /* @var $em EntityManager */

        //TODO: use single query with agg by categories + count agg instead of getItemsCountByCriteria
        $dataFetcher = $this->container->get('metal_products.data_fetcher_factory')
            ->getDataFetcher(ProductIndex::SCOPE);

        $specification = (new ProductsFilteringSpec())
            ->company($company);

        $productsCountByCompany = $dataFetcher->getItemsCountByCriteria($specification, DataFetcher::TTL_INFINITY);

        $productsInCategoryCount = null;
        if ($category && !$this->options['hide_category_link']) {
            $specification
                ->category($category)
                ->city($contactInfo->getCity());
            $productsInCategoryCount = $dataFetcher->getItemsCountByCriteria($specification, DataFetcher::TTL_INFINITY);
        }

        $companyReviews = $em->getRepository('MetalCompaniesBundle:CompanyReview')
            ->getCompanyReviewBySpecification(
                array('company' => $company),
                array('created_at' => 'DESC'),
                $this->options['reviews_count']
            );

        $favoriteCompany = null;
        if ($this->isGranted('ROLE_USER')) {
            $user = $this->getUser();
            $favoriteCompany = $em->getRepository('MetalUsersBundle:FavoriteCompany')
                ->findOneBy(array('user' => $user, 'company' => $company));
        }

        return compact('company', 'companyReviews', 'currentCity', 'favoriteCompany', 'productsInCategoryCount', 'productsCountByCompany', 'contactInfo');
    }
}
