<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20141106164218 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql(
            "CREATE TABLE attribute_category (attribute_id INT NOT NULL, category_id INT NOT NULL, INDEX IDX_9ACE8331B6E62EFA (attribute_id), INDEX IDX_9ACE833112469DE2 (category_id), UNIQUE INDEX UNIQ_attribute_category (attribute_id, category_id), PRIMARY KEY(attribute_id, category_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB"
        );

        $this->addSql("ALTER TABLE attribute_category ADD CONSTRAINT FK_9ACE8331B6E62EFA FOREIGN KEY (attribute_id) REFERENCES attribute (id)");
        $this->addSql("ALTER TABLE attribute_category ADD CONSTRAINT FK_9ACE833112469DE2 FOREIGN KEY (category_id) REFERENCES Message73 (Message_ID)");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
