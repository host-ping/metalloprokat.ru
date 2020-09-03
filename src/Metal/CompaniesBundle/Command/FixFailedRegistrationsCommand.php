<?php

namespace Metal\CompaniesBundle\Command;

use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManager;
use Metal\CompaniesBundle\Entity\CompanyCity;
use Metal\MiniSiteBundle\Entity\MiniSiteConfig;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class FixFailedRegistrationsCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('metal:companies:fix-failed-registrations');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln(sprintf('%s: Start command "%s"', date('d.m.Y H:i:s'), $this->getName()));
        $doctrine = $this->getContainer()->get('doctrine');
        $em = $doctrine->getManager();
        /* @var  $em EntityManager */
        $conn = $em->getConnection();
        /* @var $conn Connection */

//        $conn->executeQuery('
//            INSERT IGNORE INTO company_phone (company_id, phone)
//              (
//              SELECT c.Message_ID, null
//              FROM Message75 c
//              )
//              '
//        );

        //---

        $conn->executeQuery('
                 INSERT IGNORE INTO company_counter (id, company_id, company_updated_at, products_updated_at, minisite_colors_updated_at)
              SELECT
                c.Message_ID,
                c.Message_ID,
                now(),
                now(),
                now()
              FROM Message75 AS c
               '
        );

        $conn->executeQuery('UPDATE Message75 SET counter_id = Message_ID');
        //---

        $companies = $conn->fetchAll('
            SELECT c.Message_ID
            FROM Message75 c
            WHERE NOT EXISTS (
                SELECT cd.id
                FROM company_delivery_city cd
                WHERE cd.company_id = c.Message_ID AND cd.city_id = c.company_city
            )
        ');
        $companiesIds = array_map(function ($company) {
            return (int)$company['Message_ID'];
        }, $companies);

        if ($companiesIds) {
            $conn->executeQuery(
                "
            INSERT IGNORE INTO company_delivery_city (company_id, city_id, kind, created_at, updated_at, phone, adress, is_main_office)
            (
              SELECT Message_ID, company_city, :kind, Created, LastUpdated, '', '', 1
              FROM Message75
              WHERE Message_ID IN (:companies_ids)
            )
            ON DUPLICATE KEY UPDATE kind = :kind,
                updated_at = VALUES(updated_at),
                created_at = VALUES(created_at),
                phone = VALUES(phone),
                adress = VALUES(adress),
                is_main_office = 1",
                array('kind' => CompanyCity::KIND_BRANCH_OFFICE, 'companies_ids' => $companiesIds),
                array('companies_ids' => Connection::PARAM_INT_ARRAY)
            );
        }

        //---
        $conn->executeQuery('
            INSERT IGNORE INTO company_payment_details (id, company_id, updated_at)
            (
              SELECT Message_ID, Message_ID, Created
              FROM Message75 AS c
            ) ON DUPLICATE KEY UPDATE
            company_payment_details.updated_at = IF(company_payment_details.updated_at > c.Created, company_payment_details.updated_at, c.Created)'
        );

        $conn->executeUpdate('
            UPDATE Message75 SET payment_details_id = Message_ID'
        );
        //---
        $conn->executeQuery('
            INSERT IGNORE INTO company_description (company_id, description)
              (
              SELECT c.Message_ID, null
              FROM Message75 c
              )
              '
        );

        $conn->executeUpdate('
            UPDATE Message75 SET company_description_id = Message_ID'
        );
        //---
        $conn->executeQuery('
            INSERT IGNORE INTO company_log (company_id, created_by)
              (
              SELECT c.Message_ID, u.User_ID
              FROM Message75 c
              JOIN User u
              ON u.ConnectCompany = c.Message_ID
              )
              '
        );

        $conn->executeUpdate('
            UPDATE Message75 SET company_log_id = Message_ID'
        );
        //---
        $conn->executeQuery('
            INSERT IGNORE INTO company_minisite (company_id, updated_at, background_color, primary_color, secondary_color)
            (
              SELECT Message_ID, Created, :background_color, :primary_color, :secondary_color
              FROM Message75 AS c
            ) ON DUPLICATE KEY UPDATE
              company_minisite.updated_at = IF (company_minisite.updated_at < c.Created, c.Created, company_minisite.updated_at)',
            array('background_color' => MiniSiteConfig::DEFAULT_BACKGROUND_COLOR, 'primary_color' => MiniSiteConfig::DEFAULT_PRIMARY_COLOR, 'secondary_color' => MiniSiteConfig::DEFAULT_SECONDARY_COLOR));

        $generateSlugsCommand = $this->getApplication()->find('metal:companies:generate-slugs');
        $generateSlugsCommand->run($input, $output);

        $compileUrlRewritesCommand = $this->getApplication()->find('metal:project:compile-url-rewrites');
        $compileUrlRewritesCommand->run(
            new ArrayInput(
                array(
                    'command' => 'metal:project:compile-url-rewrites',
                    '--truncate' => true
                )),
            $output
        );

        $createVirtualProductsCommand = $this->getApplication()->find('metal:products:create-virtual-products');
        $createVirtualProductsCommand->run($input, $output);

        $output->writeln(sprintf('%s: Done command "%s"', date('d.m.Y H:i:s'), $this->getName()));
    }
}
