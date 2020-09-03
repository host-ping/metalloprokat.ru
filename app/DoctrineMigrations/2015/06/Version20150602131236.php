<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Metal\CompaniesBundle\Entity\CompanyCity;
use Metal\ProjectBundle\Helper\TextHelperStatic;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150602131236 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->connection->getConfiguration()->setSQLLogger(null);

        $this->connection->executeUpdate('UPDATE Message75 SET company_categories_titles = null');

        $minId = $this->connection->fetchColumn('SELECT MIN(Message_ID) FROM Message75');
        $maxId = $this->connection->fetchColumn('SELECT MAX(Message_ID) FROM Message75');

        $idFrom = $minId;
        do {
            $idTo = $idFrom + 1000;

            $companyCategories = $this->connection->createQueryBuilder()
                ->select('cc.company_id, cat.title_ablative')
                ->from('Message76', 'cc')
                ->join('cc', 'Message73', 'cat', 'cc.cat_id = cat.Message_ID')
                ->andWhere('cc.company_id >= :id_from')
                ->andWhere('cc.company_id < :id_to')
                ->setParameter('id_from', $idFrom)
                ->setParameter('id_to', $idTo)
                ->execute()
                ->fetchAll();

            $companyCategoriesArray = array();
            foreach ($companyCategories as $companyCategory) {
                $companyCategoriesArray[$companyCategory['company_id']][] = TextHelperStatic::normalizeTitleForEmbed($companyCategory['title_ablative']);
            }

            foreach ($companyCategoriesArray as $companyId => $categories) {
                $this->connection->executeUpdate('UPDATE Message75 SET company_categories_titles = :csv WHERE Message_ID = :company_id',
                    array('csv' => $categories, 'company_id' => $companyId), array('csv' => 'csv'));
            }

            $idFrom = $idTo;
        } while ($idFrom <= $maxId);

        $this->connection->executeUpdate('UPDATE Message75 SET company_delivery_titles = null');

        $idFrom = $minId;
        do {
            $idTo = $idFrom + 1000;

            $companyCities = $this->connection->createQueryBuilder()
                ->select('cdc.company_id', 'c.title_accusative')
                ->from('company_delivery_city', 'cdc')
                ->where('kind = :kind_delivery')
                ->join('cdc', 'Classificator_Region', 'c', 'cdc.city_id = c.Region_ID')
                ->andWhere('cdc.company_id >= :id_from')
                ->andWhere('cdc.company_id < :id_to')
                ->setParameter('id_from', $idFrom)
                ->setParameter('id_to', $idTo)
                ->setParameter('kind_delivery', CompanyCity::KIND_DELIVERY)
                ->execute()
                ->fetchAll();

            $companyCitiesArray = array();
            foreach ($companyCities as $companyCity) {
                $companyCitiesArray[$companyCity['company_id']][] = $companyCity['title_accusative'];
            }

            foreach ($companyCitiesArray as $companyId => $cities) {
                $this->connection->executeUpdate('UPDATE Message75 SET company_delivery_titles = :csv WHERE Message_ID = :company_id',
                    array('csv' => $cities, 'company_id' => $companyId), array('csv' => 'csv'));
            }

            $idFrom = $idTo;
        } while ($idFrom <= $maxId);
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
    }
}
