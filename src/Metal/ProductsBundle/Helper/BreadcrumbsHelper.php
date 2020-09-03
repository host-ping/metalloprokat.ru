<?php

namespace Metal\ProductsBundle\Helper;

use Brouzie\Bundle\HelpersBundle\Helper\HelperAbstract;
use Metal\AttributesBundle\Entity\DTO\AttributesCollection;
use Metal\CategoriesBundle\Entity\Category;
use Metal\CategoriesBundle\Entity\LandingPage;
use Metal\ProductsBundle\Entity\Product;
use Metal\ProjectBundle\Helper\SeoHelper;
use Symfony\Component\HttpFoundation\Request;

class BreadcrumbsHelper extends HelperAbstract
{
    public function getBreadcrumbsForProduct(Product $product)
    {
        $category = $product->getCategory();
        $categoryTitle = $category->getTitle();
        $categorySlug = $category->getSlugCombined();

        $attributesCollection = $product->getAttribute('product_attributes_collection');
        /* @var $attributesCollection AttributesCollection */

        $seoHelper = $this->getHelper('MetalProjectBundle:Seo');
        /* @var $seoHelper SeoHelper */

        $combinations = array_reverse($seoHelper->getAttributeValuesCombination($attributesCollection), true);

        $productBreadcrumb = array(
            'id' => $product->getId(),
            'title' => $product->getTitle(),
            'is_label' => true,
        );

        foreach ($combinations as $slug => $title) {
            $productBreadcrumb['siblings'][] = array(
                'title' => $categoryTitle.' '.$title,
                'is_label' => false,
                'slug_combined' => $categorySlug.'/'.$slug
            );
        }

        return array($productBreadcrumb);
    }

    public function getBreadcrumbsForCategory(Request $request, Category $category)
    {
        $attributesCollection = $request->attributes->get('attributes_collection');
        /* @var $attributesCollection AttributesCollection */

        if (!count($attributesCollection)) {
            return array();
        }

        $categoryTitle = $category->getTitle();
        $categorySlug = $category->getSlugCombined();

        $seoHelper = $this->getHelper('MetalProjectBundle:Seo');
        /* @var $seoHelper SeoHelper */

        $combinations = $seoHelper->getAttributeValuesCombination($attributesCollection, true);
        array_pop($combinations);
        $combinations = array_reverse($combinations, true);

        $additionalBreadcrumb = array(
            'id' => mt_rand(0, 1000),
            'title' => $category->getTitle() .' '.$attributesCollection->toString(' ', ' '),
            'is_label' => true,
        );

        foreach ($combinations as $slug => $title) {
            $additionalBreadcrumb['siblings'][] = array(
                'title' => $categoryTitle.' '.$title,
                'is_label' => false,
                'slug_combined' => $categorySlug.'/'.$slug
            );
        }

        return array($additionalBreadcrumb);
    }

    public function getBreadcrumbsForLandingPage(LandingPage $landingPage)
    {
        $additionalBreadcrumb = array(
            'id' => $landingPage->getId(),
            'title' => $landingPage->getTitle(),
            'is_label' => true,
        );

        return array($additionalBreadcrumb);
    }
}
