<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160614130055 extends AbstractMigration implements ContainerAwareInterface
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
            $this->addSql("TRUNCATE instagram_stop_word");

            $this->addSql("
                INSERT INTO instagram_stop_word (id, pattern, enabled, created_at, description, pattern_for)
                VALUES (1, '(whatsapp|viber|sms|доставка|telegram|direct|директ)', 1, '2016-05-31 14:53:05', 'Стоп слова для мессенджеров и директа', 1)
                ");
            $this->addSql("
                INSERT INTO instagram_stop_word (id, pattern, enabled, created_at, description, pattern_for)
                VALUES (9, '(facebook\\.com|twitter\\.com|ok\\.ru|vk\\.com|livejournal\\.com|instagram\\.com)', 1, '2016-06-14 12:54:44', 'проверка сайтов', 2)
                ");
            $this->addSql("
                INSERT INTO instagram_stop_word (id, pattern, enabled, created_at, description, pattern_for)
                VALUES (10, '^[a-z0-9\\W]+$', 1, '2016-06-14 12:57:50', 'нет кириллицы в описании', 1)
                ");
            $this->addSql("
                INSERT INTO instagram_stop_word (id, pattern, enabled, created_at, description, pattern_for)
                VALUES (11, '([a-z0-9_\\.\\-])+\\@(([a-z0-9\\-])+\\.)+([a-z0-9]{2,4})+', 1, '2016-06-14 12:59:07', 'найден емейл', 1)
                ");
            
            $this->addSql("UPDATE instagram_stats SET stop_word_pattern_reject_id = 1 WHERE reject = 5");
            $this->addSql("UPDATE instagram_stats SET stop_word_pattern_reject_id = 9 WHERE reject = 3");
            $this->addSql("UPDATE instagram_stats SET stop_word_pattern_reject_id = 10 WHERE reject = 1");
            $this->addSql("UPDATE instagram_stats SET stop_word_pattern_reject_id = 11 WHERE reject = 6");
            $this->addSql("UPDATE instagram_stats SET stop_word_pattern_reject_id = NULL WHERE reject = 0");
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
