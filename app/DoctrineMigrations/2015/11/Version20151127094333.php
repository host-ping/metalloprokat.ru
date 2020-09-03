<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;use Symfony\Component\DependencyInjection\ContainerAwareInterface;use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20151127094333 extends AbstractMigration implements ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function up(Schema $schema)
    {
        if ($this->container->getParameter('project.family') === 'metalloprokat') {
            $this->addSql("UPDATE newsletter SET template = '@MetalProject/emails/Metalloprokat/no_client_newsletter.html.twig' WHERE id = 10");
        }

        if ($this->container->getParameter('project.family') === 'product') {
            $this->addSql(
            "
            REPLACE INTO newsletter (id, title, created_at, updated_at, start_at, processed_at, template)
              VALUES
              (:newsletter_id, 'Больше бонус - больше сделок __ от Product.ru', '2015-11-24 17:02:32', '2015-11-25 10:55:31',
               '2015-11-24 16:00:02', NULL, '@MetalProject/emails/Product/no_client_newsletter.html.twig')
            ",
            array('newsletter_id' => 1),
            array('newsletter_id' => \PDO::PARAM_INT)
            );

            $this->addSql(
               "
                INSERT INTO `newsletter_recipient` (`newsletter_id`, `subscriber_id`)
                SELECT :newsletter_id, subscribers.ID FROM UserSend AS subscribers
                  JOIN User AS user ON user.User_ID = subscribers.user_id
                  LEFT JOIN Message75 AS company ON user.ConnectCompany = company.Message_ID
                WHERE company.spros_end IS NULL
                AND subscribers.subscribed_on_news = true
                AND subscribers.is_invalid = false
                AND subscribers.deleted = false
                AND NOT EXISTS(
                  SELECT * FROM Message106 AS package WHERE company.Message_ID = package.company_id AND company.promocode_id IS NULL
                );
               ",
               array('newsletter_id' => 1),
               array('newsletter_id' => \PDO::PARAM_INT)
            );
        }
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
