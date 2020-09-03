<?php

namespace Metal\CompaniesBundle\Widget;

use Brouzie\WidgetsBundle\Widget\TwigWidget;
use Metal\CompaniesBundle\Entity\Company;
use Metal\ProjectBundle\Helper\SeoHelper;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LimitAboutCompanyWidget extends TwigWidget
{
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setRequired(array('company'));
    }

    public function getContext()
    {
        $company = $this->options['company'];
        /* @var $company Company */

        $text = SeoHelper::limitCompanyText($company->getCompanyDescription()->getDescription());

        return compact('text');
    }
}
