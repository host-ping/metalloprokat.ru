<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20161219114622 extends AbstractMigration implements ContainerAwareInterface
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

        $this->addSql('CREATE TABLE landing_page_country_count (id INT AUTO_INCREMENT NOT NULL, landing_page_id INT NOT NULL, country_id INT DEFAULT NULL, results_count INT DEFAULT 0 NOT NULL, INDEX IDX_D3AE2732DF122DC5 (landing_page_id), INDEX IDX_D3AE2732F92F3E70 (country_id), UNIQUE INDEX UNIQ_landing_page_country (landing_page_id, country_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;');
        $this->addSql('ALTER TABLE landing_page_country_count ADD CONSTRAINT FK_D3AE2732DF122DC5 FOREIGN KEY (landing_page_id) REFERENCES landing_page (id) ON DELETE CASCADE;');
        $this->addSql('ALTER TABLE landing_page_country_count ADD CONSTRAINT FK_D3AE2732F92F3E70 FOREIGN KEY (country_id) REFERENCES Classificator_Country (Country_ID) ON DELETE SET NULL;');
    }

    public function postUp(Schema $schema)
    {
        $input = new ArrayInput(array("metal:categories:update-landing-page"));
        $output = new BufferedOutput();
        $application = new Application($this->container->get('kernel'));
        $application->setAutoExit(false);
        $application->run($input, $output);
        $this->write($output->fetch());
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
