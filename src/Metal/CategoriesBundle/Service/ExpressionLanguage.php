<?php

namespace Metal\CategoriesBundle\Service;

use Symfony\Component\ExpressionLanguage\ExpressionLanguage as BaseExpressionLanguage;

class ExpressionLanguage extends BaseExpressionLanguage
{
    protected function registerFunctions()
    {
        parent::registerFunctions();

        $this->register(
            'match',
            function ($variable, $pattern) {
                $pattern = strtr($pattern, ProductCategoryDetector::$patternReplacements);

                return sprintf('preg_match("/%s/ui", %s)', trim($pattern, '"'), $variable);
            },
            function (array $variables, $str, $pattern) {
                $pattern = strtr($pattern, ProductCategoryDetector::$patternReplacements);

                return preg_match('/'.$pattern.'/ui', $str);
            }
        );

        $this->register(
            'match_category',
            function ($categoryId, $title) {
                return sprintf('$category_detector->matchCategory(%d, %s)', $categoryId, $title);
            },
            function (array $variables, $categoryId, $title) {
                return $variables['category_detector']->matchCategory($categoryId, $title);
            }
        );
    }
}
