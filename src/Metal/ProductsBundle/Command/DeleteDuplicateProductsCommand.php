<?php

namespace Metal\ProductsBundle\Command;

use Doctrine\ORM\EntityManager;
use Metal\ProductsBundle\ChangeSet\ProductsBatchEditChangeSet;
use Metal\ProjectBundle\Doctrine\Utils;
use Metal\ProjectBundle\Util\InsertUtil;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class DeleteDuplicateProductsCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('metal:products:delete-duplicate-products');
        $this->addOption('company-id', null, InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY);
        $this->addOption('all-companies', null, InputOption::VALUE_NONE);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $output->writeln(sprintf('Start command %s at %s', $this->getName(), date('Y-m-d H:i')));

        $em = $this->getContainer()->get('doctrine')->getManager();
        /* @var $em EntityManager */
        Utils::disableLogger($em);

        $allCompanies = $input->getOption('all-companies');
        if ($allCompanies) {
            $companiesIds = $em
                ->getRepository('MetalCompaniesBundle:Company')
                ->createQueryBuilder('c')
                ->select('c.id')
                ->getQuery()
                ->getResult();
            $companiesIds = array_column($companiesIds, 'id');
        } else {
            $companiesIds = $input->getOption('company-id');
            if (!$companiesIds) {
                $output->writeln(sprintf('%s : No companies ids', date('Y-m-d H:i')));

                return;
            }
        }

        $backend = $this->getContainer()->get('sonata.notification.backend');

        $processor = function ($companiesIds) use ($em, $backend, $output) {
            $productsIds = $em->getRepository('MetalProductsBundle:Product')
                ->disableDuplicatedProductsForCompanies($companiesIds);

            $output->writeln(sprintf('%s: Disable %d products.', date('Y-m-d H:i'), count($productsIds)));

            if ($productsIds) {
                $output->writeln(sprintf('%s: Remove from sphinx.', date('Y-m-d H:i')));

                $productsChangeSet = new ProductsBatchEditChangeSet();
                $productsChangeSet->productsToDisable = $productsIds;

                $backend->createAndPublish('admin_products', array('changeset' => $productsChangeSet));
            }
        };

        InsertUtil::processBatch($companiesIds, $processor, 50);

        $output->writeln(sprintf('End command %s at %s', $this->getName(), date('Y-m-d H:i')));
    }
}
