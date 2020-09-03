<?php

namespace Metal\TerritorialBundle\Command;

use Buzz\Bundle\BuzzBundle\Buzz\Buzz;
use Doctrine\ORM\EntityManagerInterface;
use Metal\ProjectBundle\Util\InsertUtil;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SynchronizeCitiesCommand extends ContainerAwareCommand
{
    const REGIONS_URL = 'https://www.metalloprokat.ru/api/regions';
    const CITIES_URL = 'https://www.metalloprokat.ru/api/cities';

    protected function configure()
    {
        $this->setName('metal:territorial:synchronize-cities');
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
        $conn = $em->getConnection();

        $regionsFromMetalloprokat = json_decode($browser->get(self::CITIES_URL)->getContent(), true);

        InsertUtil::insertMultipleOrUpdate(
            $conn,
            'Classificator_Regions',
            $regionsFromMetalloprokat,
            array_keys($regionsFromMetalloprokat[0]),
            500
        );

        $citiesFromMetalloprokat = json_decode($browser->get(self::CITIES_URL)->getContent(), true);

        InsertUtil::insertMultipleOrUpdate(
            $conn,
            'Classificator_Region',
            $citiesFromMetalloprokat,
            array_keys($citiesFromMetalloprokat[0]),
            500
        );

        $output->writeln(sprintf('End command %s at %s', $this->getName(), date('Y-m-d H:i')));
    }
}
