<?php

namespace Metal\PrivateOfficeBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Metal\CompaniesBundle\Repository\CustomCompanyCategoryRepository;


class ImportCustomCatalogCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('metal:private:import-custom-catalog')
            ->addArgument('company-id', InputArgument::REQUIRED)
            ->addOption('start', null, InputOption::VALUE_OPTIONAL, '', 1)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $companyId = $input->getArgument('company-id');
        $startRow = $input->getOption('start');
        $uploadDir = $this->getContainer()->getParameter('upload_dir');

        $fileName = $companyId.'-custom-catalog.xls';
        $dir = $uploadDir.'/products-import';

        $doctrine = $this->getContainer()->get('doctrine');
        $em = $doctrine->getManager();

        $connection = $em->getConnection();

        $customCompanyCategoryRepository = $em->getRepository('MetalCompaniesBundle:CustomCompanyCategory');
        /* @var $customCompanyCategoryRepository CustomCompanyCategoryRepository */

        $cellsKeys = array(
            'A' => 'title',
            'B' => 'category'
        );

        \PHPExcel_Settings::setLibXmlLoaderOptions(LIBXML_DTDLOAD | LIBXML_DTDATTR | LIBXML_COMPACT | LIBXML_PARSEHUGE);
        $phpExcelObject = $this->getContainer()->get('phpexcel')->createPHPExcelObject($dir.'/'.$fileName);
        $objWorksheet = $phpExcelObject->getActiveSheet();

        $results = array();

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

        $categories = $customCompanyCategoryRepository->getCategoriesForCompany($companyId);
        $arrCategoriesForCompany = array ();
        foreach ($categories as $category) {
            $arrCategoriesForCompany[$category->getId()] = $category->getTitle();
        }

        $productsUpdated = 0;
        foreach ($results as $k => $val) {

            if (array_search($val['B'], $arrCategoriesForCompany)) {
                $output->writeln(array_search($val['B'], $arrCategoriesForCompany).$val['B']);
                $qb = $connection->createQueryBuilder()
                    ->update('Message142', 'p')
                    ->where('p.Name = :title')
                    ->andwhere('p.Company_ID = :companyId')
                    ->set('p.custom_category_id', ':categoryId')
                    ->setParameter('categoryId', array_search($val['B'], $arrCategoriesForCompany ))
                    ->setParameter('title', $val['A'])
                    ->setParameter('companyId', $companyId);

                $output->writeln($qb->getSQL());
                $qb->execute();

                $em->flush();
                $productsUpdated++;
            }

        }

        $output->writeln('Command result. updated - '.$productsUpdated);
    }

}
