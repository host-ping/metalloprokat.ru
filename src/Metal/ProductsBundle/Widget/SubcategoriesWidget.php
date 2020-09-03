<?php

namespace Metal\ProductsBundle\Widget;

use Brouzie\Bundle\WidgetsBundle\Widget\WidgetAbstract;
use Brouzie\WidgetsBundle\Cache\CacheProfile;
use Brouzie\WidgetsBundle\Widget\CacheableWidget;
use Metal\CategoriesBundle\Entity\Category;
use Metal\ProductsBundle\DataFetching\Spec\ProductsFacetSpec;
use Metal\ProductsBundle\DataFetching\Spec\ProductsFilteringSpec;
use Metal\ProjectBundle\DataFetching\DataFetcher;
use Metal\ProjectBundle\DataFetching\Sphinxy\FacetResultExtractor;
use Metal\TerritorialBundle\Entity\City;
use Metal\TerritorialBundle\Entity\Country;

class SubcategoriesWidget extends WidgetAbstract implements CacheableWidget
{
    const COUNT_CATEGORIES_TO_SHOW = 10;

    public function setDefaultOptions()
    {
        parent::setDefaultOptions();

        $this->optionsResolver
            ->setDefined(array('city', 'country'))
            ->setRequired(array('category', 'country'))
            ->setAllowedTypes('category', array(Category::class, 'null'))
            ->setAllowedTypes('city', array(City::class, 'null'))
            ->setAllowedTypes('country', array(Country::class))
        ;
    }

    public function getParametersToRender()
    {
        $dataFetcher = $this->container->get('metal.products.data_fetcher');

        $city = $this->options['city'];
        /* @var $city City */
        $country = $this->options['country'];
        /* @var $country Country */

        $specification = $this->getSpecification();

        $attributeValueRepository = $this->getDoctrine()->getRepository('MetalAttributesBundle:AttributeValue');

        $facetSpec = (new ProductsFacetSpec())->facetByConcreteCategory(self::COUNT_CATEGORIES_TO_SHOW);

        $facetsResultSet = $dataFetcher->getFacetedResultSetByCriteria($specification, $facetSpec, false);

        $categoriesFacet = (new FacetResultExtractor($facetsResultSet, ProductsFacetSpec::COLUMN_CATEGORY_ID))
            ->getCounts();

        if (!$categoriesFacet) {
            return array('categories' => array());
        }

        $sphinxConnection = $this->container->get('sphinxy')->getConnection();

        $categoriesToAttributes = array();
        foreach ($categoriesFacet as $categoryId => $count) {
            $sqb = $sphinxConnection->createQueryBuilder()
                ->select('id, category_id, attributes_ids, GROUPBY() as attr_id')
                ->from('products')
                ->andWhere('category_id = :categoryId')
                ->setParameter('categoryId', $categoryId);

            if ($city) {
                $sqb->andWhere('cities_ids = :city')->setParameter('city', $city->getId());
            } else {
                $sqb->andWhere('countries_ids = :country')->setParameter('country', $country->getId());
            }

            $resultSet = $sqb
                ->groupBy('attributes_ids')
                ->orderBy('COUNT(*)', 'DESC')
                ->setMaxResults(self::COUNT_CATEGORIES_TO_SHOW)
                ->getResult();

            foreach ($resultSet as $row) {
                if (!isset($categoriesToAttributes[$row['category_id']]) || count($categoriesToAttributes[$row['category_id']]) < self::COUNT_CATEGORIES_TO_SHOW) {
                    $categoriesToAttributes[$row['category_id']][] = $row['attr_id'];
                }
            }
        }

        $categoriesParameters = array();
        $attributesValuesIds = array();
        foreach ($categoriesToAttributes as $categoryId => $categoryAttrIds) {
            foreach ($categoryAttrIds as $categoryAttrId) {
                $attributesValuesIds[$categoryAttrId][$categoryId] = $categoryAttrId;
            }
        }

        $attributesValues = $attributeValueRepository->createQueryBuilder('attributeValue')
            ->select('attributeValue.slug')
            ->addSelect('attributeValue.value AS title')
            ->addSelect('attributeValue.id AS id')
            ->andWhere('attributeValue.id IN (:attributes_ids)')
            ->setParameter('attributes_ids', array_keys($attributesValuesIds))
            ->orderBy('attributeValue.outputPriority')
            ->getQuery()
            ->getResult();

        foreach ($attributesValues as $attributesValue) {
            if (!isset($attributesValuesIds[$attributesValue['id']])) {
                continue;
            }

            foreach ($attributesValuesIds[$attributesValue['id']] as $categoryId => $categoryAttrId) {
                $categoriesParameters[$categoryId][] = array(
                    'title' => $attributesValue['title'],
                    'slug' => $attributesValue['slug']
                );
            }
        }

        $categories = $this->getDoctrine()
            ->getRepository('MetalCategoriesBundle:Category')
            ->findBy(array('id' => array_keys($categoriesParameters)), array('title' => 'ASC'));
        /* @var $category Category */

        $categoriesData = array();
        foreach ($categories as $category) {
            if (empty($categoriesData[$category->getId()])) {
                $categoriesData[$category->getId()]['title'] = $category->getTitle();
            }

            foreach ($categoriesParameters[$category->getId()] as $params) {
                $categoriesData[$category->getId()]['attributes'][] = array(
                    'title' => $category->getTitle().' '.$params['title'],
                    'slug' => $category->getSlugCombined().'/'.$params['slug']
                );
            }
        }

        return array('categories' => $categoriesData);
    }

    public function getCacheProfile()
    {
        return new CacheProfile(
            array(
                'key' => $this->getSpecification()->getCacheKey()
            ),
            DataFetcher::TTL_5DAYS
        );
    }

    /**
     * @return ProductsFilteringSpec
     */
    protected function getSpecification()
    {
        $currentCategory = $this->options['category'];
        /* @var $currentCategory Category */
        $city = $this->options['city'];
        /* @var $city City */
        $country = $this->options['country'];
        /* @var $country Country */

        $specification = (new ProductsFilteringSpec())
            ->category($currentCategory)
            ->notEmptyAttributes();

        if ($city) {
            $specification->city($city);
        } else {
            $specification->country($country);
        }

        return $specification;
    }
}
