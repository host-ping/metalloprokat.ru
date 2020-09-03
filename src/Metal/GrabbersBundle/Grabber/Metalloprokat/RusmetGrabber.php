<?php

namespace Metal\GrabbersBundle\Grabber\Metalloprokat;

use Metal\DemandsBundle\Entity\DemandItem;
use Metal\GrabbersBundle\Entity\Site;
use Metal\GrabbersBundle\Grabber\GrabberInterface;
use Metal\GrabbersBundle\Grabber\GrabberHelper;
use Metal\GrabbersBundle\Grabber\GrabberResult;
use Monolog\Logger;
use Symfony\Component\DomCrawler\Crawler;

class RusmetGrabber implements GrabberInterface
{
    const MAX_DUPLICATE_COUNT = 15;

    public function getCode()
    {
        return 'rusmet';
    }

    public function grab(Site $site, GrabberHelper $grabberHelper, $page = 0)
    {
        $limit = 50;
        $duplicateCount = 0;

        do {
            
            $uri = sprintf('/board/list/section_2/page_%d/?otraslid=2', $page);

            $crawler = new Crawler();
            $crawler->addHtmlContent($grabberHelper->getContent($site, $uri));

            $page++;

            $demandsLinks = $this->getDemandLinks($crawler);

            if (!count($demandsLinks)) {
                $grabberHelper->log(Logger::ERROR, 'Список заявок не найден', array('site_id' => $site->getId()));

                return;
            }

            foreach ($demandsLinks as $demandLink) {

                if ($duplicateCount >= self::MAX_DUPLICATE_COUNT) {
                    $grabberHelper->log(Logger::ALERT, 'Лимит дубликатов исчерпан.', array('site_id' => $site->getId()));

                    return;
                }

                preg_match('/\/(\d+)\/?$/ui', $demandLink, $matches);

                $demandId = $matches[1];
                $hash = md5($demandId);

                $demandCrawler = new Crawler();

                $demandCrawler->addHtmlContent(mb_convert_encoding($grabberHelper->getContent($site, $demandLink), 'UTF-8', 'windows-1251'));

                if (!$demandCrawler->count()) {
                    $grabberHelper->log(Logger::ALERT, 'Нет данных в заявке, пытаемся распарсить следующую', array('site_id' => $site->getId()));

                    continue;
                }

                $titleCrawler = $demandCrawler
                    ->filter('#main_text')
                    ->filter('h1')
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

                $grabberResult = new GrabberResult();

                $demandPublicationDate = $demandCrawler
                    ->filter('#board_show_container')
                    ->filter('i')
                    ->each(function (Crawler $crawler) {
                        preg_match('/добавлено (.+?) и действует/ui', $crawler->text(), $matches);
                        foreach ($crawler as $node) {
                            $node->parentNode->removeChild($node);
                        }

                        if ($matches) {
                            return $matches[1];
                        }

                        return null;
                    })
                ;

                if ($demandPublicationDate = array_filter($demandPublicationDate)) {
                    $grabberResult->createdAt = $grabberHelper->parseDate($site, $demandPublicationDate[0]);
                    $grabberResult->siteDemandPublicationDate = $demandPublicationDate[0];
                }

                if ($grabberHelper->isAdded(array('hash' => $hash, 'site' => $site->getId()), $grabberResult->createdAt)) {
                    $duplicateCount++;
                    $grabberHelper->log(Logger::INFO, GrabberInterface::MESSAGE_DEMAND_IS_ALREADY_EXIST, array('site_id' => $site->getId(), 'hash' => $hash));

                    continue;
                }

                $demandItem = new DemandItem();

                $demandItem->setTitle($title);
                $grabberResult->demandItems[] = $demandItem;

                $grabberHelper->log(Logger::NOTICE, sprintf("Найден заголовок заявки \n \"%s\"", $title), array('site_id' => $site->getId()));


                $associatedRows = array(
                    'Контакт:' => 'person',
                    'Телефон:' => 'phone',
                    'Адрес:' => 'address',
                    'Учетная запись:' => 'companyTitle',
                );

                $demandCrawler
                    ->filter('#board_show_container')
                    ->filter('div[style="width:90%; padding:5px;"]')
                    ->filter('table')
                    ->first()
                    ->filter('tr')
                    ->each(function (Crawler $crawler) use ($associatedRows, $grabberResult) {
                        $elCrawler = $crawler
                            ->filter('td')
                            ->filter('strong')
                        ;

                        if (!$elCrawler->count()) {
                            return null;
                        }

                        foreach ($associatedRows as $key => $associatedRow) {
                            if ($elCrawler->text() === $key) {
                                $grabberResult->$associatedRow = $crawler->filter('td')->last()->text();
                            }
                        }
                    })
                ;

                $grabberResult->companyTitle = str_replace('| Скрыть объявления этой компании', '', $grabberResult->companyTitle);

                $demandCrawler
                    ->filter('#board_show_container')
                    ->filter('div[style="width:90%; padding:5px;"]')
                    ->each(function (Crawler $crawler) {
                        foreach ($crawler as $node) {
                            $node->parentNode->removeChild($node);
                        }
                    })
                ;

                $demandCrawler
                    ->filter('#board_show_container')
                    ->filter('div[style="background:#fff; margin:-5px; padding:10px; text-align:right;"]')
                    ->each(function (Crawler $crawler) {
                        foreach ($crawler as $node) {
                            $node->parentNode->removeChild($node);
                        }
                    })
                ;

                $text = $demandCrawler
                    ->filter('#board_show_container')
                    ->text();

                $text = str_replace('Изображения объявления', '', $text);

                $grabberResult->setInfo($text);

                $grabberResult->siteDemandUrl = $demandLink;
                $grabberResult->siteDemandId = $demandId;
                $grabberResult->siteDemandHash = $hash;

                yield $grabberResult;

                $limit--;

                if ($limit <= 0) {
                    $grabberHelper->log(Logger::NOTICE, 'Лимит исчерпан', array('limit' => $limit, 'site_id' => $site->getId()));
                    $grabberHelper->log(Logger::INFO, 'Отправка данных', array('site_id' => $site->getId()));
                    return;
                }
            }

        } while ($crawler->count() && $limit > 0);

        $grabberHelper->log(Logger::INFO, 'Отправка данных', array('site_id' => $site->getId()));
    }

    protected function getDemandLinks(Crawler $crawler)
    {
        return $crawler
            ->filter('a.board_list_header')
            ->each(function (Crawler $el) {
                return $el->attr('href');
            });
    }
}
