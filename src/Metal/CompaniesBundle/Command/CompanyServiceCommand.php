<?php

namespace Metal\CompaniesBundle\Command;

use Doctrine\ORM\EntityManager;
use Metal\CompaniesBundle\Entity\ValueObject\CompanyServiceProvider;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CompanyServiceCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('metal:companies:company-service-update');
        $this->addOption('truncate', null, InputOption::VALUE_NONE);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln(sprintf('%s: Start command "%s"', date('d.m.Y H:i:s'), $this->getName()));
        $em = $this->getContainer()->get('doctrine');
        /* @var $em EntityManager */

        $typeRezka = CompanyServiceProvider::create(2);

        if ($input->getOption('truncate')) {
            $em->getConnection()->executeQuery('TRUNCATE company_attribute');
        }

        $em->getConnection()->executeQuery('
            INSERT IGNORE INTO company_attribute (company_id, type)
            (
              SELECT distinct Company_ID, :type
              FROM Message142
              WHERE
                Category_ID = :rezkaCategoryId
                OR P_Category_ID = :rezkaCategoryId
            )',
            array('type' => $typeRezka->getId(), 'rezkaCategoryId' => 206)
        );

        $typeGibka = CompanyServiceProvider::create(3);
        $em->getConnection()->executeQuery('
            INSERT IGNORE INTO company_attribute (company_id, type)
            (
              SELECT distinct Company_ID, :type
              FROM Message142
              WHERE
                Category_ID = :rezkaCategoryId
                OR P_Category_ID = :rezkaCategoryId
            )',
            array('type' => $typeGibka->getId(), 'rezkaCategoryId' => 204)
        );

        $output->writeln(sprintf('%s: Done command "%s"', date('d.m.Y H:i:s'), $this->getName()));
    }
}
