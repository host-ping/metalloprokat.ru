<?php

namespace Metal\PrivateOfficeBundle\Widget;

use Brouzie\Bundle\WidgetsBundle\Widget\WidgetAbstract;
use Metal\CompaniesBundle\Entity\Company;
use Metal\CompaniesBundle\Entity\CompanyPackage;

class MaxProductsCountLoadWidget extends WidgetAbstract
{
    protected function setDefaultOptions()
    {
        parent::setDefaultOptions();

        $this->optionsResolver
            ->setRequired(array('company'))
            ->setAllowedTypes('company', array(Company::class))
        ;
    }

    public function getParametersToRender()
    {
        $company = $this->options['company'];
        /* @var $company Company */
        $companyPackage = null;

        if ($company->getPackageChecker()->isPaidAccount()) {
            $companyPackageRepo = $this->getDoctrine()->getRepository('MetalCompaniesBundle:CompanyPackage');
            $companyPackage = $companyPackageRepo->findOneBy(array('company' => $company->getId()));
            /* @var $package CompanyPackage */

            if (!$companyPackage) {
                throw new \LogicException(sprintf(
                    'У компании платника должен быть пакет услуг. Нужно добавить в Message106 новую запись для компании с id = %d и выставить компанию',
                    $company->getId()
                ));
            }
        }

        $maxProductsCount = $company->getPackageChecker()->getMaxAvailableProductsCount();
        $maxProductsCountMinisite = $company->getPackageChecker()->getMaxAvailableProductsCountMinisite();

        return compact('maxProductsCount', 'maxProductsCountMinisite', 'companyPackage', 'company');
    }
}
