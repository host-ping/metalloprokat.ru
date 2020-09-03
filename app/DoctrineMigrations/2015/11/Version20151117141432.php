<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20151117141432 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql("ALTER TABLE announcement CHANGE show_everywhere show_everywhere TINYINT(1) DEFAULT '1' NOT NULL");
        $this->addSql("UPDATE announcement SET show_everywhere = 1");
        $this->addSql("ALTER TABLE announcement ADD CONSTRAINT FK_4DB9D91C979B1AD6 FOREIGN KEY (company_id) REFERENCES Message75 (Message_ID)");
        $this->addSql("CREATE INDEX IDX_4DB9D91C9F2C3FAB ON announcement (zone_id)");
        $this->addSql("ALTER TABLE announcement ADD CONSTRAINT FK_4DB9D91C9F2C3FAB FOREIGN KEY (zone_id) REFERENCES announcement_zone (id)");

        $this->addSql("CREATE INDEX IDX_97D8BF4F9F2C3FAB ON announcement_order (zone_id)");
        $this->addSql("ALTER TABLE announcement_order ADD CONSTRAINT FK_97D8BF4F9F2C3FAB FOREIGN KEY (zone_id) REFERENCES announcement_zone (id)");
        $this->addSql("ALTER TABLE announcement_order ADD CONSTRAINT FK_97D8BF4FA76ED395 FOREIGN KEY (user_id) REFERENCES User (User_ID)");
        $this->addSql("ALTER TABLE announcement_order ADD CONSTRAINT FK_97D8BF4F888A646A FOREIGN KEY (processed_by) REFERENCES User (User_ID)");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
