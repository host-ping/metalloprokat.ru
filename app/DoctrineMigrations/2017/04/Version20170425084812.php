<?php

namespace Application\Migrations;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170425084812 extends AbstractMigration implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $connections = array(
            'Archive connection' => $this->container->get('doctrine.dbal.archive_connection'),
            'Default connection' => $this->container->get('doctrine.dbal.default_connection')
        );
        /* @var $connections Connection[] */

        $indexForTables = array(
            'announcement_stats_element' => 'UNIQ_itemHash_fake',
            'stats_element' => 'UNIQ_itemHash_fake',
        );

        foreach ($connections as $connName => $connection) {
            $this->write($connName);

            $schemaManager = $connection->getSchemaManager();
            foreach ($indexForTables as $table => $index) {
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

            }

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
