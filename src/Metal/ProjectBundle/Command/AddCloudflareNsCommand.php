<?php

namespace Metal\ProjectBundle\Command;

use Doctrine\ORM\EntityManagerInterface;
use Metal\ProjectBundle\Doctrine\Utils;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AddCloudflareNsCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('metal:project:add-cloudflare-ns');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln(sprintf('%s: Start command "%s"', date('d.m.Y H:i:s'), $this->getName()));

        $em = $this->getContainer()->get('doctrine.orm.default_entity_manager');
        /* @var $em EntityManagerInterface */

        $container = $this->getContainer();

        list($wwwSlug) = explode('.', $container->getParameter('host_prefix'), 2);
        list($corpSlug) = explode('.', $container->getParameter('base_host_corp_site'), 2);

        $specialSlugs = array(
            $wwwSlug,
            $container->getParameter('private_office_subdomain'),
            $corpSlug,
        );

        $countryId = $container->getParameter('hostnames_map')[$container->getParameter('base_host')]['country_id'];
        $cloudflareService = $container->get('metal.project.cloudflare_service');
        $logger = function ($record, $success, $errorMsg) use ($output) {
            if ($success) {
                $output->writeln(sprintf('Record "%s" added.', $record));
            } else {
                $output->writeln(sprintf('Record "%s" failed ("%s").', $record, $errorMsg));
            }
        };

        $cloudflareService->insertRecords($specialSlugs, $logger);

        $citiesSlugs = $em->getRepository('MetalTerritorialBundle:City')->getSlugsForCountry($countryId);
        $cloudflareService->insertRecords($citiesSlugs, $logger);

        Utils::checkEmConnection($em);

        $regionsSlugs = $em->getRepository('MetalTerritorialBundle:Region')->getSlugsForCountry($countryId);
        $cloudflareService->insertRecords($regionsSlugs, $logger);

        Utils::checkEmConnection($em);

        $companyRepository = $em->getRepository('MetalCompaniesBundle:Company');
        $companiesSlugs = $companyRepository->getCloudflareSlugsForCountry($countryId);
        $logger = function ($record, $success, $errorMsg) use ($output, $companyRepository) {
            if ($success) {
                $output->writeln(sprintf('Record "%s" added.', $record));
                $companyRepository->updateCompanyIsAddedToCloudflareStatus($record, true);
            } else {
                $output->writeln(sprintf('Record "%s" failed ("%s").', $record, $errorMsg));
            }
        };

        $cloudflareService->insertRecords($companiesSlugs, $logger);

        $output->writeln(sprintf('%s: Finish command "%s" ', date('d.m.Y H:i:s'), $this->getName()));
    }
}
