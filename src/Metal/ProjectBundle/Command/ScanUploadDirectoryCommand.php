<?php

namespace Metal\ProjectBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;

class ScanUploadDirectoryCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('metal:project:scan-upload-directory');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $uploadDir = $this->getContainer()->getParameter('upload_dir');
        $finder = new Finder();
        $sort = function ($a, $b) {
            return strlen($b) - strlen($a);
        };

        $directories = $finder
            //->directories()
            ->in($uploadDir)
            ->depth('> 3')
            ->sort($sort);

        foreach ($directories as $directory) {
            $output->writeln(
                $directory->getRelativePathname()
            );
        }
    }
}
