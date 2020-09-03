<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150121170607 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql("DROP TABLE IF EXISTS Banner_CampaignBanner");
        $this->addSql("DROP TABLE IF EXISTS Banner_CampaignZone");
        $this->addSql("DROP TABLE IF EXISTS Banner_Log");
        $this->addSql("DROP TABLE IF EXISTS Banner_StatsBanner");
        $this->addSql("DROP TABLE IF EXISTS Banner_StatsCampaign");
        $this->addSql("DROP TABLE IF EXISTS Banner_StatsZone");
        $this->addSql("DROP TABLE IF EXISTS Blog_Parent");
        $this->addSql("DROP TABLE IF EXISTS Blog_Subdivision");
        $this->addSql("DROP TABLE IF EXISTS Cache_Audit");
        $this->addSql("DROP TABLE IF EXISTS Cache_Clear");
        $this->addSql("DROP TABLE IF EXISTS Comments_Count");
        $this->addSql("DROP TABLE IF EXISTS Comments_Rules");
        $this->addSql("DROP TABLE IF EXISTS Comments_Settings");
        $this->addSql("DROP TABLE IF EXISTS Comments_Text");
        $this->addSql("DROP TABLE IF EXISTS Comp_turn");
        $this->addSql("DROP TABLE IF EXISTS counter_referer");
        $this->addSql("DROP TABLE IF EXISTS counter_stats");
        $this->addSql("DROP TABLE IF EXISTS counter_statsFull");
        $this->addSql("DROP TABLE IF EXISTS counter_visitors");
        $this->addSql("DROP TABLE IF EXISTS CronTasks");
        $this->addSql("DROP TABLE IF EXISTS directSearch");
        $this->addSql("DROP TABLE IF EXISTS Forum_categories");
        $this->addSql("DROP TABLE IF EXISTS Forum_forums");
        $this->addSql("DROP TABLE IF EXISTS Forum_permgroup");
        $this->addSql("DROP TABLE IF EXISTS Forum_replies1");
        $this->addSql("DROP TABLE IF EXISTS Forum_replies2");
        $this->addSql("DROP TABLE IF EXISTS Forum_repliesXX");
        $this->addSql("DROP TABLE IF EXISTS Forum_subdiv");
        $this->addSql("DROP TABLE IF EXISTS Forum_subscribe");
        $this->addSql("DROP TABLE IF EXISTS Forum_topics1");
        $this->addSql("DROP TABLE IF EXISTS Forum_topics2");
        $this->addSql("DROP TABLE IF EXISTS Forum_topicsXX");
        $this->addSql("DROP TABLE IF EXISTS Forum_usergroup");
        $this->addSql("DROP TABLE IF EXISTS GeoIP");
        $this->addSql("DROP TABLE IF EXISTS geoIPCity");
        $this->addSql("DROP TABLE IF EXISTS geoIpNew");
        $this->addSql("DROP TABLE IF EXISTS hotlineChat");
        $this->addSql("DROP TABLE IF EXISTS hotlineChatold");
        $this->addSql("DROP TABLE IF EXISTS Message4");
        $this->addSql("DROP TABLE IF EXISTS Message5");
        $this->addSql("DROP TABLE IF EXISTS Message6");
        $this->addSql("DROP TABLE IF EXISTS Message7");
        $this->addSql("DROP TABLE IF EXISTS Message8");
        $this->addSql("DROP TABLE IF EXISTS Message9");
        $this->addSql("DROP TABLE IF EXISTS Message10");
        $this->addSql("DROP TABLE IF EXISTS Message11");
        $this->addSql("DROP TABLE IF EXISTS Message12");
        $this->addSql("DROP TABLE IF EXISTS Message13");
        $this->addSql("DROP TABLE IF EXISTS Message14");
        $this->addSql("DROP TABLE IF EXISTS Message15");
        $this->addSql("DROP TABLE IF EXISTS Message16");
        $this->addSql("DROP TABLE IF EXISTS Message17");
        $this->addSql("DROP TABLE IF EXISTS Message18");
        $this->addSql("DROP TABLE IF EXISTS Message19");
        $this->addSql("DROP TABLE IF EXISTS Message20");
        $this->addSql("DROP TABLE IF EXISTS Message21");
        $this->addSql("DROP TABLE IF EXISTS Message22");
        $this->addSql("DROP TABLE IF EXISTS Message23");
        $this->addSql("DROP TABLE IF EXISTS Message24");
        $this->addSql("DROP TABLE IF EXISTS Message25");
        $this->addSql("DROP TABLE IF EXISTS Message32");
        $this->addSql("DROP TABLE IF EXISTS Message33");
        $this->addSql("DROP TABLE IF EXISTS Message34");
        $this->addSql("DROP TABLE IF EXISTS Message35");
        $this->addSql("DROP TABLE IF EXISTS Message36");
        $this->addSql("DROP TABLE IF EXISTS Message37");
        $this->addSql("DROP TABLE IF EXISTS Message39");
        $this->addSql("DROP TABLE IF EXISTS Message40");
        $this->addSql("DROP TABLE IF EXISTS Message41");
        $this->addSql("DROP TABLE IF EXISTS Message42");
        $this->addSql("DROP TABLE IF EXISTS Message43");
        $this->addSql("DROP TABLE IF EXISTS Message44");
        $this->addSql("DROP TABLE IF EXISTS Message45");
        $this->addSql("DROP TABLE IF EXISTS Message46");
        $this->addSql("DROP TABLE IF EXISTS Message59");
        $this->addSql("DROP TABLE IF EXISTS Message60");
        $this->addSql("DROP TABLE IF EXISTS Message62");
        $this->addSql("DROP TABLE IF EXISTS Message63");
        $this->addSql("DROP TABLE IF EXISTS Message65");
        $this->addSql("DROP TABLE IF EXISTS Message66");
        $this->addSql("DROP TABLE IF EXISTS Message67");
        $this->addSql("DROP TABLE IF EXISTS Message68");
        $this->addSql("DROP TABLE IF EXISTS Message69");
        $this->addSql("DROP TABLE IF EXISTS Message70");
        $this->addSql("DROP TABLE IF EXISTS Message72");
        $this->addSql("DROP TABLE IF EXISTS Message74");
        $this->addSql("DROP TABLE IF EXISTS Message77");
        $this->addSql("DROP TABLE IF EXISTS Message78");
        $this->addSql("DROP TABLE IF EXISTS Message82");
        $this->addSql("DROP TABLE IF EXISTS Message84");
        $this->addSql("DROP TABLE IF EXISTS Message85");
        $this->addSql("DROP TABLE IF EXISTS Message88");
        $this->addSql("DROP TABLE IF EXISTS Message91");
        $this->addSql("DROP TABLE IF EXISTS Message100");
        $this->addSql("DROP TABLE IF EXISTS Message101");
        $this->addSql("DROP TABLE IF EXISTS Message105");
        $this->addSql("DROP TABLE IF EXISTS Message105");
        $this->addSql("DROP TABLE IF EXISTS Message108");
        $this->addSql("DROP TABLE IF EXISTS Message110");
        $this->addSql("DROP TABLE IF EXISTS Message112");
        $this->addSql("DROP TABLE IF EXISTS Message116");
        $this->addSql("DROP TABLE IF EXISTS Message124");
        $this->addSql("DROP TABLE IF EXISTS Message125");
        $this->addSql("DROP TABLE IF EXISTS Message126");
        $this->addSql("DROP TABLE IF EXISTS Message127");
        $this->addSql("DROP TABLE IF EXISTS Message129");
        $this->addSql("DROP TABLE IF EXISTS Message130");
        $this->addSql("DROP TABLE IF EXISTS Message136");
        $this->addSql("DROP TABLE IF EXISTS Message137");
        $this->addSql("DROP TABLE IF EXISTS Message138");
        $this->addSql("DROP TABLE IF EXISTS Message139");
        $this->addSql("DROP TABLE IF EXISTS Message140");
        $this->addSql("DROP TABLE IF EXISTS Message141");
        $this->addSql("DROP TABLE IF EXISTS Message144");
        $this->addSql("DROP TABLE IF EXISTS Message145");
        $this->addSql("DROP TABLE IF EXISTS Message147");
        $this->addSql("DROP TABLE IF EXISTS Message148");
        $this->addSql("DROP TABLE IF EXISTS Message149");
        $this->addSql("DROP TABLE IF EXISTS Message151");
        $this->addSql("DROP TABLE IF EXISTS Message156");
        $this->addSql("DROP TABLE IF EXISTS Message160");
        $this->addSql("DROP TABLE IF EXISTS Message164");
        $this->addSql("DROP TABLE IF EXISTS Message165");
        $this->addSql("DROP TABLE IF EXISTS Message170");
        $this->addSql("DROP TABLE IF EXISTS Message175");
        $this->addSql("DROP TABLE IF EXISTS Message176");
        $this->addSql("DROP TABLE IF EXISTS Message181");
        $this->addSql("DROP TABLE IF EXISTS Message182");
        $this->addSql("DROP TABLE IF EXISTS Message183");
        $this->addSql("DROP TABLE IF EXISTS Message184");
        $this->addSql("DROP TABLE IF EXISTS Message186");
        $this->addSql("DROP TABLE IF EXISTS Message188");
        $this->addSql("DROP TABLE IF EXISTS Message189");
        $this->addSql("DROP TABLE IF EXISTS Message192");

        $this->addSql("ALTER TABLE tradeCategory DROP foreign key  tradeCategory_ibfk_1");

        $this->addSql("DROP TABLE IF EXISTS Message960");
        $this->addSql("DROP TABLE IF EXISTS MetalIndexData");
        $this->addSql("DROP TABLE IF EXISTS Poll_Protect");
        $this->addSql("DROP TABLE IF EXISTS price_add_results");
        $this->addSql("DROP TABLE IF EXISTS price_add_resultsPar");
        $this->addSql("DROP TABLE IF EXISTS price_card_links");
        $this->addSql("DROP TABLE IF EXISTS Price_City_Categ");
        $this->addSql("DROP TABLE IF EXISTS Price_Friends");
        $this->addSql("DROP TABLE IF EXISTS Price_titul_scroller");
        $this->addSql("DROP TABLE IF EXISTS priceCityAll");
        $this->addSql("DROP TABLE IF EXISTS priceModerStat");
        $this->addSql("DROP TABLE IF EXISTS Redirect_new");
        $this->addSql("DROP TABLE IF EXISTS UserSendt");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
