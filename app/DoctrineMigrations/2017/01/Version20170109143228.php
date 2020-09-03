<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170109143228 extends AbstractMigration
{
    /**
     * @param Schema $schema
     * @throws \Doctrine\DBAL\Schema\SchemaException
     */
    public function up(Schema $schema)
    {
        $tablesName = array(
            'foursquare_place',
            'foursquare_place_by_coord_radius',
            'grab_order',
            'instagram_account',
            'instagram_account_events',
            'instagram_account_location',
            'instagram_account_location_group',
            'instagram_account_tag',
            'instagram_account_tag_comment',
            'instagram_liker',
            'instagram_profile',
            'instagram_stats',
            'instagram_stop_word',
            'location',
            'location_group',
            'order_instagram_profile',
            'order_keyword',
            'order_location',
            'order_stop_word',
            'order_tag',
            'order_value_cursor',
            'profile_to_parse'
        );

        $schemaManager = $this->connection->getSchemaManager();
        foreach ($tablesName as $tableName) {
            if ($schema->hasTable($tableName)) {
                $table = $schema->getTable($tableName);
                foreach ($table->getForeignKeys() as $foreignKey) {
                    $schemaManager->dropForeignKey($foreignKey, $table);
                }
            }
        }

        foreach ($tablesName as $tableName) {
            if ($schema->hasTable($tableName)) {
                $schema->dropTable($tableName);
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
