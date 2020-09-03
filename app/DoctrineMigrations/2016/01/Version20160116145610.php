<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160116145610 extends AbstractMigration implements ContainerAwareInterface
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
            $this->addSql(
                "INSERT INTO newsletter (id, title, created_at, updated_at, start_at, processed_at, template)
                  VALUES (
                  11,
                  'Рассылка по скидкам на баннеры',
                  '2016-01-16 14:52:51',
                  '2016-01-16 14:52:51',
                  '2016-01-18 00:00:01',
                  null,
                  '@MetalAnnouncements/emails/banners_sale.html.twig'
                  )"
            );

            $this->addSql(
                "
                INSERT INTO `newsletter_recipient` (`newsletter_id`, `subscriber_id`)
                SELECT :newsletter_id, subscribers.ID FROM UserSend AS subscribers
                  JOIN User AS user ON user.User_ID = subscribers.user_id
                AND subscribers.is_invalid = false
                AND subscribers.deleted = false
                AND user.Checked = true
                AND subscribers.bounced_at IS NULL
                AND subscribers.subscribed_on_news = true
               ",
                array('newsletter_id' => 11),
                array('newsletter_id' => \PDO::PARAM_INT)
            );
        }
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
