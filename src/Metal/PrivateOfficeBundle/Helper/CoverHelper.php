<?php

namespace Metal\PrivateOfficeBundle\Helper;

use Brouzie\Bundle\HelpersBundle\Helper\HelperAbstract;
use Metal\CompaniesBundle\Entity\Company;

class CoverHelper extends HelperAbstract
{
    public function companyCover(Company $company, $checkPackage = true)
    {
        if ($checkPackage && !$company->getPackageChecker()->isAllowedSetHeader()) {
            return null;
        }

        return $company->getMinisiteConfig()->getCover();
    }
}
