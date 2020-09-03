<?php

namespace Application\Migrations;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\Schema;
use Metal\ProjectBundle\Doctrine\Utils;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170524135256 extends Version20170425084812
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

        $userAgents = array(
            'Mozilla/5.0 (compatible; YandexAccessibilityBot/3.0; +http://yandex.com/bots)',
            'Mozilla/5.0 (compatible; YandexDirectDyn/1.0; +http://yandex.com/bots)',
            'CCBot/2.0 (http://commoncrawl.org/faq/)',
            'Mozilla/5.0 (compatible; RukiCrawler/1.0; +http://ruki.rezko.net)',
            'Mozilla/5.0 (compatible; Findxbot/1.0; +http://www.findxbot.com)',
            'Mozilla/5.0 (compatible; oBot/2.3.1; +http://filterdb.iss.net/crawler/)',
            'Mozilla/5.0 (compatible; CarianBot/0.2; +https://carian.ru)'
        );

        foreach ($connections as $key => $connection) {
            foreach ($tables as $table) {
                Utils::checkConnection($connection);
                $result = $connection->executeUpdate(
                    sprintf('DELETE FROM `%s` WHERE user_agent IN(:user_agents)', $table),
                    array(
                        'user_agents' => $userAgents,
                    ),
                    array(
                        'user_agents' => Connection::PARAM_STR_ARRAY,
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
