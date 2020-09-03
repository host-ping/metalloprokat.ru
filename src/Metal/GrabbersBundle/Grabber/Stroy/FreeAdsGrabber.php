<?php

namespace Metal\GrabbersBundle\Grabber\Stroy;

use Metal\DemandsBundle\Entity\DemandItem;
use Metal\GrabbersBundle\Entity\Site;
use Metal\GrabbersBundle\Grabber\GrabberHelper;
use Metal\GrabbersBundle\Grabber\GrabberInterface;
use Metal\GrabbersBundle\Grabber\GrabberResult;
use Monolog\Logger;
use Symfony\Component\DomCrawler\Crawler;

class FreeAdsGrabber implements GrabberInterface
{
    const MAX_DUPLICATES = 40;

    private $limitDuplicates = self::MAX_DUPLICATES;

    private $categoriesUri;

    private $code;

    public function __construct($code, array $categoriesUri)
    {
        $this->code = $code;
        $this->categoriesUri = $categoriesUri;
    }

    public function getCode()
    {
        return $this->code;
    }

    public function grab(Site $site, GrabberHelper $grabberHelper, $page = 1)
    {
        $headers = $grabberHelper->getHeaders(sprintf('%s%s', $site->getHost(), current($this->categoriesUri)));
        usleep(2500000);

        foreach ($this->categoriesUri as $allowedCategoryUri) {

            $grabberHelper->log(Logger::DEBUG, 'Категория', array('uri' => $allowedCategoryUri, 'site_id' => $site->getId()));

            $page = 1;
            $this->limitDuplicates = self::MAX_DUPLICATES;
            $limitPage = 2;
            do {
                if ($limitPage <= 0 || $this->limitDuplicates <= 0) {
                    continue 2;
                }

                $limitPage--;

                $folder = null;
                preg_match('/\/(\d+)\/$/ui', $allowedCategoryUri, $folder);

                $grabberHelper->log(Logger::DEBUG, 'Номер страницы', array('number' => $page, 'site_id' => $site->getId()));
                $demandContent = $grabberHelper->getContent(
                    $site,
                    sprintf('%s%d', $allowedCategoryUri, $page),
                    $headers,
                    array(
                        'Type' => 0,
                        'Folder' => $folder[1],
                        'City' => -1,
                        'qact' => 'search_adv',
                        'pg' => $page,
                        'OwnerAdvID' => 0,
                        'sort_select' => 'TimeOriginated-d',
                        'SortBy' => 'TimeOriginated',
                        'Dir' => 'd'
                    )
                );

                $page++;
                usleep(2500000);
                $crawler = new Crawler();
                $crawler->addHtmlContent($demandContent);

                $filter = $crawler->filter('h4 a[target="_blank"]');

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

                    $url = $content->getAttribute('href');

                    preg_match('/\/(\d+)\.html$/ui', $url, $matchDemandId);

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
                    usleep(2500000);

                    $demandCrawler = new Crawler();
                    $demandCrawler->addHtmlContent($demandContent);

                    if (!$demandCrawler->count()) {
                        $grabberHelper->log(Logger::NOTICE, 'Нет данных в заявке, пытаемся распарсить следующую', array('site_id' => $site->getId()));
                        $this->limitDuplicates--;

                        continue;
                    }

                    $grabberResult = new GrabberResult();
                    $demandItem = new DemandItem();

                    $adInfo = $demandCrawler->filter('article[role="article"] table[style="margin-bottom: 18px;"] tr');

                    if ($demandCrawler->filter('div.qa_label span.b')->count() && $demandCrawler->filter('div.qa_label span.b')->text()) {
                        $grabberResult->person = $demandCrawler->filter('div.qa_label span.b')->text();
                        $grabberResult->companyTitle = $grabberResult->person;
                        $grabberHelper->log(Logger::INFO, 'Найдена персона', array('person' => $grabberResult->person, 'site_id' => $site->getId()));
                        $grabberHelper->log(Logger::INFO, 'Найдена компания', array('companyTitle' => $grabberResult->companyTitle, 'site_id' => $site->getId()));
                    }

                    if ($demandCrawler->filter('div.advtxt')->count() && $demandCrawler->filter('div.advtxt')->text()) {
                        $grabberResult->setInfo($demandCrawler->filter('div.advtxt')->text());
                        $grabberHelper->log(Logger::INFO, 'Найдено описание', array('info' => $grabberResult->info, 'site_id' => $site->getId()));
                    }

                    if ($demandCrawler->filter('h1.advert-header')->count() && $demandCrawler->filter('h1.advert-header')->text()) {
                        $demandItem->setTitle(trim($demandCrawler->filter('h1.advert-header')->text()));
                        $grabberHelper->log(Logger::INFO, 'Найдена позиция', array('demandItem' => $demandItem->getTitle(), 'site_id' => $site->getId()));

                    }

                    foreach ($adInfo as $info) {

                        if (trim($info->firstChild->nodeValue) === 'Город:') {
                            $grabberResult->cityTitle = trim(str_replace('Город:', '', $info->nodeValue));
                            $grabberHelper->log(Logger::INFO, 'Найден город', array('phone' => $grabberResult->cityTitle, 'site_id' => $site->getId()));
                            continue;
                        }

                        if (trim($info->firstChild->nodeValue) === 'Телефон:') {
                            $grabberResult->phone = trim(str_replace('Телефон:', '', $info->nodeValue));
                            $grabberHelper->log(Logger::INFO, 'Найден телефон', array('phone' => $grabberResult->phone, 'site_id' => $site->getId()));
                            continue;
                        }

                        if (trim($info->firstChild->nodeValue) === 'Дата:') {
                            $grabberResult->siteDemandPublicationDate = trim(str_replace('Дата:', '', $info->nodeValue));
                            $grabberResult->createdAt = $grabberHelper->parseDate($site, $grabberResult->siteDemandPublicationDate);
                            $grabberHelper->log(Logger::INFO, 'Найдена дата', array('siteDemandPublicationDate' => $grabberResult->siteDemandPublicationDate, 'site_id' => $site->getId()));
                            continue;
                        }
                    }

                    if ($grabberHelper->isAdded(array('hash' => $hash, 'site' => $site->getId()))) {
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

                    $grabberResult->demandItems[] = $demandItem;
                    $grabberResult->siteDemandUrl = $url;
                    $grabberResult->siteDemandId = $demandId;
                    $grabberResult->siteDemandHash = $hash;

                    yield $grabberResult;
                }

            } while ($crawler->count() || $this->limitDuplicates > 0);
        }
    }
}
