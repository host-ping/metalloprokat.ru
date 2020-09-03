<?php

namespace Metal\DemandsBundle\Command;

use Doctrine\ORM\EntityManager;
use Metal\ProjectBundle\Util\InsertUtil;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CreateDemandItemAttributeValueCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('metal:demands:create-demands-items-attributes-values')
            ->addOption('truncate', null, InputOption::VALUE_NONE);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln(sprintf('%s: Start command "%s"', date('d.m.Y H:i:s'), $this->getName()));

        $doctrine = $this->getContainer()->get('doctrine');
        $em = $doctrine->getManager();
        /* @var  $em EntityManager */
        $conn = $em->getConnection();

        $productParameterValueRepository = $em->getRepository('MetalProductsBundle:ProductParameterValue');

        if ($input->getOption('truncate')) {
            $output->writeln(sprintf('%s: Truncate table', date('d.m.Y H:i:s')));
            $conn->executeUpdate('TRUNCATE TABLE demand_item_attribute_value');
        }

        $minId = $conn->fetchColumn('SELECT MIN(id) FROM demand');
        $maxId = $conn->fetchColumn('SELECT MAX(id) FROM demand');
        $idFrom = $minId;
        do {
            $idTo = $idFrom + 500;

            $demandsData = $conn->fetchAll(
                'SELECT d.id, di.id AS item_id, di.title, di.category_id 
                  FROM demand AS d
                  JOIN demand_item AS di ON d.id = di.demand_id AND di.is_locked_attribute_values = false
                WHERE d.id >= :id_from AND d.id < :id_to',
                array(
                    'id_from' => $idFrom,
                    'id_to' => $idTo
                )
            );

            $rows = array();
            foreach ($demandsData as $demandData) {
                $matchAttributesForTitle = $productParameterValueRepository->matchAttributesForTitle($demandData['category_id'], $demandData['title']);
                foreach ($matchAttributesForTitle as $matchAttributeForTitle) {
                    $rows[] = array(
                        'demand_item_id' => $demandData['item_id'],
                        'attribute_value_id' => $matchAttributeForTitle['parameterOptionId']
                    );
                }
            }

            $output->writeln(sprintf('Insert attribute value to %d ', $idTo));
            InsertUtil::insertMultipleOrUpdate($conn, 'demand_item_attribute_value', $rows, array('attribute_value_id'), 500);

            $idFrom = $idTo;
        } while ($idFrom <= $maxId);

        $output->writeln('finish command');

    }
}
