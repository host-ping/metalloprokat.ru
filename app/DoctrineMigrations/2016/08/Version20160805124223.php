<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160805124223 extends AbstractMigration implements ContainerAwareInterface
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
        if ($this->container->getParameter('project.family') === 'product') {
            $this->addSql("
            INSERT INTO newsletter (id, title, created_at, updated_at, start_at, processed_at, template)
              VALUES (:newsletter_id, 'Успейте подключить доступ к Product.ru по старым ценам в августе!', '2016-08-05 12:28:13',
              '2016-08-08 08:00:13', '2016-08-08 09:00:13', NULL,
              '@MetalProject/emails/Product/product_price_increase.html.twig')",
                array('newsletter_id' => 3),
                array('newsletter_id' => \PDO::PARAM_INT)
            );

            $this->addSql(
                "
                INSERT INTO `newsletter_recipient` (`newsletter_id`, `subscriber_id`)
                    SELECT :newsletter_id, subscribers.ID FROM UserSend AS subscribers
                    WHERE subscribers.is_invalid = false
                    AND subscribers.deleted = false
                    AND subscribers.bounced_at IS NULL
                    AND subscribers.subscribed_on_news = true
               ",
                array('newsletter_id' => 3),
                array('newsletter_id' => \PDO::PARAM_INT)
            );
        }
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
