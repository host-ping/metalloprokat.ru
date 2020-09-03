<?php

namespace Metal\CompaniesBundle\Command;

use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManager;
use Geocoder\Exception\NoResultException;
use Metal\ProjectBundle\Repository\SiteRepository;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

class UpdateCompanyCoordinatesCommand extends  ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('metal:companies:address');

        $this->addOption('limit', null, InputOption::VALUE_OPTIONAL, '', 50);

        $this->addOption('truncate-company', null, InputOption::VALUE_NONE);
        $this->addOption('truncate-office', null, InputOption::VALUE_NONE);
        $this->addOption('clean-bad-addresses', null, InputOption::VALUE_NONE);

        $this->addOption('truncate-date-start', null, InputOption::VALUE_OPTIONAL);
        $this->addOption('truncate-date-stop', null, InputOption::VALUE_OPTIONAL);

        $this->addOption('company-id', null, InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY, 'array of companies ids', array());
        $this->addOption('company-slug', null, InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY, 'array of companies slugs', array());
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln(sprintf('%s: Start command "%s"', date('d.m.Y H:i:s'), $this->getName()));

        $em = $this->getContainer()->get('doctrine');
        /* @var $em EntityManager */
        $conn = $em->getConnection();
        /* @var $conn \Doctrine\DBAL\Connection */

        $siteRepository =  $em->getRepository('MetalProjectBundle:Site');
        /* @var $siteRepository SiteRepository */

        $siteRepository->disableLogging();
        $limit = $input->getOption('limit') + 0;
        $truncateDateStart = null;
        if ($input->getOption('truncate-date-start')) {
            //'2014-06-25 01:53:58' or '2014-06-25'
            preg_match('/^[\d]{4}-[\d]{2}-[\d]{2}( [\d]{2}:[\d]{2}:[\d]{2})?$/', $input->getOption('truncate-date-start'), $truncateCompanyDateMatches);

            if (count($truncateCompanyDateMatches)) {
                $truncateDateStart = trim($input->getOption('truncate-date-start'));
            } else {
                $output->writeln(sprintf('%s: Не правильная дата, нужно в формате "%s"', date('d.m.Y H:i:s'), '\'2014-06-25 01:53:58\' or \'2014-06-25\''));

                return;
            }
        }

        $truncateDateStop = null;
        if ($input->getOption('truncate-date-stop')) {
            //'2014-06-25 01:53:58' or '2014-06-25'
            preg_match('/^[\d]{4}-[\d]{2}-[\d]{2}( [\d]{2}:[\d]{2}:[\d]{2})?$/', $input->getOption('truncate-date-stop'), $truncateCompanyDateMatches);

            if (count($truncateCompanyDateMatches)) {
                $truncateDateStop = trim($input->getOption('truncate-date-stop'));
            } else {
                $output->writeln(sprintf('%s: Не правильная дата, нужно в формате "%s"', date('d.m.Y H:i:s'), '\'2014-06-25 01:53:58\' or \'2014-06-25\''));

                return;
            }
        }

        if ($input->getOption('truncate-company')) {
            $output->writeln('truncate company');
            $conn->executeUpdate('
                update Message75
                set coordinates_updated_at = NULL,
                    latitude = NULL,
                    longitude = NULL');
        }

        if ($input->getOption('truncate-office')) {
            $output->writeln('truncate office');
            $conn->executeUpdate('
                update company_delivery_city
                set coordinates_updated_at = NULL,
                    latitude = NULL,
                    longitude = NULL');
        }

        if ($input->getOption('clean-bad-addresses')) {
            $output->writeln('cleaning bad addresses');

            $conn->executeUpdate("
                UPDATE Message75
                SET coordinates_updated_at = NULL,
                    latitude = NULL,
                    longitude = NULL,
                    address_new = ''
                WHERE address_new = '?' OR address_new = '-'");

            $conn->executeUpdate("
                UPDATE company_delivery_city
                SET coordinates_updated_at = NULL,
                    latitude = NULL,
                    longitude = NULL,
                    adress = ''
                WHERE adress = '?' OR adress = '-'");

            $output->writeln(sprintf('%s: Done "%s"', date('d.m.Y H:i:s'), $this->getName()));

            return;
        }

        $companiesIds = $input->getOption('company-id');
        $companiesSlugs = $input->getOption('company-slug');

        $qb = $conn->createQueryBuilder()->select('Message_ID, address_new, c.Region_Name, cr.Regions_Name')
            ->from('Message75', 'com')
            ->leftJoin('com', 'Classificator_Region', 'c', 'com.company_city = c.Region_ID')
            ->leftJoin('com', 'Classificator_Regions', 'cr', 'com.company_region = cr.Regions_ID')
            ->andWhere('com.company_city IS NOT NULL')
            ->andWhere('com.company_region IS NOT NULL')
        ;

        if ($companiesIds || $companiesSlugs ) {
            if ($companiesIds) {
                $qb->andWhere('com.Message_ID IN (:ids)')
                    ->setParameter('ids', $companiesIds, Connection::PARAM_INT_ARRAY);
            }

            if ($companiesSlugs) {
                $qb->andWhere('com.slug IN (:slugs)')
                    ->setParameter('slugs', $companiesSlugs, Connection::PARAM_STR_ARRAY);
            }
        } else {
            if ($truncateDateStart || $truncateDateStop) {
                if ($truncateDateStart) {
                    $qb->andWhere('com.coordinates_updated_at >= :dateStart')
                        ->setParameter('dateStart', $truncateDateStart);
                }

                if ($truncateDateStop) {
                    $qb->andWhere('com.coordinates_updated_at <= :dateStop')
                        ->setParameter('dateStop', $truncateDateStop);
                }
            } else {
                $qb->andWhere('com.coordinates_updated_at IS NULL');
            }
        }

        $qb->andWhere("com.address_new <> '' ");

        if ($limit) {
            $qb->setMaxResults($limit);
        }

        $companies = $qb->execute()->fetchAll();
        $now = new \DateTime();
        $count = count($companies);
        $i = 0;

        foreach ($companies as $company) {
            $i++;
            $companyAddress = $company['address_new'];
            $output->writeln(sprintf('%s: Default company address: "%s"', date('d.m.Y H:i:s'), $companyAddress));

            //Удаляем "г.", "г," или "г " (с пробелом, точкой или запятой)
            $companyAddress = preg_replace('/(^|[^а-яА-Яё0-9])(г|город)($|[^а-яА-Яё0-9])/ui', ' ', $companyAddress);
            $output->writeln(sprintf('%s: Удаляем "г.", "г," или "г " (с пробелом, точкой или запятой): "%s"', date('d.m.Y H:i:s'), $companyAddress));

            //тримим пробелы
            $companyAddress = trim($companyAddress);
            $output->writeln(sprintf('%s: Тримим пробелы: "%s"', date('d.m.Y H:i:s'), $companyAddress));

            //Удаляем обл\.|обл$|область|респ\.|респ$|республика
            $companyRegionsName = preg_replace('/(^|[^а-яА-Яё0-9])(область|республика|обл|респ)($|[^а-яА-Яё0-9])/ui', ' ', $company['Regions_Name']);
            $output->writeln(sprintf('%s: Удаляем обл\.|обл$|область|респ\.|респ$|республика: "%s"', date('d.m.Y H:i:s'), $companyRegionsName));


            $companyRegionsName = trim($companyRegionsName);
            $output->writeln(sprintf('%s: Тримим пробелы: "%s"', date('d.m.Y H:i:s'), $companyRegionsName));

            $companyAddress = preg_replace('/'.preg_quote(trim($companyRegionsName)).'\s?(область|республика|обл\.|обл$|респ\.|респ$|)[\s,]/ui', '', $companyAddress);
            $output->writeln(sprintf('%s: Удаляем область: "%s"', date('d.m.Y H:i:s'), $companyAddress));

            $companyAddress = trim($companyAddress);
            $output->writeln(sprintf('%s: Тримим пробелы: "%s"', date('d.m.Y H:i:s'), $companyAddress));

            //Удаляем город только если следующий символ не буква
            $companyAddress = preg_replace('/'.preg_quote($company['Region_Name']).'($|[^а-яА-Яё0-9])/ui', ' ', $companyAddress);
            $output->writeln(sprintf('%s: Удаляем город только если следующий символ не буква: "%s"', date('d.m.Y H:i:s'), $companyAddress));

            //Тримим точки и запятые
            $companyAddress = trim($companyAddress, ' ,.');
            $output->writeln(sprintf('%s: Тримим точки и запятые: "%s"', date('d.m.Y H:i:s'), $companyAddress));

            //Удаляем запятые подряд
            $companyAddress = preg_replace('#[ ,]{2,}#', ', ', $companyAddress);
            $output->writeln(sprintf('%s: Удаляем запятые подряд: "%s"', date('d.m.Y H:i:s'), $companyAddress));

            $output->writeln(sprintf('%s: Final company address: "%s"', date('d.m.Y H:i:s'), $companyAddress));

            $cityCoords['latitude'] = null;
            $cityCoords['longitude'] = null;

            if ($companyAddress) {
                try {
                    $cityCoords = $this->getContainer()->get('bazinga_geocoder.geocoder')
                        ->using('yandex')
                        ->geocode(trim($company['Region_Name'] . ' ' . $company['address_new']));
                } catch (NoResultException $e) {

                }
            }

            $conn->executeUpdate('
                    update Message75
                    set
                      latitude = :latitude,
                      longitude = :longitude,
                      coordinates_updated_at = :now,
                      address_new = :address
                    where Message_ID = :id',
                array(
                    'latitude' => $cityCoords["latitude"],
                    'longitude' => $cityCoords["longitude"],
                    'now' => $now,
                    'address' => $companyAddress,
                    'id' => $company['Message_ID']
                ),
                array(
                    'now' => 'datetime'
                )
            );

            if ($i % 2 == 0) {
                $output->writeln($i . '/' . $count);
            }
        }

//-----------------------------------------------------------------------------------------------------------------------

        $output->writeln('companies offices started');
        $qb = $conn->createQueryBuilder()->select('cdc.id, cdc.adress, c.Region_Name')
            ->from('company_delivery_city', 'cdc')
            ->join('cdc', 'Message75', 'com', 'cdc.company_id = com.Message_ID')
            ->leftJoin('cdc', 'Classificator_Region', 'c', 'cdc.city_id = c.Region_ID')
            ->andWhere('cdc.city_id IS NOT NULL')
        ;

        if ($companiesIds || $companiesSlugs ) {
            if ($companiesIds) {
                $qb->andWhere('com.Message_ID IN (:ids)')
                    ->setParameter('ids', $companiesIds, Connection::PARAM_INT_ARRAY);
            }

            if ($companiesSlugs) {
                $qb->andWhere('com.slug IN (:slugs)')
                    ->setParameter('slugs', $companiesSlugs, Connection::PARAM_STR_ARRAY);
            }
        } else {
            if ($truncateDateStart || $truncateDateStop) {
                if ($truncateDateStart) {
                    $qb->andWhere('cdc.coordinates_updated_at >= :dateStart')
                        ->setParameter('dateStart', $truncateDateStart);
                }

                if ($truncateDateStop) {
                    $qb->andWhere('cdc.coordinates_updated_at <= :dateStop')
                        ->setParameter('dateStop', $truncateDateStop);
                }
            } else {
                $qb->andWhere('cdc.coordinates_updated_at IS NULL');
            }
        }

        $qb->andWhere("cdc.adress <> '' ");

        if ($limit) {
            $qb->setMaxResults($limit);
        }

        $companies = $qb->execute()->fetchAll();

        $now = new \DateTime();
        $count = count($companies);
        $i = 0;

        foreach ($companies as $company) {
            $i++;

            $companyAddress = $company['adress'];
            $output->writeln(sprintf('%s: Default company address: "%s"', date('d.m.Y H:i:s'), $companyAddress));

            //Удаляем "г.", "г," или "г " (с пробелом, точкой или запятой)
            $companyAddress = trim(preg_replace('/(^|[^а-яА-Яё0-9])(г|город)($|[^а-яА-Яё0-9])/ui', ' ', $companyAddress));
            $output->writeln(sprintf('%s: Удаляем "г.", "г," или "г " (с пробелом, точкой или запятой): "%s"', date('d.m.Y H:i:s'), $companyAddress));

            //Удаляем город только если следующий символ не буква
            $companyAddress = preg_replace('/'.preg_quote($company['Region_Name']).'($|[^а-яА-Яё0-9])/ui', ' ', $companyAddress);
            $output->writeln(sprintf('%s: Удаляем город только если следующий символ не буква: "%s"', date('d.m.Y H:i:s'), $companyAddress));

            //Тримим точки и запятые
            $companyAddress = trim($companyAddress, ' ,.');

            //Удаляем запятые подряд
            $companyAddress = preg_replace('#[ ,]{2,}#', ', ', $companyAddress);
            $output->writeln(sprintf('%s: Final company address: "%s"', date('d.m.Y H:i:s'), $companyAddress));

            $cityCoords['latitude'] = null;
            $cityCoords['longitude'] = null;

            if ($companyAddress) {
                try {
                    $cityCoords = $this->getContainer()->get('bazinga_geocoder.geocoder')
                        ->using('yandex')
                        ->geocode(trim($company['Region_Name'] . ' ' . $company['adress']));
                } catch (NoResultException $e) {

                }
            }

            $conn->executeUpdate('
                update company_delivery_city
                set
                  latitude = :latitude,
                  longitude = :longitude,
                  coordinates_updated_at = :now,
                  address_new = :address
                where id = :id',
                array(
                    'latitude' => $cityCoords['latitude'],
                    'longitude' => $cityCoords['longitude'],
                    'now' => $now,
                    'address' => $companyAddress,
                    'id' => $company['id']),
                array('now' => 'datetime'));

            if ($i % 2 == 0) {
                $output->writeln($i . '/' . $count);
            }
        }

        $siteRepository->restoreLogging();

        $output->writeln(sprintf('%s: Done "%s"', date('d.m.Y H:i:s'), $this->getName()));
    }
}
