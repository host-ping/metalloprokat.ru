<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170816135224 extends AbstractMigration implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->skipIf(
            $this->container->getParameter('project.family') !== 'metalloprokat',
            'Миграция только для металлороката.'
        );

        $this->addSql(
            "INSERT INTO newsletter (id, title, created_at, updated_at, start_at, processed_at, template)
                  VALUES (
                  27,
                  'УСПЕЙ ПОДКЛЮЧИТЬ РАЗМЕЩЕНИЕ ПО ВСЕЙ РОССИИ ПО СТАРЫМ ЦЕНАМ',
                  '2017-08-16 16:55:51',
                  '2017-08-16 16:55:51',
                  '2017-08-16 16:55:51',
                  null,
                  '@MetalProject/emails/Metalloprokat/metalloprokat_price_increase_2017.html.twig'
                  )"
        );

        $this->addSql(
            "
                INSERT INTO `newsletter_recipient` (`newsletter_id`, `subscriber_id`)
                    SELECT :newsletter_id, subscribers.ID FROM UserSend AS subscribers
                    JOIN User AS users ON users.User_ID = subscribers.user_id
                    JOIN Message75 AS company ON company.Message_ID = users.ConnectCompany
                    WHERE subscribers.is_invalid = false
                    AND subscribers.deleted = false
                    AND subscribers.bounced_at IS NULL
                    AND subscribers.subscribed_on_news = true
               ",
            array(
                'newsletter_id' => 27
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
