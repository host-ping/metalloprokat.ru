<?php

namespace Application\Migrations;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Metal\ServicesBundle\Entity\Package;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150226121816 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $conn = $this->connection;
        $companies = $conn->fetchAll(
            'SELECT Message_ID, domain FROM Message75 WHERE code_access IN (:code_access) AND company_url = :company_url',
            array(
                'code_access' => array(
                    Package::ADVANCED_PACKAGE,
                    Package::FULL_PACKAGE
                ),
                'company_url' => '[]'
            ),
            array(
                'code_access' => Connection::PARAM_INT_ARRAY
            )
        );

        foreach ($companies as $company) {
            $this->addSql(
                "UPDATE Message75 SET company_url = :site WHERE Message_ID = :company_id",
                array(
                    'site' => json_encode(array(sprintf('http://%s', $company['domain']))),
                    'company_id' => $company['Message_ID']
                )
            );
        }

    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
