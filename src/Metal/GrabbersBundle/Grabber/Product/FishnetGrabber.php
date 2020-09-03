<?php

namespace Metal\GrabbersBundle\Grabber\Product;

use Metal\DemandsBundle\Entity\DemandItem;
use Metal\GrabbersBundle\Entity\Site;
use Metal\GrabbersBundle\Grabber\GrabberInterface;
use Metal\GrabbersBundle\Grabber\GrabberHelper;
use Metal\GrabbersBundle\Grabber\GrabberResult;
use Monolog\Logger;
use Symfony\Component\DomCrawler\Crawler;

class FishnetGrabber implements GrabberInterface
{
    const MAX_DUPLICATES = 20;

    private $limitDuplicates = self::MAX_DUPLICATES;

    public function getCode()
    {
        return 'fishnet';
    }

    public function grab(Site $site, GrabberHelper $grabberHelper, $page = 1)
    {
        $headers = $grabberHelper->getAuthorizationHeaders(
            $site,
            '/login.html',
            array('row[login]' => $site->getLogin(), 'row[password]' => $site->getPassword(), 'referer' => '', 'backonceagain' => ''),
            array(),
            true
        );

        if (!$headers) {
            return;
        }

        preg_match('/(lsa\=\d+;)\s/ui', $headers['Cookie'], $lsa);
        preg_match('/(lsb\=.{32};)/ui', $headers['Cookie'], $lsb);

        $headers['Cookie'] = $lsa[1].$lsb[1];

        $limitPage = 3;
        do {

            if ($limitPage <= 0 || $this->limitDuplicates <= 0) {
                return;
            }

            $limitPage--;

            $demandContent = $grabberHelper->getContent($site, sprintf('/tradingboard/russia/buy?page=%d', $page), $headers);
            $page++;
            $crawler = new Crawler();
            $crawler->addHtmlContent($demandContent);

            $filter = $crawler->filter('h3.posttitle a');
            if (!iterator_count($filter)) {
                $grabberHelper->log(Logger::ERROR, 'Список заявок не найден, пытаемся распарсить следующую страницу',
                    array(
                        'site_id' => $site->getId(),
                        'page'    => $page - 1
                    )
                );

                $limitPage--;

                continue;
            }

            foreach ($filter as $i => $content) {
                /* @var $content \DOMElement */

                if ($this->limitDuplicates <= 0) {
                    return;
                }

                $url = $content->getAttribute('href');
                if (strpos($url, 'http://') !== 0) {
                    continue;
                }
                preg_match('/-(\d+)\.html$/ui', $url, $matchDemandId);
                if (!$matchDemandId) {
                    $grabberHelper->log(Logger::ERROR, 'Не найдена ссылка на заявку, ищем дальше.',
                        array(
                            'site_id' => $site->getId(),
                            'url' => $url,
                            'page'    => $page - 1
                        )
                    );

                    $this->limitDuplicates--;

                    continue;
                }

                $demandId = $matchDemandId[1];
                $hash = md5($demandId);

                $demandContent = $grabberHelper->getContent($site, $url, $headers);

                $demandCrawler = new Crawler();
                $demandCrawler->addHtmlContent($demandContent);
                if (!$demandCrawler->count()) {
                    $grabberHelper->log(Logger::NOTICE, 'Нет данных в заявке, пытаемся распарсить следующую', array('site_id' => $site->getId()));

                    $this->limitDuplicates--;

                    continue;
                }

                $grabberResult = new GrabberResult();

                //Дата публикации
                $grabberResult->siteDemandPublicationDate = $demandCrawler->filter('.post_title_date')->first()->text();
                $grabberResult->siteDemandPublicationDate = preg_replace('/^\s\/\s/ui', '', $grabberResult->siteDemandPublicationDate);
                $grabberResult->createdAt = $grabberHelper->parseDate($site, $grabberResult->siteDemandPublicationDate);

                if ($grabberHelper->isAdded(array('hash' => $hash, 'site' => $site->getId()), $grabberResult->createdAt)) {
                    $grabberHelper->log(
                        Logger::NOTICE,
                        GrabberInterface::MESSAGE_DEMAND_IS_ALREADY_EXIST,
                        array(
                            'site_id' => $site->getId(),
                            'hash' => $hash,
                            'double_count' => self::MAX_DUPLICATES - $this->limitDuplicates,
                            'page'    => $page - 1
                        )
                    );

                    $this->limitDuplicates--;

                    continue;
                }

                $demandItem = new DemandItem();
                $demandItemTitleFilter= $demandCrawler->filter('.post_body')->first();
                if ($demandItemTitleFilter->count()) {
                    $demandItem->setTitle(trim($demandItemTitleFilter->text()));
                    $grabberHelper->log(Logger::INFO, 'Найдена позиция', array('demandItem' => $demandItem->getTitle(), 'site_id' => $site->getId()));
                }

                $companyTitle = $demandCrawler->filter('.post_owner_title');
                if ($companyTitle->count()) {
                    $grabberResult->companyTitle = trim($companyTitle->text());
                    $grabberHelper->log(Logger::INFO, 'Найдена компания', array('site_id' => $site->getId(), 'title' => $grabberResult->companyTitle));
                }

                $address = $demandCrawler->filter('.post_owner_adr');
                if ($address->count()) {
                    $grabberResult->cityTitle = trim($address->text());
                    $grabberResult->address = trim($address->text());
                    $grabberHelper->log(Logger::INFO, 'Найден адрес', array('site_id' => $site->getId(), 'title' => $grabberResult->address));
                }

                $demandInfoFilter = $demandCrawler->filter('.content h1');
                if ($demandInfoFilter->count()) {
                    $grabberResult->setInfo($demandInfoFilter->text());
                }

                preg_match('/Телефон:(.*?)(    |$)/ui', $demandCrawler->filter('div.post_sidebar_block_content > div:nth-child(2)')->text(), $matches);
                if ($matches) {
                    $grabberResult->phone = trim($matches[1]);
                    $grabberHelper->log(Logger::INFO, 'Найден телефон', array('site_id' => $site->getId(), 'phone' => $grabberResult->phone));
                } else {
                    $grabberHelper->log(Logger::NOTICE, 'Телефон не найден', array('site_id' => $site->getId(), 'text' => $demandCrawler->filter('div.post_sidebar_block_content > div:nth-child(2)')->text()));
                }

                $grabberResult->demandItems[] = $demandItem;
                $grabberResult->siteDemandUrl = $url;
                $grabberResult->siteDemandId = $demandId;
                $grabberResult->siteDemandHash = $hash;

                yield $grabberResult;
            }

        } while ($crawler->count() || $this->limitDuplicates > 0);
    }
}
