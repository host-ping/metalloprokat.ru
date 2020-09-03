<?php

namespace Metal\CompaniesBundle\Command;

use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManager;
use Metal\CompaniesBundle\Entity\CompanyCity;
use Metal\ProjectBundle\Helper\TextHelperStatic;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class RefreshCompanyTitlesCommand extends ContainerAwareCommand
{
    private $minId;

    private $maxId;

    /**
     * @var Connection
     */
    private $conn;

    protected function configure()
    {
        $this->setName('metal:companies:refresh-company-titles');
        $this->addOption('refresh', null, InputOption::VALUE_OPTIONAL|InputOption::VALUE_IS_ARRAY, '', array('categories', 'cities'));
        $this->addOption('company-id', null, InputOption::VALUE_OPTIONAL|InputOption::VALUE_IS_ARRAY, '', array());
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine');
        /* @var $em EntityManager */

        $this->conn = $em->getConnection();

        $this->conn->getConfiguration()->setSQLLogger(null);
        $companiesIds = $input->getOption('company-id');

        $this->minId = $this->conn->fetchColumn('SELECT MIN(Message_ID) FROM Message75');
        $this->maxId = $this->conn->fetchColumn('SELECT MAX(Message_ID) FROM Message75');

        if (in_array('categories', $input->getOption('refresh'))) {
            $this->truncateCategoryTitles($output, $companiesIds);
        }

        if (in_array('cities', $input->getOption('refresh'))) {
            $this->truncateCompanyDeliveryTitles($output, $companiesIds);
        }
    }

    private function truncateCategoryTitles(OutputInterface $output, array $companiesIds = array())
    {
        $output->writeln('SET NULL company_categories_titles');
        $this->conn->executeUpdate('UPDATE Message75 SET company_categories_titles = null');
        $idFrom = $this->minId;
        do {
            $idTo = $idFrom + 1000;

            $qb = $this->conn->createQueryBuilder()
                ->select('cc.company_id, cat.title_ablative')
                ->from('Message76', 'cc')
                ->join('cc', 'Message73', 'cat', 'cc.cat_id = cat.Message_ID');

            if ($companiesIds) {
                $qb->andWhere('cc.company_id IN (:companiesIds)')
                    ->setParameter('companiesIds', $companiesIds, Connection::PARAM_INT_ARRAY);
            } else {
                $qb->andWhere('cc.company_id >= :id_from')
                    ->andWhere('cc.company_id < :id_to')
                    ->setParameter('id_from', $idFrom)
                    ->setParameter('id_to', $idTo);
            }

            $companyCategories = $qb->execute()->fetchAll();

            $companyCategoriesArray = array();
            foreach ($companyCategories as $companyCategory) {
                $normalizeTitle = TextHelperStatic::normalizeTitleForEmbed($companyCategory['title_ablative']);
                $output->writeln(sprintf('Normalize title "%s" for company_id %d', $normalizeTitle, $companyCategory['company_id']));
                $companyCategoriesArray[$companyCategory['company_id']][] = $normalizeTitle;
            }

            foreach ($companyCategoriesArray as $companyId => $categories) {
                $this->conn->executeUpdate('UPDATE Message75 SET company_categories_titles = :csv WHERE Message_ID = :company_id',
                    array('csv' => $categories, 'company_id' => $companyId), array('csv' => 'csv'));
            }

            $idFrom = $idTo;

        } while (!$companiesIds && ($idFrom <= $this->maxId));
    }

    private function truncateCompanyDeliveryTitles(OutputInterface $output, array $companiesIds = array())
    {
        $output->writeln('SET NULL company_delivery_titles');
        $this->conn->executeUpdate('UPDATE Message75 AS company SET company.company_delivery_titles = null');

        $idFrom = $this->minId;
        do {
            $idTo = $idFrom + 1000;

            $qb = $this->conn->createQueryBuilder()
                ->select('cdc.company_id', 'c.title_accusative')
                ->from('company_delivery_city', 'cdc')
                ->where('kind = :kind_delivery')
                ->andWhere('cdc.enabled = true')
                ->join('cdc', 'Classificator_Region', 'c', 'cdc.city_id = c.Region_ID')
                ->setParameter('kind_delivery', CompanyCity::KIND_DELIVERY);

            if ($companiesIds) {
                $qb->andWhere('cdc.company_id IN (:companiesIds)')
                    ->setParameter('companiesIds', $companiesIds, Connection::PARAM_INT_ARRAY);
            } else {
                $qb->andWhere('cdc.company_id >= :id_from')
                    ->andWhere('cdc.company_id < :id_to')
                    ->setParameter('id_from', $idFrom)
                    ->setParameter('id_to', $idTo);
            }

            $companyCities = $qb->execute()->fetchAll();

            $companyCitiesArray = array();
            foreach ($companyCities as $companyCity) {
                $companyCitiesArray[$companyCity['company_id']][] = $companyCity['title_accusative'];
            }

            foreach ($companyCitiesArray as $companyId => $cities) {
                $this->conn->executeUpdate('UPDATE Message75 SET company_delivery_titles = :csv WHERE Message_ID = :company_id',
                    array('csv' => $cities, 'company_id' => $companyId), array('csv' => 'csv'));
            }

            $idFrom = $idTo;
        } while (!$companiesIds && ($idFrom <= $this->maxId));
    }
}
