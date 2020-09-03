<?php

namespace Metal\ProjectBundle\Command;

use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Metal\ProductsBundle\Entity\Product;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RefreshStroyDataFromProkatCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('metal:project:refresh-stroy-data-from-prokat');
    }
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln(sprintf('%s: Start command "%s"', date('d.m.Y H:i:s'), $this->getName()));

        $doctrine = $this->getContainer()->get('doctrine');
        $em = $doctrine->getManager();
        /* @var  $em EntityManager */
        $em->getConfiguration()->setSQLLogger(null);

        $conn = $em->getConnection();
        /* @var $conn Connection */
        $conn->getConfiguration()->setSQLLogger(null);

        $parametersMetalloprokat = $this->getContainer()->getParameter('database_metalloprokat');
        $metalloprokatConn = $this->getContainer()->get('doctrine.dbal.connection_factory')->createConnection($parametersMetalloprokat);
        $metalloprokatEm = EntityManager::create($metalloprokatConn, $em->getConfiguration());


        // получаем ids demand_item на строй
        $demandItemsIds = $em->createQueryBuilder()
            ->select('di.id')
            ->from('MetalDemandsBundle:DemandItem', 'di', 'di.id')
            ->getQuery()
            ->getResult();

        // получаем ids product на строй
        $productsIds = $em->createQueryBuilder()
            ->select('p.id')
            ->from('MetalProductsBundle:Product', 'p', 'p.id')
            ->getQuery()
            ->getResult();

        // удаляем из проката product которые есть на строй
        $metalloprokatEm->createQueryBuilder()
            ->update('MetalProductsBundle:Product', 'p')
            ->where('p.id IN (:products_ids)')
            ->set('p.checked', Product::STATUS_NOT_CHECKED)
            ->setParameter('products_ids', array_keys($productsIds))
            ->getQuery()
            ->execute();

        $output->writeln(sprintf('%s: Done command "%s"', date('d.m.Y H:i:s'), $this->getName()));
    }
}
