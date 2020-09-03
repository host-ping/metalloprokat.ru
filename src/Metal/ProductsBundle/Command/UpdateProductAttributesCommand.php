<?php

namespace Metal\ProductsBundle\Command;

use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManager;
use Metal\ProductsBundle\ChangeSet\ProductsBatchEditStructure;
use Metal\ProductsBundle\Entity\Product;
use Metal\ProjectBundle\Doctrine\Utils;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class UpdateProductAttributesCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('metal:products:update-product-attributes');

        $this->addOption(
            'company-id',
            null,
            InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY,
            'array of companies ids',
            array()
        );

        $this->addOption(
            'category-id',
            null,
            InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY,
            'array of categories ids',
            array()
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln(sprintf('%s: Start command "%s"', date('d.m.Y H:i:s'), $this->getName()));

        $em = $this->getContainer()->get('doctrine')->getManager();
        /* @var $em EntityManager */

        Utils::disableLogger($em);

        $conn = $em->getConnection();

        $companiesIds = $input->getOption('company-id');
        $categoriesIds = $input->getOption('category-id');

        $productRepository = $em->getRepository('MetalProductsBundle:Product');
        $productParameterValueRepository = $em->getRepository('MetalProductsBundle:ProductParameterValue');

        $cqb = $conn->createQueryBuilder()
            ->select('MIN(product.Message_ID) AS minId')
            ->addSelect('MAX(product.Message_ID) AS maxId')
            ->from('Message142 AS product')
        ;

        if ($companiesIds) {
            $cqb->andWhere('product.Company_ID IN (:companiesIds)')
                ->setParameter('companiesIds', $companiesIds, Connection::PARAM_INT_ARRAY);
        }

        if ($categoriesIds) {
            $cqb->andWhere('product.Category_ID IN (:categoriesIds)')
                ->setParameter('categoriesIds', $categoriesIds, Connection::PARAM_INT_ARRAY);
        }

        $rangeIds = $cqb->execute()->fetch();

        $productsBatchEditStructure = new ProductsBatchEditStructure();
        $limit = 50;
        $idFrom = $rangeIds['minId'];
        do {
            $idTo = $idFrom + $limit;

            $qb = $productRepository
                ->createQueryBuilder('product')
                ->select('product.id AS id')
                ->addSelect('IDENTITY(product.category) AS categoryId')
                ->addSelect('product.title AS title')
                ->addSelect('product.size AS size');

            if ($companiesIds) {
                $qb
                    ->andWhere('product.company IN (:companiesIds)')
                    ->setParameter('companiesIds', $companiesIds);
            }

            if ($companiesIds) {
                $qb
                    ->andWhere('product.Category_ID IN (:categoriesIds)')
                    ->setParameter('categoriesIds', $categoriesIds);
            }

            $productsBatchEditStructure->products = $qb
                ->andWhere('product.checked <> :status_deleted')
                ->setParameter('status_deleted', Product::STATUS_DELETED)
                ->andWhere('product.id >= :idFrom')
                ->setParameter('idFrom', $idFrom)
                ->andWhere('product.id < :idTo')
                ->setParameter('idTo', $idTo)
                ->getQuery()
                ->getResult();
            /* @var $products Product[] */

            if ($productsBatchEditStructure->products) {
                $productParameterValueRepository->onProductStatusChanging($productsBatchEditStructure, true);
                $output->writeln(sprintf('%s: Process %d / %d products.', date('d.m.Y H:i:s'), $idTo, $rangeIds['maxId']));
            }

            $idFrom = $idTo;
        } while ($idFrom <= $rangeIds['maxId']);

        $output->writeln(sprintf('%s: Success command "%s"', date('d.m.Y H:i:s'), $this->getName()));
    }
}
