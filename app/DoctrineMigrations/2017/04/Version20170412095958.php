<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170412095958 extends AbstractMigration implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql('ALTER TABLE user_visiting ADD company_id INT DEFAULT NULL');
        $this->addSql('CREATE INDEX IDX_63D9B5AD979B1AD6 ON user_visiting (company_id)');
    }

    public function postUp(Schema $schema)
    {
        $input = new ArrayInput(array("metal:companies:update-last-visit"));
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

    }
}
