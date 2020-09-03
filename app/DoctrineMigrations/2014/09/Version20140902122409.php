<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20140902122409 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is autogenerated, please modify it to your needs

        $this->addSql('CREATE TABLE IF NOT EXISTS `parameters_types_priorities` (
                          `id` tinyint(4) NOT NULL,
                          `priority` tinyint(4) NOT NULL,
                          `title` varchar(255) NOT NULL,
                          PRIMARY KEY (`id`),
                          UNIQUE KEY `priority` (`priority`)
                        ) ENGINE=InnoDB DEFAULT CHARSET=cp1251;');

        $this->addSql('INSERT INTO `parameters_types_priorities` (`id`, `priority`, `title`) VALUES
                        (1, 2, \'Марка\'),
                        (2, 3, \'ГОСТ\'),
                        (3, 1, \'Размер\'),
                        (4, 4, \'Класс\'),
                        (5, 5, \'Тип\'),
                        (6, 6, \'Вид\');');

    }

    public function down(Schema $schema)
    {
        // this down() migration is autogenerated, please modify it to your needs

    }
}