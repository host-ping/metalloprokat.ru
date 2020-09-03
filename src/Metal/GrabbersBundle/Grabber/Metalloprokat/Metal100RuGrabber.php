<?php

namespace Metal\GrabbersBundle\Grabber\Metalloprokat;

use Metal\DemandsBundle\Entity\DemandItem;
use Metal\GrabbersBundle\Entity\Site;
use Metal\GrabbersBundle\Grabber\GrabberInterface;
use Metal\GrabbersBundle\Grabber\GrabberHelper;
use Metal\GrabbersBundle\Grabber\GrabberResult;
use Metal\ProductsBundle\Entity\ValueObject\ProductMeasureProvider;
use Monolog\Logger;
use Symfony\Component\DomCrawler\Crawler;

class Metal100RuGrabber implements GrabberInterface
{
    const MAX_DUPLICATES = 40;

    private $limitDuplicates = self::MAX_DUPLICATES;

    public function getCode()
    {
        return 'metal100_ru';
    }

    public function grab(Site $site, GrabberHelper $grabberHelper, $page = 1)
    {
        $headers = $grabberHelper->getAuthorizationHeaders(
            $site,
            '/system/php/service.php',
            array('f' => 'login', 'redirect' => '/', 'login' => $site->getLogin(), 'password' => $site->getPassword())
        );

        if (!$headers) {
            return null;
        }

        $limitPage = 2;
        $grabberResults = array();
        do {
            $crawler = new Crawler($grabberHelper->getContent($site, sprintf('/orders/?page=%d', $page), $headers));
            $filter = $crawler->filter('div.content.clear.blueLinks > div.orderList.clear > div > a:nth-child(2)');

            if ($limitPage <= 0 || $this->limitDuplicates <= 0) {

                return $grabberResults;
            }

            $limitPage--;

            if (!iterator_count($filter)) {
                $grabberHelper->log(Logger::ERROR, 'Список заявок не найден, пытаемся распарсить следующую страницу',
                    array(
                        'site_id' => $site->getId(),
                        'page'    => $page - 1
                    )
                );
                $this->limitDuplicates--;

                continue;
            }

            foreach ($filter as $i => $content) {
                if ($this->limitDuplicates <= 0) {
                    return $grabberResults;
                }

                $contentCrawler = new Crawler($content);
                $demandId = preg_replace('/\D/ui', '', $contentCrawler->text());

                $hash = md5($demandId);

                $uri = $content->getAttribute('href');
                $demandCrawler = new Crawler($grabberHelper->getContent($site, $uri, $headers));
                if (!$demandCrawler->count()) {
                    continue;
                }

                $grabberResult = new GrabberResult();

                $datePublicationFilter = $demandCrawler->filter('div[class="content clear orderPage"] h1');
                if ($datePublicationFilter->count()) {
                    preg_match('/от\s(.{15,20})$/ui', $datePublicationFilter->text(), $matchesDate);
                    $grabberResult->createdAt = $grabberHelper->parseDate($site, $matchesDate[1]);
                    $grabberResult->siteDemandPublicationDate = $matchesDate[1];
                }

                if ($grabberHelper->isAdded(array('hash' => $hash, 'site' => $site->getId()), $grabberResult->createdAt)) {

                    $grabberHelper->log(
                        Logger::NOTICE,
                        GrabberInterface::MESSAGE_DEMAND_IS_ALREADY_EXIST,
                        array(
                            'site_id' => $site->getId(),
                            'hash' => $hash,
                            'page'    => $page - 1
                        )
                    );

                    $this->limitDuplicates--;

                    continue;
                }

                if (!$grabberResult->createdAt || !$grabberHelper->isToday($grabberResult->createdAt)) {
                    $this->limitDuplicates -= 5;

                    continue;
                }

                $personFilter = $demandCrawler->filter('div.content.clear.orderPage > div:nth-child(3) > ul > li:nth-child(1) > strong');
                if ($personFilter->count()) {
                    $grabberResult->person = trim($personFilter->text());
                    $grabberHelper->log(Logger::INFO, 'Найдена информация', array('person' => $grabberResult->person, 'site_id' => $site->getId()));
                }

                $cityTitleFilter = $demandCrawler->filter('div.content.clear.orderPage > div:nth-child(3) > ul > li:nth-child(3) > strong');
                if ($cityTitleFilter->count()) {
                    $grabberResult->cityTitle = trim($cityTitleFilter->text());
                    $grabberHelper->log(Logger::INFO, 'Найдена информация', array('cityTitle' => $grabberResult->cityTitle, 'site_id' => $site->getId()));
                }

                //TODO Для авторизированных поставщиков
                $phoneFilter = $demandCrawler->filter('div.content.clear.orderPage > div:nth-child(3) > ul > li:nth-child(5) > strong');
                if ($phoneFilter->count()) {
                    $grabberResult->phone = trim($phoneFilter->text());
                    $grabberHelper->log(Logger::INFO, 'Найдена информация', array('phone' => $grabberResult->phone, 'site_id' => $site->getId()));
                }

                $demandItems = $demandCrawler->filter('#contentBlock > div.content.clear.orderPage > div:nth-child(2) > table');
                $demandItemsCrawler = new Crawler();
                $demandItemsCrawler->addHtmlContent($demandItems->html());
                $rows = $demandItemsCrawler->filter('tbody tr');

                foreach ($rows as $row) {
                    $demandItem = new DemandItem();
                    $dom = new Crawler($row);

                    $demandItemFilter = $dom->filter('td:nth-child(2)');
                    if ($demandItemFilter->count()) {
                        $demandItem->setTitle(trim(preg_replace('/\s{2,}/ui', ' ', $demandItemFilter->text())));
                        $grabberHelper->log(Logger::INFO, 'Найдена позиция', array('demandItemTitle' => $demandItem->getTitle(), 'site_id' => $site->getId()));
                    }

                    preg_match('/(\d+)\s(.+)/ui', trim($dom->filter('td:nth-child(3)')->text()), $matchesVolume);
                    if (isset($matchesVolume[2]) && $measure = ProductMeasureProvider::createByPattern($matchesVolume[2])) {

                        $demandItem->setVolume($matchesVolume[1]);
                        $demandItem->setVolumeType($measure);

                        $grabberHelper->log(Logger::INFO, 'Найдена информация', array('measure_type' => $measure->getTitle(), 'measure_type_volume' => $matchesVolume[1], 'site_id' => $site->getId()));
                    }

                    $grabberResult->demandItems[] = $demandItem;
                }

                $grabberResult->siteDemandUrl = $uri;
                $grabberResult->siteDemandId = $demandId;
                $grabberResult->siteDemandHash = $hash;

                $grabberResults[] = $grabberResult;
            }
        } while ($crawler->count() || $this->limitDuplicates > 0);

        return $grabberResults;
    }
}
