<?php

namespace Metal\GrabbersBundle\Grabber\Metalloprokat;

use Metal\DemandsBundle\Entity\DemandItem;
use Metal\GrabbersBundle\Entity\Site;
use Metal\GrabbersBundle\Grabber\GrabberInterface;
use Metal\GrabbersBundle\Grabber\GrabberHelper;
use Metal\GrabbersBundle\Grabber\GrabberResult;
use Monolog\Logger;
use Symfony\Component\DomCrawler\Crawler;

class PromindexGrabber implements GrabberInterface
{
    const MAX_DUPLICATE_COUNT = 5;

    protected $stopWords = '(Продаем|Продается|продам|Продадим|Продажа|Продаю|В наличии|Предлагаем|Оказываем услуги)';

    public function getCode()
    {
        return 'promindex';
    }

    public function grab(Site $site, GrabberHelper $grabberHelper, $page = 0)
    {
        $mainHeaders = $grabberHelper->getAllHeaders($site->getHost().'/login');
        list($requestHeders, $csrf) = $this->getCookie($mainHeaders);
        $authHeaders = $grabberHelper->getAuthorizationHeaders(
            $site,
            '/login',
            array(
                'csrf_form' => $csrf,
                'email' => $site->getLogin(),
                'password' => $site->getPassword(),
                'submitButton' => true
            ),
            $requestHeders,
            true
        );

        $resCookie = $this->getCookie($authHeaders);
        if (!$resCookie[1]) {
            $grabberHelper->log(Logger::ERROR, 'Csrf not found', array('page' => $page, 'site_id' => $site->getId()));
            return;
        }

        $headers = array();
        $headers[':authority'] = 'promindex.ru';
        $headers[':method'] = 'GET';
        $headers[':scheme'] = 'https';
        $headers['referer'] = $site->getHost();
        $headers['upgrade-insecure-requests'] = 1;
        $headers['cookie'] = $resCookie[0]['cookie'];

        $limit = 100;
        $duplicateCount = 0;

        do {
            $uri = sprintf('/orders?page=%d', $page);
            $grabberHelper->log(Logger::DEBUG, 'Страница.', array('page' => $page, 'site_id' => $site->getId()));
            $page += 50;

            $crawler = new Crawler();
            $crawler->addHtmlContent($grabberHelper->getContent($site, $uri, $headers));

            $demandsLink = $crawler
                ->filter('div[class="title title-3rows"] a')
                ->each(function (Crawler $crawler) {
                    return $crawler->attr('href');
                });

            if (!count($demandsLink)) {
                $grabberHelper->log(Logger::ERROR, 'Список заявок не найден', array('site_id' => $site->getId()));

                break;
            }

            foreach ($demandsLink as $demandLink) {
                if ($duplicateCount >= self::MAX_DUPLICATE_COUNT) {
                    $grabberHelper->log(Logger::ALERT, 'Лимит дубликатов исчерпан.', array('duplicateCount' => $duplicateCount, 'site_id' => $site->getId()));

                    break 2;
                }

                $matchesId = null;
                preg_match('/\/orders\/(\d+)-/u', $demandLink, $matchesId);
                $demandId = $matchesId[1];
                $hash = md5($demandId);

                $grabberHelper->log(Logger::INFO, 'Найден id заявки.', array('siteDemandId' => $demandId, 'site_id' => $site->getId()));

                if ($grabberHelper->isAdded(array('hash' => $hash, 'site' => $site->getId()), new \DateTime())) {
                    $duplicateCount++;
                    $grabberHelper->log(Logger::INFO, GrabberInterface::MESSAGE_DEMAND_IS_ALREADY_EXIST, array('site_id' => $site->getId(), 'hash' => $hash));

                    continue;
                }

                $grabberResult = new GrabberResult();

                $siteCrawler = clone $crawler;
                $cityFilter = $siteCrawler->filter('a[href="'.$demandLink.'"]')->parents()
                    ->filter('.location');
              if (!count($cityFilter)) {
                    $grabberHelper->log(Logger::NOTICE, 'Город не найден.', array('site_id' => $site->getId()));
                    continue;
                }

                $grabberResult->cityTitle = trim($cityFilter->text());
                $grabberHelper->log(Logger::INFO, 'Найден город.', array('cityTitle' => $grabberResult->cityTitle, 'site_id' => $site->getId()));

                $demandCrawler = new Crawler();
                $demandCrawler->addHtmlContent($grabberHelper->getContent($site, $demandLink, $headers));

                if ($demandCrawler->filter('.col-md-9')->count()) {
                    $html = $demandCrawler->filter('.col-md-9')->html();
                    $explodeAbout = array_filter(explode('<hr>', $html));
                    if (count($explodeAbout)) {
                        $textDemandItems = array_filter(explode('<br><br>', $explodeAbout[0]));
                        foreach ($textDemandItems as $textDemandItem) {
                            if ($itemTitle = preg_replace('/^\d+\s/ui', '', trim(strip_tags($textDemandItem)))) {
                                $demandItem = new DemandItem();
                                $demandItem->setTitle(trim($itemTitle));
                                $grabberResult->demandItems[] = $demandItem;
                                $grabberHelper->log(Logger::INFO, 'Найдена позиция.', array('demandItem' => $itemTitle, 'site_id' => $site->getId()));
                            }
                        }

                        if (isset($explodeAbout[1]) && trim($explodeAbout[1]) !== '') {
                            $grabberResult->setInfo(strip_tags($explodeAbout[1]));
                        }
                    } else {
                        $grabberResult->setInfo($demandCrawler->filter('.col-md-9')->text());
                    }

                    if ($grabberResult->info) {
                        $grabberHelper->log(Logger::INFO, 'Найдено описание.', array('info' => $grabberResult->info, 'site_id' => $site->getId()));
                    }
                }

                $lastData = '';
                $demandCrawler->filter('#orders_customer dl')->children()->each(function (Crawler $crawler) use (&$lastData, $grabberResult, $grabberHelper, $site, $demandLink,$resCookie) {
                    $text = trim($crawler->text());
                    $html = $crawler->html();
                    if ($lastData === 'Имя') {
                        $grabberResult->person = $text;
                        $grabberHelper->log(Logger::INFO, 'Найдено контактное лицо.', array('person' => $grabberResult->person, 'site_id' => $site->getId()));
                    }

                    if ($lastData === 'Компания') {
                        $grabberResult->companyTitle = $text;
                        $grabberResult->person = $text;
                        $grabberHelper->log(Logger::INFO, 'Найдена компания.', array('person' => $grabberResult->person, 'site_id' => $site->getId()));
                    }

                    $headersPost = array();

                    $headersPost[':authority'] = 'promindex.ru';
                    $headersPost[':method'] = 'POST';
                    $headersPost[':scheme'] = 'https';
                    $headersPost['referer'] = $demandLink;
                    $headersPost['upgrade-insecure-requests'] = 1;
                    $headersPost['cookie'] = $resCookie[0]['cookie'];
                    $headersPost['X-Requested-With'] = 'XMLHttpRequest';

                    if ($lastData === 'Телефон') {
                        $grabberResult->phone = $html;
                        $crawlerPhone = new Crawler();
                        $crawlerPhone->addHtmlContent($html);
                        $linkPhone = $crawlerPhone->filter('span[class="layout-pseudo_link"]')->attr('data-src');
                        $crawlerPhone->clear();
                        $crawlerPhone->addHtmlContent($grabberHelper->getContent($site, $linkPhone, $headersPost, ['type'=>'phone']));
                        $grabberResult->phone = json_decode($crawlerPhone->text())->html;
                        $grabberHelper->log(Logger::INFO, 'Найден телефон.', array('phone' => $grabberResult->phone, 'site_id' => $site->getId()));
                    }

                    if ($lastData === 'Email') {
                        $grabberResult->email = $html;
                        $crawlerPhone = new Crawler();
                        $crawlerPhone->addHtmlContent($html);
                        $linkPhone = $crawlerPhone->filter('span[class="layout-pseudo_link"]')->attr('data-src');
                        $crawlerPhone->clear();
                        $crawlerPhone->addHtmlContent($grabberHelper->getContent($site, $linkPhone, $headersPost, ['type'=>'email']));
                        $grabberResult->email = json_decode($crawlerPhone->text())->html;
                        $grabberHelper->log(Logger::INFO, 'Найден email.', array('email' => $grabberResult->email, 'site_id' => $site->getId()));
                    }

                    $lastData = $text;
                });

                $date = $demandCrawler->filter('.text-muted');
                preg_match('/(.+),[\s\d]+$/ui', $date->text(), $dateMatches);
                if ($dateMatches) {
                    $grabberResult->siteDemandPublicationDate = trim($dateMatches[1]);
                    $grabberResult->createdAt = $grabberHelper->parseDate($site, $dateMatches[1]);
                    $grabberHelper->log(Logger::INFO, 'Найдена дата публикации', array('limit' => $limit, 'siteDemandPublicationDate' => $grabberResult->siteDemandPublicationDate, 'site_id' => $site->getId()));
                }

                $grabberResult->siteDemandUrl = $demandLink;
                $grabberResult->siteDemandId = $demandId;
                $grabberResult->siteDemandHash = $hash;

                $sleepTime = mt_rand(1, 2);
                $grabberHelper->log(Logger::INFO, sprintf('Задержка %d секунд.', $sleepTime), array('limit' => $limit, 'site_id' => $site->getId()));
                sleep($sleepTime);

                if (preg_match_all(sprintf('/%s/ui', $this->stopWords), $grabberResult->info, $stopWordMatches)) {
                    $grabberHelper->log(Logger::INFO, 'В описании найдены стоп слова, пропускаем.', array('limit' => $limit, 'stop_words' => implode(', ', $stopWordMatches[1]), 'site_id' => $site->getId()));
                    continue;
                }

                yield $grabberResult;

                $limit--;

                if ($limit <= 0) {
                    $grabberHelper->log(Logger::NOTICE, 'Лимит исчерпан', array('limit' => $limit, 'site_id' => $site->getId()));

                    break 2;
                }
            }
        } while($crawler->count() && $limit > 0);

        $grabberHelper->log(Logger::INFO, 'Отправка данных', array('site_id' => $site->getId()));
    }

    private function getCookie(array $mainHeaders)
    {
        $csrf = '';
        $requestHeders = array('cookie' => 'city_id=' . 370 .';');
        foreach ($mainHeaders as $mainHeader) {
            $matches = null;
            if (preg_match('/session_id=(.*?);/ui', $mainHeader, $matches)) {
                $requestHeders['cookie'] .= $matches[0];
            }

            $matches = null;
            if (preg_match('/csrf_form=(.*?);/ui', $mainHeader, $matches)) {
                $requestHeders['cookie'] .= $matches[0];
                $csrf = $matches[1];
            }
        }

        return array($requestHeders, $csrf);
    }

    private function getEmail($string)
    {
        $matches = null;
        preg_match_all("/\] = '(.+)';\n/ui", $string, $matches);

        if (!isset($matches[1])) {
            return '';
        }

        $simbols = array_reverse($matches[1]);
        $link = '';
        foreach ($simbols as $simbol) {
            if (substr($simbol, 0, 1) === '|') {
                $link .= '&#'.stripcslashes(substr($simbol, 1)).';';
            } else {
                $link .= stripcslashes($simbol);
            }
        }
        $crawler = new Crawler(html_entity_decode($link));

        if (!$crawler->count()) {
            return '';
        }

        return $crawler->text();
    }
}
