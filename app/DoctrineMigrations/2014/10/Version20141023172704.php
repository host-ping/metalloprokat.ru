<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20141023172704 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is autogenerated, please modify it to your needs

        $parametersOptions = $this->connection->executeQuery('SELECT po.* FROM Message155 AS po')->fetchAll();

        usort($parametersOptions, function ($b, $a) {
                return strnatcmp($a['name'], $b['name']) * -1;
            }
        );

        foreach ($parametersOptions as $key => $parameterOption) {
            $this->connection->executeUpdate(
                'UPDATE Message155 AS po SET po.minisite_priority = :priority WHERE po.Message_ID = :id',
                array(
                    'priority' => $key,
                    'id' => $parameterOption['Message_ID']
                )
            );
        }
    }

    public function down(Schema $schema)
    {
        // this down() migration is autogenerated, please modify it to your needs

    }
}