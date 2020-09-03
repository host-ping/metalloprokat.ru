<?php

namespace Metal\CompaniesBundle\Widget;

use Brouzie\Bundle\WidgetsBundle\Widget\WidgetAbstract;
use Metal\CategoriesBundle\Entity\Category;
use Metal\CategoriesBundle\Entity\CategoryAbstract;
use Metal\CompaniesBundle\Entity\Company;
use Metal\CompaniesBundle\Entity\CompanyCategory;
use Metal\ProductsBundle\DataFetching\Spec\ProductsFacetSpec;
use Metal\ProductsBundle\DataFetching\Spec\ProductsFilteringSpec;
use Metal\ProjectBundle\DataFetching\DataFetcher;
use Metal\ProjectBundle\DataFetching\Sphinxy\FacetResultExtractor;

class CompanyCategoriesWidget extends WidgetAbstract
{
    protected $companyCategories;

    public function setDefaultOptions()
    {
        parent::setDefaultOptions();

        $this->optionsResolver
            ->setDefined(array('company', 'category'))
            ->setAllowedTypes('company', array(Company::class))
            ->setAllowedTypes('category', array(CategoryAbstract::class, 'null'))
        ;
    }

    public function getParametersToRender()
    {
        $route = 'MetalMiniSiteBundle:MiniSite:products_category';
        $company = $this->options['company'];
        /* @var $company Company */

        $minisiteConfig = $company->getMinisiteConfig();
        if ($minisiteConfig->getHasCustomCategory() && $company->getPackageChecker()->isCustomCategoriesAllowed()) {
            $route = 'MetalMiniSiteBundle:MiniSite:products_custom_category';
        }

        $showHotOfferMenuItem = $company->getPackageChecker()->getShowHotOfferMenuItem();
        $isModerator = $this->container
            ->get('security.authorization_checker')
            ->isGranted('COMPANY_MODERATOR', $company);

        $count = 0;
        if ($showHotOfferMenuItem) {
            $hotOfferSpec = $specification = (new ProductsFilteringSpec())
                ->isHotOffer(true)
                ->company($company)
                ->showOnPortal(null);

            $count = $this->container->get('metal.products.data_fetcher')
                ->getItemsCountByCriteria($hotOfferSpec, DataFetcher::TTL_INFINITY);
        }

        $hotOfferProductsCount = null;
        if ($isModerator || ($count && $showHotOfferMenuItem)) {
            $hotOfferProductsCount = $count;
        }

        return array(
            'companyCategories' => $this->getCompanyCategories(),
            'route' => $route,
            'hotOfferProductsCount' => $hotOfferProductsCount,
        );
    }

    public function hasCategoriesToRender()
    {
        return count($this->getCompanyCategories());
    }

    protected function getCompanyCategories()
    {
        if (null !== $this->companyCategories) {
            return $this->companyCategories;
        }

        $company = $this->options['company'];
        /* @var $company Company */

        $specification = new ProductsFilteringSpec();
        $specification
            ->company($company)
            ->showOnPortal(null);

        $facetSpecification = new ProductsFacetSpec();

        $minisiteConfig = $company->getMinisiteConfig();
        if ($minisiteConfig->getHasCustomCategory() && $company->getPackageChecker()->isCustomCategoriesAllowed()) {
            $facetSpecification->facetByCustomCategories($specification);
            $column = ProductsFacetSpec::COLUMN_CUSTOM_CATEGORIES_IDS;
        } else {
            $facetSpecification->facetByCategories($specification);
            $column = ProductsFacetSpec::COLUMN_CATEGORIES_IDS;
        }

        $dataFetcher = $this->container->get('metal.products.data_fetcher');
        $facetsResultSet = $dataFetcher
            ->getFacetedResultSetByCriteria($specification, $facetSpecification, DataFetcher::TTL_INFINITY);

        $facetResultExtractor = new FacetResultExtractor($facetsResultSet, $column);
        $productsCountPerCategory = $facetResultExtractor->getCounts();
        $customCompanyCategoryRepository = $this->getDoctrine()->getRepository('MetalCompaniesBundle:CustomCompanyCategory');

        if ($minisiteConfig->getHasCustomCategory() && $company->getPackageChecker()->isCustomCategoriesAllowed()) {
            $categories = $customCompanyCategoryRepository
                ->findBy(array('id' =>$facetResultExtractor->getIds()), array('displayPosition' => 'ASC'));
        } else {
            $categories = $this->getCategories($company, $facetResultExtractor->getIds());
        }
        /* @var $categories CategoryAbstract[] */

        $categoriesToParent = array();
        $parentsCategories = array();
        foreach ($categories as $category) {
            $category->setAttribute('company_counter', $productsCountPerCategory[$category->getId()]);
            // проставляем родительским категориям пустой массив а не массив категорий, что бы в детях не выводился родитель.
            $parentsCategories[$category->getSuperParent()->getId()] = true;
            if ($category->getParent()) {
                $categoriesToParent[$category->getSuperParent()->getId()][] = $category;
            }
        }

        if ($minisiteConfig->getHasCustomCategory() && $company->getPackageChecker()->isCustomCategoriesAllowed()) {
            $parentCategories = $customCompanyCategoryRepository
                ->findBy(array('id' =>array_keys($parentsCategories)), array('displayPosition' => 'ASC'));
        } else {
            $parentCategories = $this->getCategories($company, array_keys($parentsCategories));

            $virtualCategoriesIds = array();
            foreach ($parentCategories as $parentCategory) {
                if ($parentCategory->getVirtual()) {
                    $virtualCategoriesIds[$parentCategory->getId()] = true;
                }
            }

            $virtualCategories = $this->getCategories($company, array_keys($virtualCategoriesIds), 'virtualParentsIds');

            foreach ($virtualCategories as $virtualCategory) {
                if (!$virtualCategory->getAttribute('company_counter')) {
                    continue;
                }

                $categoriesToParent[(int)$virtualCategory->getVirtualParentsIds()][] = $virtualCategory;
            }
        }

        /* @var $parentCategories CategoryAbstract[] */
        foreach ($parentCategories as $parentCategory) {
            $id = $parentCategory->getId();
            $children = !empty($categoriesToParent[$id]) ? $categoriesToParent[$id] : array();
            $parentCategory->setAttribute('children', $children);
        }

        return $this->companyCategories = $parentCategories;
    }

    /**
     * @param Company $company
     * @param array $categoriesIds
     * @param string $column
     *
     * @return Category[]
     */
    protected function getCategories(Company $company, array $categoriesIds, $column = 'id')
    {
        $categoryRepository = $this->getDoctrine()->getRepository('MetalCategoriesBundle:Category');

        return $categoryRepository->createQueryBuilder('c')
            ->select('c')
            ->addSelect('COALESCE(cc.displayPosition, :default_display_position) AS HIDDEN displayPosition')
            ->leftJoin('MetalCompaniesBundle:CompanyCategory', 'cc', 'WITH', 'c.id = cc.category AND cc.company = :company AND cc.enabled = true')
            ->andWhere(sprintf('c.%s IN (:categories_ids)', $column))
            ->setParameter('company', $company)
            ->setParameter('categories_ids', $categoriesIds)
            ->setParameter('default_display_position', CompanyCategory::DEFAULT_DISPLAY_POSITION)
            ->orderBy('displayPosition', 'ASC')
            ->addOrderBy('c.title', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
