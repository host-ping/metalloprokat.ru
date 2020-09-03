<?php

namespace Metal\CategoriesBundle\Command;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RemoveWrongProductAttributesCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('metal:categories:remove-wrong-product-attributes')
            ->addArgument('category', InputArgument::OPTIONAL);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine')->getManager();
        /* @var $em EntityManager */

        $conn = $em->getConnection();

        $output->writeln(sprintf('Start command %s at %s', $this->getName(), date('Y-m-d H:i')));

        $category = $input->getArgument('category');

        $sql = 'SELECT `Message_ID` FROM `Message73` WHERE `cat_parent` != 0 ';
        if ($category) {
            $sql .= ' AND `Message_ID` = '.$category;
        }

        $allCategories = $conn->fetchAll($sql);

        foreach ($allCategories as $category) {
            $allowedPars = $conn->fetchAll(
                'SELECT `Par_ID`
                        FROM `Message162` m162
                        JOIN Message157 m157 ON m157.Message_ID = m162.Parent_Razd
                        WHERE m157.Parent_Razd = :category ',
                array('category' => $category['Message_ID'])
            );

            $allowedArr = array('0');
            foreach ($allowedPars as $allowedPar) {
                $allowedArr[] = $allowedPar['Par_ID'];
            }

            $conn->executeQuery(
                'DELETE FROM `Message159`
                            WHERE `GostM_ID` NOT IN ('.implode(',', $allowedArr).')
                            AND `Price_ID` IN (SELECT `Message_ID` FROM `Message142` WHERE `Category_ID`= :category)',
                array('category' => $category['Message_ID'])
            );

            $output->writeln('End for category '.$category['Message_ID']);
        }
    }
}
