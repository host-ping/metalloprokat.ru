<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170517135131 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql(
            'DELETE FROM newsletter_recipient WHERE newsletter_recipient.newsletter_id IN (
                SELECT t.newsletter_id FROM (
                    SELECT n_r.newsletter_id FROM newsletter_recipient n_r LEFT JOIN newsletter n ON n.id = n_r.newsletter_id WHERE n.id IS NULL
                ) AS t
            )
        '
        );

        $this->addSql(
            'DELETE FROM stats_announcement WHERE stats_announcement.announcement_id IN (
                SELECT t.announcement_id FROM (
                    SELECT s_a.announcement_id FROM stats_announcement s_a LEFT JOIN announcement a ON a.id = s_a.announcement_id WHERE a.id IS NULL
                ) AS t
            )
        '
        );

        $this->addSql(
            'DELETE FROM user_city WHERE user_city.user_id IN (
                SELECT t.user_id FROM (
                    SELECT u_s.user_id FROM user_city u_s LEFT JOIN User u ON u.User_ID = u_s.user_id WHERE u.User_ID IS NULL
                ) AS t
            )
        '
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
