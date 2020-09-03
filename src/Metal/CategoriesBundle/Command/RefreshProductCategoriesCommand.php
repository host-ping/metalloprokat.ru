<?php

namespace Metal\CategoriesBundle\Command;

use Doctrine\ORM\EntityManagerInterface;
use Metal\CategoriesBundle\Service\CategoryDetectorInterface;
use Metal\ProductsBundle\Entity\Product;
use Metal\ProductsBundle\Repository\ProductRepository;
use Metal\ServicesBundle\Entity\Package;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class RefreshProductCategoriesCommand extends ContainerAwareCommand
{
    /**
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     * @var CategoryDetectorInterface
     */
    protected $categoryService;

    /**
     * @var ProductRepository
     */
    protected $productRepository;

    public function configure()
    {
        $this
            ->setName('metal:categories:refresh-product-categories-and-attributes')
            ->addOption('disable-refresh-categories', null, InputOption::VALUE_NONE)
            ->addOption('disable-refresh-attributes', null, InputOption::VALUE_NONE)
            ->addOption('include-disabled-products', null, InputOption::VALUE_NONE)
            ->addOption('include-payed-companies', null, InputOption::VALUE_NONE)
            ->addOption('batch-size', null, InputOption::VALUE_OPTIONAL, '', 500);
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln(sprintf('Start command %s at %s', $this->getName(), date('Y-m-d H:i')));

        $this->em = $this->getContainer()->get('doctrine')->getManager();
        $this->em->getConfiguration()->setSQLLogger(null);
        $this->em->getConnection()->getConfiguration()->setSQLLogger(null);
        $this->categoryService = $this->getContainer()->get('metal.categories.category_matcher');
        $this->productRepository = $this->em->getRepository('MetalProductsBundle:Product');

        $qb = $this->productRepository->createQueryBuilder('p')
            ->select('p.title, p.id')
            ->addSelect('category.title AS category_title')
            ->addSelect('IDENTITY(p.category) AS category_id')
            ->leftJoin('p.category', 'category')
            ->andWhere('p.isVirtual = false');

        if (!$input->getOption('include-disabled-products')) {
            $qb->andWhere('p.checked = :checked')
                ->setParameter('checked', Product::STATUS_CHECKED);
        }

        if (!$input->getOption('include-payed-companies')) {
            $qb->join('p.company', 'company')
                ->andWhere('company.codeAccess = :codeAccess')
                ->setParameter('codeAccess', Package::BASE_PACKAGE);
        }

        $products = $qb
            ->getQuery()
            ->iterate();

        $table = new Table($output);
        if (!$input->getOption('disable-refresh-categories')) {
            $table->setHeaders(array('id', 'Название', 'Новая категория', 'Текущая категория'));
        }

        $i = 0;
        $productsIds = array();
        foreach ($products as $row) {
            $product = current($row);
            $productsIds[] = $product['id'];

            if (!$input->getOption('disable-refresh-categories')) {
                $output->writeln(sprintf('Refresh product category.'));
                $this->refreshProductCategory($product, $table);
            }

            if (!$input->getOption('disable-refresh-attributes') && (($i % $input->getOption('batch-size')) === 0)) {
                $output->writeln(sprintf('Refresh products attributes.'));
                $this->refreshProductAttributes($productsIds);
                $productsIds = array();
            }

            if (($i % $input->getOption('batch-size')) === 0) {
                if (!$input->getOption('disable-refresh-categories')) {
                    $table->render();
                    $table->setRows(array());
                }
                $output->writeln(sprintf('Update %d elements', $i));
                $this->em->clear();
            }

            $i++;
        }

        if ($productsIds) {
            $output->writeln(sprintf('Refresh products attributes.'));
            $this->refreshProductAttributes($productsIds);
        }

        $this->em->clear();

        $output->writeln(sprintf('End command %s at %s', $this->getName(), date('Y-m-d H:i')));
    }

    private function refreshProductCategory($product, Table $table)
    {
        $category = $this->categoryService->getCategoryByTitle($product['title']);

        if ($category->getId() == $product['category_id']) {
            return;
        }

        $table->addRow(
            array(
                $product['id'],
                $product['title'],
                sprintf('%s: %d', $category->getTitle(), $category->getId()),
                sprintf('%s: %d', $product['category_title'], $product['category_id']),
            )
        );

        $this->productRepository->createQueryBuilder('p')
            ->update('MetalProductsBundle:Product', 'p')
            ->set('p.category', $category->getId())
            ->where('p.id = :id')
            ->setParameter('id', $product['id'])
            ->getQuery()
            ->execute();
    }

    private function refreshProductAttributes($productsIds)
    {
        $productsStructure = $this->em->getRepository('MetalProductsBundle:Product')->initializeProductsStructure($productsIds);
        $this->em->getRepository('MetalProductsBundle:ProductParameterValue')->onProductStatusChanging($productsStructure, true);
    }
}
