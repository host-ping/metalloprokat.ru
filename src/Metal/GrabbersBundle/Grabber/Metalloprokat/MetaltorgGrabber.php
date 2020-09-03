<?php

namespace Metal\GrabbersBundle\Grabber\Metalloprokat;

use Metal\DemandsBundle\Entity\DemandItem;
use Metal\GrabbersBundle\Entity\Site;
use Metal\GrabbersBundle\Grabber\GrabberInterface;
use Metal\GrabbersBundle\Grabber\GrabberHelper;
use Metal\GrabbersBundle\Grabber\GrabberResult;
use Monolog\Logger;
use Symfony\Component\DomCrawler\Crawler;

class MetaltorgGrabber implements GrabberInterface
{
    const OFFSET_COUNT = 10;

    public function getCode()
    {
        return 'metaltorg';
    }

    public function grab(Site $site, GrabberHelper $grabberHelper, $offset = 0)
    {
        $limit = 50;

        $grabberResults = array();
        do {
            $crawler = new Crawler();

            sleep(3);
            $crawler->addHtmlContent(
                $grabberHelper->getContent(
                    $site,
                    sprintf(
                        '/?SMDeclar_Type=1&page=%d',
                        $offset
                    )
                )
            );

            $offset += self::OFFSET_COUNT;

            $filter = $crawler->filter('div.list div.offer div:nth-child(3) a');
            if (!iterator_count($filter) > 0) {
                $grabberHelper->log(Logger::ERROR, 'Список заявок не найден', array('site_id' => $site->getId()));

                return $grabberResults;
            }

            foreach ($filter as $i => $content) {
                if ($limit <= 0) {
                    $grabberHelper->log(
                        Logger::NOTICE,
                        'Лимит исчерпан',
                        array('limit' => $limit, 'site_id' => $site->getId())
                    );
                    $grabberHelper->log(
                        Logger::INFO,
                        'Отправка данных',
                        array('site_id' => $site->getId())
                    );

                    return $grabberResults;
                }

                $contentCrawler = new Crawler($content);

                $href = $contentCrawler->attr('href');
                preg_match('/_(\d+).html/ui', $href, $matchDemandId);

                if (!$matchDemandId) {
                    $grabberHelper->log(Logger::ERROR, 'Не найдена ссылка на заявку', array('site_id' => $site->getId()));

                    return $grabberResults;
                }

                $demandId = $matchDemandId[1];

                $hash = md5($demandId);

                if ($grabberHelper->isAdded(array('hash' => $hash, 'site' => $site->getId()))) {
                    $grabberHelper->log(Logger::INFO, GrabberInterface::MESSAGE_DEMAND_IS_ALREADY_EXIST, array('site_id' => $site->getId(), 'hash' => $hash));
                    $limit--;

                    continue;
                }

                $demandContent = $grabberHelper->getContent($site, $href);
                $demandCrawler = new Crawler();
                $demandCrawler->addHtmlContent($demandContent);
                if (!$demandCrawler->count()) {
                    $grabberHelper->log(Logger::NOTICE, 'Нет данных в заявке, пытаемся распарсить следующую', array('site_id' => $site->getId()));

                    continue;
                }

                $grabberResult = new GrabberResult();
                $demandItem = new DemandItem();

                $title = $demandCrawler->filter('h1')->text();
                $demandItem->setTitle(trim($title));
                $grabberResult->demandItems[] = $demandItem;

                $region = $demandCrawler->filter('#offer div[class="flex flex-justify-space-between"] div:nth-child(1) div:nth-child(1)');
                if ($region->count()) {
                    preg_match('/Регионы: ([а-яА-Я]+),(.+)/ui', $region->text(), $matchesComp);
                    if ($matchesComp) {
                        $grabberResult->cityTitle = trim($matchesComp[1]);
                    }
                    else {
                        $grabberResult->cityTitle = $region->text();
                    }
                }

                $person = $demandCrawler->filter('#offer div[class="flex flex-justify-space-between"] div:nth-child(1) div:nth-child(3) a');
                if ($person->count()) {
                    $grabberResult->person = trim($person->text());
                }

                $phone = $demandCrawler->filter('#offer div[class="flex flex-justify-space-between"] div:nth-child(1) div:nth-child(4)');
                if ($person->count()) {
                    $grabberResult->phone = trim($phone->text());
                }

                $info = $demandCrawler->filter('div[class="offer_text"]');
                if ($person->count()) {
                    $grabberResult->setInfo(trim($info->text()));
                }

                $grabberResult->siteDemandUrl = $href;
                $grabberResult->siteDemandId = $demandId;
                $grabberResult->siteDemandHash = $hash;

                $grabberResults[] = $grabberResult;

                $limit--;
            }

        }while ($crawler->count() && $limit > 0);

        $grabberHelper->log(Logger::INFO, 'Отправка данных', array('site_id' => $site->getId()));

        return $grabberResults;
    }


}