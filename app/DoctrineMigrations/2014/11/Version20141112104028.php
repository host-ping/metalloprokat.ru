<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20141112104028 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql("ALTER TABLE menu_item_closure DROP FOREIGN KEY FK_3C585A209A8FAD16");
        $this->addSql("ALTER TABLE menu_item_closure ADD CONSTRAINT FK_3C585A209A8FAD16 FOREIGN KEY (descendant) REFERENCES menu_item (id) ON DELETE CASCADE");

        $this->addSql("ALTER TABLE attribute_value_category ADD CONSTRAINT FK_7668C36012469DE2 FOREIGN KEY (category_id) REFERENCES Message73 (Message_ID)");

        $this->addSql("ALTER TABLE attribute_value_category_friend DROP FOREIGN KEY FK_5701A8D18D286381");
        $this->addSql("ALTER TABLE attribute_value_category_friend ADD CONSTRAINT FK_5701A8D18D286381 FOREIGN KEY (attribute_value_category_id) REFERENCES attribute_value_category (id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE attribute_value_category_friend DROP FOREIGN KEY FK_5701A8D1FBD39C17");
        $this->addSql("ALTER TABLE attribute_value_category_friend ADD CONSTRAINT FK_5701A8D1FBD39C17 FOREIGN KEY (attribute_value_category_friend_id) REFERENCES attribute_value_category (id) ON DELETE CASCADE");

        $this->addSql("ALTER TABLE attribute_value_category_friend_category ADD CONSTRAINT FK_E5A4C6198D286381 FOREIGN KEY (attribute_value_category_id) REFERENCES attribute_value_category (id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE category_closure ADD CONSTRAINT FK_8FDFCDAF9A8FAD16 FOREIGN KEY (descendant) REFERENCES Message73 (Message_ID) ON DELETE CASCADE");

    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
