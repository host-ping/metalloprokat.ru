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
class Version20200325131358 extends AbstractMigration implements ContainerAwareInterface
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
              VALUES (:newsletter_id, 'Металлопрокат.ру снижает цены на 30% на все услуги', '2020-03-25 12:28:13',
              '2020-03-25 12:28:13', '2020-03-25 12:28:13', NULL,
              '@MetalProject/emails/Metalloprokat/metalloprokat_2019_hb_discount.html.twig')",
                array('newsletter_id' => 54),
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
               ",
                array(
                    'newsletter_id' => 54,
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
            6303,
            8388,
            9432,
            12622,
            26486,
            27868,
            29017,
            29686,
            31523,
            31716,
            2009148,
            2023262,
            2026548,
            2030837,
            2031168,
            2032018,
            2033425,
            2034148,
            2036518,
            2039254,
            2040918,
            2043904,
            2044039,
            2046690,
            2046843,
            2046875,
            2047327,
            2047635,
            2048489,
            2048655,
            2049125,
            2049823,
            2051650,
            2052655,
            2052857,
            2052995,
            2053130,
            2054262,
            2055390,
            2055468,
            2056354,
            2058032,
            2058410,
            2059085,
            2059867,
            2060622,
            2060745,
            2061007,
            2061057,
            2061245,
            2061246,
            2061495,
            2061667,
            2061791,
            2061843,
            2061933,
            2062020,
            2062129,
            2062170,
            2062174,
            2062403,
            2062496,
            2062846,
            2062908,
            2062951,
            2062986,
            2063000,
            2063043,
            2063059,
            2063232,
            2063246,
            2063247,
            2063280,
            2063286,
            2063305,
            2063473,
            2063644,
            2063667,
            2063725,
            2063865,
            2063870,
            2063873,
            2063894,
            2063945,
            2063994,
            2064002,
            2064023,
            2064162,
            2064380,
            2064476,
            2064761,
            2064765,
            2064858,
            2065048,
            2065092
        );
    }
}
