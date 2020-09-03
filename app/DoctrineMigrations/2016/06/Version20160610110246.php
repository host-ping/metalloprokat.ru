<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160610110246 extends AbstractMigration implements ContainerAwareInterface
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
        if ($this->container->getParameter('project.family') === 'stroy') {
            $this->addSql("INSERT INTO instagram_account_location (instagram_account_id, location_id, enabled, last_parsed_at, created_at, title) VALUES (8, '1014071024', 1, null, '2016-06-10 11:00:07', 'Эффект Любви ');");
            $this->addSql("INSERT INTO instagram_account_location (instagram_account_id, location_id, enabled, last_parsed_at, created_at, title) VALUES (8, '1016121011', 1, null, '2016-06-10 11:00:07', 'Tez Tour Москва Сити ');");
            $this->addSql("INSERT INTO instagram_account_location (instagram_account_id, location_id, enabled, last_parsed_at, created_at, title) VALUES (8, '1016162639', 1, null, '2016-06-10 11:00:07', 'Белая ворона ');");
            $this->addSql("INSERT INTO instagram_account_location (instagram_account_id, location_id, enabled, last_parsed_at, created_at, title) VALUES (8, '1016279637', 1, null, '2016-06-10 11:00:07', 'Velobeat ');");
            $this->addSql("INSERT INTO instagram_account_location (instagram_account_id, location_id, enabled, last_parsed_at, created_at, title) VALUES (8, '1019116782', 1, null, '2016-06-10 11:00:07', 'Inventure Partners ');");
            $this->addSql("INSERT INTO instagram_account_location (instagram_account_id, location_id, enabled, last_parsed_at, created_at, title) VALUES (8, '1019122740', 1, null, '2016-06-10 11:00:07', 'Abba Centre ');");
            $this->addSql("INSERT INTO instagram_account_location (instagram_account_id, location_id, enabled, last_parsed_at, created_at, title) VALUES (8, '1019154894', 1, null, '2016-06-10 11:00:07', 'МФК Башня Федерация ');");
            $this->addSql("INSERT INTO instagram_account_location (instagram_account_id, location_id, enabled, last_parsed_at, created_at, title) VALUES (8, '1020377195', 1, null, '2016-06-10 11:00:07', 'Матч ТВ ');");
            $this->addSql("INSERT INTO instagram_account_location (instagram_account_id, location_id, enabled, last_parsed_at, created_at, title) VALUES (8, '1020757935', 1, null, '2016-06-10 11:00:07', 'Культ сигар ');");
            $this->addSql("INSERT INTO instagram_account_location (instagram_account_id, location_id, enabled, last_parsed_at, created_at, title) VALUES (8, '1022784759', 1, null, '2016-06-10 11:00:07', 'Gethome.ru ');");
            $this->addSql("INSERT INTO instagram_account_location (instagram_account_id, location_id, enabled, last_parsed_at, created_at, title) VALUES (8, '1024000926', 1, null, '2016-06-10 11:00:07', 'ABC Kitchen ');");
            $this->addSql("INSERT INTO instagram_account_location (instagram_account_id, location_id, enabled, last_parsed_at, created_at, title) VALUES (8, '1026725061', 1, null, '2016-06-10 11:00:07', 'Строительная компания Вель ');");
            $this->addSql("INSERT INTO instagram_account_location (instagram_account_id, location_id, enabled, last_parsed_at, created_at, title) VALUES (8, '1029820392', 1, null, '2016-06-10 11:00:07', 'CeoRooms ');");
            $this->addSql("INSERT INTO instagram_account_location (instagram_account_id, location_id, enabled, last_parsed_at, created_at, title) VALUES (8, '1030288307', 1, null, '2016-06-10 11:00:07', 'Химчистка Suzy Wong ');");
            $this->addSql("INSERT INTO instagram_account_location (instagram_account_id, location_id, enabled, last_parsed_at, created_at, title) VALUES (8, '1031671214', 1, null, '2016-06-10 11:00:07', 'Ecn24 ');");
            $this->addSql("INSERT INTO instagram_account_location (instagram_account_id, location_id, enabled, last_parsed_at, created_at, title) VALUES (8, '1032165646', 1, null, '2016-06-10 11:00:07', 'Apples City ');");
            $this->addSql("INSERT INTO instagram_account_location (instagram_account_id, location_id, enabled, last_parsed_at, created_at, title) VALUES (8, '1032899011', 1, null, '2016-06-10 11:00:07', 'Insight Russia ');");
            $this->addSql("INSERT INTO instagram_account_location (instagram_account_id, location_id, enabled, last_parsed_at, created_at, title) VALUES (8, '1034802327', 1, null, '2016-06-10 11:00:07', 'Bb&Burgers ');");
            $this->addSql("INSERT INTO instagram_account_location (instagram_account_id, location_id, enabled, last_parsed_at, created_at, title) VALUES (8, '11821642', 1, null, '2016-06-10 11:00:07', 'Меркурий Сити Тауэр ');");
            $this->addSql("INSERT INTO instagram_account_location (instagram_account_id, location_id, enabled, last_parsed_at, created_at, title) VALUES (8, '12364993', 1, null, '2016-06-10 11:00:07', 'Стройгазконсалтинг ');");
            $this->addSql("INSERT INTO instagram_account_location (instagram_account_id, location_id, enabled, last_parsed_at, created_at, title) VALUES (8, '123811291', 1, null, '2016-06-10 11:00:07', 'Fleurs Royales ');");
            $this->addSql("INSERT INTO instagram_account_location (instagram_account_id, location_id, enabled, last_parsed_at, created_at, title) VALUES (8, '125423274', 1, null, '2016-06-10 11:00:07', 'City Club International ');");
            $this->addSql("INSERT INTO instagram_account_location (instagram_account_id, location_id, enabled, last_parsed_at, created_at, title) VALUES (8, '214660444', 1, null, '2016-06-10 11:00:07', 'Bamboobar ');");
            $this->addSql("INSERT INTO instagram_account_location (instagram_account_id, location_id, enabled, last_parsed_at, created_at, title) VALUES (8, '220118786', 1, null, '2016-06-10 11:00:07', 'Tutto Bene ');");
            $this->addSql("INSERT INTO instagram_account_location (instagram_account_id, location_id, enabled, last_parsed_at, created_at, title) VALUES (8, '224814074', 1, null, '2016-06-10 11:00:07', 'КПМГ ');");
            $this->addSql("INSERT INTO instagram_account_location (instagram_account_id, location_id, enabled, last_parsed_at, created_at, title) VALUES (8, '23834', 1, null, '2016-06-10 11:00:07', 'Башня-2000 ');");
            $this->addSql("INSERT INTO instagram_account_location (instagram_account_id, location_id, enabled, last_parsed_at, created_at, title) VALUES (8, '264593618', 1, null, '2016-06-10 11:00:07', 'Город Мастерславль ');");
            $this->addSql("INSERT INTO instagram_account_location (instagram_account_id, location_id, enabled, last_parsed_at, created_at, title) VALUES (8, '299897824', 1, null, '2016-06-10 11:00:07', 'Organic Religion ');");
            $this->addSql("INSERT INTO instagram_account_location (instagram_account_id, location_id, enabled, last_parsed_at, created_at, title) VALUES (8, '3652908', 1, null, '2016-06-10 11:00:07', 'VietCafe ');");
            $this->addSql("INSERT INTO instagram_account_location (instagram_account_id, location_id, enabled, last_parsed_at, created_at, title) VALUES (8, '36977352', 1, null, '2016-06-10 11:00:07', 'Паста Дели ');");
            $this->addSql("INSERT INTO instagram_account_location (instagram_account_id, location_id, enabled, last_parsed_at, created_at, title) VALUES (8, '39088844', 1, null, '2016-06-10 11:00:07', 'InSmile Dental Lounge ');");
            $this->addSql("INSERT INTO instagram_account_location (instagram_account_id, location_id, enabled, last_parsed_at, created_at, title) VALUES (8, '428358310', 1, null, '2016-06-10 11:00:07', 'High Level Hostel ');");
            $this->addSql("INSERT INTO instagram_account_location (instagram_account_id, location_id, enabled, last_parsed_at, created_at, title) VALUES (8, '450940680', 1, null, '2016-06-10 11:00:07', 'Capital Group ');");
            $this->addSql("INSERT INTO instagram_account_location (instagram_account_id, location_id, enabled, last_parsed_at, created_at, title) VALUES (8, '45205682', 1, null, '2016-06-10 11:00:07', 'Aegis Media ');");
            $this->addSql("INSERT INTO instagram_account_location (instagram_account_id, location_id, enabled, last_parsed_at, created_at, title) VALUES (8, '46280550', 1, null, '2016-06-10 11:00:07', 'Клиника Чайка ');");
            $this->addSql("INSERT INTO instagram_account_location (instagram_account_id, location_id, enabled, last_parsed_at, created_at, title) VALUES (8, '498412320', 1, null, '2016-06-10 11:00:07', 'Тапас Марбелья ');");
            $this->addSql("INSERT INTO instagram_account_location (instagram_account_id, location_id, enabled, last_parsed_at, created_at, title) VALUES (8, '504393058', 1, null, '2016-06-10 11:00:07', 'Капиталъ ');");
            $this->addSql("INSERT INTO instagram_account_location (instagram_account_id, location_id, enabled, last_parsed_at, created_at, title) VALUES (8, '533660796760236', 1, null, '2016-06-10 11:00:07', 'Капитал Групп ');");
            $this->addSql("INSERT INTO instagram_account_location (instagram_account_id, location_id, enabled, last_parsed_at, created_at, title) VALUES (8, '533809079', 1, null, '2016-06-10 11:00:07', 'Адвокатское бюро S&K Вертикаль ');");
            $this->addSql("INSERT INTO instagram_account_location (instagram_account_id, location_id, enabled, last_parsed_at, created_at, title) VALUES (8, '54418289', 1, null, '2016-06-10 11:00:07', 'CP Capital ');");
            $this->addSql("INSERT INTO instagram_account_location (instagram_account_id, location_id, enabled, last_parsed_at, created_at, title) VALUES (8, '555056446', 1, null, '2016-06-10 11:00:07', 'Персона Lab ');");
            $this->addSql("INSERT INTO instagram_account_location (instagram_account_id, location_id, enabled, last_parsed_at, created_at, title) VALUES (8, '574386888', 1, null, '2016-06-10 11:00:07', 'БЦ Город Столиц ');");
            $this->addSql("INSERT INTO instagram_account_location (instagram_account_id, location_id, enabled, last_parsed_at, created_at, title) VALUES (8, '575778857', 1, null, '2016-06-10 11:00:07', 'Little Black Shoe ');");
            $this->addSql("INSERT INTO instagram_account_location (instagram_account_id, location_id, enabled, last_parsed_at, created_at, title) VALUES (8, '579261193', 1, null, '2016-06-10 11:00:07', 'Kung Fu Kitchen ');");
            $this->addSql("INSERT INTO instagram_account_location (instagram_account_id, location_id, enabled, last_parsed_at, created_at, title) VALUES (8, '58244569', 1, null, '2016-06-10 11:00:07', 'Fresh & Ko ');");
            $this->addSql("INSERT INTO instagram_account_location (instagram_account_id, location_id, enabled, last_parsed_at, created_at, title) VALUES (8, '60054550', 1, null, '2016-06-10 11:00:07', 'Posterscope ');");
            $this->addSql("INSERT INTO instagram_account_location (instagram_account_id, location_id, enabled, last_parsed_at, created_at, title) VALUES (8, '603644616', 1, null, '2016-06-10 11:00:07', 'WorldexSport ');");
            $this->addSql("INSERT INTO instagram_account_location (instagram_account_id, location_id, enabled, last_parsed_at, created_at, title) VALUES (8, '607615515', 1, null, '2016-06-10 11:00:07', 'Point Estate ');");
            $this->addSql("INSERT INTO instagram_account_location (instagram_account_id, location_id, enabled, last_parsed_at, created_at, title) VALUES (8, '608802277', 1, null, '2016-06-10 11:00:07', 'Издательство АСТ ');");
            $this->addSql("INSERT INTO instagram_account_location (instagram_account_id, location_id, enabled, last_parsed_at, created_at, title) VALUES (8, '620826433', 1, null, '2016-06-10 11:00:07', 'Белый Тюлень ');");
            $this->addSql("INSERT INTO instagram_account_location (instagram_account_id, location_id, enabled, last_parsed_at, created_at, title) VALUES (8, '62230344', 1, null, '2016-06-10 11:00:07', 'Fornetto ');");
            $this->addSql("INSERT INTO instagram_account_location (instagram_account_id, location_id, enabled, last_parsed_at, created_at, title) VALUES (8, '643781962', 1, null, '2016-06-10 11:00:07', 'Toscana-Loft ');");
            $this->addSql("INSERT INTO instagram_account_location (instagram_account_id, location_id, enabled, last_parsed_at, created_at, title) VALUES (8, '669873866', 1, null, '2016-06-10 11:00:07', 'Modulbank ');");
            $this->addSql("INSERT INTO instagram_account_location (instagram_account_id, location_id, enabled, last_parsed_at, created_at, title) VALUES (8, '732620479', 1, null, '2016-06-10 11:00:07', 'Moscow City Towers ');");
            $this->addSql("INSERT INTO instagram_account_location (instagram_account_id, location_id, enabled, last_parsed_at, created_at, title) VALUES (8, '774925120', 1, null, '2016-06-10 11:00:07', 'London Priority Club ');");
            $this->addSql("INSERT INTO instagram_account_location (instagram_account_id, location_id, enabled, last_parsed_at, created_at, title) VALUES (8, '7787497', 1, null, '2016-06-10 11:00:07', 'Руки-ножницы ');");
            $this->addSql("INSERT INTO instagram_account_location (instagram_account_id, location_id, enabled, last_parsed_at, created_at, title) VALUES (8, '790829907', 1, null, '2016-06-10 11:00:07', 'Ordo Group ');");
            $this->addSql("INSERT INTO instagram_account_location (instagram_account_id, location_id, enabled, last_parsed_at, created_at, title) VALUES (8, '8045422', 1, null, '2016-06-10 11:00:07', 'Башня на Набережной ');");
            $this->addSql("INSERT INTO instagram_account_location (instagram_account_id, location_id, enabled, last_parsed_at, created_at, title) VALUES (8, '831533945', 1, null, '2016-06-10 11:00:07', 'Pin Group ');");
            $this->addSql("INSERT INTO instagram_account_location (instagram_account_id, location_id, enabled, last_parsed_at, created_at, title) VALUES (8, '849509655167817', 1, null, '2016-06-10 11:00:07', 'Office Production ');");
            $this->addSql("INSERT INTO instagram_account_location (instagram_account_id, location_id, enabled, last_parsed_at, created_at, title) VALUES (8, '878564203', 1, null, '2016-06-10 11:00:07', 'Tim&Tim IceCream ');");
            $this->addSql("INSERT INTO instagram_account_location (instagram_account_id, location_id, enabled, last_parsed_at, created_at, title) VALUES (8, '881429425', 1, null, '2016-06-10 11:00:07', 'Салон Реформа ');");
            $this->addSql("INSERT INTO instagram_account_location (instagram_account_id, location_id, enabled, last_parsed_at, created_at, title) VALUES (8, '886376848', 1, null, '2016-06-10 11:00:07', 'Империя Сити ');");
            $this->addSql("INSERT INTO instagram_account_location (instagram_account_id, location_id, enabled, last_parsed_at, created_at, title) VALUES (8, '905329411', 1, null, '2016-06-10 11:00:07', 'Black Beard Moscow ');");
            $this->addSql("INSERT INTO instagram_account_location (instagram_account_id, location_id, enabled, last_parsed_at, created_at, title) VALUES (8, '938681813', 1, null, '2016-06-10 11:00:07', 'МФК Город Столиц ');");
            $this->addSql("INSERT INTO instagram_account_location (instagram_account_id, location_id, enabled, last_parsed_at, created_at, title) VALUES (8, '949856959', 1, null, '2016-06-10 11:00:07', 'Бюро точных механизмов ');");
            $this->addSql("INSERT INTO instagram_account_location (instagram_account_id, location_id, enabled, last_parsed_at, created_at, title) VALUES (8, '974101478', 1, null, '2016-06-10 11:00:07', 'Арт-корпорация Око ');");
            $this->addSql("INSERT INTO instagram_account_location (instagram_account_id, location_id, enabled, last_parsed_at, created_at, title) VALUES (8, '97414224', 1, null, '2016-06-10 11:00:07', 'Даблби ');");
            $this->addSql("INSERT INTO instagram_account_location (instagram_account_id, location_id, enabled, last_parsed_at, created_at, title) VALUES (8, '978700418', 1, null, '2016-06-10 11:00:07', 'Empire Beauty ');");
            $this->addSql("INSERT INTO instagram_account_location (instagram_account_id, location_id, enabled, last_parsed_at, created_at, title) VALUES (8, '983365807', 1, null, '2016-06-10 11:00:07', 'Салон красоты Мильфей ');");
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
