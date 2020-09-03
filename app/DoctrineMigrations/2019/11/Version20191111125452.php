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
class Version20191111125452 extends AbstractMigration implements ContainerAwareInterface
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
              VALUES (:newsletter_id, 'Только в наябре - 25% скидка на Металлопрокат-repeat', '2019-11-11 12:28:13',
              '2019-11-11 12:28:13', '2019-11-11 12:28:13', NULL,
              '@MetalProject/emails/Metalloprokat/metalloprokat_2019_hb_discount.html.twig')",
                array('newsletter_id' => 52),
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
                    'newsletter_id' => 52,
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
            2623,
            6260,
            6303,
            6791,
            6840,
            8388,
            9432,
            12622,
            13076,
            13274,
            14360,
            14787,
            22863,
            22973,
            24413,
            25201,
            26058,
            26486,
            27113,
            29686,
            30752,
            31376,
            31523,
            35383,
            36056,
            127706,
            2009148,
            2020987,
            2025906,
            2026548,
            2029654,
            2029780,
            2030516,
            2031168,
            2031804,
            2032281,
            2032697,
            2034148,
            2035904,
            2036199,
            2036518,
            2037264,
            2037281,
            2037530,
            2039859,
            2040456,
            2040625,
            2040918,
            2041093,
            2041271,
            2043533,
            2044039,
            2045589,
            2046429,
            2046547,
            2046843,
            2047107,
            2047653,
            2047722,
            2048109,
            2048489,
            2049125,
            2049823,
            2050407,
            2051072,
            2051650,
            2051777,
            2052655,
            2052784,
            2052995,
            2053418,
            2053553,
            2053859,
            2053882,
            2053998,
            2054262,
            2054560
        );
    }
}
