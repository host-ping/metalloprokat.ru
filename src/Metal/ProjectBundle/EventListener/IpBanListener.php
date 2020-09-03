<?php

namespace Metal\ProjectBundle\EventListener;

use Doctrine\DBAL\Connection;
use Metal\ProjectBundle\Entity\BanIP;
use Metal\ProjectBundle\Helper\DefaultHelper;
use Metal\ProjectBundle\Http\TransparentPixelResponse;
use Metal\UsersBundle\Entity\User;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

class IpBanListener
{
    /**
     * @var Connection
     */
    private $conn;

    /**
     * @var ContainerInterface
     */
    private $container;

    private $debug;

    private $recaptchaEnabled;

    private $requestLoggingEnabled;

    public function __construct(Connection $conn, ContainerInterface $container, $debug, $recaptchaEnabled, $requestLoggingEnabled)
    {
        $this->conn = $conn;
        $this->container = $container;
        $this->debug = $debug;
        $this->recaptchaEnabled = $recaptchaEnabled;
        $this->requestLoggingEnabled = $requestLoggingEnabled;
    }

    public function onEarlyKernelRequest(GetResponseEvent $event)
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        $request = $event->getRequest();
        $uri = $request->getUri();
        $requestMethod = $request->getMethod();

        if ($requestMethod === 'HEAD' || $this->isPageNotRequireChecks($uri)) {
            return;
        }

        $ip = $request->getClientIp();
        $ip2long = ip2long($ip);
        $time = time();
        $now = new \DateTime();

        if (0 === strpos($request->getPathInfo(), '/pixxxel.gif')) {
            $this->storeBanRequestPixel($ip2long, $time, $ip, $now, $request->query->get('url') ?: $uri);

            $event->setResponse(new TransparentPixelResponse());
            return;
        }
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        $request = $event->getRequest();
        $uri = $request->getUri();
        $requestMethod = $request->getMethod();

        $ip = $request->getClientIp();
        $referer = $request->headers->get('REFERER');
        $ip2long = ip2long($ip);
        $time = time();
        $now = new \DateTime();
        $hostname = null;

        $bannedIp = $this->conn->fetchAssoc('SELECT * FROM ban_ip WHERE int_ip = :int_ip', array('int_ip' => $ip2long));
        if ($bannedIp) {
            if ($bannedIp['status'] == BanIP::STATUS_BLOCKED_AUTO || $bannedIp['status'] == BanIP::STATUS_BLOCKED_MANUALLY) {
                $event->setResponse(Response::create('Слишком много запросов. Напишите на info@metalloprokat.ru для уточнения деталей.', Response::HTTP_TOO_MANY_REQUESTS));
            }

            return;
        }

        if ($requestMethod === 'HEAD' || $this->isPageNotRequireChecks($uri)) {
            return;
        }

        if ($this->requestLoggingEnabled) {
            $userIid = null;
            $tokenStorage = $this->container->get('security.token_storage');

            if ($tokenStorage->getToken() && $tokenStorage->getToken()->getUser() instanceof User) {
                $userIid = $tokenStorage->getToken()->getUser()->getId();
            }

            $this->storeBanRequest($ip2long, $time, $ip, $now, $uri, $referer, $requestMethod, $userIid);
        }

        $validateRecaptcha = $this->recaptchaEnabled
            && !$request->isXmlHttpRequest()
            && !$request->query->get('utm_source')
            && !DefaultHelper::isRefererFromSearch($referer);
        if ($validateRecaptcha && $request->request->has('g-recaptcha-response')) {
            $request->attributes->set('_controller', 'MetalProjectBundle:Captcha:captcha');

            return;
        }

        if ($validateRecaptcha) {
            $session = $this->container->get('session');
            if ($session->get('recaptcha_successfully_validated')) {
                $hostname = gethostbyaddr($ip);
                $this->storeIp($ip2long, $ip, $now, $hostname, BanIP::STATUS_WHITELISTED_CAPTCHA);
                $session->set('recaptcha_successfully_validated', false);

                return;
            }

            if (!$referer || $referer === $request->getUri()) {
                $hostname = gethostbyaddr($ip);
                if ($this->isSearchBot($hostname)) {
                    $this->storeIp($ip2long, $ip, $now, $hostname, BanIP::STATUS_WHITELISTED_AUTO);

                    return;
                }
                $request->attributes->set('_controller', 'MetalProjectBundle:Captcha:captcha');

                return;
            }
        }

        $limitRequestsPerInterval = $this->debug ? 1000 : 20;
        $requestsPerInterval = $this->conn->fetchColumn(
            'SELECT COUNT(*) FROM ban_request_pixel WHERE int_ip = :int_ip AND int_created_at > :int_created_at',
            array(
                'int_ip' => $ip2long,
                'int_created_at' => strtotime('-1 minute'),
            )
        );

        if ($requestsPerInterval < $limitRequestsPerInterval) {
            return;
        }

        $hostname = $hostname ?: gethostbyaddr($ip);
        $status = BanIP::STATUS_BLOCKED_AUTO;
        if ($this->isSearchBot($hostname)) {
            $status = BanIP::STATUS_WHITELISTED_AUTO;
        }

        $this->storeIp($ip2long, $ip, $now, $hostname, $status);
    }

    private function isPageNotRequireChecks($uri)
    {
        //TODO: move filters to parameters
        $ignorePatterns = array(
            '#(.*?)/robokassa/(result|success|fail)$#',
            '#(.*?)/load-announcements$#',
            '#(.*?)/search-suggest#',
            '#(.*?)/_admin(.*?)#',
            '#^https?://(www\.)?metaltop.ru/#',
        );

        foreach ($ignorePatterns as $ignorePattern) {
            if (preg_match($ignorePattern, $uri)) {

                return true;
            }
        }

        return false;
    }

    private function isSearchBot($hostname)
    {
        $searchBots = array(
            'search.msn.com',
            'yandex.ru',
            'yandex.net',
            'yandex.com',
            'google.com',
            'googlebot.com',
            'yahoo.net',
            'sputnik.ru',
            'mail.ru',
            '1adm.ru',
        );

        $searchBots = array_map('preg_quote', $searchBots);

        return preg_match('#(^|\.)'.implode('|', $searchBots).'$#', $hostname);
    }

    private function storeIp($ip2long, $ip, $now, $hostname, $status)
    {
        return $this->conn->executeUpdate(
            'INSERT IGNORE INTO ban_ip SET int_ip = :int_ip, ip = :ip, created_at = :created_at, hostname = :hostname, status = :status',
            array(
                'int_ip' => $ip2long,
                'ip' => $ip,
                'created_at' => $now,
                'hostname' => $hostname,
                'status' => $status,
            ),
            array('created_at' => 'datetime')
        );
    }

    private function storeBanRequest($ip2long, $time, $ip, $now, $uri, $referer, $method, $user_id = null)
    {
        $this->conn->insert(
            'ban_request',
            array(
                'int_ip' => $ip2long,
                'int_created_at' => $time,
                'ip' => $ip,
                'created_at' => $now,
                'uri' => $uri,
                'referer' => $referer,
                'method' => $method,
                'user_id' => $user_id
            ),
            array('created_at' => 'datetime')
        );
    }

    private function storeBanRequestPixel($ip2long, $time, $ip, $now, $uri)
    {
        $this->conn->insert(
            'ban_request_pixel',
            array(
                'int_ip' => $ip2long,
                'int_created_at' => $time,
                'ip' => $ip,
                'created_at' => $now,
                'uri' => $uri,
            ),
            array('created_at' => 'datetime')
        );
    }
}
