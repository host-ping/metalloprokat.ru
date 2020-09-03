<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20161028121027 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql('
                    ALTER TABLE instagram_account
              ADD parse_photos_tag_at DATETIME NOT NULL,
              ADD parse_photos_geo_tag_at DATETIME NOT NULL,
              ADD parse_photos_likers_at DATETIME NOT NULL,
              ADD like_all_at DATETIME NOT NULL,
              ADD like_likers_at DATETIME NOT NULL,
              ADD comments_at DATETIME NOT NULL,
              ADD refresh_stats_at DATETIME NOT NULL,
              ADD refresh_likers_at DATETIME NOT NULL,
              ADD get_likers_at DATETIME NOT NULL,
              ADD un_following_at DATETIME NOT NULL,
              ADD actualize_tag_at DATETIME NOT NULL;
        ');

    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
