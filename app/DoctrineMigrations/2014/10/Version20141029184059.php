<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20141029184059 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql('UPDATE UserSend SET Email = LOWER(Email)');
        // обнуляем завязку на пользователя там, где пробиты несуществующие пользователи
        $this->addSql('UPDATE UserSend SET user_id = NULL WHERE ID IN
            (SELECT sub.ID FROM (SELECT UserSend.ID
            FROM UserSend LEFT JOIN User ON UserSend.user_id = User.User_ID
            WHERE User.User_ID IS NULL) AS sub)
        ');
        // обнуляем завязку на пользователя там, где имейл пользователя отличается от имейла подписчика
        $this->addSql('UPDATE UserSend SET user_id = NULL WHERE ID IN
            (SELECT sub.ID FROM (SELECT us.ID FROM UserSend AS us
            JOIN User AS u ON us.user_id = u.User_ID AND us.Email != u.Email) AS sub)
        ');
    }

    public function down(Schema $schema)
    {
        // this down() migration is autogenerated, please modify it to your needs

    }
}
