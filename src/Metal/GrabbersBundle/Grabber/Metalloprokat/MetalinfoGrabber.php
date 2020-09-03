<?php

namespace Metal\GrabbersBundle\Grabber\Metalloprokat;

use Metal\DemandsBundle\Entity\DemandItem;
use Metal\GrabbersBundle\Entity\Site;
use Metal\GrabbersBundle\Grabber\GrabberInterface;
use Metal\GrabbersBundle\Grabber\GrabberHelper;
use Metal\GrabbersBundle\Grabber\GrabberResult;
use Monolog\Logger;
use Symfony\Component\DomCrawler\Crawler;

class MetalinfoGrabber implements GrabberInterface
{
    public function getCode()
    {
        return 'metalinfo';
    }

    public function grab(Site $site, GrabberHelper $grabberHelper, $page = 1)
    {
        $limit = 50;
        $grabberResults = array();

        do {

            $crawler = new Crawler();
            $crawler->addHtmlContent($grabberHelper->getContent($site, sprintf('/ru/board?category=b?page=%d', $page)));
            $page++;

            $result = $crawler
                ->filter('span.title')
                ->filter('a')
                ->each(function (Crawler $crawler) use ($site, $grabberHelper, &$limit) {
                    
                    if ($limit <= 0) {
                        return null;
                    }

                    $cityCrawler = $crawler->parents()->filter('span.region');

                    if (!$cityCrawler->count()) {
                        $grabberHelper->log(Logger::NOTICE, 'Город не найден, парсим следующую', array('site_id' => $site->getId()));

                        return null;
                    }
                    
                    $demandCrawler = new Crawler();
                    $demandCrawler->addHtmlContent($grabberHelper->getContent($site, $crawler->attr('href')));

                    if (!$demandCrawler->count()) {
                        $grabberHelper->log(Logger::NOTICE, 'Нет данных в заявке, пытаемся распарсить следующую', array('site_id' => $site->getId()));
                        $limit--;

                        return null;
                    }

                    preg_match('/board\/bulletin(\d+)\.html$/ui', $crawler->attr('href'), $matches);

                    $demandId = $matches[1];
                    $hash = md5($demandId);

                    $titleCrawler = $demandCrawler
                        ->filter('section.metalsite-page')
                        ->filter('h1')
                    ;

                    if (!$titleCrawler->count()) {
                        $grabberHelper->log(Logger::INFO, 'Нету тайтла, обрабатываем следующую', array('site_id' => $site->getId(), 'hash' => $hash));

                        return null;
                    }

                    $grabberResult = new GrabberResult();
                    $demandItem = new DemandItem();

                    $demandItem->setTitle($titleCrawler->text());
                    $grabberResult->demandItems[] = $demandItem;
                    $grabberResult->cityTitle = $cityCrawler->text();

                    $grabberHelper->log(Logger::NOTICE, sprintf("Найден город \n \"%s\"", $grabberResult->cityTitle), array('site_id' => $site->getId()));

                    $grabberResult->setInfo($demandCrawler
                        ->filter('section.metalsite-page')
                        ->filter('span[itemprop="text"]')
                        ->text());

                    $grabberHelper->log(Logger::NOTICE, sprintf("Найдена описание заявки \n \"%s\"", $grabberResult->info), array('site_id' => $site->getId()));

                    $data = $demandCrawler->filter('small.transperent')->html();

                    preg_match('/<strong>Добавлено:<\/strong>(.+?) г\.<br>/ui', $data, $dateMatches);
                    $grabberResult->siteDemandPublicationDate = trim($dateMatches[1]);
                    $grabberResult->createdAt = $grabberHelper->parseDate($site, $dateMatches[1]);
                    $grabberHelper->log(Logger::NOTICE, sprintf("Найдена дата заявки \n \"%s\"", $dateMatches[1]), array('site_id' => $site->getId()));


                    if ($grabberHelper->isAdded(array('hash' => $hash, 'site' => $site->getId()), $grabberResult->createdAt)) {
                        $grabberHelper->log(Logger::INFO, GrabberInterface::MESSAGE_DEMAND_IS_ALREADY_EXIST, array('site_id' => $site->getId(), 'hash' => $hash));
                        $limit--;

                        return null;
                    }
                    
                    preg_match('/<strong>Организация:<\/strong>(.+?)<br>/ui', $data, $companyTitleMatches);
                    if (preg_match('/< ?a href/ui', $companyTitleMatches[1])) {
                        preg_match('/">(.+?)<\/a>/ui', $companyTitleMatches[1], $companyTitle);
                        $grabberResult->companyTitle = trim($companyTitle[1]);
                    } else {
                        $grabberResult->companyTitle = trim($companyTitleMatches[1]);
                    }

                    $grabberHelper->log(Logger::NOTICE, sprintf("Найден компания заявки \n \"%s\"", $grabberResult->companyTitle), array('site_id' => $site->getId()));\

                    preg_match('/<script type="text\/javascript">eval\(unescape\(\'(.+?)\'\)\)<\/script>/ui', $data, $emailMatches);
                    preg_match('/href=&quot;mailto:(.+?)\?subject=/ui', htmlspecialchars(urldecode($emailMatches[1])), $email);
                    $grabberResult->email = trim($email[1]);
                    $grabberHelper->log(Logger::NOTICE, sprintf("Найден email заявки \n \"%s\"", $grabberResult->email), array('site_id' => $site->getId()));

                    preg_match('/<strong>Телефон:<\/strong>(.+?)<br><strong>/ui', $data, $phoneMatches);
                    $grabberResult->phone = trim($phoneMatches[1]);
                    $grabberHelper->log(Logger::NOTICE, sprintf("Найден телефон заявки \n \"%s\"", $grabberResult->phone), array('site_id' => $site->getId()));

                    $limit--;

                    $grabberHelper->log(Logger::NOTICE, sprintf("Осталось разобрать \"%d\" заявок.", $limit), array('site_id' => $site->getId()));

                    $grabberResult->siteDemandUrl = $crawler->attr('href');
                    $grabberResult->siteDemandId = $demandId;
                    $grabberResult->siteDemandHash = $hash;

                    return $grabberResult;

                });

            $grabberResults = array_merge($grabberResults, $result);

        } while ($crawler->count() && $limit > 0);

        $grabberHelper->log(Logger::INFO, 'Отправка данных', array('site_id' => $site->getId()));

        return array_filter($grabberResults);
    }
}
