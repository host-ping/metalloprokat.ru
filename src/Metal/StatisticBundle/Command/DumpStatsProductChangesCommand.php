<?php

namespace Metal\StatisticBundle\Command;

use Doctrine\DBAL\Connection;
use Metal\StatisticBundle\Repository\StatsDailyRepository;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\ProcessBuilder;


class DumpStatsProductChangesCommand extends ContainerAwareCommand
{
    /**
     * @var string
     */
    protected $saveDirectory;

    protected $tmpSqlFile;

    protected $tmpGzipFile;

    protected $dbUser;
    protected $dbPassword;
    protected $dbName;
    protected $dbHost;

    /**
     * @var StatsDailyRepository
     */
    protected $statsDailyRepo;

    protected function configure()
    {
        $this
            ->setName('metal:stats:dump-stats-product-changes')
            ->addOption('all', null, InputOption::VALUE_NONE, 'Обработка всех месяцев начиная с даты создания проекта.')
            ->addOption('date', null, InputOption::VALUE_OPTIONAL, 'Обработать конкретный месяц. Format: 2016-05')
            ->addOption('recovery', null, InputOption::VALUE_NONE, 'Включить режим исправления записей в таблице stats_product_change.')
            ->addOption('delete-rows', null, InputOption::VALUE_NONE, 'После дампа удалять сдампленные строки.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln(sprintf('%s: Start command "%s"', date('d.m.Y H:i:s'), $this->getName()));

        $container = $this->getContainer();

        $this->saveDirectory = $container->getParameter('upload_dir').'/archive';
        $this->tmpSqlFile = sprintf('%s/stats_product_change.sql', $this->saveDirectory);

        if (!@mkdir($this->saveDirectory) && !is_dir($this->saveDirectory)) {
            throw new \RuntimeException(sprintf('Unable to create directory "%s"', $this->saveDirectory));
        }

        $this->dbUser = $container->getParameter('database_user');
        $this->dbPassword = $container->getParameter('database_password');
        $this->dbName = $container->getParameter('database_name');
        $this->dbHost = $container->getParameter('database_host');
        $this->statsDailyRepo = $container->get('doctrine')->getRepository('MetalStatisticBundle:StatsDaily');

        $date = null;
        if ($input->getOption('date')) {
            $date = \DateTime::createFromFormat('Y-m-d', $input->getOption('date').'-01');
        } elseif (!$input->getOption('all')) {
            $date = (new \DateTime('-1 month'))->modify('first day of this month')->modify('midnight');
        }

        if ($date instanceof \DateTime) {
            $this->processImport($output, $date);
            if ($input->getOption('recovery')) {
                $this->processRecovery($output, $date);
            }
            $this->recalculateStatsPerMonth($output, $date);
            $this->dumpPeriod($output, $date, $input->getOption('delete-rows'));
        } else {
            $this->processAll($input, $output);
        }

        $output->writeln(sprintf('%s: Done "%s"', date('d.m.Y H:i:s'), $this->getName()));
    }

    private function recalculateStatsPerMonth(OutputInterface $output, \DateTime $date)
    {
        list($dateFrom, $dateTo) = $this->getDatePeriod($date);
        /* @var $dateFrom \DateTime */
        /* @var $dateTo \DateTime */

        $command = $this->getApplication()
            ->find('metal:recalculate-stats-product-change-per-month');
        $arguments = array(
            '--date-from' => $dateFrom->format('d.m.Y'),
            '--date-to' => $dateTo->format('d.m.Y')
        );

        $input = new ArrayInput($arguments);
        $returnCode = $command->run($input, $output);
        if ($returnCode !== 0) {
            throw new \RuntimeException('Error code: '. $returnCode);
        }
    }

    private function processImport(OutputInterface $output, \DateTime $date)
    {
        $filePathSql = sprintf('%s/stats_product_change-%s.sql', $this->saveDirectory, $date->format('Y-m'));
        $this->tmpGzipFile = $filePathSql.'.gz';

        if (file_exists($this->tmpGzipFile)) {

            $this->processGunzip($output);

            $this->processReplaceInto($output);

            $this->processMysqlImport($output);
        }
    }

    private function processAll(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();
        $year = (int)$container->getParameter('project.copyright_year');
        $yearNow = (int)date('Y');
        $monthNow = (int)date('n');
        for (; $year <= $yearNow; $year++) {
            for ($month = 1; $month < 12; $month++) {
                if ($year === $yearNow && $month === $monthNow) {
                    break 2;
                }

                $date = \DateTime::createFromFormat('Y-n-d', sprintf('%s-%s-01', $year, $month));

                $this->processImport($output, $date);
                if ($input->getOption('recovery')) {
                    $this->processRecovery($output, $date);
                }

                $this->recalculateStatsPerMonth($output, $date);
                $this->dumpPeriod($output, $date, $input->getOption('delete-rows'));
            }
        }
    }

    private function processGunzip(OutputInterface $output)
    {
        $output->writeln(sprintf('%s: Разархивируем файл "%s"', date('d.m.Y H:i:s'), $this->tmpGzipFile));


        $processZcat = sprintf('cat %s | gunzip > %s', $this->tmpGzipFile, $this->tmpSqlFile);

        exec($processZcat, $processOutput, $success);

        if ($success !== 0) {
            throw new \RuntimeException(sprintf('Error exec code: %s', $success));
        }

        $output->writeln('GUNZIP CODE: '.$success);
    }

    private function processReplaceInto(OutputInterface $output)
    {
        $processReplace = sprintf(
            'sed -i \'s/INSERT INTO/INSERT IGNORE INTO/g\' %s',
            realpath($this->tmpSqlFile)
        );

        exec($processReplace, $replaceOutput, $success);

        if ($success !== 0) {
            throw new \RuntimeException(sprintf('Error exec code: %s', $success));
        }

        $output->writeln('REPLACE CODE: '.$success);
    }

    private function processMysqlImport(OutputInterface $output)
    {
        $processMysql = sprintf(
            'mysql -u %s -p%s %s -h%s < %s',
            $this->dbUser,
            $this->dbPassword,
            $this->dbName,
            $this->dbHost,
            realpath($this->tmpSqlFile)
        );

        $output->writeln('Выполняем импорт.');
        exec($processMysql, $mysqlOutput, $success);
        if ($success !== 0) {
            throw new \RuntimeException(sprintf('Error exec code: %s', $success));
        }

        $output->writeln('IMPORT CODE: '.$success);

        $output->writeln('Удаляем Sql файл.');
        unlink(realpath($this->tmpSqlFile));
    }

    private function processRecovery(OutputInterface $output, \DateTime $date)
    {
        $this->recoveryPeriod($output, $date);

        list($dateFrom, $dateTo) = $this->getDatePeriod($date);

        $output->writeln('Пересчитываем данные в таблице stats_daily');
        //FIXME: тут некорректно метод вызывается, должен вызываться updateProductsChangesStats и возможно передавать ему какой-то коннекшн
        $this->statsDailyRepo->updateProductsChangesCounters(array(), $dateFrom, $dateTo);
    }

    private function recoveryPeriod(OutputInterface $output, \DateTime $date)
    {
        $conn = $this->getContainer()->get('doctrine')->getConnection();
        /* @var $conn Connection */

        list($dateFrom, $dateTo) = $this->getDatePeriod($date);

        $output->writeln('Выставляем id компании от продукта тем полям у которых выставленно 0.');
        $conn->executeUpdate(
            '
            UPDATE stats_product_change spc
                JOIN Message142 p ON p.Message_ID = spc.product_id
                SET spc.company_id = p.Company_ID
            WHERE spc.company_id = 0 AND spc.date_created_at >= :date_from AND spc.date_created_at <= :date_to',
            array(
                'date_from' => $dateFrom,
                'date_to' => $dateTo
            ),
            array(
                'date_from' => 'date',
                'date_to' => 'date',
            )
        );

        $output->writeln('Удаляем строки где id компании равен 0.');
        $conn->executeUpdate(
            '
            DELETE FROM stats_product_change 
            WHERE company_id = 0 AND date_created_at >= :date_from AND date_created_at <= :date_to',
            array(
                'date_from' => $dateFrom,
                'date_to' => $dateTo
            ),
            array(
                'date_from' => 'date',
                'date_to' => 'date',
            )
        );

        $output->writeln('Удаляем строки где дата являться null.');
        $conn->executeUpdate(
            'DELETE FROM stats_product_change WHERE date_created_at IS NULL'
        );

        $output->writeln('Выставляю всем строкам is_added равным 0.');
        $conn->executeUpdate(
            '
               UPDATE stats_product_change spc 
               SET spc.is_added = 0 WHERE 
               spc.date_created_at >= :date_from AND spc.date_created_at <= :date_to
           ',
            array(
                'date_from' => $dateFrom,
                'date_to' => $dateTo
            ),
            array(
                'date_from' => 'date',
                'date_to' => 'date',
            )
        );

        $output->writeln('Делаю вставку в stats_product_change из таблицы проудктов по Created с обновлением is_added');
        $conn->executeUpdate(
            '
              INSERT INTO stats_product_change (date_created_at, product_id, is_added, company_id)
                SELECT DATE(product.Created), product.Message_ID, 1, product.Company_ID FROM Message142 AS product WHERE product.Created >= :date_from AND product.Created <= :date_to
              ON DUPLICATE KEY UPDATE is_added = 1
           ',
            array(
                'date_from' => $dateFrom,
                'date_to' => $dateTo
            ),
            array(
                'date_from' => 'date',
                'date_to' => 'date',
            )
        );
    }

    private function dumpPeriod(OutputInterface $output, \DateTime $date, $deleteRows)
    {
        $container = $this->getContainer();
        $conn = $container->get('doctrine')->getConnection();
        /* @var $conn Connection */

        $fileName = sprintf('stats_product_change-%s.sql', $date->format('Y-m'));

        $output->writeln(sprintf('%s: Dump "%s"', date('d.m.Y H:i:s'), $fileName));

        list($dateFrom, $dateTo) = $this->getDatePeriod($date);

        if (!$this->hasDataForDump($conn, $dateFrom, $dateTo)) {
            $output->writeln(sprintf('%s: No records for date = "%s"', date('d.m.Y H:i:s'), $date->format('m.Y')));

            return;
        }

        $output->writeln(sprintf('%s: Make dump', date('d.m.Y H:i:s')));

        $processMysqlDump = (new ProcessBuilder())
            ->add('mysqldump')
            ->add(sprintf('--host=%s', $container->getParameter('database_host')))
            ->add(sprintf('--user=%s', $container->getParameter('database_user')))
            ->add(sprintf('--password=%s', $container->getParameter('database_password')))
            ->add($container->getParameter('database_name'))
            ->add('stats_product_change')
            ->add('--no-create-info')
            ->add('--verbose')
            ->add('--insert-ignore')
            ->add('--complete-insert')
            ->add('--lock-tables=false')
            ->add(sprintf('--where=date_created_at >= "%s" AND date_created_at <= "%s"', $conn->convertToDatabaseValue($dateFrom, 'date'), $conn->convertToDatabaseValue($dateTo, 'date')))
            ->add(sprintf('--result-file=%s', $fileName))
            ->setWorkingDirectory($this->saveDirectory)
            ->setTimeout(null)
            ->getProcess();

        $processMysqlDump->run(function ($type, $data) use ($output) {
            $output->writeln($data);
        });

        if (!$processMysqlDump->isSuccessful()) {
            throw new \RuntimeException($processMysqlDump->getErrorOutput());
        }

        if (file_exists(realpath($this->tmpGzipFile))) {
            $output->writeln('Удаляем временный gzip файл. '.$this->tmpGzipFile);
            unlink(realpath($this->tmpGzipFile));
        }

        $output->writeln(sprintf('%s: Archive "%s"', date('d.m.Y H:i:s'), $fileName));

        $processGzip = (new ProcessBuilder())
            ->add('gzip')
            ->add('-9')
            ->add('-f')
            ->add('-v')
            ->add($fileName)
            ->setTimeout(null)
            ->setWorkingDirectory($this->saveDirectory)
            ->getProcess();

        $processGzip->run(function ($type, $data) use ($output) {
            $output->writeln($data);
        });

        if (!$processGzip->isSuccessful()) {
            throw new \RuntimeException($processGzip->getErrorOutput());
        }

        if ($deleteRows) {
            $output->writeln(sprintf('%s: Delete records for date = "%s"', date('d.m.Y H:i:s'), $date->format('m.Y')));

            $conn->executeUpdate(
                'DELETE FROM stats_product_change WHERE date_created_at >= :date_from AND date_created_at <= :date_to',
                array(
                    'date_from' => $dateFrom,
                    'date_to' => $dateTo
                ),
                array(
                    'date_from' => 'date',
                    'date_to' => 'date',
                )
            );
        }
    }

    private function hasDataForDump(Connection $conn, \DateTime $dateFrom, \DateTime $dateTo)
    {
        return $conn
            ->executeQuery(
                'SELECT 1 FROM stats_product_change WHERE date_created_at >= :date_from AND date_created_at <= :date_to LIMIT 1',
                array(
                    'date_from' => $dateFrom,
                    'date_to' => $dateTo
                ),
                array(
                    'date_from' => 'date',
                    'date_to' => 'date',
                )
            )
            ->fetchColumn();
    }

    /**
     * @param \DateTime $date
     *
     * @return \DateTime[]
     */
    private function getDatePeriod(\DateTime $date)
    {
        $dateFrom = clone $date;
        $dateFrom->modify('first day of this month');

        $dateTo = clone $date;
        $dateTo->modify('last day of this month');

        return array($dateFrom, $dateTo);
    }
}
