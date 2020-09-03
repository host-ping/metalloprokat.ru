<?php

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Metal\ProjectBundle\Doctrine\Utils;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170515145540 extends Version20170425084812
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $connection = $this->container->get('doctrine.dbal.default_connection');
        Utils::checkConnection($connection);
        $schemaManager = $connection->getSchemaManager();

        $index = 'IDX_D8FA1F64A76ED395';
        $table = 'stats_element';
        $tableIndexes = $schemaManager->listTableIndexes($table);
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

        $this->addSql('CREATE INDEX IDX_company_id ON stats_element (company_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
