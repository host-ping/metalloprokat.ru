<?php

namespace Metal\CompaniesBundle\Command;

use Doctrine\DBAL\Connection;
use Metal\CompaniesBundle\Helper\DefaultHelper;

use Metal\ProjectBundle\Repository\SiteRepository;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateSlugsCommand extends  ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('metal:companies:generate-slugs');
        $this->addOption('truncate', null, InputOption::VALUE_NONE);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln(sprintf('%s: Start command "%s"', date('d.m.Y H:i:s'), $this->getName()));

        $companiesHelper = $this->getContainer()->get('brouzie.helper_factory')->get('MetalCompaniesBundle');
        /* @var $companiesHelper DefaultHelper */

        $em = $this->getContainer()->get('doctrine');
        $siteRepository =  $em->getRepository('MetalProjectBundle:Site');
        /* @var $siteRepository SiteRepository */
        $companyRepository =  $em->getRepository('MetalCompaniesBundle:Company');
        
        $siteRepository->disableLogging();
        $i = 0;
        $conn = $em->getConnection();
        /* @var $conn Connection  */
        $conn->getConfiguration()->setSQLLogger(null);

        if ($input->getOption('truncate')) {
            $conn->executeQuery('UPDATE Message75 SET slug = null');
        }

        $lastId = $conn->fetchColumn('SELECT MAX(Message_ID) FROM Message75');
        $companies = $conn->fetchAll('
          SELECT c.Message_ID, cr.Keyword as city_slug, c.title as title
          FROM Message75 c
              LEFT JOIN Classificator_Country cc ON c.country_id = cc.Country_ID
              LEFT JOIN Classificator_Region cr on c.company_city = cr.Region_ID
          WHERE  (c.slug IS NULL OR
            c.slug = c.Message_ID OR
            c.slug = "" OR
            c.slug LIKE "-%")
            AND c.deleted_at_ts = 0
          ORDER BY c.Message_ID'
        );

        foreach ($companies as $company) {
            $i++;
            $companyId = $company['Message_ID'];

            $slug = $companiesHelper->generateCompanySlug($companyId, $company['title'], $company['city_slug']);

            $conn->executeUpdate('UPDATE Message75 SET slug = :slug WHERE Message_ID = :id',
                array(
                    'slug' => $slug,
                    'id' => $companyId
                )
            );

            $output->writeln(sprintf('Generate Slug: %s for company %s', $slug, $companyId));

            if ($i % 50 == 0) {
                $output->writeln($companyId . '/' . $lastId);
            }
        }

        $companiesCountBadSlug = $conn->fetchColumn('
          SELECT COUNT(c.Message_ID) AS count
          FROM Message75 c
          WHERE (c.slug IS NULL OR c.slug = Message_ID OR c.slug = "" OR c.slug LIKE "-%") AND c.deleted_at_ts = 0
          '
        );

        $output->writeln(sprintf('The number of companies with poor slugs: %d', $companiesCountBadSlug));

        $companyRepository->updateCompanyDomain();

        $siteRepository->restoreLogging();
        $output->writeln(sprintf('End command %s at %s', $this->getName(), date('Y-m-d H:i')));
        $output->writeln('Run <info>metal:project:compile-url-rewrites --truncate</info> to apply all changes.');
    }
}
