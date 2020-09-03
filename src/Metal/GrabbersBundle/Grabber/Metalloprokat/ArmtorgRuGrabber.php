<?php

namespace Metal\GrabbersBundle\Grabber\Metalloprokat;

use Metal\DemandsBundle\Entity\DemandItem;
use Metal\GrabbersBundle\Entity\Site;
use Metal\GrabbersBundle\Grabber\GrabberInterface;
use Metal\GrabbersBundle\Grabber\GrabberHelper;
use Metal\GrabbersBundle\Grabber\GrabberResult;
use Monolog\Logger;
use Symfony\Component\DomCrawler\Crawler;

class ArmtorgRuGrabber implements GrabberInterface
{
    public function getCode()
    {
        return 'armtorg';
    }

    public function grab(Site $site, GrabberHelper $grabberHelper, $page = 1)
    {
        $limit = 50;

        do {
            $crawler = new Crawler();
            $crawler->addHtmlContent($grabberHelper->getContent($site, sprintf('/board/buy/%d/?show=standart', $page)));

            $filter = $crawler->filter('td a[class="title board-swap"]');

            if (!iterator_count($filter)) {
                $grabberHelper->log(Logger::ERROR, 'Список заявок не найден', array('site_id' => $site->getId()));

                return;
            }

            foreach ($filter as $content) {

                if ($limit <= 0) {
                    $grabberHelper->log(Logger::NOTICE, 'Лимит исчерпан', array('limit' => $limit, 'site_id' => $site->getId()));

                    return;
                }

                $contentCrawler = new Crawler($content);
                $href = $contentCrawler->attr('href');
                preg_match('/\/board\-(\d+)\-/ui', $href, $matchDemandId);
                $demandId = $matchDemandId[1];

                $hash = md5($contentCrawler->text().'-'.$demandId);

                preg_match('/(\/board\-\d+)\-/ui', $href, $matchHref);
                if (!$matchHref) {
                    $grabberHelper->log(Logger::ERROR, 'Не найдена ссылка на заявку', array('site_id' => $site->getId()));

                    continue;
                }

                sleep(3);
                $demandCrawler = new Crawler();
                $demandCrawler->addHtmlContent($grabberHelper->getContent($site, $matchHref[1]));

                if (!$demandCrawler->count()) {
                    $grabberHelper->log(Logger::NOTICE, 'Нет данных в заявке, пытаемся распарсить следующую', array('site_id' => $site->getId()));

                    continue;
                }

                $grabberResult = new GrabberResult();
                $grabberResult->siteDemandPublicationDate = trim($demandCrawler->filter('div[class="board-info__date detail_top"]')->text());

                $grabberResult->siteDemandPublicationDate = str_replace('Дата подачи: ', '', $grabberResult->siteDemandPublicationDate);

                $grabberHelper->log(Logger::NOTICE, sprintf('Найдена дата публикации "%s"', $grabberResult->siteDemandPublicationDate), array('site_id' => $site->getId()));
                $grabberResult->createdAt = $grabberHelper->parseDate($site, $grabberResult->siteDemandPublicationDate);

                if ($grabberHelper->isAdded(array('hash' => $hash, 'site' => $site->getId()), $grabberResult->createdAt)) {
                    $grabberHelper->log(Logger::INFO, GrabberInterface::MESSAGE_DEMAND_IS_ALREADY_EXIST, array('site_id' => $site->getId(), 'hash' => $hash));
                    $limit--;

                    continue;
                }

                $titleCrawler = $demandCrawler->filter('.item-content span[itemprop="description"]');
                if ($titleCrawler->count()) {
                    $grabberResult->setInfo($titleCrawler->text());
                    $grabberHelper->log(
                        Logger::INFO,
                        'Найдена информация о заявке',
                        array('site_id' => $site->getId())
                    );
                }

                $demandItemContent = $demandCrawler->filter('h1.first');
                if ($demandItemContent->count()) {
                    $demandItem = new DemandItem();
                    $demandItem->setTitle(trim($demandItemContent->text()));
                    $grabberResult->demandItems[] = $demandItem;

                    $grabberHelper->log(
                        Logger::INFO,
                        'Найден заголовок, заносим в позицию.',
                        array('site_id' => $site->getId())
                    );
                }

                $infoCrawler = $demandCrawler
                    ->filter('fieldset')
                    ->filter('table[width="100%"]')
                    ->filter('td[valign="top"]')
                    ->filter('p');

                if ($infoCrawler->count()) {
                    $grabberResult->setInfo($infoCrawler->text());
                }

                $i = 0;
                $selector = $demandCrawler->filter('fieldset dl[class="dl-horizontal dl-board"]');
                $demandCrawler
                    ->filter('fieldset dl[class="dl-horizontal dl-board"]')
                    ->filter('dt')
                    ->each(function (Crawler $crawler) use (&$i, $selector, $grabberResult, $grabberHelper, $site) {

                        switch ($crawler->text()) {
                            case 'ФИО':
                                $grabberResult->person = $selector->filter('dd')->eq($i)->text();
                                $grabberHelper->log(Logger::NOTICE, sprintf('Найден автор заявки "%s"', $grabberResult->person), array('site_id' => $site->getId()));
                                break;
                            
                            case 'Организация':
                                $grabberResult->companyTitle = $selector->filter('dd')->eq($i)->text();
                                $grabberHelper->log(Logger::NOTICE, sprintf('Найдена компания заявки "%s"', $grabberResult->companyTitle), array('site_id' => $site->getId()));
                                break;

                            case 'Телефон':
                                $grabberResult->phone = $selector->filter('dd')->eq($i)->text();
                                $grabberHelper->log(Logger::NOTICE, sprintf('Найден телефон заявки "%s"', $grabberResult->phone), array('site_id' => $site->getId()));
                                break;

                            case 'E-mail':
                                $grabberResult->email = $selector->filter('dd')->eq($i)->text();
                                $grabberHelper->log(Logger::NOTICE, sprintf('Найден емейл заявки "%s"', $grabberResult->email), array('site_id' => $site->getId()));
                                break;

                            case 'Город':
                                $grabberResult->cityTitle = $selector->filter('dd')->eq($i)->text();
                                $grabberHelper->log(Logger::NOTICE, sprintf('Найден город в объявлении "%s"', $grabberResult->cityTitle), array('site_id' => $site->getId()));
                                break;
                        }

                        $i++;
                    });

                $grabberResult->siteDemandUrl = $href;
                $grabberResult->siteDemandId = $demandId;
                $grabberResult->siteDemandHash = $hash;

                yield $grabberResult;

                $limit--;
            }

            $page++;
        } while ($crawler->count() || $limit > 0);

        $grabberHelper->log(Logger::INFO, 'Отправка данных', array('site_id' => $site->getId()));
    }
}
