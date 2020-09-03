<?php

namespace Metal\NewsletterBundle\Command;

use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Validator\Constraints as Assert;

class ValidateEmailsCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('metal:newsletter:validate-emails');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln(sprintf('%s: Start command "%s"', date('d.m.Y H:i:s'), $this->getName()));
        $em = $this->getContainer()->get('doctrine');
        /* @var $em EntityManager */
        $conn = $em->getConnection();

        $siteRepository = $em->getRepository('MetalProjectBundle:Site');
        $siteRepository->disableLogging();

        $minId = $conn->fetchColumn('SELECT MIN(ID) FROM UserSend');
        $maxId = $conn->fetchColumn('SELECT MAX(ID) FROM UserSend');

        $emailConstraint = new Assert\Email(array('checkHost' => true, 'checkMX' => true, 'strict' => true));

        $idFrom = $minId;
        $validator = $this->getContainer()->get('validator');

        do {
            $idTo = $idFrom + 1000;
            $output->writeln(sprintf('%s: idFrom: %s idTo: %s', date('d.m.Y H:i:s'), $idFrom, $idTo));

            $subscribers = $conn->fetchAll(
                'SELECT ID, Email FROM UserSend WHERE ID >= :id_from AND ID < :id_to AND is_invalid = :is_invalid',
                array(
                    'id_from' => $idFrom,
                    'id_to' => $idTo,
                    'is_invalid' => false,
                )
            );

            foreach ($subscribers as $subscriber) {
                $email = strtolower($subscriber['Email']);
                $errorList = $validator->validate($email, $emailConstraint);

                if (0 === count($errorList)) {
                    $output->writeln(sprintf('%s: Email "%s" валидный.', date('d.m.Y H:i:s'), $email));
                    continue;
                }

                $errorMessage = $errorList[0]->getMessage();

                $output->writeln(sprintf('%s: %s : %s', date('d.m.Y H:i:s'), $errorMessage, $email));
                $conn->executeUpdate(
                    'UPDATE UserSend SET is_invalid = :is_invalid WHERE ID = :id',
                    array('is_invalid' => true, 'id' => $subscriber['ID'])
                );
            }

            $idFrom = $idTo;
        } while ($idFrom <= $maxId);

        $siteRepository->restoreLogging();

        $output->writeln(sprintf('%s: Done command "%s"', date('d.m.Y H:i:s'), $this->getName()));
    }
}
