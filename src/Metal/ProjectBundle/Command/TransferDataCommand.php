<?php

namespace Metal\ProjectBundle\Command;

use Doctrine\DBAL\Connection;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\ProcessBuilder;

/**
 * Command that transfers mysql data to archive server.
 */
class TransferDataCommand extends ContainerAwareCommand
{
    const WEEK_DAYS = 7;

    /**
     * @var OutputInterface
     */
    private $output;

    /**
     * @var InputInterface
     */
    private $input;

    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var \DateTime
     */
    private $date;

    /**
     * @var string
     *
     * Path to temporary sql file.
     */
    private $temporaryDumpFilePath;

    protected function configure()
    {
        $this
            ->setName('metal:project:transfer-data')
            ->addArgument('table', null, InputArgument::REQUIRED)
            ->addArgument('date-column', null, InputArgument::REQUIRED)
            ->addOption('delete', null, InputOption::VALUE_NONE)
            ->addOption('no-optimize', null, InputOption::VALUE_NONE, 'Отключает оптимизацию таблицы.')
            ->addOption('mode', null, InputOption::VALUE_OPTIONAL, 'normal|only-delete', 'normal')
            ->addOption('leave-days', null, InputOption::VALUE_OPTIONAL, 'За сколько дней оставлять данные на default сервере', self::WEEK_DAYS)
            ->addOption(
                'batch-delete-size',
                null,
                InputOption::VALUE_OPTIONAL,
                'Number of rows to be deleted for query.',
                10000
            );
        //TODO: add support of tokudb tables (use suffix "toku" for table names)
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln(sprintf('%s: Start command "%s"', date('d.m.Y H:i:s'), $this->getName()));

        $this->input = $input;
        $this->output = $output;
        $container = $this->getContainer();
        $this->connection = $container->get('doctrine.dbal.default_connection');

        $tmpDir = $container->getParameter('kernel.root_dir').'/var/dbdumps';
        if (!@mkdir($tmpDir) && !is_dir($tmpDir)) {
            throw new \RuntimeException(sprintf('Don\'t created directory "%s"', $tmpDir));
        }

        $this->temporaryDumpFilePath = sprintf('%s/%s.sql', $tmpDir, $input->getArgument('table'));

        $this->date = (new \DateTime(sprintf('-%d days', $input->getOption('leave-days'))))
            ->modify('midnight');

        switch ($input->getOption('mode')) {
            case 'normal':
                $this->processDump();
                $this->processImport();

                if ($input->getOption('delete')) {
                    $this->processDeleteRows();
                }
                break;

            case 'only-delete':
                $this->processDeleteRows();
                break;

            default:
                throw new \InvalidArgumentException(sprintf('Mode "%s" not supported.', $input->getOption('mode')));
        }

        if (!$input->getOption('no-optimize')) {
            $this->processOptimizeTable();
        }

        $output->writeln(sprintf('%s: Done "%s"', date('d.m.Y H:i:s'), $this->getName()));
    }

    protected function processDump()
    {
        $processMysqlDump = (new ProcessBuilder())
            ->add('mysqldump')
            ->add(sprintf('--host=%s', $this->getContainer()->getParameter('database_host')))
            ->add(sprintf('--user=%s', $this->getContainer()->getParameter('database_user')))
            ->add(sprintf('--password=%s', $this->getContainer()->getParameter('database_password')))
            ->add($this->getContainer()->getParameter('database_name'))
            ->add($this->input->getArgument('table'))
            ->add('--no-create-info')
            ->add('--verbose')
            ->add('--complete-insert')
            ->add('--insert-ignore')
            ->add('--lock-tables=false')
            ->add(
                sprintf(
                    '--where=%s <= "%s"',
                    $this->input->getArgument('date-column'),
                    $this->connection->convertToDatabaseValue($this->date, 'date')
                )
            )
            ->add(sprintf('--result-file=%s', basename($this->temporaryDumpFilePath)))
            ->setWorkingDirectory(dirname($this->temporaryDumpFilePath))
            ->setTimeout(null)
            ->getProcess();

        // Закрываем соединение чтоб  в processDeleteRows не вылететь с таймаутом на соединение
        $this->connection->close();

        $processMysqlDump->run(
            function ($type, $data) {
                $this->output->writeln($data);
            }
        );

        if (!$processMysqlDump->isSuccessful()) {
            throw new \RuntimeException($processMysqlDump->getErrorOutput());
        }
    }

    protected function processImport()
    {
        $this->output->writeln('Start import.');
        $process = sprintf(
            'mysql -u %s -p%s %s -h%s < %s',
            $this->getContainer()->getParameter('database_archive_user'),
            $this->getContainer()->getParameter('database_archive_password'),
            $this->getContainer()->getParameter('database_archive_name'),
            $this->getContainer()->getParameter('database_archive_host'),
            $this->temporaryDumpFilePath
        );

        exec($process, $output, $success);

        if ($success !== 0) {
            throw new \RuntimeException(sprintf('%s: Error exec code: %s', date('d.m.Y H:i:s'), $success));
        }

        $this->output->writeln('Import status code: '.$success);

        $this->output->writeln(sprintf('%s: Remove temporary sql dump file.', date('d.m.Y H:i:s')));

        unlink($this->temporaryDumpFilePath);
    }

    protected function processDeleteRows()
    {
        $this->output->writeln(sprintf('%s: Remove rows.', date('d.m.Y H:i:s')));
        $batchDeleteSize = (int)$this->input->getOption('batch-delete-size');
        do {
            $affectRows = $this->connection
                ->createQueryBuilder()
                ->delete($this->input->getArgument('table'))
                ->where(sprintf('%s <= :dateTo LIMIT :limit', $this->input->getArgument('date-column')))
                ->setParameter('dateTo', $this->date, 'date')
                ->setParameter('limit', $batchDeleteSize, \PDO::PARAM_INT)
                ->execute();

            if ($affectRows) {
                $this->output->writeln(sprintf('%s: Removed %d rows.', date('d.m.Y H:i:s'), $affectRows));
            }
        } while ($affectRows);

        // Закрываем соединение чтоб  в processOptimizeTable не вылететь с таймаутом на соединение
        $this->connection->close();
    }

    protected function processOptimizeTable()
    {
        $this->output->writeln(sprintf('%s: Optimize table <info>%s</info>.', date('d.m.Y H:i:s'), $this->input->getArgument('table')));
        $results = $this->connection->executeQuery(sprintf('OPTIMIZE TABLE %s', $this->input->getArgument('table')))->fetchAll();
        foreach ($results as $result) {
            $this->output->writeln(sprintf('%s: Optimize message: <info>"%s"</info>.', date('d.m.Y H:i:s'), $result['Msg_text']));
        }
    }
}
