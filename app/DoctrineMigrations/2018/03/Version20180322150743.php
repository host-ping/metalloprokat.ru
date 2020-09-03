<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180322150743 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs

        $cities = [
            125 => 'balakovo',
            2570 => 'kerch',
            2587 => 'novocheboksarsk',
            1333 => 'orehovo-zuevo',
            1271 => 'noviy-urengoi',
            2405 => 'mozir',
            1286 => 'obninsk',
            481 => 'domodedovo',
            80 => 'artem',
            1272 => 'noginsk',
            985 => 'leninsk-kuznetskiy',
        ];

        $robots = 'User-agent: *
Disallow: /images/js/
Disallow: /redirect.php
Disallow: /auth/
Disallow: /registration/
Disallow: *sort=*
Disallow: /price/complaint/
Disallow: /board/
Disallow: /catalog/
Disallow: /hotmetal/
Disallow: /script/
Disallow: /netcat/
Disallow: /profile/
Disallow: /company/search/?text=*
Disallow: */?&mprice=*
Disallow: *curPos=*
Disallow: */search/?text=*
Disallow: */?type=5
Disallow: /top/
Disallow: /stat/
Disallow: /exhibition/
Disallow: /associacii_sousi
Disallow: /smi/
Disallow: */trade/
Disallow: /img/
Disallow: *area_id=*
Disallow: *?fo=*
Disallow: *&fo=*
Disallow: *?params=*
Disallow: *?q=*
Disallow: /users
Disallow: *order=*
Disallow: *view=*
Disallow: *consumers=*
Disallow: *periodicity=*
Disallow: *wholesale=*
Disallow: *price_to=*
Disallow: *price_from=*
Disallow: *?city=*
Disallow: *date_to=*
Disallow: *date_from=*
Disallow: *period=*
Disallow: *_redirect_to=*
Disallow: /redirect.php?url=*
Disallow: *redirect_site*
Disallow: /landing/
Disallow: *out*?url=
Disallow: /out?url=*
Disallow: *sub=*
Disallow: /*tab=*
Disallow: /*&
Disallow: /*type=
Disallow: /*concrete_city=*
Disallow: /statistic/redirect_site?source=*
Disallow: /bundles/metalproject/js/noindex/*
Disallow: /price/*.html?page=*
Disallow: /pixxxel.gif
Disallow: /demands/
Disallow: /*catalog/*

User-agent: Googlebot
Disallow: /
                    
Host: {{ base_url }}
Sitemap: {{ sitemap_url }}';

        foreach ($cities as $cityId => $slug) {
            $this->addSql(
                '
                    UPDATE Classificator_Region SET 
                    Keyword = :slug, robots_text = :robots_text
                    WHERE Region_ID = :id ',
                ['slug' => $slug, 'id' => $cityId, 'robots_text' => $robots]
            );
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
