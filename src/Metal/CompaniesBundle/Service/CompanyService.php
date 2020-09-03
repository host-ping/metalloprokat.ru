<?php

namespace Metal\CompaniesBundle\Service;

use Doctrine\ORM\EntityManager;
use Brouzie\Sphinxy\IndexManager;
use Brouzie\Sphinxy\Connection;

use Metal\CompaniesBundle\Entity\Company;
use Metal\CompaniesBundle\Entity\CompanyHistory;
use Metal\CompaniesBundle\Entity\ValueObject\CompanyTypeProvider;
use Metal\UsersBundle\Entity\User;

class CompanyService
{
    /**
     * @var IndexManager
     */
    protected $indexManager;

    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @var Connection
     */
    protected $sphinxy;


    public function __construct(EntityManager $em, IndexManager $indexManager, Connection $sphinxy)
    {
        $this->em = $em;
        $this->indexManager = $indexManager;
        $this->sphinxy = $sphinxy;
    }

    public function updateRelativeDataCities($oldCompanyAsArray, $data = null)
    {
        $oldCityId = $oldCompanyAsArray['cityId'];
        $company = $this->em->getRepository('MetalCompaniesBundle:Company')->find($oldCompanyAsArray['companyId']);
        /* @var $company Company */

        $addedCitiesIds = array();
        if (is_array($data['added'])) {
            $addedCitiesIds = $data['added'];
        } elseif ($data['added']) {
            $addedCitiesIds[] = $data['added'];
        }

        if ($oldCityId != $company->getCity()->getId()) {
            $addedCitiesIds[] = $company->getCity()->getId();
        }

        $removedCitiesIds = array();
        if (is_array($data['removed'])) {
            $removedCitiesIds = $data['removed'];
        } elseif ($data['removed']) {
            $removedCitiesIds[] = $data['removed'];
        }

        if (count($addedCitiesIds) || count($removedCitiesIds)) {
            $this->sphinxy
                ->createQueryBuilder()
                ->update('products')
                ->set('cities_ids', ':a')
                ->setParameter('a', $company->getCitiesIds())
                ->where('company_id = :c')
                ->setParameter('c', $company->getId())
                ->execute();
        }
    }

    public function addCompanyHistory(Company $company, User $author, $actionId)
    {
        $companyHistory = new CompanyHistory();
        $companyHistory->setCompany($company);
        $companyHistory->setActionId($actionId);
        $companyHistory->setAuthor($author);

        $this->em->persist($companyHistory);

        return $companyHistory;
    }

    public static function getCompanyTypeByTitle($title)
    {
        $allTypes = CompanyTypeProvider::getAllTypes();
        foreach ($allTypes as $type) {
            $matches = null;
            preg_match(
                sprintf('/(?:\W|\s|^)%s(?:\W|\s|$)/ui', $type->getTitle()),
                (string)$title,
                $matches
            );

            if (!empty($matches[0])) {
                return $type;
            }
        }

        return null;
    }
}
