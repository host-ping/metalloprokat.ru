<?php

namespace Metal\GrabbersBundle\Grabber\Metalloprokat;

use Metal\DemandsBundle\Entity\DemandItem;
use Metal\GrabbersBundle\Entity\Site;
use Metal\GrabbersBundle\Grabber\GrabberInterface;
use Metal\GrabbersBundle\Grabber\GrabberHelper;
use Metal\GrabbersBundle\Grabber\GrabberResult;
use Monolog\Logger;
use Symfony\Component\DomCrawler\Crawler;

class MetalbulletinGrabber implements GrabberInterface
{
    const MAX_DUPLICATE_COUNT = 15;

    public function getCode()
    {
        return 'metalbulletin';
    }

    public function grab(Site $site, GrabberHelper $grabberHelper, $page = null)
    {
        $limit = 50;
        $duplicateCount = 0;

        $grabberResults = array();

        do {

            $uri = '/board/';
            if (null !== $page) {
                $uri .= 'page/'.$page.'/';
            }

            $crawler = new Crawler();
            $crawler->addHtmlContent($grabberHelper->getContent($site, $uri));

            if (null === $page) {
                $pageCrawler = $crawler->filter('table.sdvig2 tr td a.board_pager')->first();
                if ($pageCrawler->count()) {
                    preg_match('/\/page\/(\d+)\/$/ui', $pageCrawler->attr('href'), $matches);

                    if ($matches) {
                        $page = $matches[1] + 1;
                    }
                }
            }

            $page--;

            $demandsLinks = $this->getDemandLinks($crawler);

            if (!count($demandsLinks)) {
                $grabberHelper->log(Logger::ERROR, 'Список заявок не найден', array('site_id' => $site->getId()));

                return $grabberResults;
            }

            foreach ($demandsLinks as $demandLink) {
                preg_match('/\/(\d+)\/$/ui', $demandLink, $matches);

                $demandId = ltrim($matches[1], '0');
                $hash = md5($demandId);

                if ($duplicateCount >= self::MAX_DUPLICATE_COUNT) {
                    $grabberHelper->log(Logger::ALERT, 'Лимит дубликатов исчерпан.', array('site_id' => $site->getId()));
                    return $grabberResults;
                }

                $demandCrawler = new Crawler();
                $demandCrawler->addHtmlContent(
                    mb_convert_encoding($grabberHelper->getContent($site, $demandLink), 'UTF-8', 'windows-1251')
                );

                $this->wait(array(2,5), $grabberHelper, $site);

                if (!$demandCrawler->count()) {
                    $grabberHelper->log(Logger::ALERT, 'Нет данных в заявке, пытаемся распарсить следующую', array('site_id' => $site->getId()));

                    continue;
                }

                $grabberResult = new GrabberResult();

                $dateCrawler = $demandCrawler->filter('.text2')->eq(5);
                if ($dateCrawler->count()) {
                    $grabberResult->siteDemandPublicationDate = $dateCrawler->text();
                    $grabberResult->createdAt = $grabberHelper->parseDate($site, $dateCrawler->text());
                    $grabberHelper->log(Logger::NOTICE, sprintf("Найдена дата заявки \n \"%s\"", $dateCrawler->text()), array('site_id' => $site->getId()));
                }

                if ($grabberHelper->isAdded(array('hash' => $hash, 'site' => $site->getId()), $grabberResult->createdAt)) {
                    $duplicateCount++;
                    $grabberHelper->log(Logger::ALERT, GrabberInterface::MESSAGE_DEMAND_IS_ALREADY_EXIST, array('site_id' => $site->getId(), 'hash' => $hash));

                    continue;
                }

                $demandItem = new DemandItem();

                $titleCrawler = $demandCrawler
                    ->filter('div.one_news_title')
                    ->filter('a')
                ;

                if (!$titleCrawler->count()) {
                    $grabberHelper->log(Logger::ERROR, 'Заголовок заявки пуст, обрабатываем следующую', array('site_id' => $site->getId()));
                    continue;
                }

                $title = $titleCrawler->text();
                if (preg_match('/(^|\s|\W)(Производство|продам|продаем|продаю|продажа)($|\s|\W)/ui', $title)) {
                    $grabberHelper->log(Logger::ALERT, sprintf("Найдено предложение, пропускаем \n \"%s\"", $title), array('site_id' => $site->getId()));
                    continue;
                }

                $demandItem->setTitle($title);
                $grabberResult->demandItems[] = $demandItem;

                $grabberHelper->log(Logger::NOTICE, sprintf("Найден заголовок заявки \n \"%s\"", $title), array('site_id' => $site->getId()));

                $infoCrawler = $demandCrawler->filter('.text2')->eq(0);
                $authorCrawler = $demandCrawler->filter('.text2')->eq(1);
                $phoneCrawler = $demandCrawler->filter('.text2')->eq(2);
                $emailImageCrawler = $demandCrawler->filter('.text2')->eq(3)->filter('a');
                $addressCrawler = $demandCrawler->filter('.text2')->eq(4);

                if ($infoCrawler->count()) {
                    $grabberResult->setInfo($infoCrawler->text());
                    $grabberHelper->log(Logger::NOTICE, sprintf("Найдена описание заявки \n \"%s\"", $grabberResult->info), array('site_id' => $site->getId()));

                    if (preg_match('/(^|\s|\W)(Производство|продам|продаем|продаю|продажа)($|\s|\W)/ui', $grabberResult->info)) {
                        $grabberHelper->log(Logger::ALERT, sprintf("В описании найдено предложение, пропускаем \n \"%s\"", $grabberResult->info), array('site_id' => $site->getId()));
                        continue;
                    }
                }

                if ($authorCrawler->count()) {
                    $grabberResult->companyTitle = $authorCrawler->text();
                    $grabberHelper->log(Logger::NOTICE, sprintf("Найден компания заявки \n \"%s\"", $grabberResult->companyTitle), array('site_id' => $site->getId()));
                }

                if ($phoneCrawler->count()) {
                    $grabberResult->phone = $phoneCrawler->text();
                    $grabberHelper->log(Logger::NOTICE, sprintf("Найден телефон заявки \n \"%s\"", $grabberResult->phone), array('site_id' => $site->getId()));
                }

                if ($emailImageCrawler->count()) {
                    preg_match('/javascript:sendmail\(\'(.+?)\'\);/ui', $emailImageCrawler->attr('href'), $emailCodeMatches);

                    if ($emailCodeMatches) {
                        $emailData = $grabberHelper->getContent($site, 'http://www.metalbulletin.ru/mail/?email='.$emailCodeMatches[1]);
                        preg_match('/open\(\'mailto:(.+?)\'\);<\/script>/ui', $emailData, $email);
                        if ($email) {
                            $grabberResult->email = $email[1];
                            $grabberHelper->log(Logger::NOTICE, sprintf("Найден email заявки \n \"%s\"", $email[1]), array('site_id' => $site->getId()));
                        }
                    }
                }

                if ($addressCrawler->count()) {
                    $grabberResult->address = $addressCrawler->text();
                    $grabberHelper->log(Logger::NOTICE, sprintf("Найден address заявки \n \"%s\"", $grabberResult->address), array('site_id' => $site->getId()));
                }

                if (preg_match('/, (.+)$/', $grabberResult->address, $cityTitleMatches)) {
                    $grabberResult->cityTitle = $cityTitleMatches[1];
                    $grabberHelper->log(Logger::NOTICE, sprintf("Найден город в объявлении \n \"%s\"", $grabberResult->cityTitle), array('site_id' => $site->getId()));
                }

                $grabberResult->siteDemandUrl = $demandLink;
                $grabberResult->siteDemandId = $demandId;
                $grabberResult->siteDemandHash = $hash;

                $grabberResults[] = $grabberResult;

                $limit--;

                if ($limit <= 0) {
                    $grabberHelper->log(Logger::NOTICE, 'Лимит исчерпан', array('limit' => $limit, 'site_id' => $site->getId()));
                    $grabberHelper->log(Logger::INFO, 'Отправка данных', array('site_id' => $site->getId()));

                    return $grabberResults;
                }
            }


        } while ($crawler->count() && $limit > 0);

        $grabberHelper->log(Logger::INFO, 'Отправка данных', array('site_id' => $site->getId()));

        return $grabberResults;
    }

    protected function getDemandLinks(Crawler $crawler)
    {
        return $crawler
            ->filter('table[style="font-family: Arial;font-size: 12px;color:black;float:left;margin-bottom:20px;"]')
            ->filter('tr')
            ->filter('td')
            ->filter('font[color ="red"]')
            ->each(function (Crawler $el) {
                return $el->parents()->filter('td')->filter('a')->attr('href');
            });
    }

    protected function wait($seconds, GrabberHelper $grabberHelper = null, Site $site = null)
    {
        if (is_array($seconds)) {
            if (!isset($seconds[0], $seconds[1])) {
                throw new \InvalidArgumentException();
            }

            $seconds = mt_rand((int)$seconds[0] , (int)$seconds[1]);
        }

        $seconds = (int)$seconds;

        if ($grabberHelper instanceof GrabberHelper && $site instanceof Site) {
            $grabberHelper->log(Logger::ALERT, sprintf('Задержка %d секунд.', $seconds), array('site_id' => $site->getId()));
        }

        sleep($seconds);
    }
}
