<?php

namespace Metal\ProductsBundle\Command;

use Doctrine\ORM\EntityManager;
use Metal\ProductsBundle\Entity\Product;
use Metal\ProductsBundle\ChangeSet\ProductsBatchEditChangeSet;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MoveProductsFromNACategoriesCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('metal:products:move-products');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln(sprintf('%s: Start command "%s"', date('d.m.Y H:i:s'), $this->getName()));

        $doctrine = $this->getContainer()->get('doctrine');
        $em = $doctrine->getManager();
        /* @var  $em EntityManager */
        $em->getConfiguration()->setSQLLogger(null);

        $categoryService = $this->getContainer()->get('metal.categories.category_matcher');

        $products = $em->getRepository('MetalProductsBundle:Product')
            ->createQueryBuilder('p')
            ->join('p.category','cat')
            ->andWhere('cat.allowProducts = false')
            ->getQuery()
            ->getResult();

        $count = 0;
        $batchSize = 20;
        foreach ($products as $product){
            $category = $categoryService->getCategoryByTitle($product->getTitle());
            $product->setCategory($category);
            $output->writeln(sprintf('%d - %s - new Category_ID - %d', $product->getId(), $product->getTitle(), $category->getId()));
            $count ++;
            if (($count % $batchSize) === 0) {
                $em->flush();
            }
        }

        $em->flush();

        $output->writeln(sprintf('Found %d of %d', $count, count($products)));
    }
}
