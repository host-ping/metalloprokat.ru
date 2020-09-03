<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150117171612 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $skip = 0;
        $limit = 100;
        $this->connection->getConfiguration()->setSQLLogger(null);

        do {
            $companyDescriptions = $this->connection->createQueryBuilder()
                ->select('*')
                ->from('company_description', 'cd')
                ->setMaxResults($limit)
                ->setFirstResult($skip)
                ->execute()
                ->fetchAll();

            $skip += $limit;

            foreach ($companyDescriptions as $companyDescription) {
                $this->connection->executeUpdate('UPDATE company_description SET description = :description WHERE company_id = :company_id', array(
                    'description' => strip_tags($companyDescription['description']),
                    'company_id' => $companyDescription['company_id']
                ) );
            }
        } while ($companyDescriptions);

    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
