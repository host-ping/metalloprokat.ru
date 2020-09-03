<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Metal\ProjectBundle\Doctrine\Utils;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170516121231 extends AbstractMigration implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $connection = $this->container->get('doctrine.dbal.default_connection');
        Utils::checkConnection($connection);
        
        $table = 'announcement_stats_element';
        $indexes = array(
            'IDX_E103DD188BAC62AF', //city_id
            'IDX_E103DD18A76ED395', //user_id
            'IDX_E103DD1812469DE2', //category_id
        );

        //Нам нужно оставить только: announcement_id, date_created_at
        $schemaManager = $connection->getSchemaManager();
        $tableIndexes = $schemaManager->listTableIndexes($table);
        foreach ($indexes as $index) {
            foreach ($tableIndexes as $tableIndex) {
                if ($tableIndex->getName() === $index) {
                    $schemaManager->dropIndex($index, $table);
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
