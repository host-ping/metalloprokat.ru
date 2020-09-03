<?php

namespace Metal\DemandsBundle\Command;

use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DemandFakeUpdatingCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('metal:demands:fake-updating');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln(sprintf('%s: Start command "%s"', date('d.m.Y H:i:s'), $this->getName()));

        $doctrine = $this->getContainer()->get('doctrine');
        $em = $doctrine->getManager();
        /* @var  $em EntityManager */
        $conn = $em->getConnection();
        $monthBefore = new \DateTime('-30 days');
        $now = new \DateTime();

        // выставляем некоторым заявкам fake_updated_at, чтоб раномерно на 30 дней распределить обновление
        $conn->executeUpdate(
            'UPDATE demand SET fake_updated_at = updated_at WHERE MOD(id, 30) = :current_day',
            array('current_day' => $now->format('d'))
        );

        // обновляем старые заявки
        $em->createQueryBuilder()
            ->update('MetalDemandsBundle:Demand', 'd')
            ->set('d.fakeUpdatedAt', ':now')
            ->where('d.fakeUpdatedAt < :days')
            ->andWhere("d.fakeUpdatedAt IS NOT NULL")
            ->setParameter('now', $now)
            ->setParameter('days', $monthBefore)
            ->getQuery()
            ->execute();

        $output->writeln(sprintf('%s: Finish command.', date('d.m.Y H:i:s')));
    }
}
