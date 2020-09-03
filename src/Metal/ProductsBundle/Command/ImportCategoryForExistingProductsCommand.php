<?php

namespace Metal\ProductsBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Metal\ProductsBundle\ChangeSet\ProductsBatchEditChangeSet;

class ImportCategoryForExistingProductsCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('metal:products:import-category-for-products')
            ->addArgument('company-id', InputArgument::REQUIRED)
            ->addOption('start', null, InputOption::VALUE_OPTIONAL, '', 1);
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $companyId = $input->getArgument('company-id');
        $startRow = $input->getOption('start');
        $uploadDir = $this->getContainer()->getParameter('upload_dir');

        $fileName = $companyId.'-prices-and-categories.xls';
        $dir = $uploadDir.'/products-import';

        $doctrine = $this->getContainer()->get('doctrine');
        $em = $doctrine->getManager();

        $connection = $em->getConnection();

        $cellsKeys = array(
            'A' => 'title',
            'B' => 'size',
            'C' => 'category'
        );

        \PHPExcel_Settings::setLibXmlLoaderOptions(LIBXML_DTDLOAD | LIBXML_DTDATTR | LIBXML_COMPACT | LIBXML_PARSEHUGE);
        $phpExcelObject = $this->getContainer()->get('phpexcel')->createPHPExcelObject($dir.'/'.$fileName);
        $objWorksheet = $phpExcelObject->getActiveSheet();

        $results = array();
        $categories = $em->getRepository('MetalCategoriesBundle:Category')->getCategoriesAsSimpleArray(true);

        foreach ($objWorksheet->getRowIterator($startRow) as $k => $row) {
            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(false);

            $parsedRow = array();
            foreach ($cellIterator as $letter => $cell) {
                if (!isset($cellsKeys[$letter])) {
                    continue;
                }
                $value = $cell->getCalculatedValue();
                $parsedRow[$letter] = $value;
            }

            $results[$k] = $parsedRow;

            $rowIndex = $row->getRowIndex();

            if ($rowIndex > 10000) {
                break;
            }
        }

        $productsToChangeCategory = array();
        foreach ($results as $k => $val) {

            if (array_search($val['C'],$categories)){
                $newCategory = $em->getRepository('MetalCategoriesBundle:Category')->find(array_search($val['C'],$categories));
                $qb = $em->createQueryBuilder()
                    ->select('p')
                    ->from('MetalProductsBundle:Product', 'p', 'p.id')
                    ->where('p.title = :name')
                    ->andWhere('p.company = :companyId')
                    ->setParameter('name', $val['A'])
                    ->setParameter('companyId', $companyId);
                $products = $qb->getQuery()
                    ->getResult()
                ;

                foreach ($products as $product) {
                    if ($product->getCategoryId() != array_search($val['C'],$categories)) {
                        $product->setCategory($newCategory);
                        $productsToChangeCategory[$product->getId()]
                            = array(
                            'old' => $product->getCategoryId(),
                            'new' => array_search($val['C'], $categories)
                        );
                    }
                }


            }
        }

        $em->flush();
        $productsChangeSet = new ProductsBatchEditChangeSet();
        $productsChangeSet->productsToChangeCategory = $productsToChangeCategory;
        $this->getContainer()->get('sonata.notification.backend')->createAndPublish('admin_products', array('changeset' => $productsChangeSet));
    }
}
