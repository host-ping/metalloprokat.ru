<?php

namespace Metal\CompaniesBundle\Command;

use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class AssociateCompanyWithCityCodeCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('metal:companies:associate-with-city-code');
        $this->addOption('truncate', null, InputOption::VALUE_NONE);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln(sprintf('Start command %s at %s', $this->getName(), date('Y-m-d H:i')));

        $em = $this->getContainer()->get('doctrine')->getManager();
        /* @var $em EntityManager */

        $conn = $em->getConnection();

        $conn->getConfiguration()->setSQLLogger(null);

        if ($input->getOption('truncate')) {
            $output->writeln(sprintf('Reset association with city code'));
            $conn->executeUpdate('UPDATE company_delivery_city SET is_associated_with_city_code = FALSE');
        }
        $citiesCodeData = $em->getRepository('MetalTerritorialBundle:CityCode')->createQueryBuilder('cc')
            ->select('IDENTITY(cc.city) as cityId, cc.code')
            ->getQuery()
            ->getResult();

        $citiesCode = array();
        foreach ($citiesCodeData as $cityCode) {
            $citiesCode[$cityCode['cityId']][] = $cityCode['code'];
        }

        $minId = $conn->fetchColumn('SELECT MIN(id) FROM company_delivery_city');
        $maxId = $conn->fetchColumn('SELECT MAX(id) FROM company_delivery_city');
        $i = 0;
        $idFrom = $minId;
        $badCities = array();
        do {
            $idTo = $idFrom + 100;
            $companiesCities = $conn->executeQuery('
                SELECT cdc.city_id AS cityId, IF(cdc.is_main_office, com.phones_as_string, cdc.phones_as_string) AS phone, cdc.company_id AS companyId
                FROM company_delivery_city AS cdc
                JOIN Message75 AS com
                ON cdc.company_id = com.Message_ID
                WHERE cdc.id >= :id_from AND cdc.id < :id_to',
                array(
                    'id_from' => $idFrom,
                    'id_to' => $idTo
                )
            )->fetchAll();

            foreach ($companiesCities as $companiesCity) {
                if (!isset($citiesCode[$companiesCity['cityId']])) {
                    $badCities[] = $companiesCity['cityId'];
                    continue;
                }

                foreach ($citiesCode[$companiesCity['cityId']] as $cityCode) {
                    preg_match('/\(?'.$cityCode.'\)?/', $companiesCity['phone'], $matches);

                    if (isset($matches[0])) {
                        $conn->executeUpdate('
                        UPDATE company_delivery_city
                        SET is_associated_with_city_code = TRUE
                        WHERE company_id = :company_id AND city_id = :city_id',
                            array('company_id' => $companiesCity['companyId'], 'city_id' => $companiesCity['cityId'])
                        );
                    }
                }
            }

            $idFrom = $idTo;
            $i++;
            if ($i % 50 == 0) {
                $output->writeln($idTo.' / '.$maxId.' '.date('d.m.Y H:i:s'));
            }
        } while ($idFrom <= $maxId);

        $badCities = array_unique($badCities);
        $badCitiesStr = implode(',', $badCities);
        $output->writeln(sprintf('Cities not in City Code: %s', $badCitiesStr));
        $output->writeln(sprintf('End command %s at %s', $this->getName(), date('Y-m-d H:i')));
    }
}
