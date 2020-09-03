<?php

namespace Metal\CompaniesBundle\Command;

use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManager;
use Metal\CompaniesBundle\Entity\Company;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class RefreshNormalizedCompanyTitleCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('metal:companies:refresh-normalized-company-title');
        $this->addOption('batch-size', null, InputOption::VALUE_OPTIONAL, '', 500);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln(sprintf('%s: Start command "%s"', date('d.m.Y H:i:s'), $this->getName()));

        $em = $this->getContainer()->get('doctrine');
        /* @var $em EntityManager */
        $conn = $em->getConnection();
        /* @var $conn Connection */

        $batchSize = $input->getOption('batch-size');

        $minId = $conn->fetchColumn('SELECT MIN(Message_ID) FROM Message75');
        $maxId = $conn->fetchColumn('SELECT MAX(Message_ID) FROM Message75');

        $idFrom = $minId;
        do {
            $idTo = $idFrom + $batchSize;
            $output->writeln('Normalize companies title: ' . $idTo . ' / ' . $maxId . ' ' . date('d.m.Y H:i:s'));

            $companies = $conn->createQueryBuilder()
                ->select('c.Message_ID as id, c.title')
                ->from('Message75', 'c')
                ->where('c.Message_ID >= :id_from')
                ->andWhere('c.Message_ID < :id_to')
                ->setParameter('id_from', $idFrom)
                ->setParameter('id_to', $idTo)
                ->execute()
                ->fetchAll()
            ;

            foreach ($companies as $company) {
                $conn->createQueryBuilder()
                    ->update('Message75', 'c')
                    ->set('c.normalized_title', ':normalized_title')
                    ->where('c.Message_ID = :company_id')
                    ->setParameter('company_id', $company['id'])
                    ->setParameter('normalized_title', Company::normalizeTitle($company['title']))
                    ->execute();
            }

            $idFrom = $idTo;
        } while ($idFrom <= $maxId);

        $output->writeln('Finish normalized company title. ' . date('d.m.Y H:i:s'));
    }
}
