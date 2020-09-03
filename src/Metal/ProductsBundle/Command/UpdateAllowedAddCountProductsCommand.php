<?php

namespace Metal\ProductsBundle\Command;

use Doctrine\ORM\EntityManager;
use Metal\CompaniesBundle\Entity\Company;
use Metal\ProductsBundle\Entity\Product;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class UpdateAllowedAddCountProductsCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('metal:products:update-allowed-add-count-products');
        $this->addOption(
            'company-id',
            null,
            InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY,
            'array of companies ids',
            array()
        );

        $this->setDescription('Актуализация продуктов, выставляет для компаний лимит товаров которые попадут в сфинкс, или будут отображатся на портале.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln(sprintf('%s: Start command "%s"', date('d.m.Y H:i:s'), $this->getName()));
        $em = $this->getContainer()->get('doctrine')->getManager();
        /* @var $em EntityManager */
        $conn = $em->getConnection();

        $companiesIds = $input->getOption('company-id');

        $productRepository = $em->getRepository('MetalProductsBundle:Product');
        $rangeIds = $conn->fetchAssoc('SELECT MIN(Message_ID) AS minId, MAX(Message_ID) AS maxId FROM Message75');

        $limit = 50;
        $idFrom = $rangeIds['minId'];
        do {
            $idTo = $idFrom + $limit;

            $qb = $em->getRepository('MetalCompaniesBundle:Company')
                ->createQueryBuilder('company')
                ->andWhere('company.deletedAtTS = 0')
            ;

            if ($companiesIds) {
                $qb->andWhere('company.id IN (:companiesIds)')
                    ->setParameter('companiesIds', $companiesIds);
            } else {
                $subQuery = $em->createQueryBuilder()
                    ->select('product')
                    ->from('MetalProductsBundle:Product', 'product')
                    ->andWhere('product.company = company.id')
                    ->andWhere('product.isVirtual = false')
                    ->andWhere('product.checked <> :status_deleted')
                ;

                $qb
                    ->andWhere($qb->expr()->exists($subQuery->getDQL()))
                    ->andWhere('company.id >= :idFrom')
                    ->setParameter('idFrom', $idFrom)
                    ->andWhere('company.id < :idTo')
                    ->setParameter('idTo', $idTo)
                    ->setParameter('status_deleted', Product::STATUS_DELETED)
                ;
            }
          
            $companies = $qb->getQuery()->getResult();
            /* @var $companies Company[] */

            foreach ($companies as $key => $company) {
                $productRepository->updatePermissionShowProducts($company);

                $output->writeln(
                    sprintf(
                        '%s: Company "<info>%d</info>" max %d products.',
                        date('d.m.Y H:i:s'),
                        $company->getId(),
                        $company->getPackageChecker()->getMaxAvailableProductsCount()
                    )
                );

                unset($companies[$key]);
            }

            $output->writeln(sprintf('%s: Process %d / %d companies.', date('d.m.Y H:i:s'), $idTo, $rangeIds['maxId']));
            $idFrom = $idTo;
        } while ($idFrom <= $rangeIds['maxId'] && !$companiesIds);

        $output->writeln(sprintf('%s: Success command "%s"', date('d.m.Y H:i:s'), $this->getName()));
    }
}
