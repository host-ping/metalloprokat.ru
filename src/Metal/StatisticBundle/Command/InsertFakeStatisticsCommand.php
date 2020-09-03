<?php


namespace Metal\StatisticBundle\Command;

use Doctrine\DBAL\Connection;
use Metal\StatisticBundle\Entity\StatsElement;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Metal\ProjectBundle\Entity\ValueObject\SourceTypeProvider;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

class InsertFakeStatisticsCommand extends ContainerAwareCommand
{
    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var OutputInterface
     */
    private $output;

    private $workHours;

    protected function configure()
    {
        $this
            ->setName('metal:stats:insert-fake-stats');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln(sprintf('%s: Start command "%s"', date('d.m.Y H:i:s'), $this->getName()));

        $this->workHours = 4;

        $this->connection = $this->getContainer()->get('doctrine.dbal.default_connection');
        $this->em = $this->getContainer()->get('doctrine')->getManager();

        $this->output = $output;

        /* $language = new ExpressionLanguage();

         $multiplyStatisticsCompanies = $this->getContainer()->getParameter('multiply_statistics_per_company');

         foreach ($multiplyStatisticsCompanies as $multiplyStatisticsCompany) {
             $now = new \DateTime('now');
             $date_condition = $multiplyStatisticsCompany['date_condition'];
             $coefficient = $multiplyStatisticsCompany['company_id'];
             $output->writeln($coefficient);
             $output->writeln(date('N'));

         }*/

        $arrCompanies = $this->getCompaniesList();
        foreach ($arrCompanies as $company_id => $val) {
            foreach ($val as $action_id => $actionLimits) {
                $qb = $this->connection->createQueryBuilder()
                    ->select('COUNT(*) as count')
                    ->from('stats_element')
                    ->where('company_id = :company_id')
                    ->andWhere('action = :action')
                    ->andWhere('date_created_at = :date_midnight')
                    ->setParameter('company_id', $company_id)
                    ->setParameter('action', $action_id)
                    ->setParameter('date_midnight', new \DateTime(),'date');
                $action_count = $qb->execute()->fetchColumn();

                $corrected_min = 0;(($actionLimits['min'] - $action_count) > 0 ? ($actionLimits['min'] - $action_count) : 0);
                $corrected_max = (($actionLimits['max'] - $action_count) > 0 ? $actionLimits['max'] - $action_count : 0);

                $fakes_per_hour_min = (int)($corrected_min / $this->workHours);
                $fakes_per_hour_max = (int)($corrected_max / $this->workHours);
                $this->output->writeln((new \DateTime())->format('Y-m-d').':'.$action_count.'-'.$fakes_per_hour_min.'-'.$fakes_per_hour_max);
                $fakes_per_hour = mt_rand($fakes_per_hour_min, $fakes_per_hour_max);
                $this->output->writeln($fakes_per_hour);

                if ($fakes_per_hour > 0) {
                    $cities_ids = $actionLimits['preferred_cities'];
                    $this->insertFakeActions($company_id, $fakes_per_hour, $action_id, $cities_ids);
                }
            }
        }

        $this->output->writeln(sprintf('%s: End command "%s"', date('d.m.Y H:i:s'), $this->getName()));
    }

    private function insertFakeActions($company_id, $fakeCount, $action, $cities_ids)
    {
        $this->output->writeln(sprintf('company - %s, insert fake action %s - %s times', $company_id, $action, $fakeCount));

        $qb = $this->connection->createQueryBuilder()
            ->select('COUNT(*) as count, category_id')
            ->from('stats_category')
            ->where('company_id = :company_id')
            ->andWhere('category_id IS NOT NULL')
            ->setParameter('company_id', $company_id)
            ->groupBy('category_id')
            ->orderBy('count', 'DESC')
            ->setMaxResults(10);
        $categories = $qb->execute()->fetchAll();

        $cityRepo = $this->em->getRepository('MetalTerritorialBundle:City');

        if (count($cities_ids) > 0) {
            $city = $cityRepo->findOneBy(array('id' => $cities_ids[array_rand($cities_ids)]));
            $this->output->writeln($city->getId());
        }else{
            $qb = $this->connection->createQueryBuilder()
                ->select('COUNT(*) as count, city_id')
                ->from('stats_city')
                ->where('company_id = :company_id')
                ->andWhere('city_id IS NOT NULL')
                ->setParameter('company_id', $company_id)
                ->groupBy('city_id')
                ->orderBy('count', 'DESC')
                ->setMaxResults(10);
            $cities = $qb->execute()->fetchAll();
            $city = $cityRepo->findOneBy(array('id' => $cities[array_rand($cities)]['city_id']));
        }

        $statsElement = new StatsElement();
        $statsElement->setCompanyId($company_id);
        $statsElement->setCategoryId($categories[array_rand($categories)]['category_id']);
        $statsElement->setAction($action);
        $statsElement->setIp($this->getRandomIP());
        $statsElement->setUserAgent($this->getRandomUserAgent());
        $statsElement->setFake(true);
        $statsElement->setCity($city);
        $statsElement->setSourceType(SourceTypeProvider::create($action));

        $statsElementRepo = $this->em->getRepository('MetalStatisticBundle:StatsElement');

        for ($ff = 1; $ff <= $fakeCount; $ff++) {
            $statsElementRepo->insertStatsElement($statsElement, false);
        }

        $this->output->writeln('inserted');

    }

    private function getCompaniesList()
    {
        $now = new \DateTime();

        $arrcompanies = array();

        $multiplyStatCompanies = array();
        $multiplyStatCompanies[] = [
            'company_id' => 2044259,
            'date_start' => '2019-09-30',
            'date_end' => '2020-07-30',
            'days_of_week' => [1,2,3,4,5],
            'actions' => [
                StatsElement::ACTION_VIEW_PRODUCT => ['min' => 10, 'max' => 50, 'preferred_cities' => [1123, 2000]],
                StatsElement::ACTION_SHOW_PRODUCT => ['min' => 1000, 'max' => 4000, 'preferred_cities' => [1123, 2000]]],
        ];
        $multiplyStatCompanies[] = [
            'company_id' => 2044259,
            'date_start' => '2019-09-30',
            'date_end' => '2020-07-30',
            'days_of_week' => [6,7],
            'actions' => [
                StatsElement::ACTION_VIEW_PRODUCT => ['min' => 0, 'max' => 20,'preferred_cities' => [1123,2000]],
                StatsElement::ACTION_SHOW_PRODUCT => ['min' => 200, 'max' => 700, 'preferred_cities' => [1123, 2000]]],
        ];

        $multiplyStatCompanies[] = [
            'company_id' =>  2052127,
            'date_start' => '2019-12-03',
            'date_end' => '2020-05-14',
            'days_of_week' => [1,2,3,4,5],
            'actions' => [
                StatsElement::ACTION_VIEW_PRODUCT => ['min' => 80, 'max' => 110, 'preferred_cities' => [1123, 2000]]],
        ];

        foreach ($multiplyStatCompanies as $key=>$val) {
            if ($now > new \DateTime($val['date_start']) && $now < new \DateTime($val['date_end']) && in_array(date('N'), $val["days_of_week"])) {
                $arrcompanies[$val['company_id']] = $val['actions'];
            }
        }

        return $arrcompanies;
    }

    private function getRandomUserAgent()
    {
        $arrAgents = [
            'Mozilla/4.79 [en] (Windows NT 5.0; U)',
            'Mozilla/4.76 [en] (Windows NT 5.0; U)',
            'Mozilla/0.91 Beta (Windows)',
            'Mozilla/0.6 Beta (Windows)',
            'Mozilla/4.7 (compatible; OffByOne; Windows 2000) Webster Pro V3.4',
            'Opera/9.00 (Windows NT 4.0; U; en)',
            'Opera/9.00 (Windows NT 5.1; U; en)',
            'Opera/9.0 (Windows NT 5.1; U; en)',
            'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; en) Opera 9.0',
            'Opera/8.01 (Windows NT 5.1)',
            'Mozilla/5.0 (Windows NT 5.1; U; en) Opera 8.01',
            'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1)',
            'Mozilla/5.0 (Windows NT 5.1; U; en) Opera 8.00',
            'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; en) Opera 8.00',
            'Opera/8.00 (Windows NT 5.1; U; en)',
            'Opera/7.60 (Windows NT 5.2; U) [en] (IBM EVV/3.0/EAK01AG9/LE)',
            'Opera/7.54 (Windows NT 5.1; U) [pl]',
            'Opera/7.11 (Windows NT 5.1; U) [en]',
            'Mozilla/4.0 (compatible; MSIE 6.0; Windows ME) Opera 7.11 [en]',
            'Mozilla/4.0 (compatible; MSIE 6.0; MSIE 5.5; Windows NT 5.0) Opera 7.02 Bork-edition [en]',
            'Mozilla/4.0 (compatible; MSIE 6.0; MSIE 5.5; Windows NT 4.0) Opera 7.0 [en]',
            'Mozilla/4.0 (compatible; MSIE 5.0; Windows 2000) Opera 6.0 [en]',
            'Mozilla/4.0 (compatible; MSIE 5.0; Windows 95) Opera 6.01 [en]',
            'Mozilla/3.0 (compatible; WebCapture 2.0; Auto; Windows)',
            'Mozilla/4.0 (compatible; Powermarks/3.5; Windows 95/98/2000/NT)'
        ];

        return $arrAgents[array_rand($arrAgents)];
    }

    private function getRandomIP()
    {
        $arrIP = [
            '46.52.244.1',
            '46.52.244.2',
            '46.52.244.3',
            '46.52.244.4',
            '46.52.244.5',
            '46.52.244.6',
            '46.52.244.7',
            '46.52.244.8',
            '46.52.244.9',
            '46.52.244.10',
            '46.52.244.11',
            '46.52.244.12',
            '46.52.244.13',
            '46.52.244.14',
            '46.52.244.15',
            '46.52.244.16',
            '46.52.244.17',
            '46.52.244.18',
            '46.52.244.19',
            '46.52.244.20',
            '46.52.244.21',
            '46.52.244.22',
            '46.52.244.23',
            '46.52.244.24',
            '46.52.244.25',
            '46.52.244.26',
            '46.52.244.27',
            '46.52.244.28',
            '46.52.244.29',
            '46.52.244.30',
            '46.52.244.31',
            '46.52.244.32',
            '46.52.244.33',
            '46.52.244.34',
            '46.52.244.35'
        ];

        return $arrIP[array_rand($arrIP)];
    }
}