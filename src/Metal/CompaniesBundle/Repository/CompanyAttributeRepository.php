<?php

namespace Metal\CompaniesBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Metal\CompaniesBundle\Entity\CompanyAttribute;

class CompanyAttributeRepository extends EntityRepository
{
    public function attachAttributesToCompanies(array $companies)
    {
        if (empty($companies)) {
            return ;
        }

        $attributes = $this->findBy(array('company' => $companies));
        /* @var $attributes CompanyAttribute[] */

        foreach ($attributes as $attr) {
            $attr->getCompany()->setAttribute('company_attribute', $attr);
        }
    }
}