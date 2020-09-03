<?php

namespace Metal\GrabbersBundle\Grabber\Metalloprokat;

use Metal\DemandsBundle\Entity\DemandItem;
use Metal\GrabbersBundle\Entity\Site;
use Metal\GrabbersBundle\Grabber\GrabberInterface;
use Metal\GrabbersBundle\Grabber\GrabberHelper;
use Metal\GrabbersBundle\Grabber\GrabberResult;
use Monolog\Logger;
use Symfony\Component\DomCrawler\Crawler;

class ArmaturkaGrabber implements GrabberInterface
{
    const MAX_DUPLICATES = 40;

    private $limitDuplicates = self::MAX_DUPLICATES;

    public function getCode()
    {
        return 'armaturka';
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

            $grabberHelper->log(Logger::DEBUG, 'Номер страницы', array('number' => $page, 'site_id' => $site->getId()));

            $demandContent = $grabberHelper->getContent($site, sprintf('/page-%d_mode-2.html', $page));
            $page++;
            $crawler = new Crawler();
            $crawler->addHtmlContent($demandContent);

            $filter = $crawler->filter('#message_small .nounder');

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
                /* @var $content \DOMElement */

                if ($this->limitDuplicates <= 0) {
                    return $grabberResults;
                }

                $url = $content->getAttribute('href');
                preg_match('/(\d+)/ui', $url, $matchDemandId);

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

                $demandContent = mb_convert_encoding($grabberHelper->getContent($site, $url), 'UTF-8', 'windows-1251');

                $demandCrawler = new Crawler();
                $demandCrawler->addHtmlContent($demandContent);
                if (!$demandCrawler->count()) {
                    $grabberHelper->log(Logger::NOTICE, 'Нет данных в заявке, пытаемся распарсить следующую', array('site_id' => $site->getId()));

                    $this->limitDuplicates--;

                    continue;
                }

                $grabberResult = new GrabberResult();

                $grabberResult->siteDemandPublicationDate = $demandCrawler->filter('.contact1 span')->first()->text();

                $grabberResult->createdAt = $grabberHelper->parseDate($site, $grabberResult->siteDemandPublicationDate);

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
                
                if (!$grabberResult->createdAt || !$grabberHelper->isToday($grabberResult->createdAt)) {
                    $this->limitDuplicates -= 5;

                    continue;
                }

                $person = $demandCrawler->filter(sprintf('li b#name-%d', $demandId))->first();
                if ($person->count()) {
                    $grabberResult->person = $person->text();
                    $grabberHelper->log(Logger::INFO, 'Найдена информация', array('person' => $grabberResult->person, 'site_id' => $site->getId()));
                }

                $email = $demandCrawler->filter('.board_contact_big li')->last();
                if ($email->count()) {
                    preg_match(sprintf('/\{fillboardreplyform\(\'%d\'\, \'(.*?)\'\)/ui', $demandId), $email->html(), $emailMatches);
                    if ($emailMatches) {
                        $grabberResult->email = $emailMatches[1];
                    }
                }

                $contacts = $demandCrawler->filter('.board_contact_big li');
                $matchesRow = array(
                    'Телефон:' => 'phone',
                    'Контактное лицо:' => 'cityTitle'
                );

                for ($j = 1; $j <= $contacts->count(); $j++) {
                    foreach ($matchesRow as $key => $property) {

                        $currentRow = $contacts->eq($j);

                        if ($property === 'cityTitle' && $currentRow->count() && preg_match(sprintf('/%s/ui', $key), $currentRow->text())) {
                            preg_match('/\((.*?)\)$/ui', $currentRow->text(), $cityTitleMatches);
                            if ($cityTitleMatches) {
                                $grabberResult->$property = trim($cityTitleMatches[1]);
                                $grabberHelper->log(Logger::INFO, 'Найдена информация', array($property => $grabberResult->$property, 'site_id' => $site->getId()));
                                continue 2;
                            }
                        }

                        if ($property === 'phone' && $currentRow->count() && preg_match(sprintf('/%s/ui', $key), $currentRow->text())) {
                            $phone = $currentRow->filter('b');
                            if ($phone->count()) {
                                $grabberResult->$property = trim($phone->text());
                                $grabberHelper->log(Logger::INFO, 'Найдена информация', array($property => $grabberResult->$property, 'site_id' => $site->getId()));
                                continue 2;
                            }
                        }
                    }
                }

                $demandItem = new DemandItem();

                $demandItemFilter = $demandCrawler->filter('td blockquote');
                if ($demandItemFilter->count()) {
                    $demandItem->setTitle(trim($demandItemFilter->text()));
                    $grabberHelper->log(Logger::INFO, 'Найдена информация', array('demandItem' => $demandItem->getTitle(), 'site_id' => $site->getId()));
                }

                $info = $demandCrawler->filter(sprintf('span#msgtitle-%d', $demandId));
                if ($info->count()) {
                    $grabberResult->setInfo($info->text());
                    $grabberHelper->log(Logger::INFO, 'Найдена информация', array('info' => $grabberResult->info, 'site_id' => $site->getId()));
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
