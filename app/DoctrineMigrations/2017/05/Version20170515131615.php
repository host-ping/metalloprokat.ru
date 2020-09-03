<?php

namespace Application\Migrations;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\Schema;
use Metal\ProjectBundle\Doctrine\Utils;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170515131615 extends Version20170425084812
{
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

        $tables = array(
            'announcement_stats_element',
            'stats_element',
        );

        foreach ($connections as $connection) {
            Utils::checkConnection($connection);
            foreach ($tables as $table) {
                $connection->executeUpdate(
                    sprintf('DELETE FROM `%s` WHERE ip = :ip', $table),
                    array(
                        'ip' => '188.191.97.162'
                    )
                );

                $connection->executeUpdate(
                    sprintf('DELETE FROM `%s` WHERE user_agent = :empty', $table),
                    array(
                        'empty' => ''
                    )
                );
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
