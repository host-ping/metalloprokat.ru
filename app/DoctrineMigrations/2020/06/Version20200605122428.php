<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20200605122428 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs

        $sql = 'SELECT * FROM `seo_template` WHERE `meta_title` like "%| {{ host }}%" ';
        $resTitles = $this->connection->fetchAll($sql);
        foreach ($resTitles as $title) {
            $newTitle = preg_replace('/\| {{ host }}/', '', $title['meta_title']);
            $sql = 'UPDATE `seo_template` SET `meta_title`= ? WHERE id = ? ';
            $this->connection->executeUpdate($sql, array(trim($newTitle), $title['id']));
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
