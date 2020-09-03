<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170516072901 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql('
          ALTER TABLE stats_category
          CHANGE demands_count demands_count SMALLINT UNSIGNED DEFAULT 0 NOT NULL,
          CHANGE demands_processed_count demands_processed_count SMALLINT UNSIGNED DEFAULT 0 NOT NULL,
          CHANGE callback_count callback_count SMALLINT UNSIGNED DEFAULT 0 NOT NULL,
          CHANGE callbacks_processed_count callbacks_processed_count SMALLINT UNSIGNED DEFAULT 0 NOT NULL,
          CHANGE reviews_count reviews_count SMALLINT UNSIGNED DEFAULT 0 NOT NULL,
          CHANGE complaints_count complaints_count SMALLINT UNSIGNED DEFAULT 0 NOT NULL,
          CHANGE reviews_phones_count reviews_phones_count SMALLINT UNSIGNED DEFAULT 0 NOT NULL,
          CHANGE website_visits_count website_visits_count SMALLINT UNSIGNED DEFAULT 0 NOT NULL,
          CHANGE reviews_products_count reviews_products_count SMALLINT UNSIGNED DEFAULT 0 NOT NULL,
          CHANGE complaints_processed_count complaints_processed_count SMALLINT UNSIGNED DEFAULT 0 NOT NULL,
          CHANGE demands_views_count demands_views_count SMALLINT UNSIGNED DEFAULT 0 NOT NULL,
          CHANGE demands_to_favorite_count demands_to_favorite_count SMALLINT UNSIGNED DEFAULT 0 NOT NULL,
          CHANGE demands_answers_count demands_answers_count SMALLINT UNSIGNED DEFAULT 0 NOT NULL,
          CHANGE show_products_count show_products_count INT UNSIGNED DEFAULT 0 NOT NULL
        ');

        $this->addSql('
          ALTER TABLE stats_city
          CHANGE demands_count demands_count SMALLINT UNSIGNED DEFAULT 0 NOT NULL,
          CHANGE demands_processed_count demands_processed_count SMALLINT UNSIGNED DEFAULT 0 NOT NULL,
          CHANGE callback_count callback_count SMALLINT UNSIGNED DEFAULT 0 NOT NULL,
          CHANGE callbacks_processed_count callbacks_processed_count SMALLINT UNSIGNED DEFAULT 0 NOT NULL,
          CHANGE reviews_count reviews_count SMALLINT UNSIGNED DEFAULT 0 NOT NULL,
          CHANGE complaints_count complaints_count SMALLINT UNSIGNED DEFAULT 0 NOT NULL,
          CHANGE complaints_processed_count complaints_processed_count SMALLINT UNSIGNED DEFAULT 0 NOT NULL,
          CHANGE reviews_phones_count reviews_phones_count SMALLINT UNSIGNED DEFAULT 0 NOT NULL,
          CHANGE website_visits_count website_visits_count SMALLINT UNSIGNED DEFAULT 0 NOT NULL,
          CHANGE reviews_products_count reviews_products_count SMALLINT UNSIGNED DEFAULT 0 NOT NULL,
          CHANGE demands_views_count demands_views_count SMALLINT UNSIGNED DEFAULT 0 NOT NULL,
          CHANGE demands_to_favorite_count demands_to_favorite_count SMALLINT UNSIGNED DEFAULT 0 NOT NULL,
          CHANGE demands_answers_count demands_answers_count SMALLINT UNSIGNED DEFAULT 0 NOT NULL,
          CHANGE show_products_count show_products_count INT UNSIGNED DEFAULT 0 NOT NULL
        ');

        $this->addSql('
          ALTER TABLE stats_daily
          CHANGE demands_count demands_count SMALLINT UNSIGNED DEFAULT 0 NOT NULL,
          CHANGE demands_processed_count demands_processed_count SMALLINT UNSIGNED DEFAULT 0 NOT NULL,
          CHANGE callback_count callback_count SMALLINT UNSIGNED DEFAULT 0 NOT NULL,
          CHANGE callbacks_processed_count callbacks_processed_count SMALLINT UNSIGNED DEFAULT 0 NOT NULL,
          CHANGE reviews_count reviews_count SMALLINT UNSIGNED DEFAULT 0 NOT NULL,
          CHANGE complaints_count complaints_count SMALLINT UNSIGNED DEFAULT 0 NOT NULL,
          CHANGE complaints_processed_count complaints_processed_count SMALLINT UNSIGNED DEFAULT 0 NOT NULL,
          CHANGE reviews_phones_count reviews_phones_count SMALLINT UNSIGNED DEFAULT 0 NOT NULL,
          CHANGE website_visits_count website_visits_count SMALLINT UNSIGNED DEFAULT 0 NOT NULL,
          CHANGE reviews_products_count reviews_products_count SMALLINT UNSIGNED DEFAULT 0 NOT NULL,
          CHANGE demands_views_count demands_views_count SMALLINT UNSIGNED DEFAULT 0 NOT NULL,
          CHANGE demands_to_favorite_count demands_to_favorite_count SMALLINT UNSIGNED DEFAULT 0 NOT NULL,
          CHANGE demands_answers_count demands_answers_count SMALLINT UNSIGNED DEFAULT 0 NOT NULL,
          CHANGE show_products_count show_products_count INT UNSIGNED DEFAULT 0 NOT NULL
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
