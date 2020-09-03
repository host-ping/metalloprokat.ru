<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Metal\SupportBundle\Entity\Topic;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150205130536 extends AbstractMigration
{
    public function up(Schema $schema)
    {

        $this->addSql("ALTER TABLE support_topic ADD sent_from SMALLINT NOT NULL, ADD user_name VARCHAR(255) NOT NULL, ADD email VARCHAR(40) NOT NULL");
        $this->addSql("ALTER TABLE support_topic CHANGE title title VARCHAR(255) DEFAULT NULL, CHANGE user_name user_name VARCHAR(255) DEFAULT NULL, CHANGE email email VARCHAR(40) DEFAULT NULL");
        $this->addSql("ALTER TABLE support_topic CHANGE description description VARCHAR(8000) NOT NULL");
        $this->addSql("ALTER TABLE support_topic ADD CONSTRAINT FK_6FFAC8C3979B1AD6 FOREIGN KEY (company_id) REFERENCES Message75 (Message_ID)");
        $this->addSql("ALTER TABLE support_topic ADD CONSTRAINT FK_6FFAC8C3BDFC9701 FOREIGN KEY (last_answer_id) REFERENCES support_answer (id)");

        $this->addSql("UPDATE support_topic SET sent_from = :private_office", array('private_office' => Topic::SOURCE_PRIVATE_OFFICE));

        $this->addSql("
            INSERT INTO
              support_topic (email, description, user_name, created_at, resolved_at, resolved_by, sent_from, last_answer_at)
              SELECT
                feedback.Email,
                feedback.Body,
                feedback.Name,
                feedback.Created,
                feedback.processed_at,
                feedback.processed_by,
                :corp_site,
                feedback.Created
              FROM
                Message178 AS feedback;
        ", array('corp_site' => Topic::SOURCE_CORPSITE));


        $this->addSql("UPDATE agreement_template as at SET at.content = REPLACE(at.content, 'MetalCorpsiteBundle:Feedback', 'MetalSupportBundle:TopicCorpSite');");

        $this->addSql("UPDATE agreement_template as at SET at.content = REPLACE(at.content, 'feedback-popup', 'topic-popup');");

    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
