<?php

namespace Metal\CompaniesBundle\Command;

use Doctrine\DBAL\Connection;
use Metal\ProjectBundle\Repository\SiteRepository;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RandomCompanyRatingCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('metal:companies:random-rating');
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

        $companiesIds = $conn->fetchAll('SELECT Message_ID FROM Message75');

        $idsByRating = array();
        foreach ($companiesIds as $companyId) {
            $idsByRating[mt_rand(0, 3)][] = $companyId['Message_ID'];
        }

        foreach ($idsByRating as $rating => $companies) {
            $conn->executeUpdate('
                UPDATE Message75 SET company_rating = ? WHERE Message_ID IN (?)',
                array($rating, array_values($companies)),
                array(\PDO::PARAM_INT, Connection::PARAM_INT_ARRAY));
        }
    }

}
