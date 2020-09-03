<?php

namespace Metal\ProductsBundle\Command;

use Doctrine\ORM\EntityManager;
use Metal\ProductsBundle\DataFetching\EntityLoader\ProductsEntityLoader;
use Metal\ProjectBundle\Entity\ValueObject\SourceTypeProvider;
use Metal\StatisticBundle\Entity\StatsElement;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\LockHandler;

class TrackProductsDisplayCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('metal:products:track-products-display')
            ->addOption('batch-size', null, InputOption::VALUE_OPTIONAL, '', 200000);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $lock = new LockHandler($this->getName());
        if (!$lock->lock()) {
            $output->writeln(sprintf('The command "%s" is already running in another process.', $this->getName()));

            return 0;
        }

        $output->writeln(sprintf('%s: Start command "%s"', date('d.m.Y H:i:s'), $this->getName()));
        $em = $this->getContainer()->get('doctrine')->getManager();
        /* @var $em EntityManager */

        $client = $this->getContainer()->get('snc_redis.session_client');
        $detector = $this->getContainer()->get('vipx_bot_detect.detector');
        $statisticHelper = $this->getContainer()->get('brouzie.helper_factory')->get('MetalStatisticBundle');
        /* @var $statisticHelper \Metal\StatisticBundle\Helper\DefaultHelper */

        $statsElementRepository = $em->getRepository('MetalStatisticBundle:StatsElement');
        $cityRepository = $em->getRepository('MetalTerritorialBundle:City');
        $userRepository = $em->getRepository('MetalUsersBundle:User');

        $output->writeln(sprintf('%s: Retrieve data from redis.', date('d.m.Y H:i:s')));

//        $stats = $client->smembers(ProductsEntityLoader::PRODUCTS_STATS_SHOW_KEY);
        $stats = $client->srandmember(ProductsEntityLoader::PRODUCTS_STATS_SHOW_KEY, $input->getOption('batch-size'));

        $output->writeln(sprintf('%s: Start process data.', date('d.m.Y H:i:s')));

        foreach ($stats as $statString) {
            $stat = json_decode($statString, true);

            $info = $stat['info'];
            $botMetadata = $detector->detect($info['user_agent'], $info['ip']);

            if ($botMetadata) {
                $output->writeln(sprintf('%s: Bot detected.', date('d.m.Y H:i:s')));

                $client->srem(ProductsEntityLoader::PRODUCTS_STATS_SHOW_KEY, $statString);

                continue;
            }

            $city = null;
            if ($info['city']) {
                $city = $cityRepository->find($info['city']);
            }

            $user = null;
            if ($info['user']) {
                $user = $userRepository->find($info['user']);
            }

            $createdAt = new \DateTime($info['date_created_at']);
            $sourceType = SourceTypeProvider::create($info['source_type']);

            $products = is_array($stat['products']) ? $stat['products'] : array();
            foreach ($products as $productData) {
                $statsElement = new StatsElement();
                $statsElement->setProductId($productData['id']);
                $statsElement->setCompanyId($productData['cid']);
                $statsElement->setCity($city);
                $statsElement->setAction(StatsElement::ACTION_SHOW_PRODUCT);
                $statsElement->setSourceType($sourceType);
                $statsElement->setIp($info['ip']);
                $statsElement->setUserAgent($info['user_agent']);
                $statsElement->setReferer($info['referer']);
                $statsElement->setSessionId($info['session']);
                $statsElement->setCategoryId($info['category']);
                $statsElement->setUser($user);
                $statsElement->setCreatedAt($createdAt);
                $statsElement->setDateCreatedAt($createdAt);

                $statsElementRepository->insertStatsElement($statsElement, $statisticHelper->canCreateFakeStatsElement());
            }

            $client->srem(ProductsEntityLoader::PRODUCTS_STATS_SHOW_KEY, $statString);
            $output->writeln(sprintf('%s: Inserted %d rows.', date('d.m.Y H:i:s'), count($products)));
        }

        $output->writeln(sprintf('%s: Completed', date('d.m.Y H:i:s')));
    }
}
