<?php

namespace Metal\GrabbersBundle\Command;

use Metal\GrabbersBundle\Entity\Proxy;
use Metal\GrabbersBundle\Grabber\GrabberHelper;
use Metal\ProjectBundle\Util\InsertUtil;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DomCrawler\Crawler;

class ParseProxyCommand extends ContainerAwareCommand
{
    //FIXME: ссылка действительная только 1 сутки
    const PROXY_LIST_URL = 'http://awmproxy.com/freeproxy_0401173908.txt';

    protected function configure()
    {
        $this->setName('metal:grabbers:parse-proxy');
        $this->addOption('validate', null, InputOption::VALUE_NONE, 'Валидировать прокси.');
        $this->addOption('insert', null, InputOption::VALUE_NONE, 'Валидировать прокси.');

    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln(sprintf('%s: Start command "%s"', date('d.m.Y H:i:s'), $this->getName()));

        $em = $this->getContainer()->get('doctrine.orm.entity_manager');

        if ($input->getOption('insert')) {
            $proxyList = file_get_contents(self::PROXY_LIST_URL);

            if (stripos($proxyList, 'Your link is expired already') === false) {
                $proxyList = explode(PHP_EOL, $proxyList);
                $createdAt = (new \DateTime())->format('Y-m-d H:i:s');
                $data = array();
                foreach ($proxyList as $proxy) {
                    $data[] = array(
                        'proxy' => $proxy,
                        'created_at' => $createdAt,
                        'updated_at' => $createdAt,
                    );
                }

                if ($data) {
                    InsertUtil::insertMultipleOrUpdate(
                        $em->getConnection(),
                        'grabber_proxy',
                        $data,
                        array('updated_at'),
                        1000
                    );
                }
            }
        }

        if ($input->getOption('validate')) {
            $availableProxies = $em->getRepository('MetalGrabbersBundle:Proxy')->findBy(array('disabledAt' => null), array('updatedAt' => 'ASC'));
            /* @var $availableProxies Proxy[] */

            foreach ($availableProxies as $availableProxy) {
                $output->writeln(sprintf('%s: Validate proxy "%s"', date('d.m.Y H:i:s'), $availableProxy->getProxy()));

                $curl = curl_init();
                curl_setopt($curl, CURLOPT_URL, 'http://fineproxy.org/');
                curl_setopt($curl, CURLOPT_PROXY, $availableProxy->getProxy());
                curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($curl, CURLOPT_USERAGENT, GrabberHelper::USER_AGENT);
                curl_setopt($curl, CURLOPT_TIMEOUT, 10);
                $out = curl_exec($curl);
                curl_close($curl);

                if ($out === false) {
                    $output->writeln(sprintf('%s: Proxy not work "%s"', date('d.m.Y H:i:s'), $availableProxy->getProxy()));
                    $availableProxy->setDisabledAt(new \DateTime());
                } else {
                    $output->writeln(sprintf('%s: Proxy "%s" is valid', date('d.m.Y H:i:s'), $availableProxy->getProxy()));
                    $availableProxy->setUpdatedAt(new \DateTime());
                }

                $em->flush();
            }
        }

        $output->writeln(sprintf('%s: Finish command "%s"', date('d.m.Y H:i:s'), $this->getName()));
    }
}
