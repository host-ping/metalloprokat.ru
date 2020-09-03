<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150703151705 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // переименовываем город
        $this->addSql('UPDATE Classificator_Region SET Region_Name = :title,
                          administrative_center = :administrative_center,
                          title_locative = :title_locative,
                          title_genitive = :title_genitive,
                          title_accusative = :title_accusative,
                          parent = :parent
                           WHERE Region_ID = :city_id
            ',
            array(
                'city_id' => 1172,
                'title'   => 'Бирюсинск',
                'parent'  => 16,
                'administrative_center' => 645,
                'title_locative'   => 'Бирюсинске',
                'title_genitive'   => 'Бирюсинска',
                'title_accusative' => 'Бирюсинск'
            )
        );

        // переименовываем неправильный город в правильное название
        $this->addSql('UPDATE Classificator_Region
                          SET Region_Name = :title,
                          title_locative = :title_locative,
                          title_genitive = :title_genitive,
                          title_accusative = :title_accusative
                    WHERE Region_ID = :city_id',
            array(
                'city_id' => 260,
                'title'   => 'Буденновск',
                'title_locative'   => 'Буденновске',
                'title_genitive'   => 'Буденновска',
                'title_accusative' => 'Буденновск'
            )
        );

        $parameters = array(
            'bad_city_id' => 1172,
            'city_id' => 2582
        );

        // филиалы из правильного города, в уже правильный (переименнованый) город.
        $this->addSql('UPDATE company_delivery_city SET city_id = :city_id WHERE city_id = :bad_city_id', $parameters);

        // компании
        $this->addSql('UPDATE Message75 SET company_city = :city_id WHERE company_city = :bad_city_id', $parameters);

        $this->addSql('UPDATE demand SET city_id = :city_id WHERE city_id = :bad_city_id',  $parameters);

        $this->addSql('UPDATE demand_subscription_territorial SET city_id = :city_id WHERE city_id = :bad_city_id', $parameters);

        $this->addSql('UPDATE callback SET city_id = :city_id WHERE city_id = :bad_city_id', $parameters);

        $this->addSql('UPDATE company_review SET city_id = :city_id WHERE city_id = :bad_city_id', $parameters);

        $this->addSql('UPDATE demand SET product_city_id = :city_id WHERE product_city_id = :bad_city_id', $parameters);

        $this->addSql('UPDATE demand_answer SET city_id = :city_id WHERE city_id = :bad_city_id', $parameters);

        $this->addSql('UPDATE service_packages SET city_id = :city_id WHERE city_id = :bad_city_id', $parameters);

        $this->addSql('UPDATE User SET city_id = :city_id WHERE city_id = :bad_city_id', $parameters);

        $this->addSql('UPDATE metalspros_demand_subscription SET city_id = :city_id WHERE city_id = :bad_city_id', $parameters);

        $this->addSql('UPDATE stats_element SET city_id = :city_id WHERE city_id = :bad_city_id', $parameters);
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
    }
}
