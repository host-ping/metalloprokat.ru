<?php

namespace Metal\GrabbersBundle\Grabber\Metalloprokat;

use Metal\DemandsBundle\Entity\DemandItem;
use Metal\GrabbersBundle\Entity\Site;
use Metal\GrabbersBundle\Grabber\GrabberInterface;
use Metal\GrabbersBundle\Grabber\GrabberHelper;
use Metal\GrabbersBundle\Grabber\GrabberResult;
use Monolog\Logger;
use Symfony\Component\DomCrawler\Crawler;

class TradeInoxRuGrabber implements GrabberInterface
{
    public function getCode()
    {
        return 'trade_inox';
    }

    public function grab(Site $site, GrabberHelper $grabberHelper, $page = 1)
    {
        $headers = $grabberHelper->getAuthorizationHeaders(
            $site,
            '/auth/check',
            array('login' => $site->getLogin(), 'pass' => $site->getPassword())
        );

        if (!$headers) {
            return null;
        }

        $limit = 30;
        $grabberResults = array();
        do {
            $demandContent = mb_convert_encoding($grabberHelper->getContent($site, sprintf('/buy/list/page_%d', $page), $headers), 'UTF-8', 'windows-1251');
            $crawler = new Crawler();
            $crawler->addHtmlContent($demandContent);

            $filter = $crawler->filter('table.orders_table > tr > td.right > a');

            if (!iterator_count($filter)) {
                $grabberHelper->log(Logger::ERROR, 'Список заявок не найден', array('site_id' => $site->getId()));

                return $grabberResults;
            }

            foreach ($filter as $content) {
                /* @var $content \DOMElement */
                $url = $content->getAttribute('href');
                preg_match('/show\/(\d+)/ui', $url, $matchDemandId);

                if (!$matchDemandId) {
                    $grabberHelper->log(Logger::ERROR, 'Не найдена ссылка на заявку', array('site_id' => $site->getId()));
                    continue;
                }

                $demandId = $matchDemandId[1];
                $hash = md5($url);

                if ($grabberHelper->isAdded(array('hash' => $hash, 'site' => $site->getId()))) {
                    $grabberHelper->log(
                        Logger::NOTICE,
                        GrabberInterface::MESSAGE_DEMAND_IS_ALREADY_EXIST,
                        array('site_id' => $site->getId())
                    );

                    continue;
                }

                $demandContent = mb_convert_encoding($grabberHelper->getContent($site, $url, $headers), 'UTF-8', 'windows-1251');
                $demandCrawler = new Crawler();
                $demandCrawler->addHtmlContent($demandContent);
                if (!$demandCrawler->count()) {
                    $grabberHelper->log(Logger::NOTICE, 'Нет данных в заявке, пытаемся распарсить следующую', array('site_id' => $site->getId()));

                    continue;
                }

                $grabberResult = new GrabberResult();
                $grabberResult->cityTitle = $demandCrawler->filter('table > tr > td.right > b:nth-child(2) > span')->text();

                $matchesRow = array(
                    'Организация:'     => 'companyTitle',
                    'Контактное лицо:' => 'person',
                    'Адрес:'           => 'address',
                    'Телефон:'         => 'phone',
                    'Email:'           => 'email'
                );

                $countRows = $demandCrawler->filter('table > tr > td.right > div > table > tr')->count();
                foreach ($matchesRow as $key => $property) {
                    for ($i = 1; $i <= $countRows; $i++) {
                        if ($demandCrawler->filter(sprintf('table > tr > td.right > div > table > tr:nth-child(%d) > th', $i))->text() == $key) {
                            $grabberResult->$property = $demandCrawler->filter(sprintf('table > tr > td.right > div > table > tr:nth-child(%d) > td', $i))->text();

                            $grabberHelper->log(Logger::INFO, 'Найдена информация', array($property => $grabberResult->$property, 'site_id' => $site->getId()));
                        }
                    }
                }

                $demandItems = $demandCrawler->filter('table > tr > td.right > ul > li');
                foreach ($demandItems as $item) {
                    $demandItem = new DemandItem();
                    $demandItemCrawler = new Crawler($item);
                    $demandItem->setTitle($demandItemCrawler->text());
                    $grabberResult->demandItems[] = $demandItem;

                    $grabberHelper->log(
                        Logger::INFO,
                        'Найдена позиция',
                        array('title' => $demandItem->getTitle(), 'site_id' => $site->getId())
                    );
                }

                $grabberResult->siteDemandUrl = $url;
                $grabberResult->siteDemandId = $demandId;
                $grabberResult->siteDemandHash = $hash;

                $grabberResults[] = $grabberResult;

                $limit--;
                if ($limit === 0) {
                    return $grabberResults;
                }
            }

            $page++;
        } while ($crawler->count() || $limit != 0);

        return $grabberResults;
    }
}
