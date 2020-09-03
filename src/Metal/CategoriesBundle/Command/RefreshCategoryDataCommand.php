<?php

namespace Metal\CategoriesBundle\Command;

use Metal\CategoriesBundle\Entity\Category;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RefreshCategoryDataCommand extends ContainerAwareCommand
{
    public function configure()
    {
        $this->setName('metal:categories:refresh');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln(sprintf('Start command %s at %s', $this->getName(), date('Y-m-d H:i')));

        $em = $this->getContainer()->get('doctrine.orm.default_entity_manager');

        $em->getConfiguration()->setSQLLogger(null);
        $em->getConnection()->getConfiguration()->setSQLLogger(null);

        $em->getRepository('MetalCategoriesBundle:Category')->refreshDenormalizedData(
            function ($categoryId, $maxCategoryId) use ($output) {
                $output->writeln(
                    sprintf('Processing denormalized data for categories: %d/%d', $categoryId, $maxCategoryId)
                );
            }
        );

        $em->getRepository('MetalContentBundle:Category')->refreshDenormalizedData(
            function ($categoryId, $maxCategoryId) use ($output) {
                $output->writeln(
                    sprintf('Processing denormalized data for content_categories: %d/%d', $categoryId, $maxCategoryId)
                );
            }
        );

        $em->getRepository('MetalCompaniesBundle:CustomCompanyCategory')->refreshDenormalizedData(
            function ($categoryId, $maxCategoryId) use ($output) {
                $output->writeln(
                    sprintf('Processing denormalized data for custom_categories: %d/%d', $categoryId, $maxCategoryId)
                );
            }
        );

        $output->writeln(sprintf('End command %s at %s', $this->getName(), date('Y-m-d H:i')));
    }
}
