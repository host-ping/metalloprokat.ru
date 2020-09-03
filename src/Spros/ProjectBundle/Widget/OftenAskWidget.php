<?php


namespace Spros\ProjectBundle\Widget;

use Brouzie\Bundle\WidgetsBundle\Widget\WidgetAbstract;

use Metal\CategoriesBundle\Entity\Category;
use Metal\TerritorialBundle\Entity\City;

class OftenAskWidget extends WidgetAbstract
{
    protected function setDefaultOptions()
    {
        parent::setDefaultOptions();

        $this->optionsResolver
            ->setRequired(array('categories'))
            ->setDefined(array('category', 'city'))
            ->setAllowedTypes('category', array(Category::class, 'null'))
            ->setAllowedTypes('city', array(City::class, 'null'))
            ->setDefaults(array('limit' => 3, 'label' => 'Часто спрашивают', 'skipCategories' => 0))
        ;
    }
    protected function getParametersToRender()
    {
        $category = $this->options['category'];
        $categories = $this->options['categories'];
        $city = $this->options['city'];
        $limit = $this->options['limit'];
        $skipCategories = $this->options['skipCategories'];

        if (null === $category) {
            $category = current($categories);
        }

        $children = $categories;
        if (!$category->getHasSiblings()) {
            $em = $this->getDoctrine()->getManager();
            $categoryRepository = $em->getRepository('MetalCategoriesBundle:Category');
            /* @var $categoryRepository \Metal\CategoriesBundle\Repository\CategoryRepository */
            $children = $categoryRepository->createQueryBuilder('c')
                ->andWhere('c.isEnabledMetalspros = true')
                ->andWhere('c.hasSiblings = false')
                ->getQuery()
                ->getResult();
        } elseif ($category->getParent()) {
            $children = $category->getParent()->getAttribute('children');
        }

        $childrenIds = array();
        foreach ($children as $child) {
            $childrenIds[$child->getId()] = $child;
        }

        $start = 0;
        if ($category) {
            $start = array_search($category->getId(), array_keys($childrenIds)) + $skipCategories;
        }

        $resultIds = array();
        $n = count($children);
        if ($n > $limit) {
            $_limit = $limit;
        } else {
            $_limit = $n - 1;
        }

        // начиная со следующей категории зацикливаем
        for ($i = $start + 1, $j = 0; $j < $_limit; $i++, $j++) {
            if ($_limit > $limit) {
                $resultIds[] = $children[$i % $n]->getId();
            } else {
                if ($category) {
                    unset($childrenIds[$category->getId()]);
                }

                $resultIds[] = $children[$i % $n]->getId();
            }
        }
        $result = array_map(
            function ($id) use ($childrenIds) {
                return $childrenIds[$id];
            },
            $resultIds
        );

        return array(
            'result' => $result,
            'city' => $city,
        );
    }
}
