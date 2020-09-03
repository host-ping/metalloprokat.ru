<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Metal\ServicesBundle\Entity\AgreementTemplate;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160412151301 extends AbstractMigration implements ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function up(Schema $schema)
    {
        if ($this->container->getParameter('project.family') === 'stroy') {
            $this->addSql("
            UPDATE agreement_template SET content = '
                <div class=\"company-img-wrapper float-left\">
                    <div class=\"image is-bordered\">
                        <img src=\"http://www.metalloprokat.ru/netcat_files/8466.jpg\" width=\"300px\" />
                    </div>
                </div>
                <div class=\"about-text float-left\">
                    <h1 class=\"title\">О компании</h1>
                
                    <div id=\"default-styles\">
                        <p class=\"text\">Компания <strong>«Строй.ру»</strong> была образована в 2000 году и на
                            сегодняшний день является одной
                            из ведущих компаний, предоставляющих информационные услуги в области строительства и ремонта.</p>
                
                <h4>Миссия</h4>
                        <p class=\"text\">Миссия компании заключается в обеспечении оперативного взаимодействия между потребителем
                            и
                            поставщиком металла за счет информационных продуктов и услуг, основанных на современных, удобных и
                            доступных технологиях.</p>
                
                <h4>Цифры</h4>
                        <p class=\"text\">Основой компании является интернет-портал Строй.ру. Ежедневно Портал посещает порядка 5.000 пользователей, в
                            разделах зарегистрировано более 10.000 компаний — производителей, трейдеров, потребителей и
                            других представителей отрасли.</p>
                <p></p><br>
                    </div>
                </div>
                ' 
                WHERE id = :id
            ", array(
                    'id' => AgreementTemplate::CORP_ABOUT_COMPANY
                )
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
