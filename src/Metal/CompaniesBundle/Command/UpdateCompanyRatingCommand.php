<?php

namespace Metal\CompaniesBundle\Command;

use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManager;
use Metal\CompaniesBundle\Entity\PackageChecker;
use Metal\ProductsBundle\Indexer\Operation\ProductChangeSet;
use Metal\ProductsBundle\Indexer\Operation\ProductsCriteria;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UpdateCompanyRatingCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('metal:companies:update-company-rating');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln(sprintf('%s: Start command "%s"', date('d.m.Y H:i:s'), $this->getName()));
        $em = $this->getContainer()->get('doctrine');
        /* @var $em EntityManager */
        $conn = $em->getConnection();
        $conn->getConfiguration()->setSQLLogger(null);

        $minId = $conn->fetchColumn('SELECT MIN(Message_ID) FROM Message75');
        $maxId = $conn->fetchColumn('SELECT MAX(Message_ID) FROM Message75');

        $i = 0;
        $idFrom = $minId;
        do {
            $idTo = $idFrom + 1000;
            $output->writeln(sprintf('%s: idFrom: %s idTo: %s', date('d.m.Y H:i:s'), $idFrom, $idTo));
            $this->processCompanies($output, $idFrom, $idTo);
            $idFrom = $idTo;
            $i++;
        } while ($idFrom <= $maxId);

        $output->writeln(sprintf('%s: Done command "%s"', date('d.m.Y H:i:s'), $this->getName()));
    }

    private function processCompanies(OutputInterface $output, $idFrom, $idTo)
    {
        $conn = $this->getContainer()->get('doctrine')->getConnection();

        $companies = $conn->executeQuery(
            'SELECT Message_ID, company_rating FROM Message75 WHERE Message_ID >= :id_from AND Message_ID < :id_to',
            array(
                'id_from' => $idFrom,
                'id_to' => $idTo,
            )
        )->fetchAll();

        if (!$companies) {
            $output->writeln(sprintf('%s: No companies.', date('Y-m-d H:i:s')));

            return;
        }

        $originalRating = array_column($companies, 'company_rating', 'Message_ID');

        $output->writeln(sprintf('%s: Update, company_rating set 0.', date('Y-m-d H:i:s')));

        $conn->executeUpdate(
            'UPDATE Message75 c SET c.company_rating = 0
            WHERE c.Message_ID >= :id_from AND c.Message_ID < :id_to',
            array(
                'id_from' => $idFrom,
                'id_to' => $idTo,
            )
        );

        $output->writeln(sprintf('%s: Update by company_payment_details.', date('Y-m-d H:i:s')));

        $conn->executeUpdate(
            '
            UPDATE company_payment_details cpd
            JOIN Message75 c
                ON c.Message_ID = cpd.company_id
            SET
                cpd.attachment_approved_at = IFNULL(cpd.attachment_uploaded_at, c.Created)
            WHERE c.code_access IN (:statuses) AND c.Message_ID >= :id_from AND c.Message_ID < :id_to',
            array(
                'statuses' => PackageChecker::getPackagesByOption('auto_approving_documents'),
                'id_from' => $idFrom,
                'id_to' => $idTo,
            ),
            array(
                'statuses' => Connection::PARAM_INT_ARRAY,
            )
        );

        $conn->executeUpdate(
            '
            UPDATE Message75 c
            JOIN company_payment_details cpd ON c.Message_ID = cpd.company_id
            SET c.company_rating = c.company_rating + 1
            WHERE cpd.attachment_approved_at IS NOT NULL
            AND c.Message_ID >= :id_from AND c.Message_ID < :id_to',
            array(
                'id_from' => $idFrom,
                'id_to' => $idTo,
            )
        );

        $monthAgo = new \DateTime('-30 days');

        $output->writeln(sprintf('%s: Update by visit statistic.', date('Y-m-d H:i:s')));

        $conn->executeUpdate(
            'UPDATE Message75 c
             JOIN (
                SELECT uv.company_id, COUNT(DISTINCT uv.date) as visits_count FROM user_visiting uv
                WHERE uv.date >= DATE(:month_ago) AND uv.company_id >= :id_from AND uv.company_id < :id_to
                GROUP BY uv.company_id
                HAVING visits_count >= :visits_count) AS visit_stat ON visit_stat.company_id = c.Message_ID
                SET c.company_rating = c.company_rating + 1 WHERE c.Message_ID >= :id_from AND c.Message_ID < :id_to',
            array(
                'month_ago' => $monthAgo,
                'visits_count' => 4,
                'id_from' => $idFrom,
                'id_to' => $idTo,
            ),
            array(
                'month_ago' => 'date',
            )
        );

        $prevWeek = new \DateTime('Monday previous week -7 days');

        $output->writeln(sprintf('%s: Update by stats product changes.', date('Y-m-d H:i:s')));

        $companiesIds = $conn->executeQuery(
            'SELECT p.Company_ID
            FROM Message142 p
            WHERE p.LastUpdated >= :prev_week AND p.Company_ID >= :id_from AND p.Company_ID < :id_to
            GROUP BY p.Company_ID',
            array(
                'prev_week' => $prevWeek,
                'id_from' => $idFrom,
                'id_to' => $idTo,
            ),
            array(
                'prev_week' => 'date',
            )
        )->fetchAll();

        $companiesIds = array_column($companiesIds, 'Company_ID');

        $conn->executeUpdate(
            'UPDATE Message75 c
            SET c.company_rating = c.company_rating + 1 WHERE c.Message_ID IN (:companiesIds)',
            array(
                'companiesIds' => $companiesIds,
            ),
            array(
                'companiesIds' => Connection::PARAM_INT_ARRAY
            )
        );

        // Всем платникам выставляем  по 3 звезды
        $output->writeln(sprintf('%s: The paid exhibiting companies 3 stars.', date('Y-m-d H:i:s')));
        $conn->executeUpdate(
            'UPDATE Message75 c SET c.company_rating = :stars WHERE c.code_access IN (:statuses) AND c.Message_ID >= :id_from AND c.Message_ID < :id_to',
            array(
                'id_from' => $idFrom,
                'id_to' => $idTo,
                'stars' => 3,
                'statuses' => PackageChecker::getPackagesByOption('auto_approving_documents'),
            ),
            array('statuses' => Connection::PARAM_INT_ARRAY)
        );

        $companies = $conn->executeQuery(
            'SELECT Message_ID, company_rating FROM Message75 WHERE Message_ID >= :id_from AND Message_ID < :id_to',
            array(
                'id_from' => $idFrom,
                'id_to' => $idTo,
            )
        )->fetchAll();

        $updatedRating = array_diff_assoc(array_column($companies, 'company_rating', 'Message_ID'), $originalRating);

        $indexer = $this->getContainer()->get('metal_products.indexer.products');

        foreach ($updatedRating as $companyId => $companyRating) {
            $changeSet = new ProductChangeSet();
            $changeSet->setCompanyRating($companyRating);

            $criteria = new ProductsCriteria();
            $criteria->setCompanyId($companyId);

            $indexer->update($changeSet, $criteria);
        }
    }
}
