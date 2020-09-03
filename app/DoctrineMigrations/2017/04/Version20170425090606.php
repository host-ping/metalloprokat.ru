<?php

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170425090606 extends Version20170424133229
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->skipIf(
            $this->container->getParameter('project.family') !== 'metalloprokat',
            'Миграция только для металлороката.'
        );

        $newslettersIds = array(23, 24);

        foreach ($newslettersIds as $newsletterId) {
            $this->addSql(
                "
                INSERT INTO `newsletter_recipient` (`newsletter_id`, `subscriber_id`)
                  SELECT :newsletter_id, subscribers.ID
                    FROM UserSend AS subscribers
                    LEFT JOIN User AS user ON user.User_ID = subscribers.user_id
                    LEFT JOIN Message75 AS company ON user.ConnectCompany = company.Message_ID
                    WHERE subscribers.is_invalid = false
                          AND subscribers.deleted = false
                          AND subscribers.bounced_at IS NULL
                          AND subscribers.subscribed_on_news = true
                          AND company.Message_ID = :company_id
               ",
                array(
                    'newsletter_id' => $newsletterId,
                    'company_id' => 7,
                ),
                array(
                    'newsletter_id' => \PDO::PARAM_INT,
                )
            );
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
