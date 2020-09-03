<?php

namespace Metal\TerritorialBundle\Repository;

use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityRepository;
use Metal\TerritorialBundle\Entity\Country;

class TerritorialStructureRepository extends EntityRepository
{
    public function getTerritorialIdsPerCities(): array
    {
        $rows = $this
            ->createQueryBuilder('ts')
            ->select('ts.id')
            ->addSelect('IDENTITY(ts.city) AS cityId')
            ->andWhere('ts.city IS NOT NULL')
            ->getQuery()
            ->getResult();

        return array_column($rows, 'id', 'cityId');
    }

    public function getTerritorialIdsPerRegions(): array
    {
        $rows = $this
            ->createQueryBuilder('ts')
            ->select('ts.id')
            ->addSelect('IDENTITY(ts.region) AS regionId')
            ->andWhere('ts.region IS NOT NULL')
            ->getQuery()
            ->getResult();

        return array_column($rows, 'id', 'regionId');
    }

    public function getTerritorialIdsPerFederalDistrict(): array
    {
        $rows = $this
            ->createQueryBuilder('ts')
            ->select('ts.id')
            ->addSelect('IDENTITY(ts.federalDistrict) AS federalDistrictId')
            ->andWhere('ts.federalDistrict IS NOT NULL')
            ->getQuery()
            ->getResult();

        return array_column($rows, 'id', 'federalDistrictId');
    }

    public function populate(callable $logger = null)
    {
        $conn = $this->_em->getConnection();
        $logger = $logger ?: function ($line) {
        };

        $logger('Insert in territorial_structure cities');
        $conn->executeQuery(
            '
            INSERT IGNORE INTO territorial_structure (city_id, country_id)
            SELECT c.Region_ID, c.country_id
            FROM Classificator_Region c
            WHERE c.country_id IN (:countries_ids)',
            array('countries_ids' => Country::getEnabledCountriesIds()),
            array('countries_ids' => Connection::PARAM_INT_ARRAY)
        );

        $logger('Insert in territorial_structure regions');
        $conn->executeQuery(
            '
            INSERT IGNORE INTO territorial_structure (region_id, country_id)
            SELECT r.Regions_ID, r.country_id
            FROM Classificator_Regions r
            WHERE r.country_id IN (:countries_ids)',
            array('countries_ids' => Country::getEnabledCountriesIds()),
            array('countries_ids' => Connection::PARAM_INT_ARRAY)
        );

        // федеративный округ, исключаем Украину, Беларусь и Казахстан, так как они пробиты в округах
        $logger('Insert in territorial_structure federal districts');
        $conn->executeQuery(
            '
            INSERT IGNORE INTO territorial_structure (federal_district_id, country_id)
            SELECT f.FO_ID, f.country_id
            FROM Classificator_FO f
            WHERE f.country_id IN (:countries_ids) AND f.FO_ID NOT IN (10, 11, 12)',
            array('countries_ids' => Country::getEnabledCountriesIds()),
            array('countries_ids' => Connection::PARAM_INT_ARRAY)
        );

        $logger('Refresh parents');
        $conn->executeQuery('UPDATE territorial_structure SET parent = NULL');

        // вставка title по городу, родителя, страны
        $logger('Update title territorial_structure by cities');
        $conn->executeUpdate(
            '
        UPDATE territorial_structure tsc
          JOIN Classificator_Region c
          JOIN territorial_structure tsr
            ON tsc.city_id = c.Region_ID AND tsr.region_id = c.parent
        SET tsc.title = c.Region_Name, tsc.parent = tsr.id, tsc.country_id = c.country_id
            '
        );

        // вставка title по области, родителя, страны
        $logger('Update title territorial_structure by regions');
        $conn->executeUpdate(
            '
        UPDATE territorial_structure tsr
          JOIN Classificator_Regions r
          JOIN territorial_structure tsfd
            ON tsr.region_id = r.Regions_ID AND tsfd.federal_district_id = r.parent
        SET tsr.title = r.Regions_Name, tsr.parent = tsfd.id, tsr.country_id = r.country_id
            '
        );

        // вставка title по федеративный округу, страны
        $logger('Update title territorial_structure by federal districts');
        $conn->executeUpdate(
            '
            UPDATE territorial_structure AS ts
            JOIN Classificator_FO f
            ON ts.federal_district_id = f.FO_ID
            SET ts.title = f.FO_Name, ts.country_id = f.country_id
            '
        );
    }
}
