<?php

namespace Metal\ProjectBundle\Command;

use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManager;
use Metal\ProjectBundle\Util\InsertUtil;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class NormalizeEmailsCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('metal:project:normalize-emails');
        $this->addOption('truncate', null, InputOption::VALUE_NONE);
        $this->addOption('company-email', null, InputOption::VALUE_OPTIONAL, '', true);
        $this->addOption('user-email', null, InputOption::VALUE_OPTIONAL, '', true);
        $this->addOption('demand-email', null, InputOption::VALUE_OPTIONAL, '', true);
        $this->addOption('user-send-email', null, InputOption::VALUE_OPTIONAL, '', true);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln(sprintf('%s: Start command "%s"', date('d.m.Y H:i:s'), $this->getName()));
        $doctrine = $this->getContainer()->get('doctrine');
        $em = $doctrine->getManager();
        /* @var  $em EntityManager */
        $conn = $em->getConnection();

        if ($input->getOption('truncate')) {
            $output->writeln(sprintf('%s: <info>Truncate normalized_email.</info>', date('d.m.Y H:i:s')));
            $conn->executeUpdate('TRUNCATE normalized_email');
        }

        if ($input->getOption('company-email')) {
            $this->companyInsert($output, $conn);
        }

        if ($input->getOption('user-email')) {
            $this->userInsert($output, $conn);
        }

        if ($input->getOption('demand-email')) {
            $this->demandInsert($output, $conn);
        }

        if ($input->getOption('user-send-email')) {
            $this->userSendEmail($output, $conn);
        }

        $output->writeln(sprintf('%s: Finish command "%s"', date('d.m.Y H:i:s'), $this->getName()));
    }

    private function userSendEmail(OutputInterface $output, Connection $conn)
    {
        $output->writeln(sprintf('%s: <info>Insert user send email.</info>', date('d.m.Y H:i:s')));

        $rangeIds = $conn->fetchAssoc(
            'SELECT MIN(ID) AS _min, MAX(ID) AS _max FROM UserSend WHERE (Email IS NOT NULL AND Email <> :empty)',
            array('empty' => '')
        );

        $idFrom = $rangeIds['_min'];
        do {
            $idTo = $idFrom + 1000;
            $rows = $conn->fetchAll(
                'SELECT Email AS email, user_id, ID AS subscriber_id FROM UserSend 
                  WHERE (Email IS NOT NULL AND Email <> :empty) AND ID >= :idFrom AND ID <= :idTo',
                array(
                    'empty' => '',
                    'idFrom' => $idFrom,
                    'idTo' => $idTo
                )
            );

            InsertUtil::insertMultipleOrUpdate($conn, 'normalized_email', $rows, array('user_id'), 100);

            $output->writeln(sprintf('%s:<info>Insert user send email.</info> - <info>IdFrom: %d</info> <info>IdTo: %d.</info>',
                date('d.m.Y H:i:s'),
                $idFrom,
                $idTo
            ));

            $idFrom = $idTo;
        } while ($idFrom <= $rangeIds['_max']);
    }

    private function demandInsert(OutputInterface $output, Connection $conn)
    {
        $output->writeln(sprintf('%s: <info>Insert demands email.</info>', date('d.m.Y H:i:s')));

        $rangeIds = $conn->fetchAssoc(
            'SELECT MIN(id) AS _min, MAX(id) AS _max FROM demand WHERE (email IS NOT NULL AND email <> :empty)',
            array('empty' => '')
        );

        $idFrom = $rangeIds['_min'];
        do {
            $idTo = $idFrom + 1000;

            $rows = $conn->fetchAll(
                'SELECT email, id AS demand_id FROM demand WHERE (email IS NOT NULL AND email <> :empty) AND id >= :idFrom 
                  AND id <= :idTo
                  GROUP BY id, email
                 ',
                array(
                    'empty' => '',
                    'idFrom' => $idFrom,
                    'idTo' => $idTo
                )
            );

            InsertUtil::insertMultipleOrUpdate($conn, 'normalized_email', $rows, array('demand_id'), 100);

            $output->writeln(sprintf('%s: <info>Insert demands email.</info> - <info>IdFrom: %d</info> <info>IdTo: %d.</info>',
                date('d.m.Y H:i:s'),
                $idFrom,
                $idTo
            ));

            $idFrom = $idTo;
        } while ($idFrom <= $rangeIds['_max']);
    }

    private function userInsert(OutputInterface $output, Connection $conn)
    {
        $output->writeln(sprintf('%s: <info>Insert users email.</info>', date('d.m.Y H:i:s')));

        $rangeIds = $conn->fetchAssoc(
            "SELECT MIN(User_ID) AS _min, MAX(User_ID) AS _max FROM User WHERE ((Email IS NOT NULL AND Email <> '') OR (Email2 IS NOT NULL AND Email2 <> ''))",
            array('empty' => '')
        );

        $idFrom = $rangeIds['_min'];

        do {
            $idTo = $idFrom + 1000;

            $rows = $conn->fetchAll(
                "SELECT concat(IFNULL(Email, ''), IFNULL(Email2, '')) AS email, User_ID AS user_id FROM User WHERE ((Email IS NOT NULL AND Email <> '') OR 
                  (Email2 IS NOT NULL AND Email2 <> '')) AND User_ID >= :idFrom 
                  AND User_ID <= :idTo GROUP BY user_id, email",
                array(
                    'idFrom' => $idFrom,
                    'idTo' => $idTo
                )
            );

            InsertUtil::insertMultipleOrUpdate($conn, 'normalized_email', $rows, array('user_id'), 100);

            $output->writeln(sprintf('%s: <info>Insert users email</info> - <info>IdFrom: %d</info> <info>IdTo: %d.</info>',
                date('d.m.Y H:i:s'),
                $idFrom,
                $idTo
            ));

            $idFrom = $idTo;
        } while ($idFrom <= $rangeIds['_max']);
    }

    private function companyInsert(OutputInterface $output, Connection $conn)
    {
        $output->writeln(sprintf('%s: <info>Insert companies email.</info>', date('d.m.Y H:i:s')));

        $rangeIds = $conn->fetchAssoc(
            "SELECT MIN(id) AS _min, MAX(id) AS _max
                FROM company_delivery_city
                WHERE mail IS NOT NULL AND mail <> ''",
            array('empty' => '')
        );

        $idFrom = $rangeIds['_min'];

        do {
            $idTo = $idFrom + 1000;

            $rows = $conn->fetchAll(
                "SELECT
                  mail AS email, company_id
                FROM company_delivery_city
                WHERE mail IS NOT NULL AND mail <> '' AND id >= :idFrom 
                  AND id <= :idTo GROUP BY id, email",
                array(
                    'empty' => '',
                    'idFrom' => $idFrom,
                    'idTo' => $idTo
                )
            );

            InsertUtil::insertMultipleOrUpdate($conn, 'normalized_email', $rows, array('company_id'), 100);

            $output->writeln(sprintf('%s: <info>Insert companies email</info> - <info>IdFrom: %d</info> <info>IdTo: %d.</info>',
                date('d.m.Y H:i:s'),
                $idFrom,
                $idTo
            ));

            $idFrom = $idTo;
        } while ($idFrom <= $rangeIds['_max']);
    }
}
