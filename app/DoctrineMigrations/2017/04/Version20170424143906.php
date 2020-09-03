<?php

namespace Application\Migrations;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\Schema;
use Metal\ServicesBundle\Entity\Package;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170424143906 extends Version20170424133229
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
                  24,
                  'Предлагаем расширить клиентскую базу со скидками на любой срок.',
                  '2017-04-24 16:34:51',
                  '2017-04-24 16:34:51',
                  '2017-04-24 16:35:51',
                  null,
                  '@MetalProject/emails/Metalloprokat/metalloprokat_10_05_2017_discount_two.html.twig'
                  )"
        );

        $companiesIds = $this->getCompaniesIds();

        //По просьбе менеджеров
        $companiesIds[] = 2053859;

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
                          AND (company.code_access = :package OR company.Message_ID IS NULL)
                          AND company.Message_ID NOT IN(:companies_ids)
                          AND (company.spros_end IS NULL OR company.spros_end < :current_date)
               ",
            array(
                'newsletter_id' => 24,
                'package' => Package::BASE_PACKAGE,
                'companies_ids' => $companiesIds,
                'current_date' => new \DateTime()
            ),
            array(
                'newsletter_id' => \PDO::PARAM_INT,
                'companies_ids' => Connection::PARAM_INT_ARRAY,
                'current_date' => 'date',
            )
        );
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
