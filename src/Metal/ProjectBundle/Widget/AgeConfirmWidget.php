<?php

namespace Metal\ProjectBundle\Widget;

use Brouzie\Bundle\WidgetsBundle\Widget\WidgetAbstract;
use Brouzie\WidgetsBundle\Widget\ConditionallyRenderedWidget;
use Metal\CategoriesBundle\Entity\Category;

class AgeConfirmWidget extends WidgetAbstract implements ConditionallyRenderedWidget
{
    public function setDefaultOptions()
    {
        parent::setDefaultOptions();

        $this->optionsResolver
            ->setRequired(['category'])
            ->setAllowedTypes('category', Category::class);
    }

    public function shouldBeRendered()
    {
        /** @var Category $category */
        $category = $this->options['category'];

        $ageConfirmed = $this->getRequest()->cookies->get('age_confirmed');

        return $category->getCheckAge() && !$ageConfirmed;//!$ageConfirmed && !$this->getUser() && $category instanceof Category && $category->getCheckAge();
    }
}