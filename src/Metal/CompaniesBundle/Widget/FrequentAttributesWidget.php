<?php

namespace Metal\CompaniesBundle\Widget;

use Brouzie\Bundle\WidgetsBundle\Widget\WidgetAbstract;
use Brouzie\WidgetsBundle\Cache\CacheProfile;
use Brouzie\WidgetsBundle\Widget\CacheableWidget;
use Metal\CategoriesBundle\Entity\Category;
use Metal\CompaniesBundle\Entity\Company;
use Metal\ProductsBundle\DataFetching\Spec\ProductsFilteringSpec;
use Metal\ProductsBundle\Entity\Product;
use Metal\ProjectBundle\DataFetching\DataFetcher;

class FrequentAttributesWidget extends WidgetAbstract implements CacheableWidget
{
    protected function setDefaultOptions()
    {
        parent::setDefaultOptions();

        $this->optionsResolver
            ->setRequired(array('company'))
            ->setAllowedTypes('company', Company::class);
    }

    public function getCacheProfile()
    {
        $company = $this->options['company'];
        /* @var $company Company */

        return new CacheProfile(
            array(
                'company_id' => $company->getId(),
            ),
            DataFetcher::TTL_INFINITY,
            array(
                sprintf(ProductsFilteringSpec::COMPANY_TAG_PATTERN, $company->getId()),
            )
        );
    }

    protected function getParametersToRender()
    {
        $company = $this->options['company'];
        /* @var $company Company */

        $categoryRepository = $this->getDoctrine()->getRepository('MetalCategoriesBundle:Category');

        //TODO: переписать на sphinx facet, см MiniSiteController::productsAction, не потерять categoryId
        $count = 10;
        $attributes = $this->getDoctrine()->getRepository('MetalProductsBundle:Product')->createQueryBuilder('p')
            ->select('COUNT(p.id) AS productsCount, po.title, po.slug, po.id AS parameterOptionId, IDENTITY(p.category) AS categoryId')
            ->andWhere('p.company = :company')
            ->andWhere('p.checked = :status')
            ->setParameter('status', Product::STATUS_CHECKED)
            ->groupBy('p.category')
            ->setParameter('company', $company)
            ->orderBy('productsCount', 'DESC')
            ->setMaxResults($count)
            ->join('MetalProductsBundle:ProductParameterValue', 'ppv', 'WITH', 'p.id = ppv.product')
            ->join('ppv.parameterOption', 'po')
            ->addGroupBy('ppv.parameterOption')
            ->getQuery()
            ->getResult();

        $categoriesIds = array();
        foreach ($attributes as $attr) {
            $categoriesIds[$attr['categoryId']] = true;
        }

        $categories = array();
        if ($categoriesIds) {
            $categories = $categoryRepository->findBy(array('id' => array_keys($categoriesIds)));
        }

        $categoriesData = array();
        foreach ($categories as $category) {
            /* @var $category Category */
            $categoriesData[$category->getId()] = array('categoryTitle' => $category->getTitle(), 'categorySlug' => $category->getSlugCombined());
        }

        $categoriesWithAttr = array();
        foreach ($attributes as $attribute) {
            $categoriesWithAttr[] = array(
                'title' => $categoriesData[$attribute['categoryId']]['categoryTitle'].' '.$attribute['title'],
                'slugWithAttr' => $categoriesData[$attribute['categoryId']]['categorySlug'].'/'.$attribute['slug']
            );
        }

        return array('categories' => $categoriesWithAttr);
    }
}
