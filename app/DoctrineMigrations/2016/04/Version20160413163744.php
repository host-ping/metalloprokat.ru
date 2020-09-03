<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Metal\ServicesBundle\Entity\Package;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160413163744 extends AbstractMigration implements ContainerAwareInterface
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
            $this->addSql("
              INSERT INTO newsletter (id, title, created_at, updated_at, start_at, processed_at, template)
              VALUES (:newsletter_id, 'Подарок от Metalloprokat.ru внутри письма', '2016-04-13 16:05:10',
              '2016-04-13 16:05:10', '2016-04-13 16:05:10', NULL,
              '@MetalProject/emails/Metalloprokat/metalloprokat_stroy_ru_full_package_bonus.html.twig')",
                array('newsletter_id' => 15),
                array('newsletter_id' => \PDO::PARAM_INT)
            );

            $this->addSql(
                "
                INSERT INTO `newsletter_recipient` (`newsletter_id`, `subscriber_id`)
                    SELECT :newsletter_id, subscribers.ID FROM UserSend AS subscribers
                    JOIN User AS users ON users.User_ID = subscribers.user_id
                    JOIN Message75 AS company ON company.Message_ID = users.ConnectCompany
                    WHERE subscribers.is_invalid = false
                    AND company.code_access = :package
                    AND subscribers.deleted = false
                    AND subscribers.bounced_at IS NULL
                    AND subscribers.subscribed_on_news = true
               ",
                array(
                    'newsletter_id' => 15,
                    'package' => Package::FULL_PACKAGE
                ),
                array(
                    'newsletter_id' => \PDO::PARAM_INT,
                    'package' => \PDO::PARAM_INT,
                )
            );
        }
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}