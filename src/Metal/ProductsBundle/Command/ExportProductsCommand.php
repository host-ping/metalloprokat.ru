<?php

namespace Metal\ProductsBundle\Command;

use Metal\ProductsBundle\Entity\Product;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ExportProductsCommand extends ContainerAwareCommand
{

    protected function configure()
    {
        $this->setName('metal:products:export-products');
        $this->addArgument('company-id', InputArgument::REQUIRED, 'ID company with which to select products.');
        $this->addOption('city-id', null, InputOption::VALUE_OPTIONAL, 'ID of the branch of the city from which to choose products.');
        $this->addOption('format', null, InputOption::VALUE_OPTIONAL, 'Allowed formats: xls, yml, xls-admin', 'xls');
        $this->addOption('limit', null, InputOption::VALUE_OPTIONAL);
        $this->addOption('offset', null, InputOption::VALUE_OPTIONAL, '', 0);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $companyId = $input->getArgument('company-id');

        $output->writeln(sprintf('%s: Start command %s', date('Y-m-d H:i'), $this->getName()));

        if (!$companyId) {
            $output->writeln(sprintf('%s: Select company id', date('Y-m-d H:i')));
            return;
        }

        $container = $this->getContainer();
        $em = $container->get('doctrine.orm.default_entity_manager');
        $productExportService = $container->get('metal.products.product_export_service');
        $uploadDir = $container->getParameter('upload_dir');
        $company = $em->find('MetalCompaniesBundle:Company', $companyId);
        $cityId = $input->getOption('city-id');
        $format = $input->getOption('format');

        if (!$company) {
            $output->writeln(sprintf('%s: Company not found.', date('Y-m-d H:i')));
            return;
        }

        $qb = $em->createQueryBuilder()
            ->select('p.id')
            ->from('MetalProductsBundle:Product', 'p', 'p.id')
            ->join('p.productDescription', 'pd')
            ->andWhere('p.checked = :checked')
            ->andWhere('p.company = :company_id')
            ->andWhere('p.isVirtual = false')
            ->addOrderBy('p.updatedAt')
            ->setParameter('company_id', $companyId)
            ->setParameter('checked', Product::STATUS_CHECKED);

        if ($cityId) {
            $qb->join('p.branchOffice', 'off')
                ->join('off.city', 'city')
                ->andWhere('city.id = :city_id')
                ->setParameter('city_id', $cityId);
        }

        $limit = $input->getOption('limit');
        if ($limit) {
            $qb
                ->setMaxResults($limit)
                ->setFirstResult($input->getOption('offset'));
        }

        $products = $qb->getQuery()->getResult();

        $output->writeln(sprintf('%s: Loaded %d products.', date('Y-m-d H:i'), count($products)));

        $fileName = $productExportService->exportProducts($format, $company, array_keys($products));

        $locationFile = $uploadDir.'/products-export/'.$fileName;

        $output->writeln(sprintf('%s: File location "%s"', date('Y-m-d H:i'), realpath($locationFile)));
    }
}
