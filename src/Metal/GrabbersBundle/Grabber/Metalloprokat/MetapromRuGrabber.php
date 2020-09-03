<?php

namespace Metal\GrabbersBundle\Grabber\Metalloprokat;

use Metal\DemandsBundle\Entity\DemandItem;
use Metal\GrabbersBundle\Entity\Site;
use Metal\GrabbersBundle\Grabber\GrabberInterface;
use Metal\GrabbersBundle\Grabber\GrabberHelper;
use Metal\GrabbersBundle\Grabber\GrabberResult;
use Monolog\Logger;
use Symfony\Component\DomCrawler\Crawler;

class MetapromRuGrabber implements GrabberInterface
{
    const OFFSET_COUNT = 100;

    public function getCode()
    {
        return 'metaprom';
    }

    public function grab(Site $site, GrabberHelper $grabberHelper, $offset = 0)
    {
        $limit = 50;

        $grabberResults = array();
        do {
            $crawler = new Crawler();

            sleep(3);
            $crawler->addHtmlContent(
                mb_convert_encoding($grabberHelper->getContent($site, sprintf('/board-metal/spros/?section=1&start=%d', $offset)), 'UTF-8', 'windows-1251')
            );

            $offset += self::OFFSET_COUNT;

            $filter = $crawler->filter('table.maintable tr td:nth-child(2) a');

            if (!iterator_count($filter) > 0) {
                $grabberHelper->log(Logger::ERROR, 'Список заявок не найден', array('site_id' => $site->getId()));

                return $grabberResults;
            }

            foreach ($filter as $i => $content) {
                if ($i < 20) {
                    continue;
                }

                if ($limit <= 0) {
                    $grabberHelper->log(Logger::NOTICE, 'Лимит исчерпан', array('limit' => $limit, 'site_id' => $site->getId()));
                    $grabberHelper->log(Logger::INFO, 'Отправка данных', array('site_id' => $site->getId()));

                    return $grabberResults;
                }

                $contentCrawler = new Crawler($content);

                $href = $contentCrawler->attr('href');

                preg_match('/\/id(\d+)[-a-zA-Z]+/ui', $href, $matchDemandId);

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

                $demandContent = mb_convert_encoding($grabberHelper->getContent($site, $href), 'UTF-8', 'windows-1251');

                $demandCrawler = new Crawler();
                $demandCrawler->addHtmlContent($demandContent);
                if (!$demandCrawler->count()) {
                    $grabberHelper->log(Logger::NOTICE, 'Нет данных в заявке, пытаемся распарсить следующую', array('site_id' => $site->getId()));

                    continue;
                }

                $grabberResult = new GrabberResult();
                $demandItem = new DemandItem();

                $title = preg_replace('/\(.{1,20}\) \- Спрос$/ui', '', $demandCrawler->filter('h1')->text());
                $demandItem->setTitle(trim($title));
                $grabberResult->demandItems[] = $demandItem;

                $companyTitle = $demandCrawler->filter('span[itemprop="name"]');

                if ($companyTitle->count()) {
                    preg_match('/(.+)\s\((.+)\)/', $companyTitle->text(), $matchesComp);
                    if ($matchesComp) {
                        $grabberResult->companyTitle = trim($matchesComp[1]);
                        $grabberResult->cityTitle = trim($matchesComp[2]);
                    }

                }

                $phone = $demandCrawler->filter(sprintf('div[itemscope=""] table[class="maintable"] tr:nth-child(%d) td:nth-child(2)', 3));
                if ($phone->count()) {
                    $grabberResult->phone = trim($phone->text());
                }
                $person = $demandCrawler->filter(sprintf('div[itemscope=""] table[class="maintable"] tr:nth-child(%d) td:nth-child(2)', 2));
                if ($person->count()) {
                    $grabberResult->person = trim($person->text());
                }

                $info = $demandCrawler->filter('p');
                foreach ($info as $key=>$val) {
                    if ($key == 1) {
                        $grabberResult->setInfo($val->textContent);
                    }
                }

                $grabberResult->siteDemandUrl = $href;
                $grabberResult->siteDemandId = $demandId;
                $grabberResult->siteDemandHash = $hash;

                $grabberResults[] = $grabberResult;

                $limit--;
            }
        } while ($crawler->count() && $limit > 0);

        $grabberHelper->log(Logger::INFO, 'Отправка данных', array('site_id' => $site->getId()));

        return $grabberResults;
    }
}
