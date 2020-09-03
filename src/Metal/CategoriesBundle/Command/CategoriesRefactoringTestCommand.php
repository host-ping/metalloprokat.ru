<?php

namespace Metal\CategoriesBundle\Command;

use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CategoriesRefactoringTestCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('metal:categories:categories_test')
            ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $problemCases = array();

        $output->writeln(sprintf('Start command %s at %s', $this->getName(), date('Y-m-d H:i')));

        $em = $this->getContainer()->get('doctrine')->getManager();
        /* @var $em EntityManager */
        $em->getConfiguration()->setSQLLogger(null);
        $conn = $em->getConnection();
        /* @var $conn Connection */

        $categoryFinder = $this->getContainer()->get('metal.categories.category_matcher');
        $categoryTestRepository = $em->getRepository('MetalCategoriesBundle:CategoryTestItem')->findAll();
       //var_dump($categoryTestRepository);
     //   $categoriesTest = $categoryTestRepository->createQueryBuilder('c')->getQuery()->getResult();

        foreach ($categoryTestRepository as $categoryTest){
            $newCategory = $categoryFinder->getCategoryByTitle($categoryTest->getTitle());

            if ($newCategory != $categoryTest->getCategory()){
               $problemCases[]= $categoryTest;
               $output->writeln(sprintf('Failure: %d', $categoryTest->getId()));
            }
        }
        var_dump($problemCases);
        $output->writeln(sprintf('End command %s at %s', $this->getName(), date('Y-m-d H:i')));
    }
}
