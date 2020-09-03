<?php

namespace Application\Migrations;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\Schema;
use Metal\ProjectBundle\Doctrine\Utils;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170518122828 extends Version20170425084812
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

        foreach ($connections as $key => $connection) {
            foreach ($tables as $table) {
                Utils::checkConnection($connection);
                $result = $connection->executeUpdate(
                    sprintf('DELETE FROM `%s` WHERE user_agent = :user_agent', $table),
                    array(
                        'user_agent' => 'Go-http-client/2.0'
                    )
                );

                $this->write(sprintf('Deleted %d bots from "%s" and connection "%s"', $result, $table, $key));
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
