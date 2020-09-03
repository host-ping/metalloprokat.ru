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
class Version20170307133641 extends AbstractMigration implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->skipIf(
            $this->container->getParameter('project.family') !== 'product',
            'Поздравление с 8 марта. Миграция только для продукта.'
        );

        $this->addSql(
            "INSERT INTO newsletter (id, title, created_at, updated_at, start_at, processed_at, template)
                  VALUES (
                  5,
                  'Поздравление с 8 марта',
                  '2017-03-07 14:52:51',
                  '2016-03-07 14:52:51',
                  '2016-03-07 17:52:51',
                  null,
                  '@MetalProject/emails/Product/congratulations_on_march_8.html.twig'
                  )"
        );

        $this->addSql(
            "
                INSERT INTO `newsletter_recipient` (`newsletter_id`, `subscriber_id`)
                  SELECT :newsletter_id, subscribers.ID
                    FROM UserSend AS subscribers
                    JOIN User AS user ON user.User_ID = subscribers.user_id
                    JOIN Message75 AS company ON user.ConnectCompany = company.Message_ID
                    WHERE subscribers.is_invalid = false
                          AND (company.code_access <> :package OR company.spros_end > :currentDate)
                          AND subscribers.deleted = false
                          AND subscribers.bounced_at IS NULL
                          AND subscribers.subscribed_on_news = true
               ",
            array(
                'newsletter_id' => 5,
                'package' => Package::BASE_PACKAGE,
                'currentDate' => new \DateTime()
            ),
            array(
                'newsletter_id' => \PDO::PARAM_INT,
                'package' => Package::BASE_PACKAGE,
                'currentDate' => 'date',
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
