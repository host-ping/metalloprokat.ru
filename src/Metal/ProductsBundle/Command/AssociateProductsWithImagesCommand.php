<?php

namespace Metal\ProductsBundle\Command;

use Doctrine\ORM\EntityManager;
use Metal\CompaniesBundle\Entity\PackageChecker;
use Metal\ProductsBundle\Entity\Product;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AssociateProductsWithImagesCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('metal:products:associate-products-with-images');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln(sprintf('Start command %s at %s', $this->getName(), date('Y-m-d H:i')));

        $em = $this->getContainer()->get('doctrine')->getManager();
        /* @var $em EntityManager */
        $conn = $em->getConnection();
        /* @var $conn \Doctrine\DBAL\Connection */
        $conn->getConfiguration()->setSQLLogger(null);

        $productsImages = $em->getRepository('MetalProductsBundle:ProductImage')->createQueryBuilder('pi')
            ->select('pi AS image, IDENTITY(pi.category) AS categoryId')
            ->where('pi.company IS NULL')
            ->getQuery()
            ->getResult();

        $productsImagesByCategory = array();
        foreach ($productsImages as $productImage) {
            $productsImagesByCategory[$productImage['categoryId']][] = $productImage['image'];
        }

        $limit = 100;
        do {
            $products = $em->getRepository('MetalProductsBundle:Product')
                ->createQueryBuilder('p')
                ->select('p')
                ->join('p.company', 'c')
                ->where('c.codeAccess IN (:access_codes)')
                ->andWhere('c.enabledAutoAssociationWithPhotos = 1')
                ->andWhere('p.checked = :status')
                ->andWhere('p.image IS NULL')
                ->andWhere('p.category IN (:categories_ids)')
                ->setParameter('categories_ids', array_keys($productsImagesByCategory))
                ->setParameter('status', Product::STATUS_CHECKED)
                ->setParameter('access_codes', PackageChecker::getPackagesByOption('enabled_auto_association_with_photos'))
                ->setMaxResults($limit)
                ->getQuery()
                ->getResult();
            /* @var $products Product[] */

            foreach ($products as $product) {
                $productImages = $productsImagesByCategory[$product->getCategoryId()];
                $randomImage = $productImages[array_rand($productImages)];
                $product->setImage($randomImage);
            }

            $em->flush();
            $em->clear(Product::class);

            $output->writeln(sprintf('%s: Processed', date('Y-m-d H:i')));
        } while ($products);

        $em->getRepository('MetalProductsBundle:Product')->resetProductsAutoAssociationWithPhotos();

        $output->writeln(sprintf('End command %s at %s', $this->getName(), date('Y-m-d H:i')));
    }
}
