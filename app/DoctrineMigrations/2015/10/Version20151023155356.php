<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20151023155356 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->connection->getConfiguration()->setSQLLogger(null);

        $companyIds = $this->connection->createQueryBuilder()
            ->select('cl.company_id AS companyId, u.User_ID AS userId')
            ->from('company_log', 'cl')
            ->leftJoin('cl', 'User', 'u', 'cl.company_id = u.ConnectCompany')
            ->where('cl.created_by IS NULL')
            ->groupBy('cl.company_id')
            ->execute()
            ->fetchAll();

        foreach ($companyIds as $item) {
            $this->connection->executeUpdate('UPDATE company_log SET created_by = :user_id WHERE company_id = :company_id',
                array(
                    'user_id' => $item['userId'],
                    'company_id' => $item['companyId']
                ));
        }


    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
