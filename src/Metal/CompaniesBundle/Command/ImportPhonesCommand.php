<?php

namespace Metal\CompaniesBundle\Command;

use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ImportPhonesCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('metal:companies:import-phones');

        $this->addOption('skip', null, InputOption::VALUE_OPTIONAL, '', 0);
        $this->addOption('limit', null, InputOption::VALUE_OPTIONAL, '', 50);
        $this->addOption('truncate', null, InputOption::VALUE_NONE);

        $this->addOption('company-id', null, InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY, 'array of companies ids', array());
        $this->addOption('company-slug', null, InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY, 'array of companies slugs', array());
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln(sprintf('%s: Start command "%s"', date('d.m.Y H:i:s'), $this->getName()));

        $limit = $input->getOption('limit') + 0;
        $skip  = $input->getOption('skip') + 0;

        $output->writeln(sprintf('%s: Limit: %s, Skip: %s', date('d.m.Y H:i:s'), $limit, $skip));

        $companiesIds = $input->getOption('company-id');
        $companiesSlugs = $input->getOption('company-slug');

        $em = $this->getContainer()->get('doctrine');
        /* @var $em EntityManager */
        $conn = $em->getConnection();
        /* @var $conn \Doctrine\DBAL\Connection */
        $conn->getConfiguration()->setSQLLogger(null);

        if ($input->getOption('truncate')) {
            $conn->executeQuery('UPDATE Message75 SET phones_as_string = NULL');
        }

        $output->writeln(sprintf('%s: Move user numbers "Phones to phone"', date('d.m.Y H:i:s')));
        $conn->executeQuery('UPDATE User SET phone = Phones WHERE Phones IS NOT NULL');
        $output->writeln(sprintf('%s: Finish move user numbers "Phones to phone"', date('d.m.Y H:i:s')));

        $countRow = $this->getCountRow($conn, $companiesIds, $companiesSlugs, $skip);
        $pagesCount = (int)ceil($countRow / $limit);

        if ($pagesCount < 0) {
            $output->writeln(sprintf('%s: Companies not found.', date('d.m.Y H:i:s')));
            exit();
        }

        $output->writeln(sprintf('%s: Count pages: %s', date('d.m.Y H:i:s'), $pagesCount));
        sleep(2);

        for ($i = 1; $i <= $pagesCount; $i++) {
            $output->writeln(sprintf('%s: Current page %s"', date('d.m.Y H:i:s'), $i));

            $companies = $this->getCompanies($conn, $companiesIds, $companiesSlugs, $skip, $limit);
            foreach ($companies as $company) {
                $this->processCompany($company, $output);
            }
        }

        $output->writeln(sprintf('%s: Finish command "%s" iteration %s', date('d.m.Y H:i:s'), $this->getName(), $i));
    }


    private function processCompany(array $company, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine');
        /* @var $em EntityManager */
        $conn = $em->getConnection();
        /* @var $conn \Doctrine\DBAL\Connection */
        $conn->getConfiguration()->setSQLLogger(null);

        $conn->createQueryBuilder()
            ->delete('company_phone')
            ->where('company_id = :id')
            ->setParameter('id', $company['Message_ID'])
            ->execute();

        $output->writeln(sprintf('%s: Original company phone str: %s', date('d.m.Y H:i:s'), $company['company_phone']));

        $phones = $this->parsePhoneString($company['company_phone']);

        $output->writeln(sprintf('%s: Insert phone %s number for company: %s', date('d.m.Y H:i:s'), count($phones), $company['Message_ID']));

        foreach ($phones as $phone) {
            $output->writeln(sprintf('%s: Phone number: %s', date('d.m.Y H:i:s'), $phone));
            $conn->executeQuery('INSERT INTO company_phone (company_id, phone) VALUES (:companyId, :phone)',
                array('companyId' => $company['Message_ID'], 'phone' => $phone)
            );
        }

        $newPhonesAsString = implode(',', $phones);

        $output->writeln(sprintf('%s: Phone as string: %s', date('d.m.Y H:i:s'), $newPhonesAsString));

        $conn->executeQuery('UPDATE Message75 SET phones_as_string = :phoneString WHERE Message_ID = :id',
            array('id' => $company['Message_ID'], 'phoneString' => $newPhonesAsString)
        );

        $qb = $conn->createQueryBuilder()->select('*')
            ->from('company_delivery_city', 'cdc')
             // исключаем филиал главного города.
            ->andWhere('cdc.city_id != :company_id')
            ->setParameter('company_id', $company['company_city']);

        $qb->andWhere('cdc.company_id = :id')
            ->setParameter('id', $company['Message_ID']);

        $qb->andWhere('cdc.phone <> :phoneStr')
            ->setParameter('phoneStr', '');

        $qb->andWhere('cdc.phone IS NOT NULL');

        $branchCities = $qb->execute()->fetchAll();

        $output->writeln(sprintf('%s: Find %s filials for %s company', date('d.m.Y H:i:s'), count($branchCities), $company['Message_ID']));

        foreach ($branchCities as $branchCity) {
            $phones = $this->parsePhoneString($branchCity['phone']);

            $output->writeln(sprintf('%s: Original phone str %s for branch %s', date('d.m.Y H:i:s'), $branchCity['phone'], $branchCity['id']));

            foreach ($phones as $phone) {
                $output->writeln(sprintf('%s: Phone number %s for branch %s', date('d.m.Y H:i:s'), $phone, $branchCity['id']));
                $conn->executeQuery('INSERT INTO company_phone (branch_office_id, company_id, phone) VALUES (:branch_office_id, :companyId, :phone)',
                    array(
                        'companyId' => $company['Message_ID'],
                        'phone' => $phone,
                        'branch_office_id' => $branchCity['id']
                    )
                );
            }

            $newPhonesAsString = implode(',', $phones);

            $output->writeln(sprintf('%s: Phone as string: %s', date('d.m.Y H:i:s'), $newPhonesAsString));

            $conn->executeQuery('UPDATE company_delivery_city SET phones_as_string = :phoneString WHERE id = :id',
                array('id' => $branchCity['id'], 'phoneString' => $newPhonesAsString)
            );
        }
        $output->writeln("\n");
    }


    // парсим строку, возвращаем просто список телефонов
    // было "+1344555; +33131313"
    // станет array("+1344555" "+33131313")
    private function parsePhoneString($phoneString)
    {
        $phoneString = trim($phoneString);
        $numberSplit = preg_split('/[\.\,\;]/ui', $phoneString, -1);
        $result = array();
        foreach ($numberSplit as $number) {

            $numberInt = preg_replace('/\D/ui', '', $number);
            $countNumbersLetters = strlen($numberInt);

            //Возвращаем оригенальную строку c с вырезаными лишними символами
            if ($countNumbersLetters < 6 || $countNumbersLetters > 13){
                $result = array($phoneString);

                return $result;
            }

            if ($countNumbersLetters >= 6 && $countNumbersLetters <= 13){
                $result[] = $number;
            }
        }

        if (!count($result) && strlen($phoneString) >= 4) {
            $result = array($phoneString);
        }

        return $result;
    }

    protected function getCountRow($conn, $companiesIds, $companiesSlugs, $skip)
    {
        $qb = $conn->createQueryBuilder()->select('count(com.Message_ID) as count')
            ->from('Message75', 'com');

        if ($companiesIds || $companiesSlugs) {
            if ($companiesIds) {
                $qb->orWhere('com.Message_ID IN (:ids)')
                    ->setParameter('ids', $companiesIds, Connection::PARAM_INT_ARRAY);
            }

            if ($companiesSlugs) {
                $qb->orWhere('com.slug IN (:slugs)')
                    ->setParameter('slugs', $companiesSlugs, Connection::PARAM_STR_ARRAY);
            }
        } else {
            $qb->andWhere("(com.phones_as_string = '' OR com.phones_as_string IS NULL)")
                ->andWhere("(com.company_phone <> '' AND com.company_phone IS NOT NULL)");
            // старые телефоны есть, а новых нет
        }

        $companies = $qb->execute()->fetchColumn();
        $countRow = intval($companies) - $skip;

        return $countRow;
    }

    protected function getCompanies($conn, $companiesIds, $companiesSlugs, $skip, $limit)
    {
        $qb = $conn->createQueryBuilder()->select('com.Message_ID, com.company_phone, com.company_city')
            ->from('Message75', 'com');

        if ($companiesIds || $companiesSlugs) {

            if ($companiesIds) {
                $qb->orWhere('com.Message_ID IN (:ids)')
                    ->setParameter('ids', $companiesIds, Connection::PARAM_INT_ARRAY);
            }

            if ($companiesSlugs) {
                $qb->orWhere('com.slug IN (:slugs)')
                    ->setParameter('slugs', $companiesSlugs, Connection::PARAM_STR_ARRAY);
            }

        } else {
            $qb->andWhere("(com.phones_as_string = '' OR com.phones_as_string IS NULL)")
                ->andWhere("(com.company_phone <> '' AND com.company_phone IS NOT NULL)");
            // старые телефоны есть, а новых нет

            if ($skip) {
                $qb->setFirstResult($skip);
            }

            if ($limit) {
                $qb->setMaxResults($limit);
            }
        }

        $companies = $qb->execute()->fetchAll();

        return $companies;
    }
}
