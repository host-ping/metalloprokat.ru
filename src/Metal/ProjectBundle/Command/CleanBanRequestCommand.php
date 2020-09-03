<?php

namespace Metal\ProjectBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CleanBanRequestCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('metal:project:clean-ban-request')
            ->addOption('days', null, InputOption::VALUE_OPTIONAL, 'Сколько дней хранить лог.', 10)
            ->setDescription('Очищает таблицу ban_request и ban_request_pixel от записей созданых больше --days (дней назад).')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln(sprintf('%s: Start command "%s"', date('d.m.Y H:i:s'), $this->getName()));

        $days = (int) $input->getOption('days');
        $conn = $this->getContainer()->get('doctrine.orm.entity_manager')->getConnection();

        $dateFrom = new \DateTime(sprintf('-%s day', $days));

        $output->writeln(sprintf('%s: Delete logs older than %d days (%s).', date('d.m.Y H:i:s'), $days, $dateFrom->format('d.m.Y H:i:s')));

        $output->writeln(sprintf('%s: Clear ban_request.', date('d.m.Y H:i:s')));
        $conn->createQueryBuilder()
            ->delete('ban_request')
            ->where('created_at < :date')
            ->setParameter('date', $dateFrom, 'datetime')
            ->execute()
        ;

        $output->writeln(sprintf('%s: Clear ban_request_pixel.', date('d.m.Y H:i:s')));
        $conn->createQueryBuilder()
            ->delete('ban_request_pixel')
            ->where('created_at < :date')
            ->setParameter('date', $dateFrom, 'datetime')
            ->execute()
        ;

        $output->writeln(sprintf('%s: Finish command "%s"', date('d.m.Y H:i:s'), $this->getName()));
    }
}