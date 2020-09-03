<?php

namespace Metal\ProjectBundle\Service;

use Buzz\Browser;
use Buzz\Bundle\BuzzBundle\Exception\ResponseException;
use Doctrine\ORM\EntityManager;

class CloudflareService
{
    /**
     * @var EntityManager
     */
    protected $em;

    private $buzz;

    private $apiKey;

    private $authEmail;

    private $zoneId;

    private $baseHost;

    public function __construct(EntityManager $em, Browser $browser, $apiKey, $authEmail, $hostnamesMap, $baseHost)
    {
        $this->em = $em;
        $this->buzz = $browser;
        $this->apiKey = $apiKey;
        $this->authEmail = $authEmail;
        $this->zoneId = $hostnamesMap[$baseHost]['cloudflare_zone_id'];
        $this->baseHost = $baseHost;
        $client = $this->buzz->getClient();
        $client->setMaxRedirects(false);
        $client->setTimeout(10);
        $client->setVerifyPeer(false);
        $client->setVerifyHost(false);
    }

    public function insertRecords(array $records, callable $logger = null)
    {
        $logger = $logger ?: function ($line) {
        };

        $currentRecords = $this->getCurrentRecords();
        foreach ($records as $record) {

            if (!$this->zoneId) {
                $logger($record, true, null);

                continue;
            }

            if (isset($currentRecords[$record])) {
                // зона уже добавлена
                continue;
            }

            $data = array(
                'type' => 'CNAME',
                'name' => $record,
                'content' => $this->baseHost,
                'proxiable' => true,
                'proxied' => true,
            );

            try {
                $this->buzz->post($this->getZoneUrl(), $this->getHeaders(), json_encode($data));

                $logger($record, true, null);
            } catch (ResponseException $e) {
                $logger($record, false, $e->getResponse()->getContent());
            }
        }
    }

    public function removeRecords(array $records, callable $logger = null)
    {
        $logger = $logger ?: function ($line) {
        };
        $currentRecords = $this->getCurrentRecords();

        foreach ($records as $record) {

            if (!$this->zoneId) {
                $logger($record, true, null);

                continue;
            }

            if (!isset($currentRecords[$record])) {
                // зона не была добавлена, нет смысла удалять ее
                continue;
            }

            $deleteUrl = sprintf(
                'https://api.cloudflare.com/client/v4/zones/%s/dns_records/%s',
                $this->zoneId,
                $currentRecords[$record]
            );

            try {
                $this->buzz->delete($deleteUrl, $this->getHeaders());

                $logger($record, true, null);
            } catch (ResponseException $e) {
                $logger($record, false, $e->getResponse()->getContent());
            }
        }
    }

    public function renameRecord($oldName, $newName, callable $logger = null)
    {
        $logger = $logger ?: function ($line) {
        };

        if (!$this->zoneId) {
            return $logger($newName, true, null);
        }

        $currentRecords = $this->getCurrentRecords();
        if (!isset($currentRecords[$oldName])) {
            // зона не была добавлена, нет смысла обновлять ее
            return $logger($oldName, false, 'Record not found');
        }

        $recordId = $currentRecords[$oldName];
        $updateUrl = sprintf('https://api.cloudflare.com/client/v4/zones/%s/dns_records/%s', $this->zoneId, $recordId);
        $data = array(
            'type' => 'CNAME',
            'name' => $newName,
            'content' => $this->baseHost,
            'proxiable' => true,
            'proxied' => true,
        );

        try {
            $this->buzz->put($updateUrl, $this->getHeaders(), json_encode($data));

            $logger($newName, true, null);
        } catch (ResponseException $e) {
            $logger($newName, false, $e->getResponse()->getContent());
        }
    }

    public function getCurrentRecords()
    {
        $currentRecords = array();

        if (!$this->zoneId) {
            return $currentRecords;
        }

        $page = 1;

        do {
            $url = $this->getZoneUrl(array('type' => 'CNAME', 'per_page' => 100, 'page' => $page));
            $rawRecords = json_decode($this->buzz->get($url, $this->getHeaders())->getContent(), true);

            foreach ($rawRecords['result'] as $currentRecord) {
                if ($currentRecord['type'] !== 'CNAME') {
                    continue;
                }

                $slug = substr($currentRecord['name'], 0, strpos($currentRecord['name'], '.'.$currentRecord['content']));
                $currentRecords[$slug] = $currentRecord['id'];
            }

            $page++;
        } while (count($rawRecords['result']));

        return $currentRecords;
    }

    private function getHeaders()
    {
        return array(
            'Content-Type' => 'application/json',
            'X-Auth-Email' => $this->authEmail,
            'X-Auth-Key' => $this->apiKey,
        );
    }

    private function getZoneUrl(array $params = array())
    {
        $url = sprintf('https://api.cloudflare.com/client/v4/zones/%s/dns_records', $this->zoneId);

        if ($params) {
            $url .= '?'.http_build_query($params, '', '&');
        }

        return $url;
    }
}
