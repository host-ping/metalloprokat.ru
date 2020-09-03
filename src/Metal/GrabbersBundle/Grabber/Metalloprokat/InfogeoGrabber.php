<?php

namespace Metal\GrabbersBundle\Grabber\Metalloprokat;

use Metal\DemandsBundle\Entity\DemandItem;
use Metal\GrabbersBundle\Entity\Site;
use Metal\GrabbersBundle\Grabber\GrabberInterface;
use Metal\GrabbersBundle\Grabber\GrabberHelper;
use Metal\GrabbersBundle\Grabber\GrabberResult;
use Monolog\Logger;
use Symfony\Component\DomCrawler\Crawler;

class InfogeoGrabber implements GrabberInterface
{
    const MAX_DUPLICATES = 10;

    private $limitDuplicates = self::MAX_DUPLICATES;

    public function getCode()
    {
        return 'infogeo';
    }

    public function grab(Site $site, GrabberHelper $grabberHelper, $page = 1)
    {
        $limitPage = 2;
        $grabberResults = array();
        do {

            if ($limitPage <= 0 || $this->limitDuplicates <= 0) {
                return $grabberResults;
            }

            $limitPage--;

            $demandContent = $grabberHelper->getContent($site, sprintf('/metalls/board/?tp=%d&mt=11', $page));
            $page++;
            $crawler = new Crawler();
            $crawler->addHtmlContent($demandContent);


            $filter = $crawler->filter('tr > td:nth-child(1) > table:nth-child(4) > tr > td:nth-child(2) > table > tr > td:nth-child(2) > a');

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


                /* @var $content \DOMElement */
                $url = $content->getAttribute('href');

                $url = preg_replace('/^\./ui', '/metalls/board', $url);
                preg_match('/nid=(\d+)(?:\D|$)/ui', $url, $matchDemandId);

                if (!$matchDemandId) {
                    $grabberHelper->log(Logger::ERROR, 'Не найдена ссылка на заявку, ищем дальше.',
                        array(
                            'site_id' => $site->getId(),
                            'url' => $url,
                            'double_count' => self::MAX_DUPLICATES - $this->limitDuplicates,
                            'page'    => $page - 1
                        )
                    );

                    $this->limitDuplicates--;

                    continue;
                }

                $demandId = $matchDemandId[1];
                $hash = md5($demandId);

                $demandContent = mb_convert_encoding($grabberHelper->getContent($site, $url), 'UTF-8', 'windows-1251');;

                $demandCrawler = new Crawler();
                $demandCrawler->addHtmlContent($demandContent);

                if (!$demandCrawler->count()) {
                    $grabberHelper->log(Logger::NOTICE, 'Нет данных в заявке, пытаемся распарсить следующую', array('site_id' => $site->getId()));

                    $this->limitDuplicates--;

                    continue;
                }

                $grabberResult = new GrabberResult();
                $demandItem = new DemandItem();

                $body = $demandCrawler->filter('tr > td > table:nth-child(2) > tr > td:nth-child(1) > table > tr > td');
                $demandItemTitle = $body->filter('h1 > font');
                if ($demandItemTitle->count()) {
                    $demandItem->setTitle(trim($demandItemTitle->text()));
                }

                $infoBodyFilter = $body->filter('p')->eq(0);

                if ($infoBodyFilter->count()) {
                    $infoBody = trim($infoBodyFilter->text());
                    preg_match('/Автор (.*?)\s\/\s(.{12,15})\s\/\s(.+)/ui', $infoBody, $matchesInfo);
                    if ($matchesInfo) {
                        $grabberResult->cityTitle = $matchesInfo[3];
                        $grabberResult->person = $matchesInfo[1];
                        $grabberResult->siteDemandPublicationDate = $matchesInfo[2];
                        $grabberResult->createdAt = $grabberHelper->parseDate($site, $matchesInfo[2]);
                    }
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

                $info = $body->filter('p')->eq(1);

                if ($info->count()) {
                    $infoText = $info->text();
                    preg_match('/^Тел\.\s(.*?)(?:$|\n)/ui', $infoText, $phoneMatches);
                    if ($phoneMatches) {
                        $grabberResult->phone = $phoneMatches[1];
                        $infoText = preg_replace(sprintf('/%s/ui', $phoneMatches[0]), '', $infoText);
                        $grabberResult->setInfo($infoText);
                    }
                }

                $grabberResult->demandItems[] = $demandItem;


                $grabberResult->siteDemandUrl = $url;
                $grabberResult->siteDemandId = $demandId;
                $grabberResult->siteDemandHash = $hash;

                $grabberResults[] = $grabberResult;
            }
        } while ($crawler->count() || $this->limitDuplicates > 0);

        return $grabberResults;
    }
}
