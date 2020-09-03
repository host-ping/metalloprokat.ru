<?php

namespace Metal\ProductsBundle\Command;

use Doctrine\ORM\EntityManager;
use Metal\ProductsBundle\Entity\Product;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ProcessImportProductsCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('metal:products:process-imported-products');
        $this->addOption('batch-size', null, InputOption::VALUE_OPTIONAL, null, 20);
        $this->addOption('company-id', null, InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY, 'Список идентификаторов компаний', array());
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln(sprintf('Start command %s at %s', $this->getName(), date('Y-m-d H:i')));
        $batchSize = (int)$input->getOption('batch-size');
        $em = $this->getContainer()->get('doctrine')->getManager();
        /* @var $em EntityManager */

        $siteRepository =  $em->getRepository('MetalProjectBundle:Site');
        $siteRepository->disableLogging();

        $timeoutDate = new \DateTime('-3 hours');
        // Обновляю статус продуктам на Product::STATUS_PENDING_CATEGORY_DETECTION, которым выставился статус
        // Product::STATUS_PROCESSING  3 часа назад или больше

        $qb = $em->createQueryBuilder()
            ->update('MetalProductsBundle:Product', 'p')
            ->set('p.checked', ':new_status')
            ->where('p.checked = :status')
            ->andWhere('p.updatedAt <= :date')
            ->setParameter('status', Product::STATUS_PROCESSING)
            ->setParameter('new_status', Product::STATUS_PENDING_CATEGORY_DETECTION)
            ->setParameter('date', $timeoutDate, 'datetime');

        if ($input->getOption('company-id')) {
            $qb->andWhere('p.company IN (:companies_ids)')
                ->setParameter('companies_ids', $input->getOption('company-id'));
        }

        $affected = $qb->getQuery()->execute();

        $output->writeln(sprintf('Added to queue %s product at %s', $affected, date('Y-m-d H:i')));

        /* @var $products Product[] */
        $pqb = $em->getRepository('MetalProductsBundle:Product')
            ->createQueryBuilder('p')
            ->where('p.checked = :status')
            ->setParameter('status', Product::STATUS_PENDING_CATEGORY_DETECTION);

        if ($input->getOption('company-id')) {
            $pqb->andWhere('p.company IN (:companies_ids)')
                ->setParameter('companies_ids', $input->getOption('company-id'));
        }

        $products = $pqb->setMaxResults($batchSize)
            ->getQuery()
            ->getResult();

        if (!$products) {
            $output->writeln(sprintf('No new products at %s', date('Y-m-d H:i')));
        } else {
            $em->createQueryBuilder()
                ->update('MetalProductsBundle:Product', 'p')
                ->set('p.checked', ':new_status')
                ->set('p.updatedAt', ':date')
                ->where('p.checked = :status')
                ->andWhere('p.id IN (:products_id)')
                ->setParameter('status', Product::STATUS_PENDING_CATEGORY_DETECTION)
                ->setParameter('new_status', Product::STATUS_PROCESSING)
                ->setParameter('products_id', $products)
                ->setParameter('date', new \DateTime(), 'datetime')
                ->getQuery()
                ->execute();

            $categoryService = $this->getContainer()->get('metal.categories.category_matcher');
            $progress = $this->getHelper('progress');
            $progress->start($output, count($products));

            foreach ($products as $i => $product) {
                $category = $categoryService->getCategoryByTitle($product->getTitle());
                $product->setCategory($category);
                $product->setChecked(Product::STATUS_NOT_CHECKED);
                $progress->advance();
                $output->writeln(sprintf(' Current product id: %s. at %s', $product->getId(), date('Y-m-d H:i')));
                if (($i % 25) == 0) {
                    $em->flush();
                }
            }

            $em->flush();
            $progress->finish();
        }
        $output->writeln(sprintf('End command. %s at %s', $this->getName(), date('Y-m-d H:i')));
    }
}
