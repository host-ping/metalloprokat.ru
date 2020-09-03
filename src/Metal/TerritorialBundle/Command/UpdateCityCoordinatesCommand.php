<?php

namespace Metal\TerritorialBundle\Command;

use Buzz\Browser;
use Buzz\Bundle\BuzzBundle\Buzz\Buzz;
use Buzz\Bundle\BuzzBundle\Exception\ResponseException;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

class UpdateCityCoordinatesCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('metal:territorial:update-city-coordinates');

        $this->addOption('limit', null, InputOption::VALUE_OPTIONAL, null, 50);
        $this->addOption('truncate', null, InputOption::VALUE_NONE);
        $this->addOption('city-id', null, InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY);
        $this->addOption('city-slug', null, InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln(sprintf('%s: Start command "%s"', date('d.m.Y H:i:s'), $this->getName()));

        $buzz = $this->getContainer()->get('buzz');
        /* @var $buzz Buzz */
        $browser = $buzz->getBrowser('geocode.yandex');
        /* @var $browser Browser */
        $em = $this->getContainer()->get('doctrine');
        /* @var $em EntityManager */
        $conn = $em->getConnection();
        /* @var $conn \Doctrine\DBAL\Connection */

        if ($input->getOption('truncate')) {
            $output->writeln('Truncate');
            $conn->executeUpdate('
                UPDATE Classificator_Region
                SET coordinates_updated_at = NULL,
                    latitude = NULL,
                    longitude = NULL');
        }

        $citiesIds = $input->getOption('city-id');
        $citiesSlugs = $input->getOption('city-slug');
        $qb = $conn->createQueryBuilder()->select('Region_ID, Country_Name, Region_Name')
            ->from('Classificator_Region', 'city')
            ->join('city', 'Classificator_Country', 'country', 'city.country_id = country.Country_ID');

        if ($citiesIds || $citiesSlugs) {
            if ($citiesIds) {
                $qb->orWhere('city.Region_ID IN (:ids)')
                    ->setParameter('ids', $citiesIds, Connection::PARAM_INT_ARRAY);
            }

            if ($citiesSlugs) {
                $qb->orWhere('city.Keyword IN (:slugs)')
                    ->setParameter('slugs', $citiesSlugs, Connection::PARAM_STR_ARRAY);
            }
        } else {
            $qb->andWhere('city.coordinates_updated_at IS NULL');
            $limit = $input->getOption('limit') + 0;
            if ($limit) {
                $qb->setMaxResults($limit);
            }
        }

        $cities = $qb->execute()->fetchAll();
        $i = 0;
        $count = count($cities);
        $now = new \DateTime();
        $params = array('format' => 'json');

        foreach ($cities as $city) {
            $i++;
            $params['geocode'] = trim($city['Country_Name']. ' ' . $city['Region_Name']);

            try {
                $resp = $browser->get('/1.x/?'.http_build_query($params));
                $respJson = json_decode($resp->getContent(), true);
            } catch (ResponseException $e) {
                $output->writeln($e->getMessage());
                $respJson = array();
            }

            if (!empty($respJson['response']['GeoObjectCollection']['featureMember'])) {
                $point = $respJson['response']['GeoObjectCollection']['featureMember'][0]['GeoObject']['Point']['pos'];
                $point = explode(' ', $point);
                $conn->executeUpdate('
                    update Classificator_Region
                    set
                      latitude = :latitude,
                      longitude = :longitude,
                      coordinates_updated_at = :now
                    where Region_ID = :id',
                    array(
                        'latitude' => $point[0],
                        'longitude' => $point[1],
                        'now' => $now,
                        'id' => $city['Region_ID']),
                    array('now' => 'datetime'));
            } else {
                $conn->executeUpdate('
                    update Classificator_Region
                    set coordinates_updated_at = :now
                    where Region_ID = :id',
                    array('now' => $now, 'id' => $city['Region_ID']),
                    array('now' => 'datetime'));
            }
            if ($i % 2 == 0) {
                $output->writeln($i . '/' . $count);
            }
        }

        $output->writeln(sprintf('%s: Done "%s"', date('d.m.Y H:i:s'), $this->getName()));
    }
}
