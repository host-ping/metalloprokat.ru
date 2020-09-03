<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160525162623 extends AbstractMigration implements ContainerAwareInterface
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
            $this->addSql("
                INSERT INTO instagram_account (id, username, password, updated_at, created_at, enabled, last_processed_at)
                  VALUES (7, 'aikikaidojo', '', '2016-04-27 17:11:17', '2016-04-27 17:11:17', 1, '2016-05-24 14:58:58')
            ");

            $tagsStr = '#родители, #школа, #секциимосква, #дети, #детицветыжизни, #детинашевсе, #детиэтосчастье, #хобби, #физкультура, #спорт, #спортклуб, #спортзал, #единоборство, #студенты, #здоровье, #здоровьедетей, #здоровьеважнее, #здоровыйобразжизни, #ЗОЖ, #детскийспорт, #детскийклуб, #ребенок, #ребеноксчастлив, #досуг, #досугсдетьми, #досугсребенком';
            $tags = array_map(function ($tag) {
                $tag = trim(trim($tag), '#');
                return trim($tag);
            }, explode(',', $tagsStr));

            foreach ($tags as $tag) {
                $this->addSql(sprintf("
                    INSERT INTO instagram_account_tag (title, last_parsed_at, instagram_account_id, is_automatically_added, enabled)
                     VALUES ('%s', null, 7, 0, 1)
                ", $tag));
            }
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
