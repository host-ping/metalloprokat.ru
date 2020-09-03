<?php

namespace Metal\TerritorialBundle\Command;

use Buzz\Bundle\BuzzBundle\Buzz\Buzz;
use Doctrine\ORM\EntityManagerInterface;
use Metal\TerritorialBundle\Entity\CityCode;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DomCrawler\Crawler;

class UpdateCityCodeCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('metal:territorial:update-city-code');
        $this->addOption('truncate', null, InputOption::VALUE_NONE);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        //TODO: добавить вывод городов, которые не нашлись в базе у нас. Сохранять порциями
        $output->writeln(sprintf('Start command %s at %s', $this->getName(), date('Y-m-d H:i')));

        $buzz = $this->getContainer()->get('buzz');
        /* @var $buzz Buzz */
        $browser = $buzz->getBrowser('downloader');
        $browser->getClient()->setMaxRedirects(false);
        $browser->getClient()->setTimeout(10);

        $em = $this->getContainer()->get('doctrine')->getManager();
        /* @var $em EntityManagerInterface */

        if ($input->getOption('truncate')) {
            $output->writeln(sprintf('Truncate city_code'));
            $em->getConnection()->executeQuery('TRUNCATE city_code');
        }

        $content = $browser->get('http://www.telephonecode.narod.ru/')->getContent();

        $crawler = new Crawler();
        $crawler->addHtmlContent($content);

        $filters = $crawler->filter('table#Navigation1')->eq(1)->filter('tr');
        if (!iterator_count($filters)) {
            return;
        }
        $cityService = $this->getContainer()->get('metal.territorial.city_service');

        $batchSize = 50;
        foreach ($filters as $key => $filter) {
            if ($key == 0) {
                continue;
            }
            $items = new Crawler($filter);
            $cityTitle = $items->filter('td')->eq(0)->text();
            $regionTitle = $items->filter('td')->eq(1)->text();
            $cityCodesStr = $items->filter('td')->eq(2)->text();

            $city = $cityService->findTerritory($cityTitle);

            foreach (explode(',', $cityCodesStr) as $cityCodeStr) {
                $cityCode = new CityCode();
                $cityCode->setCity($city);
                $cityCode->setCode($cityCodeStr);
                $cityCode->setDefaultCityTitle($cityTitle.', '.$regionTitle);
                $em->persist($cityCode);
            }

            if (($key % $batchSize) == 0) {
                $em->flush();
                $output->writeln(sprintf('Insert %s rows', $key));
            }
        }

        $output->writeln(sprintf('End command %s at %s', $this->getName(), date('Y-m-d H:i')));
    }
}
