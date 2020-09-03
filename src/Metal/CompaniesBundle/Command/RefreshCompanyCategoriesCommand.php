<?php

namespace Metal\CompaniesBundle\Command;

use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManager;

use Metal\CategoriesBundle\Service\ProductCategoryDetector;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class RefreshCompanyCategoriesCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('metal:companies:refresh-company-categories');
        $this->addOption('truncate', null, InputOption::VALUE_NONE);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln(sprintf('%s: Start command "%s"', date('d.m.Y H:i:s'), $this->getName()));

        $em = $this->getContainer()->get('doctrine');
        /* @var $em EntityManager */

        $categoryMatchesService = $this->getContainer()->get("metal.categories.category_matcher.product");
        /* @var $categoryMatchesService ProductCategoryDetector */

        $conn = $em->getConnection();
        /* @var $conn Connection */

        if ($input->getOption('truncate')) {
            $conn->executeUpdate("TRUNCATE Message76");
        }

        $conn->getConfiguration()->setSQLLogger(null);

        $minId = $conn->fetchColumn('SELECT MIN(Message_ID) FROM Message75');
        $maxId = $conn->fetchColumn('SELECT MAX(Message_ID) FROM Message75');
        $i = 0;
        $idFrom = $minId;
        do {
            $idTo = $idFrom + 1000;
            $companyDescriptions = $this->getCompanyDescription($idFrom, $idTo);

            foreach ($companyDescriptions as $companyDescription) {
                $categories = $categoryMatchesService->getCategoriesByText($companyDescription['description']);
                foreach ($categories as $category) {
                    $conn->executeUpdate(
                        'INSERT IGNORE INTO Message76 (company_id, cat_id) VALUES (:company_id, :category_id)',
                        array(
                            'company_id'         => $companyDescription['companyId'],
                            'category_id'        => $category->getId()
                        )
                    );
                }
            }

            if ($i % 10 == 0) {
                $output->writeln($idFrom.' / '.$maxId.' '.date('d.m.Y H:i:s'));
            }

            $i++;
            $idFrom = $idTo;
        } while ($idFrom <= $maxId);

        $output->writeln(sprintf('%s: Completed', date('d.m.Y H:i:s')));
    }

    /**
     * @param $idFrom
     * @param $idTo
     * @return array
     *
     * return value - array('categoryId' => int, 'description' => string)
     */
    private function getCompanyDescription($idFrom, $idTo)
    {
        $em = $this->getContainer()->get('doctrine')->getManager();
        /* @var $em EntityManager */

        return $em->createQueryBuilder()
            ->select('identity(companyDescription.company) AS companyId')
            ->addSelect('companyDescription.description')
            ->from('MetalCompaniesBundle:CompanyDescription', 'companyDescription')
            ->where('companyDescription.company >= :id_from')
            ->andWhere('companyDescription.company < :id_to')
            ->andWhere('companyDescription.description IS NOT NULL')
            ->setParameter('id_from', $idFrom)
            ->setParameter('id_to', $idTo)
            ->getQuery()
            ->getArrayResult();
    }
}
