<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Connection;
use Metal\ServicesBundle\Entity\Package;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20200818153025 extends AbstractMigration implements ContainerAwareInterface
{
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
        if ($this->container->getParameter('project.family') === 'metalloprokat') {
            $this->addSql("
              INSERT INTO newsletter (id, title, created_at, updated_at, start_at, processed_at, template)
              VALUES (:newsletter_id, 'Metalloprokat.ru ПОВЫШАЕТ ЦЕНЫ НА УСЛУГИ С 01.09.2020 - по подключенным', '2020-08-18 12:28:13',
              '2020-08-18 12:28:13', '2020-08-18 12:28:13', NULL,
              '@MetalProject/emails/Metalloprokat/metalloprokat_2020_price_change.html.twig')",
                array('newsletter_id' => 58),
                array('newsletter_id' => \PDO::PARAM_INT)
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
                          AND (company.code_access = :package OR company.code_access = :packageFull)                        
               ",
                array(
                    'newsletter_id' => 58,
                    'package' => Package::ADVANCED_PACKAGE,
                    'packageFull' => Package::FULL_PACKAGE

                ),
                array(
                    'newsletter_id' => \PDO::PARAM_INT,
                    'companiesIds' => Connection::PARAM_INT_ARRAY
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
