<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150128181535 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->connection->getConfiguration()->setSQLLogger(null);

        $demandsFilePath = $this->connection->createQueryBuilder()
            ->select('d.id, d.file_path')
            ->from('demand', 'd')
            ->where('file_path IS NOT NULL')
            ->execute()
            ->fetchAll();

        foreach ($demandsFilePath as $path) {
            $this->connection->executeUpdate('UPDATE demand SET file_path = :path WHERE id = :demand_id', array(
                'path' => basename($path['file_path']),
                'demand_id' => $path['id']
            ));
        }

    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
