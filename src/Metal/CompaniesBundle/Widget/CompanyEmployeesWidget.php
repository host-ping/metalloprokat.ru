<?php

namespace Metal\CompaniesBundle\Widget;

use Brouzie\Bundle\WidgetsBundle\Widget\WidgetAbstract;
use Metal\CompaniesBundle\Entity\Company;
use Metal\MiniSiteBundle\Helper\DefaultHelper;

class CompanyEmployeesWidget extends WidgetAbstract
{
    public function setDefaultOptions()
    {
        parent::setDefaultOptions();

        $this->optionsResolver
            ->setDefaults(array('product_external_url' => ''))
            ->setRequired(array('company'));
    }

    protected function getParametersToRender()
    {
        $minisiteHelper = $this->container->get('brouzie.helper_factory')->get('MetalMiniSiteBundle:Default');
        /* @var $minisiteHelper DefaultHelper */
        $company = $this->options['company'];
        /* @var $company Company */
        $userRepository = $this->getDoctrine()->getRepository('MetalUsersBundle:User');

        if (!$currentCity = $minisiteHelper->getCurrentCity()) {
            $currentCity = $company->getCity();
        }

        $employees = $userRepository->getEmployeesForTerritory($company, $currentCity);

        return compact('employees', 'company');
    }
}
