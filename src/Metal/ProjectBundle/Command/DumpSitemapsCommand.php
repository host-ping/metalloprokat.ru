<?php

namespace Metal\ProjectBundle\Command;

use Metal\TerritorialBundle\Entity\City;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\ProcessBuilder;

class DumpSitemapsCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('metal:project:dump-sitemaps');
        $this->addOption('slug', null, InputOption::VALUE_OPTIONAL|InputOption::VALUE_IS_ARRAY, 'Sitemap for cities only with this slugs should be updated');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $hostnamePackage = $input->getOption('hostname-package');

        if (!$hostnamePackage) {
            throw new \InvalidArgumentException('Option hostname-package is required.');
        }

        $cityRepository = $this->getContainer()->get('doctrine')->getRepository('MetalTerritorialBundle:City');

        $countryId = null;
        if ($hostnamePackage != 'metalspros') {
            $countryId = $this->getContainer()->getParameter('domain_country_id');
        }

        $citiesSlugs = array_map(
            function (City $city) {
                return $city->getSlug();
            },
            $cityRepository->getCitiesWithSlug($countryId, array('slug' => 'ASC'))
        );
        array_unshift($citiesSlugs, 'www');

        if ($slugsToGenerate = $input->getOption('slug')) {
            $citiesSlugs = array_filter($citiesSlugs, function($slug) use ($slugsToGenerate) {
                    return in_array($slug, $slugsToGenerate);
                });
        }

        foreach ($citiesSlugs as $slug) {
            $this->dumpSitemap($slug, $output, $hostnamePackage);
        }
    }

    private function dumpSitemap($slug, OutputInterface $output, $hostnamePackage)
    {
        $appDir = $this->getContainer()->getParameter('kernel.root_dir');
        $dir = realpath($appDir.'/..');
        $dir .= '/web/sitemaps/'.$hostnamePackage.'/'.$slug;

        $baseHostParameters = array(
            'metalspros' => 'base_host_metallspros',
            'metalloprokat' => 'base_host',
            'metalloprokat-ua' => 'base_host',
            'metalloprokat-by' => 'base_host',
            'metalloprokat-kz' => 'base_host',
        );

        $host = $this->getContainer()->getParameter($baseHostParameters[$hostnamePackage]);
        $hostPrefix = $this->getContainer()->getParameter('hostnames_map')[$host]['host_prefix'];

        $pb = new ProcessBuilder(array(
            $this->getContainer()->getParameter('php_interpreter'),
            $appDir.'/console',
            'presta:sitemaps:dump',
            '--hostname-package='.$hostnamePackage,
            '--gzip',
            '--no-debug',
            sprintf('--base-url=%s://%s.%s', $hostPrefix, $slug, $host),
            $dir
        ));
        $pb->setTimeout(1200);
        $process = $pb->getProcess();
        $process->run(function($type, $out) use (&$output) {
                $output->write($out, OutputInterface::OUTPUT_RAW);
            });
    }
}
