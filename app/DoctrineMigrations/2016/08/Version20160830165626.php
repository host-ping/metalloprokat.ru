<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160830165626 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // Помечаем решенными все обращения в службу поддержки от Натальи, которые старше 2015 года
        $year = new \DateTime('2015-01-01 00:00:00');
        $userId = (int)$this->connection->fetchColumn("SELECT User_ID FROM User WHERE Email = 'tashka7@gmail.com'");

        $this->addSql('UPDATE support_topic SET resolved_at = :now, resolved_by = :user_id
            WHERE created_at < :year AND resolved_at IS NULL
            ',
            array('user_id' => $userId, 'year' => $year, 'now' => new \DateTime()),
            array('year' => 'datetime', 'now' => 'datetime')
         );
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
