<?php

namespace Metal\CompaniesBundle\Command;

use Doctrine\DBAL\Connection;
use Metal\CompaniesBundle\Entity\PackageChecker;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SynchronizeCompanySiteCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('metal:companies:synchronize-company-site');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln(sprintf('%s: Start command "%s"', date('d.m.Y H:i:s'), $this->getName()));

        $conn = $this->getContainer()->get('doctrine.orm.default_entity_manager')->getConnection();
        /* @var $em Connection */

        $companies = $conn->fetchAll(
            'SELECT Message_ID, domain FROM Message75 WHERE code_access IN (:code_access) AND company_url = :company_url',
            array(
                'code_access' => PackageChecker::getPackagesByOption('add_minisite_to_sites'),
                'company_url' => '[]',
            ),
            array(
                'code_access' => Connection::PARAM_INT_ARRAY,
            )
        );

        foreach ($companies as $company) {
            $site = sprintf('http://%s', $company['domain']);

            $output->writeln(sprintf(
                '%s: Company %d set company_url  "%s"',
                date('d.m.Y H:i:s'),
                $company['Message_ID'],
                $site
            ));

            $conn->executeUpdate(
                "UPDATE Message75 SET company_url = :site WHERE Message_ID = :company_id",
                array(
                    'site' => json_encode(array($site)),
                    'company_id' => $company['Message_ID']
                )
            );
        }
        $output->writeln(sprintf('%s: Completed', date('d.m.Y H:i:s')));
    }
}
