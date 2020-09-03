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
class Version20181218050350 extends AbstractMigration implements ContainerAwareInterface
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
              VALUES (:newsletter_id, 'НГ рассылка 2019', '2018-12-15 12:28:13',
              '2018-12-15 12:28:13', '2018-12-15 12:28:13', NULL,
              '@MetalProject/emails/Metalloprokat/metalloprokat_2018_new_year_discount.html.twig')",
                array('newsletter_id' => 45),
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
                    'newsletter_id' => 45,
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
            2045589,
            14787,
            2031804,
            127706,
            27113,
            2020987,
            2032281,
            2051025,
            30752,
            2036199,
            2059344,
            2058261,
            2057779,
            2040456,
            2053998,
            2623,
            2058530,
            2052784,
            2041093,
            25201,
            22973,
            2057183,
            2056080,
            2053859,
            2053553,
            2059893,
            2053418,
            2046429,
            2060906,
            13274,
            2037281,
            2059680,
            2029780,
            2056118,
            13076,
            2061246,
            2058452,
            2047635,
            2060920,
            2047722,
            2044259,
            26058,
            14360,
            2061436,
            2058990,
            6791,
            35383,
            2029654,
            2060381,
            2051777,
            2053882,
            2040625,
            2047653,
            2061607,
            2025906,
            6840,
            2047107,
            2061139,
            2030516,
            2050407,
            2058479,
            2037530,
            2035904,
            2060564,
            2055361,
            2061503,
            2048109,
            2059736,
            2061588,
            2062333,
            2054560,
            2046875,
            2060530,
            2046547,
            2050107,
            2062336,
            2041271,
            2058924,
            2043533,
            2038734,
            2055780,
            2062418
        );
    }
}
