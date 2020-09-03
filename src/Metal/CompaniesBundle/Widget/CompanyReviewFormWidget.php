<?php

namespace Metal\CompaniesBundle\Widget;

namespace Metal\CompaniesBundle\Widget;

use Brouzie\Bundle\WidgetsBundle\Widget\WidgetAbstract;
use Metal\CompaniesBundle\Entity\Company;
use Metal\CompaniesBundle\Entity\CompanyReview;
use Metal\ProjectBundle\Form\ReviewType;

class CompanyReviewFormWidget extends WidgetAbstract
{
    public function setDefaultOptions()
    {
        parent::setDefaultOptions();

        $this->optionsResolver
            ->setDefined(array('company'))
            ->setAllowedTypes('company', array(Company::class, 'null'))
        ;
    }

    public function getParametersToRender()
    {
        $form = $this->createForm(
            new ReviewType(),
            new CompanyReview(),
            array(
                'is_authenticated' => $this->isGranted('ROLE_USER'),
                'validation_groups' => array(
                    $this->isGranted('ROLE_USER') ? 'authenticated' : 'anonymous',
                ),
                'data_class' => CompanyReview::class,
            )
        );

        return array(
            'form' => $form->createView(),
            'company' => $this->options['company'],
        );
    }
}
