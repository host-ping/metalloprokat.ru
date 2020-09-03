<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20151126131627 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql('ALTER TABLE content_question CHANGE user_id user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE content_answer DROP FOREIGN KEY FK_B2EB3A89CF775AE8');
        $this->addSql('DROP INDEX IDX_B2EB3A89CF775AE8 ON content_answer');
        $this->addSql('ALTER TABLE content_answer ADD user_id INT DEFAULT NULL, CHANGE answer_parent parent_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE content_answer ADD CONSTRAINT FK_B2EB3A89727ACA70 FOREIGN KEY (parent_id) REFERENCES content_answer (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_B2EB3A89A76ED395 ON content_answer (user_id)');
        $this->addSql('CREATE INDEX IDX_B2EB3A89727ACA70 ON content_answer (parent_id)');

    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
