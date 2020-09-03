<?php

namespace Metal\GrabbersBundle\Grabber;

use Buzz\Browser;
use Buzz\Bundle\BuzzBundle\Buzz\Buzz;
use Buzz\Listener\CookieListener;
use Buzz\Message\RequestInterface;
use Doctrine\ORM\EntityManagerInterface;
use Metal\GrabbersBundle\Entity\ParsedDemand;
use Metal\GrabbersBundle\Entity\Proxy;
use Metal\GrabbersBundle\Entity\Site;
use Psr\Log\LoggerInterface;
use Sonata\NotificationBundle\Backend\BackendInterface;
use Symfony\Bridge\Monolog\Logger;

class GrabberHelper
{
    const USER_AGENT = 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2272.76 Safari/537.36';
    const MAX_RETRY = 5;
    const MAX_TIMEOUT = 12;
    const MAX_PROXY_RETRY = 2;

    /**
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var Browser
     */
    protected $browser;

    /**
     * @var BackendInterface
     */
    protected $notificationBackend;

    private $proxyList;

    /**
     * @var bool
     */
    private $errorProxy = false;

    private $lastProxy = '';

    /**
     * @var int
     */
    private $retryProxy = GrabberHelper::MAX_PROXY_RETRY;

    private $countRetryGetContent = GrabberHelper::MAX_RETRY;
    private $countRetryAuth = GrabberHelper::MAX_RETRY;

    public function __construct(EntityManagerInterface $em, Buzz $buzz, LoggerInterface $logger, BackendInterface $notificationBackend)
    {
        $this->em = $em;
        $this->logger = $logger;

        $this->browser = $buzz->getBrowser('grabber');
        $this->browser->getClient()->setTimeout(GrabberHelper::MAX_TIMEOUT);

        $this->notificationBackend = $notificationBackend;
    }

    /**
     * @param string $url
     * @param array $headers
     * @param array $headers
     * @param Site $site
     *
     * @return string
     */
    public function getContent(Site $site, $url, array $headers = array(), array $postFields = array())
    {
        if (true === $this->errorProxy && $site->getUseProxy()) {
            return null;
            /*return array(
                'status' => 'error',
                'message' => 'Сайт использует прокси, рабочие прокси сервера закончились.'
            );*/
        }

        if (!$this->browser->getClient()->getProxy()) {
            $this->getAvailableProxy($site, $this->prepareHeaders($headers));

            if ($this->errorProxy) {
                if ($this->lastProxy instanceof Proxy) {
                    //$this->lastProxy->setDisabledAt(new \DateTime());
                    //$this->em->flush();
                }

                return null;
            }
        }

        try {
            if (strpos($url, 'http://') === false && strpos($url, 'https://') === false) {
                $url = $site->getHost().$url;
            }
            //TODO: это небезопасный код, по всей видимости эту логику нужно в конкретных грабберах разруливать
            $url = preg_replace('/\?page=1$/ui', '', $url);

            $this->log(Logger::INFO, 'Получение страницы: '. $url,
                array(
                    'url' => $url,
                    'site_id' => $site->getId(),
                    'proxy' => $this->browser->getClient()->getProxy()
                )
            );

            if (0 === count($postFields)) {
                return $this->browser->get($url, $this->prepareHeaders($headers))->getContent();
            }

            return $this->browser->call($url, 'POST', $headers, http_build_query($postFields))->getContent();

        } catch (\Exception $e) {
            $this->log(
                Logger::ERROR,
                'Не удалось получить страницу, пытаемся получить снова',
                array(
                    'site_id' => $site->getId(),
                    'exception_message' => $e->getMessage(),
                    'exception_code' => $e->getCode(),
                    'url' => $url,
                    'retry_count' => $this->countRetryGetContent,
                    'proxy' => $this->browser->getClient()->getProxy()
                )
            );

            if ($this->countRetryGetContent) {
                if (!$site->getUseProxy()) {
                    $this->countRetryGetContent--;
                }

                $this->getAvailableProxy($site, $this->prepareHeaders($headers));
                $this->getContent($site, $url, $headers, $postFields);
            } else {
                $this->log(
                    Logger::ERROR,
                    'Исчерпан лимит повторов получения контента',
                    array(
                        'site_id' => $site->getId(),
                        'exception_message' => $e->getMessage(),
                        'exception_code' => $e->getCode(),
                        'url' => $url,
                        'retry_count' => $this->countRetryGetContent,
                        'proxy' => $this->browser->getClient()->getProxy()
                    )
                );
                $this->countRetryGetContent = GrabberHelper::MAX_RETRY;
            }
        }

        return null;
    }

    public function getAuthorizationHeaders(Site $site, $url, array $fields, array $headers = array(), $noRedirects = false)
    {
        $client = $this->browser->getClient();
        $maxRedirects = $client->getMaxRedirects();

        if (!$this->browser->getClient()->getProxy()) {
            $this->getAvailableProxy($site, $this->prepareHeaders($headers));
            if ($this->errorProxy) {
                if ($this->lastProxy instanceof Proxy) {
                    //$this->lastProxy->setDisabledAt(new \DateTime());
                    //$this->em->flush();
                }

                return null;
            }
        }

        if ($noRedirects) {
            $client->setMaxRedirects(false);
        }

        try {
            $response = $this->browser->submit($site->getHost().$url, $fields, RequestInterface::METHOD_POST, $this->prepareHeaders($headers));
            $responseHeaders = $response->getHeader('Set-Cookie');
        } catch (\Exception $e) {
            $client->setMaxRedirects($maxRedirects);
            if (!$this->browser->getLastResponse()) {
                $this->log(
                    Logger::NOTICE,
                    'Не удалось авторизироваться.',
                    array(
                        'site_id' => $site->getId(),
                        'exception_code' => $e->getCode(),
                        'exception_message' => $e->getMessage(),
                        'proxy' => $client->getProxy()
                    )
                );

                if ($this->countRetryAuth) {
                    $this->countRetryAuth--;
                    $this->getAvailableProxy($site, $this->prepareHeaders($headers));
                    $this->getAuthorizationHeaders($site, $url, $fields, $headers, $noRedirects);
                } else {
                    $this->log(
                        Logger::ERROR,
                        'Исчерпан лимит повторов авторизации.',
                        array(
                            'site_id' => $site->getId(),
                            'exception_code' => $e->getCode(),
                            'exception_message' => $e->getMessage(),
                            'url' => $url,
                            'proxy' => $client->getProxy()
                        )
                    );
                    $this->countRetryAuth = GrabberHelper::MAX_RETRY;
                }

                return null;
            }

            $responseHeaders = $this->browser->getLastResponse()->getHeader('Set-Cookie');
        }

        $client->setMaxRedirects($maxRedirects);

        return array('Cookie' => $responseHeaders);
    }

    private function prepareHeaders(array $headers = array())
    {
        if (!isset($headers['User-Agent'])) {
            $headers['User-Agent'] = self::USER_AGENT;
        }

        return $headers;
    }

    public function getHeaders($url, array $headers = array())
    {
        $client = $this->browser->getClient();
        $maxRedirects = $client->getMaxRedirects();
        $client->setMaxRedirects(false);
        $responseHeaders = array();

        try {
            $this->browser->get($url, $this->prepareHeaders($headers));
            $responseHeaders = $this->browser->getLastResponse()->getHeader('Set-Cookie');
        } catch (\Exception $e) {

            if ($this->browser->getLastResponse()) {
                $responseHeaders = $this->browser->getLastResponse()->getHeader('Set-Cookie');
            }

        }

        $client->setMaxRedirects($maxRedirects);

        return array('Cookie' => $responseHeaders);
    }

    public function getAllHeaders($url, array $headers = array())
    {
        $client = $this->browser->getClient();
        $maxRedirects = $client->getMaxRedirects();
        $client->setMaxRedirects(12);
        $responseHeaders = array();

        try {
            $this->browser->get($url, $this->prepareHeaders($headers));
            $responseHeaders = $this->browser->getLastResponse()->getHeaders();
        } catch (\Exception $e) {

            if ($this->browser->getLastResponse()) {
                $responseHeaders = $this->browser->getLastResponse()->getHeaders();
            }

        }

        $client->setMaxRedirects($maxRedirects);

        return $responseHeaders;
    }

    private function getAvailableProxy(Site $site, array $headers = array())
    {
        $client = $this->browser->getClient();

        if (!$site->getUseProxy()) {
            $client->setProxy('');

            return null;
        }

        if (null === $this->proxyList) {
            $this->proxyList = $this->getProxyList();
        }

        if (($this->retryProxy !== 0 && $this->retryProxy > 0) && $client->getProxy()) {
            $this->log(
                Logger::NOTICE,
                'Используем прокси',
                array(
                    'site_id' => $site->getId(),
                    'last_proxy' => $client->getProxy(),
                    'retry' => $this->retryProxy
                )
            );

        } elseif ($this->proxyList && (!$this->lastProxy || ($this->retryProxy === 0 && $client->getProxy()))) {

            $this->lastProxy = array_shift($this->proxyList);
            $this->retryProxy = self::MAX_PROXY_RETRY;

        } elseif (!$this->proxyList && $client->getProxy()) {
            $this->log(
                Logger::ERROR,
                'Закончились прокси',
                array(
                    'site_id' => $site->getId(),
                    'last_proxy' => $client->getProxy()
                )
            );

            $this->errorProxy = true;
            $this->countRetryGetContent = 0;

            return null;

           /* return array(
                'status' => 'error',
                'message' => 'Закончились прокси'
            );*/
        }

        if ($this->lastProxy instanceof Proxy) {
            $client->setProxy($this->lastProxy->getProxy());
        }

        $this->log(
            Logger::INFO,
            'Используем прокси',
            array(
                'site_id' => $site->getId(),
                'proxy'   => $client->getProxy()
            )
        );

        $this->log(
            Logger::INFO,
            'Количество доступных прокси серверов',
            array(
                'site_id' => $site->getId(),
                'proxy_list_length' => count($this->proxyList)
            )
        );

        $this->log(
            Logger::INFO,
            'Проверяем прокси',
            array(
                'site_id' => $site->getId(),
                'retry_count' => $this->retryProxy,
                'proxy'   => $client->getProxy()
            )
        );

        $client->setTimeout(self::MAX_TIMEOUT);

        $this->browser->setClient($client);

        $headers = $this->prepareHeaders($headers);
        try {
            $this->browser->get($site->getHost().$site->getTestProxyUri(), $this->prepareHeaders($headers));

            $this->log(
                Logger::NOTICE,
                'Найден доступный прокси',
                array(
                    'site_id' => $site->getId(),
                    'retry_count' => $this->retryProxy,
                    'proxy'   => $client->getProxy()

                )
            );

            return;

        } catch (\Exception $e) {

            $this->log(
                Logger::NOTICE,
                'Прокси не рабочий',
                array(
                    'site_id' => $site->getId(),
                    'retry_count' => $this->retryProxy,
                    'exception_code' => $e->getCode(),
                    'exception_message' => $e->getMessage(),
                    'proxy'   => $client->getProxy()
                )
            );

            $this->retryProxy--;

            $this->getAvailableProxy($site, $this->prepareHeaders($headers));
        }
    }

    private function getProxyList()
    {
        return $this->em->getRepository('MetalGrabbersBundle:Proxy')->findBy(array('disabledAt' => null), array(), 20);
    }

    public function getLastDemand(Site $site, array $order = array('parsedDemandId' => 'DESC'))
    {
        return $this->em->getRepository('MetalGrabbersBundle:ParsedDemand')->findOneBy(array('site' => $site), $order);
    }

    public function isAdded(array $criteria, \DateTime $createdAt = null)
    {
        $parsedDemand = $this
            ->em
            ->getRepository('MetalGrabbersBundle:ParsedDemand')
            ->findOneBy($criteria)
        ;

        if ($parsedDemand instanceof ParsedDemand && $createdAt instanceof \DateTime) {
            $demand = $parsedDemand->getDemand();
            if ($demand->isModerated() && $demand->getModeratedAt() < $createdAt && $demand->getCreatedAt() < $createdAt) {
                if ($demand->getCreatedAt()->diff($createdAt)->days > 0) {
                    $createdAt->modify('midnight');
                    $demand->appendBody(
                        sprintf('Дата создания заявки была обновлена c "%s" на "%s", из за обновления заявки на третьем сайте.',
                            $demand->getCreatedAt()->format('Y-m-d H:i:s'),
                            $createdAt->format('Y-m-d H:i:s')
                        )
                    );

                    $demand->setCreatedAt($createdAt);
                    $this->notificationBackend->createAndPublish('admin_demand', array('reindex' => array($demand->getId())));
                }
            }
        }

        return $parsedDemand instanceof ParsedDemand;
    }

    public function log($level, $message, array $context = array())
    {
        $this->logger->log($level, $message, $context);
    }

    public function parseDate(Site $site, $string)
    {
        try {

            $matches = null;
            //09-08-2016 15:52
            if (preg_match('/(\d{2}-\d{2}-\d{4} \d{2}:\d{2})/ui', $string, $matches)) {
                return new \DateTime($matches[1]);
            }


            $months = array(
                'января' => 'january',
                'февраля' => 'february',
                'марта' => 'march',
                'апреля' => 'april',
                'мая' => 'may',
                'июня' => 'june',
                'июля' => 'july',
                'августа' => 'august',
                'сентября' => 'september',
                'октября' => 'october',
                'ноября' => 'november',
                'декабря' => 'december'
            );

            $matches = null;
            //8 april 08:59, Сегодня, 11 апреля, 11:52 11 апреля 11:52
            if (preg_match('/(\d+)\s(.+),?\s(\d{2}:\d{2})/ui', $string, $matches)) {
                $month = trim(preg_replace('/\W+/ui', '', trim($matches[2])));
                if (isset($months[$month])) {
                    return \DateTime::createFromFormat('Y F d H:i', sprintf('%d %s %d %s', date('Y'), $months[$month], $matches[1], $matches[3]));
                }
            }

            $removedStrings = array(
                'Размещено' => '',
                'Обновлено' => '',
                ',' => '',
                ' в ' => ' '
            );

            foreach ($removedStrings as $key => $toString) {
                $string = preg_replace(sprintf('/%s/ui', $key), $toString, $string);
            }

            $string = trim($string);

            $dates = array(
                'сегодня' => 'now',
                'вчера'   => 'yesterday'
            );

            foreach ($dates as $key => $date) {
                preg_match(sprintf('/(%s)/ui', $key), $string, $matches);

                if ($matches) {
                    return new \DateTime($date);
                }
            }

            foreach ($months as $key => $month) {
                $matches = null;
                preg_match(sprintf('/(%s)/ui', $key), $string, $matches);

                if ($matches) {
                    $dateString = str_replace($key, $month, $string);

                    return new \DateTime($dateString);
                }
            }

            $matches = null;
            preg_match('/(\d{2}[\-\.]\d{2}[\-\.]\d{4})/ui', $string, $matches);
            if ($matches) {
                return new \DateTime($matches[1]);
            }

            $monthsShort = array(
                'янв' => 'january',
                'фев' => 'february',
                'мар' => 'march',
                'апр' => 'april',
                'мая' => 'may',
                'июн' => 'june',
                'июл' => 'july',
                'авг' => 'august',
                'сен' => 'september',
                'окт' => 'october',
                'ноя' => 'november',
                'дек' => 'december'
            );

            foreach ($monthsShort as $key => $monthShort) {
                $matches = null;
                if (preg_match(sprintf('/(%s)/ui', $key), $string, $matches)) {
                    return new \DateTime(trim(str_replace($key, $monthShort, $string)));
                }
            }

        } catch (\Exception $e) {
            $this->log(
                Logger::ERROR,
                'Не определилась дата',
                array(
                    'exception_message' => $e->getMessage(),
                    'exception_code' => $e->getCode(),
                    'site_id' => $site->getId(),
                    'date_string' => $string
                )
            );
        }

    }

    public function isToday(\DateTime $date)
    {
        $now = new \DateTime();

        return $date->format('d.m.Y') === $now->format('d.m.Y');
    }
}
