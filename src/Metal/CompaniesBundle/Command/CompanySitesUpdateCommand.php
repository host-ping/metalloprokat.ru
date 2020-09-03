<?php

namespace Metal\CompaniesBundle\Command;

use Doctrine\DBAL\Connection;
use Metal\ProjectBundle\Repository\SiteRepository;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CompanySitesUpdateCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('metal:companies:site-update');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln(sprintf('%s: Start command "%s"', date('d.m.Y H:i:s'), $this->getName()));
        $em = $this->getContainer()->get('doctrine');

        $siteRepository =  $em->getRepository('MetalProjectBundle:Site');
        /* @var $siteRepository SiteRepository */

        $siteRepository->disableLogging();

        $conn = $em->getConnection();
        /* @var $conn Connection */

        $skip = 0;
        $limit = 100;

        do {
            $companies = $conn->createQueryBuilder()->select('c.Message_ID id, c.company_url url')
                ->from('Message75', 'c')
                ->setMaxResults($limit)
                ->setFirstResult($skip)
                ->execute()
                ->fetchAll();
            $skip += $limit;

            foreach ($companies as $company) {
                $sites = json_decode($company['url'], true);
                if (!$sites) {
                    // invalid json
                    $sites = array();
                }
                foreach ($sites as $i => $site) {
                    if (!strstr($site, 'http')) {
                        $sites[$i] = 'http://'.$site;
                    }
                }
                $conn->executeUpdate('
                UPDATE Message75
                SET company_url = :sites
                WHERE Message_ID = :id',
                    array('id' => $company['id'], 'sites' => json_encode(array_values($sites)))
                );
            }
            $output->writeln('Completed:' . $skip);
        } while($companies);
        $siteRepository->restoreLogging();

        $output->writeln(sprintf('%s: Completed', date('d.m.Y H:i:s')));

    }
}
