<?php

namespace Metal\ProjectBundle\Command;

use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class NormalizePhonesCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('metal:project:normalize-phones');
        $this->addOption('truncate', null, InputOption::VALUE_NONE);
        $this->addOption('company-phone', null, InputOption::VALUE_OPTIONAL, '', true);
        $this->addOption('company-city-phone', null, InputOption::VALUE_OPTIONAL, '', true);
        $this->addOption('user-phone', null, InputOption::VALUE_OPTIONAL, '', true);
        $this->addOption('demand-phone', null, InputOption::VALUE_OPTIONAL, '', true);
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
            $conn->executeUpdate('TRUNCATE normalized_phones');
        }

        if ($input->getOption('company-phone')) {
            $output->writeln(sprintf('%s: <info>Update companies phones.</info>', date('d.m.Y H:i:s')));
            $companiesPhones = $conn->fetchAll(
                "SELECT company_id, phone FROM company_phone WHERE phone <> :empty",
                array('empty' => '')
            );

            foreach ($companiesPhones as $companyPhone) {
                $phone = preg_replace('/\D/ui', '', $companyPhone['phone']);
                if (!$phone) {
                    continue;
                }

                $conn->executeUpdate(
                    'INSERT IGNORE INTO normalized_phones (phone, company_id) VALUE (:phone, :company)',
                    array(
                        'phone' => $phone,
                        'company' => $companyPhone['company_id']
                    ),
                    array(
                        'phone' => \PDO::PARAM_STR
                    )
                );

                $output->writeln(
                    sprintf(
                        'Default number: <info>%s</info>, Normalize number: <info>%s</info> for company: <info>%s</info> ',
                        $companyPhone['phone'],
                        $phone,
                        $companyPhone['company_id']
                    )
                );
            }

            unset($companiesPhones);
        }


        if ($input->getOption('user-phone')) {
            $output->writeln(sprintf('%s: <info>Update users phones.</info>', date('d.m.Y H:i:s')));
            $usersPhones = $conn->fetchAll(
                "SELECT phone, User_ID FROM User WHERE phone <> :empty AND phone IS NOT NULL",
                array('empty' => '')
            );

            foreach ($usersPhones as $userPhone) {
                $phone = preg_replace('/\D/ui', '', $userPhone['phone']);
                if (!$phone) {
                    continue;
                }

                $conn->executeUpdate(
                    'INSERT INTO normalized_phones (phone, user_id) VALUE (:phone, :user)',
                    array(
                        'phone' => $phone,
                        'user' => $userPhone['User_ID']
                    ),
                    array(
                        'phone' => \PDO::PARAM_STR
                    )
                );

                $output->writeln(
                    sprintf(
                        'Default number: <info>%s</info>, Normalize number: <info>%s</info> for user id: <info>%s</info> ',
                        $userPhone['phone'],
                        $phone,
                        $userPhone['User_ID']
                    )
                );
            }

            unset($usersPhones);
        }

        if ($input->getOption('demand-phone')) {
            $output->writeln(sprintf('%s: Update demands phones.', date('d.m.Y H:i:s')));
            $demandPhones = $conn->fetchAll(
                "SELECT phone, id AS demand_id FROM demand WHERE phone <> :empty AND phone IS NOT NULL",
                array('empty' => '')
            );

            foreach ($demandPhones as $demandPhone) {
                $phone = preg_replace('/\D/ui', '', $demandPhone['phone']);
                if (!$phone) {
                    continue;
                }

                $conn->executeUpdate(
                    'INSERT INTO normalized_phones (phone, demand_id) VALUE (:phone, :demand_id)',
                    array(
                        'phone' => $phone,
                        'demand_id' => $demandPhone['demand_id']
                    ),
                    array(
                        'phone' => \PDO::PARAM_STR
                    )
                );

                $output->writeln(
                    sprintf(
                        'Default number: <info>%s</info>, Normalize number: <info>%s</info> for demand: %s',
                        $demandPhone['phone'],
                        $phone,
                        $demandPhone['demand_id']
                    )
                );
            }

            unset($demandPhones);
        }

        if ($input->getOption('company-city-phone')) {
            $output->writeln(sprintf('%s: <info>Update companies cities phones.</info>', date('d.m.Y H:i:s')));
            $companyCityPhones = $conn->fetchAll(
                "SELECT phone, company_id FROM company_delivery_city WHERE phone <> :empty AND phone IS NOT NULL",
                array('empty' => '')
            );

            foreach ($companyCityPhones as $companyCityPhone) {
                $phone = preg_replace('/\D/ui', '', $companyCityPhone['phone']);
                if (!$phone) {
                    continue;
                }

                $conn->executeUpdate(
                    'INSERT INTO normalized_phones (phone, company_id) VALUE (:phone, :company_id)',
                    array(
                        'phone' => $phone,
                        'company_id' => $companyCityPhone['company_id']
                    ),
                    array(
                        'phone' => \PDO::PARAM_STR
                    )
                );

                $output->writeln(
                    sprintf(
                        'Default number: <info>%s</info>, Normalize number: <info>%s</info> for company: %s',
                        $companyCityPhone['phone'],
                        $phone,
                        $companyCityPhone['company_id']
                    )
                );
            }
        }

        $output->writeln(sprintf('%s: Done "%s"', date('d.m.Y H:i:s'), $this->getName()));
    }
}
