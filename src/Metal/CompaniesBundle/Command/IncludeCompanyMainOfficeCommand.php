<?php

namespace Metal\CompaniesBundle\Command;

use Doctrine\DBAL\Connection;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class IncludeCompanyMainOfficeCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('metal:companies:include-company-main-office');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln(sprintf('%s: Start command "%s"', date('d.m.Y H:i:s'), $this->getName()));
        $em = $this->getContainer()->get('doctrine');
        $now = new \DateTime();

        $conn = $em->getConnection();
        /* @var $conn Connection  */

        $output->writeln(sprintf('%s: Update', date('d.m.Y H:i:s')));
        $conn->executeUpdate('UPDATE company_delivery_city SET is_main_office = 0;');
        $conn->executeUpdate('UPDATE company_delivery_city cd
            JOIN Message75 com ON com.company_city = cd.city_id AND com.Message_ID = cd.company_id
            SET cd.is_main_office = 1;
        ');

        $output->writeln(sprintf('%s: Insert', date('d.m.Y H:i:s')));
        $conn->executeQuery('INSERT INTO company_delivery_city (company_id, city_id, kind, is_main_office, created_at, updated_at, phone, adress, address_new)
            SELECT c.Message_ID, c.company_city, 0, 1, :now, :now, c.phones_as_string, c.address_new, c.address_new
            FROM Message75 c
            WHERE c.company_city IS NOT NULL AND NOT EXISTS (
            SELECT cd.id
            FROM company_delivery_city cd
            WHERE cd.company_id = c.Message_ID AND cd.city_id = c.company_city
        )',
            array('now' => $now), array('now' => 'datetime'));

        $output->writeln(sprintf('%s: Completed', date('d.m.Y H:i:s')));
    }
} 