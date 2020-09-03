<?php

namespace Metal\CompaniesBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Metal\CompaniesBundle\Entity\Company;
use Metal\CompaniesBundle\Entity\CompanyOldSlug;

class CompanyOldSlugRepository extends EntityRepository
{
    public function changeCompanySlug(Company $company, $oldSlug)
    {
        if ($this->findOneBy(array('oldSlug' => $oldSlug))) {
            return;
        }

        $companyOldSlug = $this->findOneBy(array('company' => $company));

        if ($companyOldSlug) {
            $this->_em->remove($companyOldSlug);
            $this->_em->flush();
        }

        $companyOldSlug = new CompanyOldSlug();
        $companyOldSlug->setCompany($company);
        $companyOldSlug->setOldSlug($oldSlug);

        $this->_em->persist($companyOldSlug);
        $this->_em->flush();
    }
}
