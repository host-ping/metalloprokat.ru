<?php

namespace Metal\GrabbersBundle\Grabber\Metalloprokat;

use Metal\DemandsBundle\Entity\DemandItem;
use Metal\GrabbersBundle\Entity\Site;
use Metal\GrabbersBundle\Grabber\GrabberInterface;
use Metal\GrabbersBundle\Grabber\GrabberHelper;
use Metal\GrabbersBundle\Grabber\GrabberResult;
use Monolog\Logger;
use Symfony\Component\DomCrawler\Crawler;

class StroyscanGrabber implements GrabberInterface
{
    const MAX_DUPLICATE_COUNT = 15;

    /**
     * @var GrabberHelper
     */
    private $grabberHelper;

    /**
     * @var Site
     */
    private $site;

    public function getCode()
    {
        return 'stroyscan';
    }

    public function grab(Site $site, GrabberHelper $grabberHelper, $page = 1)
    {
        $this->grabberHelper = $grabberHelper;
        $this->site = $site;

        $limit = 50;
        $duplicateCount = 0;

        do {

            $uri = sprintf('/obyavleniya/?type=3&section=&city=&PAGEN_2=%d', $page);

            $crawler = new Crawler();
            $crawler->addHtmlContent(mb_convert_encoding($grabberHelper->getContent($site, $uri), 'UTF-8', 'windows-1251'));

            $page++;

            $demandsLinks = $this->getDemandLinks($crawler);

            if (!count($demandsLinks)) {
                $grabberHelper->log(Logger::ERROR, 'Список заявок не найден', array('site_id' => $site->getId()));

                return;
            }

            foreach ($demandsLinks as $demandLink => $demandItemTitle) {

                if (preg_match('/Экспорт/ui', $demandItemTitle)) {
                    continue;
                }

                if ($duplicateCount >= self::MAX_DUPLICATE_COUNT) {
                    $grabberHelper->log(Logger::ALERT, 'Лимит дубликатов исчерпан.', array('site_id' => $site->getId()));
                    return;
                }

                preg_match('/obyavleniya\/(\d+)\/$/ui', $demandLink, $matches);

                $demandId = $matches[1];
                $hash = md5($demandId);
                if ($grabberHelper->isAdded(array('hash' => $hash, 'site' => $site->getId()))) {
                    $grabberHelper->log(Logger::INFO, GrabberInterface::MESSAGE_DEMAND_IS_ALREADY_EXIST, array('site_id' => $site->getId(), 'hash' => $hash));
                    $duplicateCount++;
                    continue;
                }

                $demandCrawler = new Crawler();
                $demandCrawler->addHtmlContent(mb_convert_encoding($grabberHelper->getContent($site, $demandLink), 'UTF-8', 'windows-1251'));

                if (!$demandCrawler->count()) {
                    $grabberHelper->log(Logger::ALERT, 'Нет данных в заявке, пытаемся распарсить следующую', array('site_id' => $site->getId()));

                    continue;
                }

                $grabberResult = new GrabberResult();

                $this->setProperty('siteDemandPublicationDate', $grabberResult, $demandCrawler->filter('.date'));
                $this->setProperty('cityTitle', $grabberResult, $demandCrawler->filter('div.data_ad span.town'));
                $this->setProperty('companyTitle', $grabberResult, $demandCrawler->filter('div.data_ad span.firm')->last());
                $this->setProperty('person', $grabberResult, $demandCrawler->filter('div.data_ad span.firm')->first());
                $this->setProperty('phone', $grabberResult, $demandCrawler->filter('div.data_ad span.phone'));
                $this->setProperty('info', $grabberResult, $demandCrawler->filter('.inform_ad'));

                if ($grabberResult->siteDemandPublicationDate) {
                    $grabberResult->createdAt = $grabberHelper->parseDate($site, $grabberResult->siteDemandPublicationDate);
                }

                $demandItem = new DemandItem();
                $demandItem->setTitle($demandItemTitle);
                $grabberResult->demandItems[] = $demandItem;

                $grabberResult->siteDemandUrl = $demandLink;
                $grabberResult->siteDemandId = $demandId;
                $grabberResult->siteDemandHash = $hash;

                yield $grabberResult;

                $limit--;

                if ($limit <= 0) {
                    $grabberHelper->log(Logger::NOTICE, 'Лимит исчерпан', array('limit' => $limit, 'site_id' => $site->getId()));
                    $grabberHelper->log(Logger::INFO, 'Отправка данных', array('site_id' => $site->getId()));

                    return;
                }
            }

        } while ($crawler->count() && $limit > 0);

        $grabberHelper->log(Logger::INFO, 'Отправка данных', array('site_id' => $site->getId()));
    }

    private function setProperty($property, GrabberResult $grabberResult, Crawler $crawler)
    {
        if (!property_exists(GrabberResult::class, $property)) {
            throw new \RuntimeException('Property not exist');
        }

        if ($crawler->count()) {
            $grabberResult->{$property} = $crawler->text();

            $this->grabberHelper->log(
                Logger::INFO, sprintf('Found proprty "%s" value: "%s"', $property, $grabberResult->{$property}),
                array('site_id' => $this->site->getId())
            );
        }
    }

    private function getDemandLinks($crawler)
    {
        $links = array();
        $crawler
            ->filter('a.inform')
            ->each(function (Crawler $el) use (&$links) {
                $links[$el->attr('href')] = $el->text();
            });

        return $links;
    }
}
