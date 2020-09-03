<?php

namespace Metal\DemandsBundle\Command;

use Metal\ProjectBundle\Doctrine\Utils;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;

class FixDemandFilePathCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('metal:demands:fix-demand-file-path');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln(sprintf('%s: Start command "%s"', date('d.m.Y H:i:s'), $this->getName()));

        $container = $this->getContainer();

        $em = $container->get('doctrine.orm.default_entity_manager');
        Utils::disableLogger($em);

        $demandFileRepository = $em->getRepository('MetalDemandsBundle:DemandFile');

        $demandFilesDir = $container->getParameter('upload_dir').'/demands';

        $demandFiles = $demandFileRepository->findBy(array('isProcessed' => false));

        $filesystem = new Filesystem();

        foreach ($demandFiles as $demandFile) {
            $filePath = $demandFilesDir.'/'.$demandFile->getFile()->getName();
            $demandId = $demandFile->getDemand()->getId();

            if (!file_exists($filePath)) {
                $output->writeln(sprintf('%s: Заявка %d, файл не найден, обрабатываем следующий.', date('d.m.Y H:i:s'), $demandId));

                continue;
            }

            $name = preg_replace('/\//u', '', $demandFile->getFile()->getName());
            $subdir = $this->generateDirName($name);
            $filesystem->copy($filePath, $demandFilesDir.'/'.$subdir.'/'.$name, true);
            $filesystem->remove($filePath);
            $demandFile->getFile()->setName($name);
            $demandFile->setIsProcessed(true);

            $em->flush();

            $output->writeln(sprintf('%s: Заявка %d, файл перенесен.', date('d.m.Y H:i:s'), $demandId));
        }
    }

    protected function generateDirName($fileName)
    {
        $pieces = array();
        for ($i = 0; $i <= 1; $i++) {
            $pieces[] = mb_substr($fileName, $i * 2, 2);
        }

        return implode('/', $pieces);
    }
}
