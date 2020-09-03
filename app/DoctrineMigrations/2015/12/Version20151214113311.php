<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20151214113311 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->connection->getConfiguration()->setSQLLogger(null);

        $query = 'SELECT c.Message_ID AS id, c.title AS title FROM Message75 AS c WHERE title RLIKE (\'([ \t \n \r \v]{2,})\')';
        $badCompanyTitles = $this->connection->executeQuery($query)->fetchAll();

        foreach ($badCompanyTitles as $badCompanyTitle) {
            $this->connection->executeUpdate(
                'UPDATE Message75 SET title = :new_title WHERE Message_ID = :id',
                array(
                    'new_title' => preg_replace('/\s+/', ' ', $badCompanyTitle['title']),
                    'id' => $badCompanyTitle['id']
                )
            );
        }

    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
