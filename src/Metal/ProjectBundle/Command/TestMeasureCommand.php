<?php

namespace Metal\ProjectBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class TestMeasureCommand  extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('metal:project:test-measure');
        $this->addOption('project-family', null, InputOption::VALUE_REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln(sprintf('%s: Start command "%s"', date('d.m.Y H:i:s'), $this->getName()));

        if (!$project = $input->getOption('project-family')) {
            $output->writeln(sprintf('%s: Select project.', date('d.m.Y')));
            return;
        }

        $dictionaryDir = $this->getContainer()->getParameter('kernel.root_dir').'/config/dictionaries/'.$project;

        $types = $findTypes = require $dictionaryDir.'/measure-list.php';

        $foundBy = array();
        $badPattern = array();
        foreach ($types as $key => $type) {
            foreach ($findTypes as $findTypeKey => $findType) {

                if (empty($foundBy[$findTypeKey])) {
                    $foundBy[$findTypeKey] = array();
                }

                foreach ($type['matches'] as $matches) {
                    foreach ($findType['patterns'] as $pattern ) {
                        if (preg_match_all($pattern, $matches)) {
                            $foundBy[$findTypeKey][$key] = array();
                            if ($findTypeKey != $key) {
                                $badPattern[$findTypeKey][$pattern] = true;
                            }
                        }
                    }
                }
            }
        }

        foreach ($foundBy as $key => $found) {
            if (count($found) <> 1) {
                $output->writeln(sprintf('%s: Bad patterns for type %d. Bad pattern %s', date('d.m.Y'), $key, implode(',', array_keys($badPattern[$key]))));
            }
        }

        $output->writeln(sprintf('%s: Command done. Found bad types %d.', date('d.m.Y'), count($badPattern)));
    }
}
