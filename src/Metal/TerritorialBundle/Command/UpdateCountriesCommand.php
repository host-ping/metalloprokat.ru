<?php

namespace Metal\TerritorialBundle\Command;

use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManager;

use Metal\TerritorialBundle\Entity\Country;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UpdateCountriesCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('metal:territorial:update-countries');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln(sprintf('%s: Start command "%s"', date('d.m.Y H:i:s'), $this->getName()));
        $em = $this->getContainer()->get('doctrine')->getManager();
        /* @var $em EntityManager */

        $companyRepository = $em->getRepository('MetalCompaniesBundle:Company');

        $conn = $em->getConnection();
        $hostnamesMap = $this->getContainer()->getParameter('hostnames_map');

        foreach ($hostnamesMap as $domain => $hostInfo) {
            $conn->executeUpdate(
                '
                UPDATE Classificator_Country
                SET
                  domain_title = :domain_title,
                  base_host = :base_host,
                  currency_id = :currency_id,
                  secure = :secure
                WHERE Country_ID = :country_id',
                array(
                    'base_host' => $domain,
                    'country_id' => $hostInfo['country_id'],
                    'domain_title' => $hostInfo['title'],
                    'currency_id' => $hostInfo['currency_id'],
                    'secure' => $hostInfo['host_prefix'] == 'https' ? 1 : 0,
                )
            );
        }

        $hostInfo = reset($hostnamesMap);
        $domain = key($hostnamesMap);
        // для всех стран без сайта прописываем данные России
        $conn->executeUpdate(
            '
                UPDATE Classificator_Country
                SET
                  domain_title = :domain_title,
                  base_host = :base_host,
                  currency_id = :currency_id
                WHERE Country_ID NOT IN (:countries_ids)',
            array(
                'base_host' => $domain,
                'domain_title' => $hostInfo['title'],
                'currency_id' => $hostInfo['currency_id'],
                'countries_ids' => Country::getEnabledCountriesIds()
            ),
            array('countries_ids' => Connection::PARAM_INT_ARRAY)
        );

        $conn->executeUpdate(
            '
            UPDATE Classificator_Region city
            JOIN Classificator_Regions region ON city.parent = region.Regions_ID
            SET city.country_id = region.country_id'
        );

        $conn->executeUpdate(
            '
            UPDATE Message75 c
            JOIN Classificator_Region reg on c.company_city = reg.Region_ID
            SET c.country_id = reg.country_id
            WHERE reg.country_id IS NOT NULL
            AND c.deleted_at_ts = 0'
        );

        $companyRepository->updateCompanyDomain();

        $output->writeln(sprintf('End command %s at %s', $this->getName(), date('Y-m-d H:i')));
    }
}
