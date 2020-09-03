<?php

namespace Metal\ProductsBundle\Command;

use Doctrine\ORM\EntityManager;
use Metal\ProductsBundle\Entity\Product;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class FixDuplicateTitlesCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('metal:products:fix-duplicate-titles');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln(sprintf('Start command %s at %s', $this->getName(), date('Y-m-d H:i')));
        $em = $this->getContainer()->get('doctrine')->getManager();
        /* @var $em EntityManager */
        $conn = $em->getConnection();
        $conn->getConfiguration()->setSQLLogger(null);
        $productRepository = $em->getRepository('MetalProductsBundle:Product');

        $minId = $conn->fetchColumn('SELECT MIN(Message_ID) FROM Message75');
        $maxId = $conn->fetchColumn('SELECT MAX(Message_ID) FROM Message75');

        $idFrom = $minId;
        do {
            $idTo = $idFrom + 20;
            $companiesArray = $conn->fetchAll(
                'SELECT Message_ID FROM Message75 WHERE Message_ID >= :idFrom AND Message_ID < :idTo',
                array(
                    'idFrom' => $idFrom,
                    'idTo' => $idTo
                )
            );

            $companiesIds = array();
            foreach ($companiesArray as $companyId) {
                $companiesIds[] = $companyId['Message_ID'];
            }

            $output->writeln(sprintf('%s: idFrom: %s idTo: %s', date('d.m.Y H:i:s'), $idFrom, $idTo));
            $productRepository->updateDuplicatedTitles($companiesIds);
            $idFrom = $idTo;
        } while ($idFrom <= $maxId);

        $output->writeln(sprintf('End command %s at %s', $this->getName(), date('Y-m-d H:i')));
    }
}
