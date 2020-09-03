<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20141111142520 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql("ALTER TABLE menu_item DROP FOREIGN KEY FK_D754D5503D8E604F");
        $this->addSql("ALTER TABLE menu_item ADD CONSTRAINT FK_D754D5503D8E604F FOREIGN KEY (parent) REFERENCES menu_item (id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE Message73 ADD CONSTRAINT FK_6281B256E389853A FOREIGN KEY (cat_parent) REFERENCES Message73 (Message_ID) ON DELETE CASCADE");

        $this->addSql("ALTER TABLE attribute_value_category DROP FOREIGN KEY FK_7668C36065A22152");
        $this->addSql("ALTER TABLE attribute_value_category ADD CONSTRAINT FK_7668C36065A22152 FOREIGN KEY (attribute_value_id) REFERENCES attribute_value (id) ON DELETE CASCADE");

        $this->addSql(
            "DELETE FROM attribute_value_category_friend_category
                WHERE NOT EXISTS(
                    SELECT Message_ID
                    FROM Message73 AS c
                    WHERE c.Message_ID = category_id
                )"
        );

        $this->addSql("ALTER TABLE attribute_value_category_friend_category ADD CONSTRAINT FK_E5A4C61912469DE2 FOREIGN KEY (category_id) REFERENCES Message73 (Message_ID) ON DELETE CASCADE");

        $this->addSql("CREATE INDEX IDX_E5A4C61912469DE2 ON attribute_value_category_friend_category (category_id)");

        $this->addSql("ALTER TABLE category_closure ADD CONSTRAINT FK_8FDFCDAFB4465BB FOREIGN KEY (ancestor) REFERENCES Message73 (Message_ID) ON DELETE CASCADE");

        $this->addSql("ALTER TABLE menu_item_closure DROP FOREIGN KEY FK_3C585A20B4465BB");
        $this->addSql("ALTER TABLE menu_item_closure ADD CONSTRAINT FK_3C585A20B4465BB FOREIGN KEY (ancestor) REFERENCES menu_item (id) ON DELETE CASCADE");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
