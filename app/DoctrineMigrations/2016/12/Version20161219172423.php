<?php

namespace Application\Migrations;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20161219172423 extends Version20161206153340
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

        $this->addSql(
            "INSERT INTO newsletter (id, title, created_at, updated_at, start_at, processed_at, template)
                  VALUES (
                  21,
                  'Осталось 10 дней до конца акции \"скидка 50% на год от Metalloprokat.ru\"',
                  '2016-12-19 14:52:51',
                  '2016-12-19 14:52:51',
                  '2016-12-20 09:30:00',
                  null,
                  '@MetalProject/emails/Metalloprokat/metalloprokat_06_12_2016_fifty_percent_discount.html.twig'
                  )"
        );

        $companiesIds = $this->getCompaniesIds();

        do {
            $spliceCompaniesIds = array_splice($companiesIds, 0 , 100);
            $this->addSql('
                INSERT INTO `newsletter_recipient` (`newsletter_id`, `subscriber_id`)
                SELECT :newsletter_id, subscribers.ID FROM UserSend AS subscribers
                  JOIN User AS user ON user.User_ID = subscribers.user_id
                  JOIN Message75 AS company ON user.ConnectCompany = company.Message_ID
                WHERE subscribers.is_invalid = false
                AND subscribers.deleted = false
                AND user.Checked = true
                AND subscribers.bounced_at IS NULL
                AND subscribers.subscribed_on_news = true
                AND company.Message_ID IN (:companiesIds)
                ',
                array(
                    'newsletter_id' => 21,
                    'companiesIds' => $spliceCompaniesIds
                ),
                array(
                    'companiesIds' => Connection::PARAM_INT_ARRAY
                )
            );
        } while ($companiesIds);
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
