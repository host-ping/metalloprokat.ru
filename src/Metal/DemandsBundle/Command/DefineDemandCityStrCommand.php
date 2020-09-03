<?php

namespace Metal\DemandsBundle\Command;

use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DefineDemandCityStrCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('metal:demands:def-city');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln(sprintf('%s: Start command "%s"', date('d.m.Y H:i:s'), $this->getName()));

        $doctrine = $this->getContainer()->get('doctrine');
        $em = $doctrine->getManager();
        /* @var  $em EntityManager */
        $conn = $em->getConnection();

        $cities = $conn->fetchAll('SELECT Region_Name, Region_ID
                FROM `Classificator_Region`
                WHERE `country_id` = :russia
                ORDER BY CHAR_LENGTH(Region_Name) DESC',
            array('russia' => 165));
        $total = 0;

        foreach ($cities as $city) {
            $cityName = preg_replace('/[-\s]/', '%', trim($city['Region_Name']));
            $demands = $conn->fetchAll(
                'SELECT old_demand_id, id, conditions
                                        FROM demand
                                        WHERE  `body` LIKE :str AND (conditions LIKE :cityStr OR conditions LIKE :cityStr1 OR conditions LIKE :cityStr2 OR conditions LIKE :cityStr3)
                                        ORDER BY `old_demand_id` DESC',
                array('str' => 'Был статус заявки - промодерирована. Но не удалось найти город%', "cityStr" => '% '.$cityName.';%', "cityStr1" => $cityName.';%', "cityStr2" => '%.'.$cityName.';%', "cityStr3" => ''.$cityName.'%;%')
            );
            $total += count($demands);
            foreach ($demands as $demand) {
                $conn->executeQuery(
                    'UPDATE demand SET body=\'\', moderated_at = created_at, conditions = \'\', city_id = :newReg WHERE id = :id',
                    array('id' => $demand['id'], 'newReg' => $city['Region_ID']));
                $output->writeln(sprintf('%s - %s', $demand['conditions'], $cityName));
            }
        }

        $areas = $conn->fetchAll('SELECT Regions_Name, Regions_ID, Capital
                FROM `Classificator_Regions`
                WHERE `country_id` = :russia
                ORDER BY CHAR_LENGTH(Regions_Name) DESC',
            array('russia' => 165));

        foreach ($areas as $area) {
            $cityName = preg_replace('/(обл\.)|(респ\.)|(край)/', '', $area['Regions_Name']);
            $cityName = preg_replace('/[-\s]/', '%', trim($cityName));;
            $demands = $conn->fetchAll(
                'SELECT old_demand_id, id, conditions
                                        FROM demand
                                        WHERE  `body` LIKE :str AND (conditions LIKE :cityStr OR conditions LIKE :cityStr1)
                                        ORDER BY `old_demand_id` DESC',
                array('str' => 'Был статус заявки - промодерирована. Но не удалось найти город%', "cityStr" => '% '.$cityName.' %;%', "cityStr1" => $cityName.' %;%')
            );
            $total += count($demands);
            foreach ($demands as $demand) {
                $conn->executeQuery(
                    'UPDATE demand SET body=\'\', moderated_at = created_at, conditions = \'\', city_id = :newReg WHERE id = :id',
                    array('id' => $demand['id'], 'newReg' => $area['Capital']));
                $output->writeln(sprintf('%s - %s', $demand['conditions'], $area['Regions_Name']));
            }
        }

        $output->writeln(sprintf('Всего включено - %d', $total));

    }
}