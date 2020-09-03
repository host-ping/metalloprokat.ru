<?php

namespace Metal\CompaniesBundle\Command;

use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class NormalizeCompanyUrlCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('metal:project:normalize-company-url');
        $this->addOption('truncate', null, InputOption::VALUE_NONE);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln(sprintf('%s: Start command "%s"', date('d.m.Y H:i:s'), $this->getName()));
        $doctrine = $this->getContainer()->get('doctrine');
        $em = $doctrine->getManager();
        /* @var  $em EntityManager */
        $conn = $em->getConnection();

        if ($input->getOption('truncate')) {
            $output->writeln(sprintf('%s: <info>Truncate.</info>', date('d.m.Y H:i:s')));
            $conn->executeUpdate('TRUNCATE normalized_company_url');
        }

        $minId = $conn->fetchColumn('SELECT MIN(Message_ID) FROM Message75');
        $maxId = $conn->fetchColumn('SELECT MAX(Message_ID) FROM Message75');
        $idFrom = $minId;
        do {
            $idTo = $idFrom + 1000;

            $companiesUrl = $conn->fetchAll(
                'SELECT Message_ID AS company_id, company_url FROM Message75 WHERE Message_ID >= :id_from AND Message_ID < :id_to ',
                array(
                    'id_from' => $idFrom,
                    'id_to' => $idTo
                )
            );

            foreach ($companiesUrl as $companyUrl) {
                $output->writeln(sprintf('%s: Process company <info>%d.</info>', date('d.m.Y H:i:s'), $companyUrl['company_id']));
                $normalizedUrl = implode('', json_decode($companyUrl['company_url'], true));

                $conn->executeUpdate(
                    'INSERT IGNORE INTO normalized_company_url (url_as_string, company_id) VALUE (:url, :company)',
                    array(
                        'url' => $normalizedUrl,
                        'company' => $companyUrl['company_id']
                    ),
                    array(
                        'url' => \PDO::PARAM_STR
                    )
                );
            }

            $idFrom = $idTo;
        } while ($idFrom <= $maxId);

        $output->writeln(sprintf('%s: Completed', date('d.m.Y H:i:s')));
    }
}