<?php

namespace Metal\CompaniesBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Metal\CompaniesBundle\Entity\Company;
use Metal\TerritorialBundle\Entity\TerritoryInterface;

class CompanyPhoneRepository extends EntityRepository
{
    /**
     * @param Company $company
     * @param TerritoryInterface $territory
     *
     * @return string
     */
    public function getPhonesForCompanyInTerritory(Company $company, TerritoryInterface $territory)
    {
        $employeesForTerritory = $this->_em
            ->getRepository('MetalUsersBundle:User')
            ->getEmployeesForTerritory($company, $territory);

        $phones = array();
        foreach ($employeesForTerritory as $employee) {
            $phone = $employee->getAttribute('territorial_phone') ?: $employee->getPhone();
            $phones[$phone] = true;
        }

        return implode(', ', array_keys($phones));
    }

    /**
     * @param Company[] $companies
     * @param TerritoryInterface $territory
     */
    public function attachPhonesToCompaniesForCurrentTerritory(array $companies, TerritoryInterface $territory = null)
    {
        if (!$territory) {
            return;
        }

        $companies = array_filter($companies, function(Company $company) {
            return $company->getHasTerritorialRules();
        });

        if (!$companies) {
            return;
        }

        /* @var $companies Company[] */
        foreach ($companies as $company) {
            $phonesStr = $this->getPhonesForCompanyInTerritory($company, $territory);
            if ($phonesStr) {
                $company->setAttribute('phones_string', $phonesStr);
            }
        }
    }
}
