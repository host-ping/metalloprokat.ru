<?php

namespace Metal\StatisticBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class TransferStatsProductsChangeCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('metal:stats:transfer-statistic')
            ->addOption('year', null, InputOption::VALUE_OPTIONAL, 'Год который обрабатываем.')
            ->addOption('delete', null, InputOption::VALUE_NONE, 'Удалять gz архив.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln(sprintf('%s: Start command "%s"', date('d.m.Y H:i:s'), $this->getName()));

        $container = $this->getContainer();
        $year = (int)$container->getParameter('project.copyright_year');

        $yearNow = (int)date('Y');
        if ($input->getOption('year')) {
            $year = $yearNow = (int)$input->getOption('year');
        }

        for (; $year <= $yearNow; $year++) {
            for ($month = 1; $month <= 12; $month++) {
                $dateFormat = \DateTime::createFromFormat('Y-m', $year.'-'.$month)->format('Y-m');
                $output->writeln(sprintf('%s: <info>Обрабатывается дата: "%s".</info>', date('d.m.Y H:i:s'), $dateFormat));
                $archivePath = sprintf(
                    '%s/stats_product_change-%s.sql.gz',
                    $container->getParameter('upload_dir').'/archive',
                    $dateFormat
                );

                if (!file_exists($archivePath)) {
                    $output->writeln(sprintf('%s: <error>Архива с дампом не существует, пропускаем дату "%s".</error>', date('d.m.Y H:i:s'), $dateFormat));
                    continue;
                }

                $output->writeln(sprintf('%s: Путь к архиву с дампом: "%s".', date('d.m.Y H:i:s'), $archivePath));

                $sqlPath =  sprintf(
                    '%s/stats_product_change-%s.sql',
                    $container->getParameter('kernel.cache_dir'),
                    $dateFormat
                );
                $output->writeln(sprintf('%s: Путь к временному sql файлу: "%s".', date('d.m.Y H:i:s'), $sqlPath));

                $output->writeln(sprintf('%s: <info>Разархивируем архив...</info>', date('d.m.Y H:i:s')));
                exec(sprintf('gunzip -c %s > %s', $archivePath, $sqlPath), $processOutput, $success);
                if ($success !== 0) {
                    throw new \RuntimeException(sprintf('Error exec code: %s', $success));
                }
                $output->writeln(sprintf('%s: Успешно', date('d.m.Y H:i:s')));

                $output->writeln(sprintf('%s: Делаем замену строк...', date('d.m.Y H:i:s')));
                $processReplace = sprintf(
                    'sed -i \'s/INSERT  IGNORE INTO `stats_product_change`/INSERT IGNORE INTO `stats_product_change` (date_created_at, product_id, company_id, is_added)/g\' %s',
                    realpath($sqlPath)
                );

                exec($processReplace, $replaceOutput, $success);

                if ($success !== 0) {
                    throw new \RuntimeException(sprintf('Error exec code: %s', $success));
                }
                $output->writeln(sprintf('%s: Успешно', date('d.m.Y H:i:s')));


                $output->writeln(sprintf('%s: <info>Выполняем импорт дампа в архивную базу...</info>', date('d.m.Y H:i:s')));
                $process = sprintf(
                    'mysql -u %s -p%s %s -h%s < %s',
                    $this->getContainer()->getParameter('database_archive_user'),
                    $this->getContainer()->getParameter('database_archive_password'),
                    $this->getContainer()->getParameter('database_archive_name'),
                    $this->getContainer()->getParameter('database_archive_host'),
                    $sqlPath
                );

                exec($process, $outputProcess, $success);

                if ($success !== 0) {
                    throw new \RuntimeException(sprintf('%s: Error exec code: %s', date('d.m.Y H:i:s'), $success));
                }

                $output->writeln(sprintf('%s: Успешно', date('d.m.Y H:i:s')));

                $output->writeln(sprintf('%s: <info>Удаляем временный sql файл.</info>', date('d.m.Y H:i:s')));
                unlink(realpath($sqlPath));

                if ($input->getOption('delete')) {
                    $output->writeln(sprintf('%s: Удаляем архив с дампом.', date('d.m.Y H:i:s')));
                    unlink(realpath($archivePath));
                }
            }
        }

        $output->writeln(sprintf('%s: Done command "%s"', date('d.m.Y H:i:s'), $this->getName()));
    }
}
