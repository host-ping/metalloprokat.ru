<?php

namespace Metal\ProjectBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\Process\ProcessBuilder;

class BackupDatabaseCommand extends ContainerAwareCommand
{
    private $projectFamily;

    protected function configure()
    {
        $this->setName('metal:project:backup-database');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln(sprintf('%s: Start command "%s"', date('d.m.Y H:i:s'), $this->getName()));
        $container = $this->getContainer();

        $saveDir = $container->getParameter('web_dir').'/dbs/';
        $this->projectFamily = $container->getParameter('project.family');

        if (!is_dir($saveDir)) {
            @mkdir($saveDir);
        }

        $saveDir = realpath($saveDir);

        $finder = new Finder();
        $iterator = $finder
            ->files()
            ->ignoreDotFiles(true)
            ->in($saveDir)
            ->sortByName();

        $date = new \DateTime('-5 days');
        foreach ($iterator as $file) {
            try {
                $fileDate = $this->getDateTimeByFile($file);

                if (null === $fileDate || $fileDate >= $date) {
                    continue;
                }

                $output->writeln(sprintf('%s: Delete "%s"', date('d.m.Y H:i:s'), $file->getRelativePathname()));

                @unlink($saveDir.'/'.$file->getRelativePathname());
            } catch (\Exception $e) {
                $output->writeln(
                    sprintf('%s: Ошибка получения даты "%s"', date('d.m.Y H:i:s'), $file->getRelativePathname())
                );
            }
        }

        $fileName = sprintf('%s-%s.sql', $this->projectFamily, (new \DateTime())->format('Y-m-d'));
        $dbName = $container->getParameter('database_name');
        $processMysqlDump = (new ProcessBuilder())
            ->add('mysqldump')
            ->add(sprintf('--host=%s', $container->getParameter('database_host_backup')))
            ->add(sprintf('--user=%s', $container->getParameter('database_user')))
            ->add(sprintf('--password=%s', $container->getParameter('database_password')))
            ->add($dbName)
            ->add('--add-drop-table')
            ->add(sprintf('--ignore-table=%s.stats_product_change', $dbName))
            ->add(sprintf('--ignore-table=%s.announcement_stats_element', $dbName))
            ->add(sprintf('--ignore-table=%s.stats_element', $dbName))
            ->add(sprintf('--ignore-table=%s.ban_request', $dbName))
            ->add(sprintf('--ignore-table=%s.Objava_Stat', $dbName))
            ->add(sprintf('--ignore-table=%s.Objava_Stat_Total', $dbName))
            ->add(sprintf('--ignore-table=%s.Message96', $dbName))
            ->add(sprintf('--ignore-table=%s.sphinx_search_log', $dbName))
            ->add(sprintf('--ignore-table=%s.grabber_log', $dbName))
            ->add('--verbose')
            ->add(sprintf('--result-file=%s', $fileName))
            ->setWorkingDirectory($saveDir)
            ->setTimeout(null)
            ->getProcess();

        $outputWriter = function ($type, $data) use ($output) {
            $output->writeln($data);
        };

        $processMysqlDump->run($outputWriter);
        if (!$processMysqlDump->isSuccessful()) {
            throw new \RuntimeException($processMysqlDump->getErrorOutput());
        }

        $output->writeln(sprintf('%s: Archive "%s"', date('d.m.Y H:i:s'), $fileName));
        $processGzip = (new ProcessBuilder())
            ->add('gzip')
            ->add('-9')
            ->add('-f')
            ->add('-v')
            ->add($fileName)
            ->setTimeout(null)
            ->setWorkingDirectory($saveDir)
            ->getProcess();

        $processGzip->run($outputWriter);
        if (!$processGzip->isSuccessful()) {
            throw new \RuntimeException($processGzip->getErrorOutput());
        }

        $content = <<<HTACCESS
AuthType Basic
AuthName "Developers only"
AuthUserFile $saveDir/.htpasswd
Require valid-user
HTACCESS;

        $output->writeln(sprintf('%s: Create file .htaccess', date('d.m.Y H:i:s')));
        file_put_contents(sprintf('%s/.htaccess', $saveDir), $content);

        $content = <<<'HTACCESS'
database:$apr1$c2/YPBsu$WteFM5Syu0XqMIrMnzzWu/
HTACCESS;

        $output->writeln(sprintf('%s: Create file .htpasswd', date('d.m.Y H:i:s')));
        file_put_contents(sprintf('%s/.htpasswd', $saveDir), $content);

        $output->writeln(sprintf('%s: Done command "%s"', date('d.m.Y H:i:s'), $this->getName()));
    }

    private function getDateTimeByFile(SplFileInfo $file)
    {
        preg_match('/'.$this->projectFamily.'\-(\d{4}-\d{2}-\d{2})/ui', $file->getRelativePathname(), $matches);

        if (!$matches) {
            return null;
        }

        return new \DateTime($matches[1]);
    }
}
