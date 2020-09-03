<?php

namespace Metal\UsersBundle\Command;

use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class SetRightToEditCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('metal:users:set-right-to-edit')
            ->addOption('truncate', null, InputOption::VALUE_NONE)
            ->addOption('company-id', null, InputOption::VALUE_OPTIONAL);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln(sprintf('%s: Start command "%s"', date('d.m.Y H:i:s'), $this->getName()));

        $doctrine = $this->getContainer()->get('doctrine');
        $em = $doctrine->getManager();
        /* @var  $em EntityManager */

        $conn = $em->getConnection();
        /* @var $conn Connection */

        $conn->getConfiguration()->setSQLLogger(null);

        $companyId = $input->getOption('company-id');

        if ($companyId) {
            $companiesIds = $conn->fetchAll(
                'SELECT Message_ID FROM Message75 WHERE Message_ID = :company_id',
                array('company_id' => $companyId)
            );
        } else {
            $companiesIds = $conn->fetchAll('SELECT Message_ID FROM Message75');
        }

        foreach ($companiesIds as $companyId) {
            $companyId = $companyId['Message_ID'];
            $usersIds = $conn->fetchAll('SELECT User_ID, RigthToEdit, Created FROM User WHERE ConnectCompany = :company_id ORDER BY Created ASC', array('company_id' => $companyId));

            if (!$usersIds) {
                continue; // no users
            }

            $allowUserId = null;
            if (count($usersIds) == 1) {
                $allowUserId = $usersIds[0]['User_ID'];
            } else {
                $hasOne = false;
                foreach ($usersIds as $userId) {
                    if ($userId['RigthToEdit']) {
                        $hasOne = true;
                    }
                }
                if (!$hasOne) {
                    $allowUserId = $usersIds[0]['User_ID'];
                }
            }

            if ($allowUserId) {
                $conn->executeUpdate('
                    UPDATE User
                    SET RigthToEdit = true, RightToUse = true
                    WHERE User_ID = :user_id',
                    array('user_id' => $allowUserId)
                );
            }

            $output->writeln($companyId.' '.date('d.m.Y H:i:s'));
        }

        $output->writeln(sprintf('%s: Done command "%s"', date('d.m.Y H:i:s'), $this->getName()));
    }
}
