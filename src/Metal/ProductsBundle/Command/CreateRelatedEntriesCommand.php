<?php

namespace Metal\ProductsBundle\Command;

use Doctrine\ORM\EntityNotFoundException;
use Metal\ProductsBundle\Entity\Product;
use Metal\ProductsBundle\Entity\ProductDescription;
use Metal\ProductsBundle\Entity\ProductLog;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CreateRelatedEntriesCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('metal:products:create-related-entries');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln(sprintf('Start command %s at %s', $this->getName(), date('Y-m-d H:i')));

        $em = $this->getContainer()->get('doctrine.orm.default_entity_manager');

        $productRepository = $em->getRepository('MetalProductsBundle:Product');
        $productLogRepository = $em->getRepository('MetalProductsBundle:ProductLog');
        $productDescriptionRepository = $em->getRepository('MetalProductsBundle:ProductDescription');

        $em->getConnection()->executeUpdate(
            'UPDATE Message142 
              SET product_description_id = Message_ID, product_log_id = Message_ID'
        );

        do {

            /** @var Product[] $products */
            $qb = $productRepository->createQueryBuilder('product');

            $productLogQb = $productLogRepository
                ->createQueryBuilder('productLog')
                ->where('productLog.product = product.id');

            $productDescriptionQb = $productDescriptionRepository
                ->createQueryBuilder('productDescription')
                ->where('productDescription.product = product.id');

            $products = $qb
                ->where($qb->expr()->not($qb->expr()->exists($productLogQb->getDQL())))
                ->orWhere($qb->expr()->not($qb->expr()->exists($productDescriptionQb->getDQL())))
                ->setMaxResults(50)
                ->getQuery()
                ->getResult();

            foreach ($products as $product) {
                $output->writeln('Process product '.$product->getId());

                $hasProductDescription = $hasProductLog = true;
                try {
                    $product->getProductLog()->getProduct();
                } catch (EntityNotFoundException $entityNotFoundException) {
                    $hasProductLog = false;
                }

                if (!$hasProductLog) {
                    if (!$createdBy = $product->getCompany()->getCompanyLog()->getCreatedBy()) {
                        $output->writeln(
                            sprintf(
                                'Connot create "%s" for "%s" id %d. CreatedBy is empty.',
                                ProductLog::class,
                                Product::class,
                                $product->getId()
                            )
                        );
                    }

                    if ($createdBy) {
                        $output->writeln('Create ProductLog');
                        $productLog = new ProductLog();
                        $productLog->setProduct($product);
                        $productLog->setCreatedBy($createdBy);
                        $product->setProductLog($productLog);
                        $em->persist($productLog);
                    }
                }

                try {
                    $product->getProductDescription()->getProduct();
                } catch (EntityNotFoundException $entityNotFoundException) {
                    $hasProductDescription = false;
                }

                if (!$hasProductDescription) {
                    $output->writeln('Create ProductDescription');
                    $productDescription = new ProductDescription();
                    $productDescription->setProduct($product);
                    $product->setProductDescription($productDescription);
                    $em->persist($productDescription);
                }
            }

            $em->flush();

        } while ($products);

        $output->writeln(sprintf('End command %s at %s', $this->getName(), date('Y-m-d H:i')));
    }
}
