<?php

namespace Metal\ProjectBundle\Command;

use Doctrine\ORM\EntityManager;
use Metal\ProjectBundle\Helper\FormattingHelper;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class InsertUserCanonicalPhoneCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('metal:project:insert-user-canonical-phone');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln(
            sprintf(
                '%s: Start command "%s"',
                date('d.m.Y H:i:s'),
                $this->getName()
            )
        );

        $doctrine = $this->getContainer()->get('doctrine');
        $em = $doctrine->getManager();
        /* @var  $em EntityManager */
        $conn = $em->getConnection();

        $rangeIds = $conn->fetchAssoc(
            'SELECT MIN(User_ID) AS _min, MAX(User_ID) AS _max FROM User WHERE (phone IS NOT NULL AND phone <> :empty)',
            array('empty' => '')
        );

        $idFrom = $rangeIds['_min'];
        do {
            $idTo = $idFrom + 5000;
            $rows = $conn->fetchAll(
                'SELECT phone, User_ID FROM User
                  WHERE (phone IS NOT NULL AND phone <> :empty) AND User_ID >= :idFrom AND User_ID <= :idTo',
                array(
                    'empty' => '',
                    'idFrom' => $idFrom,
                    'idTo' => $idTo,
                )
            );
            foreach ($rows as $val) {
                $newPhone = FormattingHelper::canonicalizePhone($val['phone']);
                $output->writeln(
                    sprintf(
                        'Old number: <info>%s</info>, Canonical number: <info>%s</info> for user id: <info>%s</info> ',
                        $val['phone'],
                        $newPhone,
                        $val['User_ID']
                    )
                );

                $conn->executeUpdate(
                    '
                    UPDATE User
                    SET phone_canonical = :newPhone
                    WHERE User_ID = :userId',
                    array('newPhone' => $newPhone, 'userId' => $val['User_ID'])
                );
            }

            $idFrom = $idTo;
        } while ($idFrom <= $rangeIds['_max']);

        $output->writeln(
            sprintf(
                '%s: End command "%s"',
                date('d.m.Y H:i:s'),
                $this->getName()
            )
        );
    }
}
