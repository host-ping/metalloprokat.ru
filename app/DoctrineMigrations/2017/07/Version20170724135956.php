<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Metal\ServicesBundle\Entity\Package;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170724135956 extends AbstractMigration implements ContainerAwareInterface
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
                  25,
                  'БАННЕР В ПОДАРОК на Metalloprokat.ru',
                  '2017-07-24 14:52:51',
                  '2017-07-24 14:52:51',
                  '2017-07-24 17:52:51',
                  null,
                  '@MetalProject/emails/Metalloprokat/announcement_as_a_gift.html.twig'
                  )"
        );

        $this->addSql(
            "
                INSERT INTO `newsletter_recipient` (`newsletter_id`, `subscriber_id`)
                    SELECT :newsletter_id, subscribers.ID FROM UserSend AS subscribers
                    JOIN User AS users ON users.User_ID = subscribers.user_id
                    JOIN Message75 AS company ON company.Message_ID = users.ConnectCompany
                    WHERE subscribers.is_invalid = false
                    AND company.code_access = :code_access
                    AND (company.spros_end IS NULL OR company.spros_end < :current_date)
                    AND subscribers.deleted = false
                    AND subscribers.bounced_at IS NULL
                    AND subscribers.subscribed_on_news = true
               ",
            array(
                'newsletter_id' => 25,
                'code_access' => Package::BASE_PACKAGE,
                'current_date' => new \DateTime()
            ),
            array(
                'newsletter_id' => \PDO::PARAM_INT,
                'code_access' => \PDO::PARAM_INT,
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
