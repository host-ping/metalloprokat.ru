<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170418141836 extends AbstractMigration implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->skipIf($this->container->getParameter('project.family') !== 'product', 'Only product.ru');

        $this->addSql("INSERT INTO redirect
(redirect_from, redirect_to, enabled, created_at, updated_at)
VALUES
('#(.+)?/ryba-i-moreprodukty/ryba-kulinariya/(.+)?#ui', '$1/polufabrikaty-gotovie/ryba-kulinariya/$2', 1, '2017-04-18 17:18:04', '2017-04-18 17:18:04');");


        $this->addSql("INSERT INTO redirect
(redirect_from, redirect_to, enabled, created_at, updated_at)
VALUES
('#(.+)?/ryba-i-moreprodukty/molluski-konserv/(.+)?#ui', '$1/ryba-i-moreprodukty/moreprodukti/molluski-konserv/$2', 1, '2017-04-18 17:18:04', '2017-04-18 17:18:04');");


        $this->addSql("INSERT INTO redirect
(redirect_from, redirect_to, enabled, created_at, updated_at)
VALUES
('#(.+)?/ryba-i-moreprodukty/konservirovannye-vodorosli/(.+)?#ui', '$1/ryba-i-moreprodukty/moreprodukti/konservirovannye-vodorosli/$2', 1, '2017-04-18 17:18:04', '2017-04-18 17:18:04');");


        $this->addSql("INSERT INTO redirect
(redirect_from, redirect_to, enabled, created_at, updated_at)
VALUES
('#(.+)?/ryba-i-moreprodukty/vodorosli/(.+)?#ui', '$1/ryba-i-moreprodukty/moreprodukti/vodorosli/$2', 1, '2017-04-18 17:18:04', '2017-04-18 17:18:04');");


        $this->addSql("INSERT INTO redirect
(redirect_from, redirect_to, enabled, created_at, updated_at)
VALUES
('#(.+)?/ryba-i-moreprodukty/delikatesy-iz-mollyuskov/(.+)?#ui', '$1/ryba-i-moreprodukty/moreprodukti/delikatesy-iz-mollyuskov/$2', 1, '2017-04-18 17:18:04', '2017-04-18 17:18:04');");


        $this->addSql("INSERT INTO redirect
(redirect_from, redirect_to, enabled, created_at, updated_at)
VALUES
('#(.+)?/ryba-i-moreprodukty/mollyuski/(.+)?#ui', '$1/ryba-i-moreprodukty/moreprodukti/mollyuski/$2', 1, '2017-04-18 17:18:04', '2017-04-18 17:18:04');");


        $this->addSql("INSERT INTO redirect
(redirect_from, redirect_to, enabled, created_at, updated_at)
VALUES
('#(.+)?/ryba-i-moreprodukty/krab/(.+)?#ui', '$1/ryba-i-moreprodukty/moreprodukti/krab/$2', 1, '2017-04-18 17:18:04', '2017-04-18 17:18:04');");


        $this->addSql("INSERT INTO redirect
(redirect_from, redirect_to, enabled, created_at, updated_at)
VALUES
('#(.+)?/ryba-i-moreprodukty/krabovie_palochki/(.+)?#ui', '$1/ryba-i-moreprodukty/moreprodukti/krabovie_palochki/$2', 1, '2017-04-18 17:18:04', '2017-04-18 17:18:04');");


        $this->addSql("INSERT INTO redirect
(redirect_from, redirect_to, enabled, created_at, updated_at)
VALUES
('#(.+)?/selhoztovary/ryba/ikra/(.+)?#ui', '$1/ryba-i-moreprodukty/ikra/$2', 1, '2017-04-18 17:18:04', '2017-04-18 17:18:04');");


        $this->addSql("INSERT INTO redirect
(redirect_from, redirect_to, enabled, created_at, updated_at)
VALUES
('#(.+)?/selhoztovary/ryba/raki/(.+)?#ui', '$1/ryba-i-moreprodukty/moreprodukti/raki/$2', 1, '2017-04-18 17:18:04', '2017-04-18 17:18:04');");


        $this->addSql("INSERT INTO redirect
(redirect_from, redirect_to, enabled, created_at, updated_at)
VALUES
('#(.+)?/selhoztovary/ryba/mintaj/(.+)?#ui', '$1/ryba-i-moreprodukty/svejaya-riba/mintaj/$2', 1, '2017-04-18 17:18:04', '2017-04-18 17:18:04');");


        $this->addSql("INSERT INTO redirect
(redirect_from, redirect_to, enabled, created_at, updated_at)
VALUES
('#(.+)?/selhoztovary/ryba/krevetki/(.+)?#ui', '$1/ryba-i-moreprodukty/moreprodukti/krevetki/$2', 1, '2017-04-18 17:18:04', '2017-04-18 17:18:04');");


        $this->addSql("INSERT INTO redirect
(redirect_from, redirect_to, enabled, created_at, updated_at)
VALUES
('#(.+)?/selhoztovary/ryba/kalmar/(.+)?#ui', '$1/ryba-i-moreprodukty/moreprodukti/kalmar/$2', 1, '2017-04-18 17:18:04', '2017-04-18 17:18:04');");


        $this->addSql("INSERT INTO redirect
(redirect_from, redirect_to, enabled, created_at, updated_at)
VALUES
('#(.+)?/selhoztovary/ryba/losos/(.+)?#ui', '$1/ryba-i-moreprodukty/svejaya-riba/losos/$2', 1, '2017-04-18 17:18:04', '2017-04-18 17:18:04');");


        $this->addSql("INSERT INTO redirect
(redirect_from, redirect_to, enabled, created_at, updated_at)
VALUES
('#(.+)?/selhoztovary/ryba/seld/(.+)?#ui', '$1/ryba-i-moreprodukty/svejaya-riba/seld/$2', 1, '2017-04-18 17:18:04', '2017-04-18 17:18:04');");


        $this->addSql("INSERT INTO redirect
(redirect_from, redirect_to, enabled, created_at, updated_at)
VALUES
('#(.+)?/selhoztovary/ryba/riba-othodi/(.+)?#ui', '$1/ryba-i-moreprodukty/riba-othodi/$2', 1, '2017-04-18 17:18:04', '2017-04-18 17:18:04');");


        $this->addSql("INSERT INTO redirect
(redirect_from, redirect_to, enabled, created_at, updated_at)
VALUES
('#(.+)?/selhoztovary/ryba/ryba-vyalen/(.+)?#ui', '$1/ryba-i-moreprodukty/ryba-vyalen/$2', 1, '2017-04-18 17:18:04', '2017-04-18 17:18:04');");


        $this->addSql("INSERT INTO redirect
(redirect_from, redirect_to, enabled, created_at, updated_at)
VALUES
('#(.+)?/selhoztovary/ryba/ryba-zhivaya/(.+)?#ui', '$1/ryba-i-moreprodukty/ryba-zhivaya/$2', 1, '2017-04-18 17:18:04', '2017-04-18 17:18:04');");


        $this->addSql("INSERT INTO redirect
(redirect_from, redirect_to, enabled, created_at, updated_at)
VALUES
('#(.+)?/selhoztovary/ryba/moreprodukti/(.+)?#ui', '$1/ryba-i-moreprodukty/moreprodukti/$2', 1, '2017-04-18 17:18:04', '2017-04-18 17:18:04');");


        $this->addSql("INSERT INTO redirect
(redirect_from, redirect_to, enabled, created_at, updated_at)
VALUES
('#(.+)?/selhoztovary/ryba/(.+)?#ui', '$1/ryba-i-moreprodukty/$2', 1, '2017-04-18 17:18:04', '2017-04-18 17:18:04');");


        $this->addSql("INSERT INTO redirect
(redirect_from, redirect_to, enabled, created_at, updated_at)
VALUES
('#(.+)?/frukty-ovoshhi/ovoshi/salat/(.+)?#ui', '$1/frukty-ovoshhi/zelen/salat/$2', 1, '2017-04-18 17:18:04', '2017-04-18 17:18:04');");

        $this->addSql("INSERT INTO redirect
(redirect_from, redirect_to, enabled, created_at, updated_at)
VALUES
('#(.+)?/selhoztovary/zelen/(.+)?#ui', '$1/frukty-ovoshhi/zelen/$2', 1, '2017-04-18 17:18:04', '2017-04-18 17:18:04');");


        $this->addSql("INSERT INTO redirect
(redirect_from, redirect_to, enabled, created_at, updated_at)
VALUES
('#(.+)?/selhoztovary/suhofrukty/(.+)?#ui', '$1/frukty-ovoshhi/suhofrukty/$2', 1, '2017-04-18 17:18:04', '2017-04-18 17:18:04');");

        $this->addSql("INSERT INTO redirect
(redirect_from, redirect_to, enabled, created_at, updated_at)
VALUES
('#(.+)?/selhoztovary/griby/(.+)?#ui', '$1/frukty-ovoshhi/griby/$2', 1, '2017-04-18 17:18:04', '2017-04-18 17:18:04');");


        $this->addSql("INSERT INTO redirect
(redirect_from, redirect_to, enabled, created_at, updated_at)
VALUES
('#(.+)?/selhoztovary/yagody/(.+)?#ui', '$1/frukty-ovoshhi/yagody/$2', 1, '2017-04-18 17:18:04', '2017-04-18 17:18:04');");


    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
