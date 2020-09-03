<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Metal\UsersBundle\Entity\UserAutoLogin;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20161013160448 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql('ALTER TABLE user_auto_login ADD target TINYINT DEFAULT 1 NOT NULL');
        $this->addSql(
            'UPDATE user_auto_login SET target = :_target',
            array('_target' => UserAutoLogin::TARGET_EMAIL)
        );

    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
