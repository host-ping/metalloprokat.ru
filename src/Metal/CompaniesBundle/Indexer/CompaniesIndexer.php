<?php

namespace Metal\CompaniesBundle\Indexer;

use Brouzie\Sphinxy\Indexer\DoctrineQbIndexer;

class CompaniesIndexer extends DoctrineQbIndexer
{
    public function processItems(array $items)
    {
        $companiesIds = array_column($items, 'company_id');

        $citiesToCompany = $this->em->getRepository('MetalCompaniesBundle:CompanyCity')
            ->getCitiesIdsForCompanies($companiesIds);

        $categoriesToCompany = $this->em->getRepository('MetalCompaniesBundle:CompanyCategory')
            ->getCategoriesIdsForCompanies($companiesIds);

        foreach ($items as $i => $companyRow) {
//            if (in_array(null, $citiesToCompany[$companyRow['company_id']], true)) {
//                vdc($companyRow['company_id'], 'cities', $citiesToCompany[$companyRow['company_id']]);
//            }
//            if (in_array(null, $categoriesToCompany[$companyRow['company_id']], true)) {
//                vdc($companyRow['company_id'], 'categories', $categoriesToCompany[$companyRow['company_id']]);
//            }

            //Добавляем пробел в конец тайтла чтобы не сетились числа
            $items[$i] = array(
                'id'             => $companyRow['company_id'],
                'title'          => $companyRow['normalized_title'] . ' ',
                'title_field'    => $companyRow['normalized_title'] . ' ',
                'cities_ids'     => $citiesToCompany[$companyRow['company_id']],
                'categories_ids' => $categoriesToCompany[$companyRow['company_id']]
            );
        }

        return $items;
    }

    protected function getQueryBuilder()
    {
        return $this->em
            ->createQueryBuilder()
            ->select('e.id AS company_id')
            ->addSelect('e.normalizedTitle as normalized_title')
            ->from('MetalCompaniesBundle:Company', 'e')
            ->andWhere('e.deletedAtTS = 0')
            ->andWhere('e.title != :empty_string')
            ->setParameter('empty_string', '');
    }
}
