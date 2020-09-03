<?php

namespace Metal\ProductsBundle\Command;

use Doctrine\ORM\EntityManager;
use Metal\CompaniesBundle\Entity\Company;
use Metal\ProductsBundle\Entity\Product;
use Metal\ProjectBundle\Repository\SiteRepository;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CreateVirtualProductsCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('metal:products:create-virtual-products');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln(sprintf('Start command %s at %s', $this->getName(), date('Y-m-d H:i')));
        $em = $this->getContainer()->get('doctrine')->getManager();
        /* @var $em EntityManager */

        $siteRepository = $em->getRepository('MetalProjectBundle:Site');
        /* @var $siteRepository SiteRepository */

        $siteRepository->disableLogging();
        $conn = $em->getConnection();

        $companyRepository = $em->getRepository('MetalCompaniesBundle:Company');

        $minId = $conn->fetchColumn('SELECT MIN(Message_ID) FROM Message75 WHERE virtual_product_id IS NULL AND deleted_at_ts = 0');
        $maxId = $conn->fetchColumn('SELECT MAX(Message_ID) FROM Message75 WHERE virtual_product_id IS NULL AND deleted_at_ts = 0');
        $i = 0;
        $idFrom = $minId;
        do {
            $idTo = $idFrom + 200;
            $output->writeln(sprintf('%s: Get companies idTo: %d idFrom: %d ', date('d.m.Y H:i:s'), $idTo, $idFrom));
            $companies = $companyRepository->createQueryBuilder('company')
                ->select('company')
                ->where('company.virtualProduct IS NULL')
                ->andWhere('company.deletedAtTS = 0')
                ->andWhere('company.id >= :id_from')
                ->andWhere('company.id < :id_to')
                ->setParameter('id_from', $idFrom)
                ->setParameter('id_to', $idTo)
                ->getQuery()
                ->getResult();

            /* @var $companies Company[] */
            foreach ($companies as $company) {
                $i++;

                if (!$company->getMainOffice()) {
                    $output->writeln(sprintf('%s: Company has no main office %d', date('d.m.Y H:i:s'), $company->getId()));
                    continue;
                }

                $virtualProduct = Product::createVirtualProduct($company);
                $em->persist($virtualProduct);
                $company->setVirtualProduct($virtualProduct);

                if ($i % 50 === 0) {
                    $output->writeln(sprintf('%s: Flush processing %d', date('d.m.Y H:i:s'), $i));
                    $em->flush();
                    $em->flush();
                }
            }

            $em->flush();
            $em->flush();
            $em->clear();

            $idFrom = $idTo;
        } while ($idFrom <= $maxId);

        $em->flush();
        $em->flush();

        $siteRepository->restoreLogging();
    }
}
