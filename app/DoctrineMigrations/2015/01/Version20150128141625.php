<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150128141625 extends AbstractMigration implements ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function up(Schema $schema)
    {
        $this->addSql('
            CREATE TABLE IF NOT EXISTS `grabber_sites` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
              `host` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
              `service` VARCHAR(255) COLLATE utf8_unicode_ci NOT NULL,
              `login` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
              `password` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
              `created_at` datetime NOT NULL,
              `is_enabled` tinyint(1) NOT NULL DEFAULT 0,
              PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
        ');

        $this->addSql('
           CREATE TABLE IF NOT EXISTS `grabber_parsed_demand` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `demand_id` int(11) DEFAULT NULL,
              `parsed_demand_id` int(11) NOT NULL,
              `url` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
              `site_id` int(11) DEFAULT NULL,
              `created_at` datetime NOT NULL,
              PRIMARY KEY (`id`),
              UNIQUE KEY `UNIQ_B85798745D022E59` (`demand_id`),
              KEY `IDX_B8579874F6BD1646` (`site_id`),
              CONSTRAINT `FK_B8579874F6BD1646` FOREIGN KEY (`site_id`) REFERENCES `grabber_sites` (`id`),
              CONSTRAINT `FK_B85798745D022E59` FOREIGN KEY (`demand_id`) REFERENCES `demand` (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
        ');

        if ($this->container->getParameter('project.family') == 'metalloprokat') {
            $this->addSql("INSERT INTO grabber_sites (id, title, created_at, is_enabled, host, service, login, password) VALUES (1, 'METAL100.ru', '2015-01-28 13:20:01', 0, 'http://metal100.ru', 'metal100_ru', 'lucchese12@ya.ru', '112233');");
            $this->addSql("INSERT INTO grabber_sites (id, title, created_at, is_enabled, host, service, login, password) VALUES (2, 'www.trade-inox.ru', '2015-01-28 13:20:50', 1, 'http://www.trade-inox.ru', 'trade_inox', 'lucchese12', '112233');");
            $this->addSql("INSERT INTO grabber_sites (id, title, created_at, is_enabled, host, service, login, password) VALUES (3, 'armtorg.ru', '2015-01-28 13:20:55', 1, 'http://armtorg.ru', 'armtorg', '', '');");
            $this->addSql("INSERT INTO grabber_sites (id, title, created_at, is_enabled, host, service, login, password) VALUES (4, 'www.metaprom.ru', '2015-01-28 13:21:02', 1, 'http://www.metaprom.ru', 'metaprom', '', '');");
        }
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
