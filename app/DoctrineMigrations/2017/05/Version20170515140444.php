<?php

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Metal\ProjectBundle\Doctrine\Utils;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170515140444 extends Version20170425084812
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $connection = $this->container->get('doctrine.dbal.archive_connection');
        Utils::checkConnection($connection);

        $table = 'stats_element';

        $schemaManager = $connection->getSchemaManager();
        $tableIndexes = $schemaManager->listTableIndexes($table);

        $index = 'IDX_D8FA1F64A76ED395';
        $existsIndex = false;
        foreach ($tableIndexes as $tableIndex) {
            if ($tableIndex->getName() === $index) {
                $existsIndex = true;
            }
        }

        if ($existsIndex) {
            $this->write(sprintf('Delete index "%s" for table "%s"', $index, $table));
            $schemaManager->dropIndex($index, $table);
        } else {
            $this->write(sprintf('Index "%s" not found for table "%s"', $index, $table));
        }

        $index = 'IDX_company_id';
        $existsIndex = false;
        foreach ($tableIndexes as $tableIndex) {
            if ($tableIndex->getName() === $index) {
                $existsIndex = true;
            }
        }

        if (!$existsIndex) {
            $connection->executeUpdate('CREATE INDEX IDX_company_id ON stats_element (company_id)');
        }
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
