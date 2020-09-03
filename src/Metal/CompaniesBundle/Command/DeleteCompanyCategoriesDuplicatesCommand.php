<?php

namespace Metal\CompaniesBundle\Command;

use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DeleteCompanyCategoriesDuplicatesCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('metal:companies:delete-company-categories-duplicate');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /* @var $em EntityManager */
        $em = $this->getContainer()->get('doctrine');
        /* @var $conn Connection */
        $conn = $em->getConnection();
        $conn->getConfiguration()->setSQLLogger(null);

        $output->writeln(sprintf('%s: Start remove duplicate company categories.', date('d.m.Y H:i:s')));

        $companyCategories = $conn->executeQuery(
            '
                SELECT Message_ID, company_id, cat_id
                FROM Message76
                GROUP BY company_id, cat_id
                HAVING COUNT(company_id) > 1;
            '
        )->fetchAll();

        foreach ($companyCategories as $companyCategory) {
            $output->writeln(sprintf('%s: Remove for category: %d and company: %d.', date('d.m.Y H:i:s'), $companyCategory['cat_id'], $companyCategory['company_id']));
            $conn->executeUpdate(
                'DELETE FROM Message76 WHERE company_id = :company_id AND cat_id = :category_id AND Message_ID != :id',
                array(
                    'company_id' => $companyCategory['company_id'],
                    'category_id' => $companyCategory['cat_id'],
                    'id' => $companyCategory['Message_ID']
                )
            );
        }

        $output->writeln(sprintf('%s: Finish remove duplicate company categories.', date('d.m.Y H:i:s')));
    }
}
