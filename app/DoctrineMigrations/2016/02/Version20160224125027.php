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
class Version20160224125027  extends AbstractMigration implements ContainerAwareInterface
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
                  12,
                  'Рассылка по скидкам по металлопрокату',
                  '2016-02-24 12:55:51',
                  '2016-02-24 12:55:51',
                  '2016-02-24 20:00:00',
                  null,
                  '@MetalProject/emails/Metalloprokat/metalloprokat_sale_24_02_2016.html.twig'
                  )"
            );

            /*
                1. У пользователя есть компания
                2. У компании нету пакета (code_access = 0)
                3. Подписка не удалена
                4. Емейл подписчика валидный
                5. Пользователь подписан на новости
            */
            $this->addSql(
                "
                INSERT INTO `newsletter_recipient` (`newsletter_id`, `subscriber_id`)
                SELECT
                  :newsletter_id,
                  subscribers.ID
                FROM UserSend AS subscribers
                JOIN User AS user ON user.User_ID = subscribers.user_id
                JOIN Message75 AS company ON user.ConnectCompany = company.Message_ID
                WHERE subscribers.deleted = FALSE
                  AND subscribers.is_invalid = FALSE
                  AND subscribers.subscribed_on_news = true
                  AND company.code_access = :code_access
               ",
                array('newsletter_id' => 12, 'code_access' => Package::BASE_PACKAGE),
                array('newsletter_id' => \PDO::PARAM_INT, 'code_access' => \PDO::PARAM_INT)
            );
        }
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}