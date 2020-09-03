<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Doctrine\DBAL\Connection;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20171123182107 extends AbstractMigration implements ContainerAwareInterface
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

        $exceptCompanies = array(2057183);

        $this->addSql(
            "INSERT INTO newsletter (id, title, created_at, updated_at, start_at, processed_at, template)
                  VALUES (
                  29,
                  'Чёрная Пятница на Metalloprokat.ru: только неделю все Центральные баннеры со скидкой 50%!',
                  '2017-11-23 21:00:00',
                  '2017-11-23 21:00:00',
                  '2017-11-23 21:00:00',
                  null,
                  '@MetalProject/emails/Metalloprokat/metalloprokat_23.11.2017_banner_sale.html.twig'
                  )"
        );

        $this->addSql('
                INSERT INTO `newsletter_recipient` (`newsletter_id`, `subscriber_id`)
                SELECT :newsletter_id, subscribers.ID FROM UserSend AS subscribers
                  JOIN User AS user ON user.User_ID = subscribers.user_id
                  JOIN Message75 AS company ON user.ConnectCompany = company.Message_ID
                WHERE subscribers.is_invalid = false
                AND subscribers.deleted = false
                AND user.Checked = true
                AND subscribers.bounced_at IS NULL
                AND company.Message_ID NOT IN (:companiesIds)
                ',
            array(
                'newsletter_id' => 29,
                'companiesIds' => $exceptCompanies
            ),
            array(
                'companiesIds' => Connection::PARAM_INT_ARRAY
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
