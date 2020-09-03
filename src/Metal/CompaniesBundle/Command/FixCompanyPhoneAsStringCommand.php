<?php

namespace Metal\CompaniesBundle\Command;

use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class FixCompanyPhoneAsStringCommand extends ContainerAwareCommand
{
    /**
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     * @var Connection
     */
    private $conn;

    /**
     * @var InputInterface
     */
    protected $input;

    /**
     * @var OutputInterface
     */
    protected $output;

    protected function configure()
    {
        $this->setName('metal:companies:fix-company-phone-as-string');
        $this->addOption('company-id', null, InputOption::VALUE_OPTIONAL);
        $this->addOption('truncate', null, InputOption::VALUE_NONE);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln(sprintf('%s: Start command "%s"', date('d.m.Y H:i:s'), $this->getName()));

        $this->em = $this->getContainer()->get('doctrine')->getManager();
        $this->input = $input;
        $this->output = $output;

        $this->conn = $this->em->getConnection();
        $this->conn->getConfiguration()->setSQLLogger(null);

        if ($this->input->getOption('truncate')) {
            if (null !== $this->input->getOption('company-id')) {
                $this->conn->executeQuery(
                    'UPDATE Message75 SET phones_as_string = NULL WHERE Message_ID = :company_id',
                    array(
                        'company_id' => $this->input->getOption('company-id')
                    )
                );

                $this->conn->executeQuery(
                    'UPDATE company_delivery_city SET phones_as_string = NULL WHERE company_id = :company_id',
                    array(
                        'company_id' => $this->input->getOption('company-id')
                    )
                );
            } else {
                $this->conn->executeQuery('UPDATE Message75 SET phones_as_string = NULL');
                $this->conn->executeQuery('UPDATE company_delivery_city SET phones_as_string = NULL');
            }
        }

        $this->fillCompaniesPhonesAsString();


        $output->writeln(sprintf('%s: Finish command "%s" ', date('d.m.Y H:i:s'), $this->getName()));
    }

    private function fillCompaniesPhonesAsString()
    {
        if (null !== $this->input->getOption('company-id')) {
            $rangeIds = $this->conn->fetchAssoc(
                'SELECT MIN(Message_ID) AS minId, MAX(Message_ID) AS maxId FROM Message75 WHERE Message_ID = :company_id',
                array(
                    'company_id' => $this->input->getOption('company-id')
                )
            );
        } else {
            $rangeIds = $this->conn->fetchAssoc('SELECT MIN(Message_ID) AS minId, MAX(Message_ID) AS maxId FROM Message75');
        }

        $i = 0;
        $limit = 1000;
        $idFrom = $rangeIds['minId'];

        do {
            $idTo = $idFrom + $limit;
            $qb = $this->conn->createQueryBuilder()
                ->select('c.Message_ID')
                ->from('Message75', 'c')
            ;

            if ($this->input->getOption('company-id')) {
                $qb->where('c.Message_ID = :company_id')
                    ->setParameter('company_id', $this->input->getOption('company-id'))
                ;
            } else {
                $qb->where('c.Message_ID >= :id_from')
                    ->setParameter('id_from', $idFrom)
                    ->andWhere('c.Message_ID < :id_to')
                    ->setParameter('id_to', $idTo)
                ;
            }

            $companiesIds = $qb->execute()->fetchAll();

            $companiesIds = array_column($companiesIds, 'Message_ID');

            $phonesByCompaniesTmp = $this->em->getRepository('MetalCompaniesBundle:CompanyPhone')
                ->createQueryBuilder('p')
                ->select('p.additionalCode, p.phone, IDENTITY(p.company) AS company_id')
                ->where('p.company IN (:companies_ids)')
                ->andWhere('p.branchOffice IS NULL')
                ->setParameter('companies_ids', $companiesIds)
                ->getQuery()
                ->getResult();

            $phonesByCompanies = array();
            foreach ($phonesByCompaniesTmp as $phone) {
                if ($phone['additionalCode']) {
                    $phonesByCompanies[$phone['company_id']][] = $phone['phone'].' доб. '.$phone['additionalCode'];
                } else {
                    $phonesByCompanies[$phone['company_id']][] = $phone['phone'];
                }
            }

            foreach ($phonesByCompanies as $companyId => $phones) {
                $this->conn->executeUpdate(
                    'UPDATE Message75 SET phones_as_string = :phone WHERE Message_ID = :id',
                    array('phone' => implode(', ', $phones), 'id' => $companyId)
                );
            }

            $phonesByOfficesTmp = $this->em->getRepository('MetalCompaniesBundle:CompanyPhone')
                ->createQueryBuilder('p')
                ->select('p.additionalCode, p.phone, IDENTITY(p.branchOffice) AS office_id')
                ->where('p.company IN (:companies_ids)')
                ->andWhere('p.branchOffice IS NOT NULL')
                ->setParameter('companies_ids', $companiesIds)
                ->getQuery()
                ->getResult();

            $phonesByOffices = array();
            foreach ($phonesByOfficesTmp as $phone) {
                if ($phone['additionalCode']) {
                    $phonesByOffices[$phone['office_id']][] = $phone['phone'].' доб. '.$phone['additionalCode'];
                } else {
                    $phonesByOffices[$phone['office_id']][] = $phone['phone'];
                }
            }

            foreach ($phonesByOffices as $officeId => $phones) {
                $this->conn->executeUpdate(
                    'UPDATE company_delivery_city SET phones_as_string = :phone WHERE id = :id',
                    array('phone' => implode(', ', $phones), 'id' => $officeId)
                );
            }

            $idFrom = $idTo;

            $i++;
            if ($i % 5 == 0) {
                $this->output->writeln($idTo.' / '.$rangeIds['maxId'].' '.date('d.m.Y H:i:s'));
            }
        } while ($idFrom <= $rangeIds['maxId']);
    }
}
