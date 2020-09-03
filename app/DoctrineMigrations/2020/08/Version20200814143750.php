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
class Version20200814143750 extends AbstractMigration implements ContainerAwareInterface
{
       /**
     * @var ContainerInterface
     */
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
        // this up() migration is auto-generated, please modify it to your needs

        if ($this->container->getParameter('project.family') === 'metalloprokat') {
            $this->addSql("
              INSERT INTO newsletter (id, title, created_at, updated_at, start_at, processed_at, template)
              VALUES (:newsletter_id, 'ПОДКЛЮЧИТЕ ДОСТУП НА Metalloprokat.ru ДО ПОВЫШЕНИЯ ЦЕН', '2020-05-06 12:28:13',
              '2020-08-14 12:28:13', '2020-08-14 12:28:13', NULL,
              '@MetalProject/emails/Metalloprokat/metalloprokat_2020_august_price_increase.html.twig')",
                array('newsletter_id' => 57),
                array('newsletter_id' => \PDO::PARAM_INT)
            );

            $companiesIds = $this->getCompaniesIds();

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
                          AND (company.code_access = :package OR company.Message_ID IS NULL)
                          AND company.Message_ID NOT IN (:companiesIds)
                          AND user.Email not like '%mail.ru%'
                          AND user.Email not like '%list.ru%'
                          AND user.Email not like '%bk.ru%'
               ",
                array(
                    'newsletter_id' => 57,
                    'package' => Package::BASE_PACKAGE,
                    'companiesIds' => $companiesIds
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

    private function getCompaniesIds()
    {
        return array(
            2033425,
2064380,
2061246,
2043904,
2064858,
2061791,
2061245,
2064761,
2047327,
2058032,
29017,
27868,
2064476,
31716,
2065092,
2052857,
2046690,
2047635,
2065048,
2028863,
2060998,
2064647,
2057171,
2063269,
25633,
26085,
1611,
23202,
2056109,
2041123,
2064288,
2065284,
2059724,
2048248,
2062329,
2050107,
8492,
47241,
30865,
2034598,
2065574,
2058833,
5546,
2063037,
2040589,
19742,
2045517,
2040894,
2050777,
2065476,
25438,
2065623,
2065866,
2065169,
2057874,
2044259,
2032127,
2052895
        );
    }
}
