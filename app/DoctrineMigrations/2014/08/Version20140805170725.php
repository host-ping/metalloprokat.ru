<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20140805170725 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is autogenerated, please modify it to your needs
        $sql = 'SELECT *  FROM `Message75` WHERE `company_city` IS NULL';
        $resEmpty = $this->connection->fetchAll($sql);
        foreach ($resEmpty as $empty) {
            $newCity = 1123;
            $newRegion = 38;
            if ($empty['company_region'] > 0){
                $sql = 'SELECT `Capital` FROM `Classificator_Regions` WHERE `Regions_ID` = ?';
                $stmt = $this->connection->executeQuery($sql, array($empty['company_region']));
                $tmpCity = $stmt->fetch()['Capital'];
                if ($tmpCity > 0){
                    $newCity = $tmpCity;
                    $newRegion = $empty['company_region'];
                }
            }
            $sql = 'UPDATE `Message75` SET `company_city`= ? , `company_region` = ? WHERE `Message_ID`= ?';
            $this->connection->executeUpdate($sql, array($newCity, $newRegion, $empty['Message_ID']));

        }
    }

    public function down(Schema $schema)
    {
        // this down() migration is autogenerated, please modify it to your needs

    }
}
