<?php

namespace Metal\ProjectBundle\Command;

use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CompileUrlRewritesCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('metal:project:compile-url-rewrites');
        $this->addOption('truncate', null, InputOption::VALUE_NONE);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln(sprintf('%s: Start command "%s"', date('d.m.Y H:i:s'), $this->getName()));

        $doctrine = $this->getContainer()->get('doctrine');
        $em = $doctrine->getManager();
        /* @var  $em EntityManager */
        $conn = $em->getConnection();

        if ($input->getOption('truncate')) {
            $conn->executeQuery('TRUNCATE url_rewrite');
        }

        $conn->executeQuery('
            INSERT IGNORE INTO url_rewrite (path_prefix, category_id)
              SELECT c.slug_combined, c.Message_ID
              FROM Message73 c'
        );

        $conn->executeQuery('
            INSERT IGNORE INTO url_rewrite (path_prefix, content_category_id)
              SELECT cc.slug_combined, cc.id
              FROM content_category cc'
        );

        $conn->executeQuery(
            'INSERT IGNORE INTO url_rewrite (path_prefix, company_id)
                    SELECT c.slug, c.Message_ID
                    FROM Message75 c
                    WHERE c.slug IS NOT NULL
                    AND c.deleted_at_ts = 0'
        );

        $output->writeln(sprintf('%s: Done command "%s"', date('d.m.Y H:i:s'), $this->getName()));
    }
}
