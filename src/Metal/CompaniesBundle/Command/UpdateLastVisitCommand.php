<?php

namespace Metal\CompaniesBundle\Command;

use Metal\CompaniesBundle\Entity\Company;
use Metal\ProductsBundle\Indexer\Operation\ProductChangeSet;
use Metal\ProductsBundle\Indexer\Operation\ProductsCriteria;
use Metal\ProjectBundle\Doctrine\Utils;
use Metal\ProjectBundle\Util\InsertUtil;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UpdateLastVisitCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('metal:companies:update-last-visit');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln(sprintf('%s: Start command "%s"', date('d.m.Y H:i:s'), $this->getName()));

        $container = $this->getContainer();
        $doctrine = $container->get('doctrine');

        Utils::disableLogger($doctrine->getManager());
        $tracker = $container->get('metal_users.service.online_tracker');
        $indexer = $container->get('metal_products.indexer.products');
        $companiesRepository = $doctrine->getRepository(Company::class);
        $companyUpdateTimeout = $container->getParameter('company_update_online_timeout');

        $output->writeln('Start process companies.');
        foreach ($tracker->getCompaniesOnline() as $companyVisit) {
            $companyId = $companyVisit->getCompanyId();

            $updated = $companiesRepository->updateCompanyOnlineStatus(
                $companyId,
                $companyVisit->getVisitedAt(),
                new \DateTIme($companyUpdateTimeout)
            );

            if (!$updated) {
                $output->writeln(sprintf('No needs update for company_id=%d.', $companyId));

                continue;
            }
            $output->writeln(sprintf('Start update company_id=%d.', $companyId));

            $changeSet = new ProductChangeSet();
            $changeSet->setCompanyLastVisitedAt($companyVisit->getVisitedAt());

            $criteria = new ProductsCriteria();
            $criteria->setCompanyId($companyId);

            $indexer->update($changeSet, $criteria);

            $tracker->invalidateCompanyIdOnline($companyId);
        }

        $output->writeln('Start process users.');

        $redis = $container->get('snc_redis.session_client');
        $conn = $doctrine->getConnection();
        $keyPrefix = 'user_last_visit:';
        $keys = $redis->keys($keyPrefix.'*');
        $visitedAt = new \DateTime();

        $clientIpData = array();
        $userVisitingData = array();
        foreach ($keys as $key) {
            $companyId = substr($key, strlen($keyPrefix));
            $data = json_decode($redis->get($key), true);
            $visitedAt->setTimestamp($data['date']);

            $clientIpData[] = array(
                'user_id' => $companyId,
                'ip' => $data['client_ip'],
                'created_at' => $visitedAt->format('Y-m-d H:i:s'),
            );

            $userVisitingData[] = array(
                'user_id' => $companyId,
                'date' => $visitedAt->format('Y-m-d'),
                'last_visit_at' => $visitedAt->format('Y-m-d H:i:s'),
                'company_id' => !empty($data['company_id']) ? $data['company_id'] : null,
            );
        }

        if ($clientIpData) {
            InsertUtil::insertMultipleOrUpdate($conn, 'client_ip', $clientIpData, array('ip'), 500);
        }

        if ($userVisitingData) {
            InsertUtil::insertMultipleOrUpdate(
                $conn,
                'user_visiting',
                $userVisitingData,
                array('last_visit_at', 'company_id'),
                500
            );
        }

        if ($keys) {
            $redis->del($keys);
        }

        $output->writeln(sprintf('%s: Finished command "%s"', date('d.m.Y H:i:s'), $this->getName()));
    }
}
