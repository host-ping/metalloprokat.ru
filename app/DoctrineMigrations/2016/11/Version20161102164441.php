<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20161102164441 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql('ALTER TABLE support_topic ADD receiver_id INT DEFAULT NULL');
        $this->addSql('CREATE INDEX IDX_6FFAC8C3CD53EDB6 ON support_topic (receiver_id)');
        $this->addSql('ALTER TABLE support_topic ADD CONSTRAINT FK_6FFAC8C3CD53EDB6 FOREIGN KEY (receiver_id) REFERENCES User (User_ID)');
        $this->addSql('ALTER TABLE support_answer ADD CONSTRAINT FK_5227AAD5F675F31B FOREIGN KEY (author_id) REFERENCES User (User_ID);');

        $this->addSql('UPDATE support_topic SET receiver_id = author_id');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
