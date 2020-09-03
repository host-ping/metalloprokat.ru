<?php

namespace Application\Migrations;


use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20161207171103 extends AbstractMigration implements ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

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
                  20,
                  'Успейте разместить баннер Премиум по старой стоимости на Metalloprokat.ru !',
                  '2016-12-07 14:52:51',
                  '2016-12-07 14:52:51',
                  '2016-12-08 14:52:51',
                  null,
                  '@MetalProject/emails/Metalloprokat/metalloprokat_07.12.2016_banner_sale.html.twig'
              )"
        );


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
                          AND NOT EXISTS(
                            SELECT * FROM newsletter_recipient AS nr 
                              WHERE nr.newsletter_id = :prev_newsletter_id
                              AND nr.subscriber_id = subscribers.ID
                          )
               ",
            array(
                'newsletter_id' => 20,
                'prev_newsletter_id' => 19
            ),
            array(
                'newsletter_id' => \PDO::PARAM_INT
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
