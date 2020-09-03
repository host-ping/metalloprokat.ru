<?php

namespace Metal\CompaniesBundle\Command;

use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManager;
use Metal\CompaniesBundle\Entity\PackageChecker;
use Metal\MiniSiteBundle\Entity\MiniSiteConfig;
use Metal\ProjectBundle\Doctrine\Utils;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class SynchronizeCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('metal:companies:synchronize');
        $this->addOption('reset-colors', null, InputOption::VALUE_NONE);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln(sprintf('%s: Start command "%s"', date('d.m.Y H:i:s'), $this->getName()));
        $em = $this->getContainer()->get('doctrine.orm.default_entity_manager');
        /* @var $em EntityManager */
        $connection = $em->getConnection();
        $now = new \DateTime();

        Utils::disableLogger($em);

        $em->getRepository('MetalCompaniesBundle:PaymentDetails')->synchronizePaymentDetails();

        $connection->executeUpdate(
            '
            UPDATE company_payment_details cpd
            JOIN Message75 c ON cpd.company_id = c.Message_ID
            SET attachment_approved_at = :now
            WHERE c.code_access IN (:code_access)',
            array('now' => $now, 'code_access' => PackageChecker::getPackagesByOption('auto_approving_documents')),
            array('code_access' => Connection::PARAM_INT_ARRAY, 'now' => 'datetime')
        );

        $connection->executeQuery(
            '
            INSERT INTO company_minisite (company_id, updated_at, background_color, primary_color, secondary_color)
            (
              SELECT Message_ID, Created, :background_color, :primary_color, :secondary_color
              FROM Message75 AS c
            ) ON DUPLICATE KEY UPDATE
              company_minisite.updated_at = IF (company_minisite.updated_at < c.Created, c.Created, company_minisite.updated_at)',
            array(
                'background_color' => MiniSiteConfig::DEFAULT_BACKGROUND_COLOR,
                'primary_color' => MiniSiteConfig::DEFAULT_PRIMARY_COLOR,
                'secondary_color' => MiniSiteConfig::DEFAULT_SECONDARY_COLOR,
            )
        );

        if ($input->getOption('reset-colors')) {
            $connection->executeUpdate(
                '
                UPDATE company_minisite
                SET updated_at = :now, background_color = :background_color, primary_color = :primary_color, secondary_color = :secondary_color',
                array(
                    'now' => $now,
                    'background_color' => MiniSiteConfig::DEFAULT_BACKGROUND_COLOR,
                    'primary_color' => MiniSiteConfig::DEFAULT_PRIMARY_COLOR,
                    'secondary_color' => MiniSiteConfig::DEFAULT_SECONDARY_COLOR,
                ),
                array('now' => 'datetime')
            );
        }

        Utils::restoreLogging($em);
        $output->writeln(sprintf('%s: Done command "%s"', date('d.m.Y H:i:s'), $this->getName()));
    }
}
