<?php

namespace Metal\CompaniesBundle\Command;

use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UpdateCompanyTypeCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('metal:companies:update-company-type');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln(sprintf('%s: Start command "%s"', date('d.m.Y H:i:s'), $this->getName()));
        $em = $this->getContainer()->get('doctrine');
        /* @var $em EntityManager */
        $conn = $em->getConnection();
        $conn->getConfiguration()->setSQLLogger(null);

        $companyTypes = array(
            3 => 1,
            1 => 2,
            2 => 3,
        );
        $inc = 100;

        $conn->executeUpdate('UPDATE Message75 SET company_type = company_type + :inc WHERE Message_ID > 2047301 AND company_type IN (:types)',
            array(
                'inc'     => $inc,
                'types'   => array_keys($companyTypes),
            ),
            array(
                'created' => 'datetime',
                'types'   => Connection::PARAM_INT_ARRAY,
            )
        );

        foreach ($companyTypes as $companyTypeId => $actualCompanyTypeId) {
            $companyTypeId += $inc;

            $conn->executeQuery(
                'UPDATE Message75 SET company_type = :actual_type_id, LastUpdated = :date WHERE company_type = :type_id',
                array(
                    'actual_type_id' => $actualCompanyTypeId,
                    'type_id' => $companyTypeId,
                    'date' => new \DateTime(),
                ),
                array(
                    'date' => 'datetime'
                )
            );
        }
        $output->writeln(sprintf('%s: Done', date('d.m.Y H:i:s')));
    }
}
