<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160404165245 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql("UPDATE `User` SET `city_id`=1219  WHERE `city_id`= 2349 ");
        $this->addSql("UPDATE `demand` SET `city_id`= 1219  WHERE `city_id`= 2349 ");
        $this->addSql("UPDATE `demand_answer` SET `city_id`= 1219  WHERE `city_id`= 2349 ");
        $this->addSql("
            UPDATE IGNORE demand_subscription_territorial dst
            SET dst.territorial_structure_id = (SELECT ts.id FROM territorial_structure ts WHERE ts.city_id = 1219)
            WHERE dst.city_id= 2349"
        );
        $this->addSql("UPDATE `demand_subscription_territorial` SET `city_id`= 1219  WHERE `city_id`= 2349 ");
        $this->addSql("UPDATE `user_city` SET `city_id`= 1219  WHERE `city_id`= 2349 ");
        $this->addSql("UPDATE `service_packages` SET `city_id`= 1219  WHERE `city_id`= 2349 ");
        $this->addSql("UPDATE `announcement_stats_element` SET `city_id`= 1219  WHERE `city_id`= 2349 ");
        $this->addSql("UPDATE `brand_city` SET `city_id`= 1219  WHERE `city_id`= 2349 ");
        $this->addSql("UPDATE `manufacturer_city` SET `city_id`= 1219  WHERE `city_id`= 2349 ");
        $this->addSql("UPDATE `catalog_product_city` SET `city_id`= 1219  WHERE `city_id`= 2349 ");
        $this->addSql("UPDATE `catalog_product_review` SET `city_id`= 1219  WHERE `city_id`= 2349 ");
        $this->addSql("UPDATE `company_review` SET `city_id`= 1219  WHERE `city_id`= 2349 ");
        $this->addSql("UPDATE `complaint` SET `city_id`= 1219  WHERE `city_id`= 2349 ");
        $this->addSql("UPDATE IGNORE `stats_city` SET `city_id`= 1219  WHERE `city_id`= 2349 ");
        $this->addSql("UPDATE `stats_element` SET `city_id`= 1219  WHERE `city_id`= 2349 ");
        $this->addSql("UPDATE `metalspros_demand_subscription` SET `city_id`= 1219  WHERE `city_id`= 2349 ");
        $this->addSql("UPDATE `Message75` SET `company_city`= 1219  WHERE `company_city`= 2349 ");
        $this->addSql("UPDATE `Classificator_Region` SET `Region_Name`= 'Великий Новгород'  WHERE `Region_ID`= 1219 ");
        $this->addSql("DELETE FROM `territorial_structure` WHERE `city_id`= 2349 ");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
