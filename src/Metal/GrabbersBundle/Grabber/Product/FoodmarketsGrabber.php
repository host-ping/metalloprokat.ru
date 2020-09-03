<?php

namespace Metal\GrabbersBundle\Grabber\Product;

use Metal\DemandsBundle\Entity\DemandItem;
use Metal\GrabbersBundle\Entity\Site;
use Metal\GrabbersBundle\Grabber\GrabberInterface;
use Metal\GrabbersBundle\Grabber\GrabberHelper;
use Metal\GrabbersBundle\Grabber\GrabberResult;
use Monolog\Logger;
use Symfony\Component\DomCrawler\Crawler;

class FoodmarketsGrabber implements GrabberInterface
{
    const MAX_DUPLICATES = 40;

    private $limitDuplicates = self::MAX_DUPLICATES;

    public function getCode()
    {
        return 'foodmarkets';
    }

    public function grab(Site $site, GrabberHelper $grabberHelper, $page = 1)
    {
        $siteHtml = $grabberHelper->getContent($site, $site->getHost());
        $siteCrawler = new Crawler();
        $siteCrawler->addHtmlContent($siteHtml);
        $sid = $siteCrawler->filter('input[name="sid"]')->first()->attr('value');

        $headers = $grabberHelper->getAuthorizationHeaders(
            $site,
            '/login/in',
            array('req_username' => $site->getLogin(), 'req_password' => $site->getPassword(), 'form_sent' => 1, 'sid' => $sid, 'redirect_url' => '/')
        );

        if (!$headers) {
            return;
        }

        preg_match('/^(.{6,15}\=.*?\;\s)/ui', $headers['Cookie'], $phpsessid);
        preg_match('/foodmara\_cookie\=(.*?\;\s)/ui', $headers['Cookie'], $foodmaraCookie);
        preg_match('/foodmara\_login\=(.*?\;\s)/ui', $headers['Cookie'], $foodmaraLogin);
        preg_match('/foodmara\_token\=(.*?\;\s)/ui', $headers['Cookie'], $foodmaraToken);
        $headers['Cookie'] = sprintf('%s%s%s%s', $phpsessid[0], $foodmaraCookie[0], $foodmaraLogin[0], $foodmaraToken[0]);
        preg_match('/^(mark\=.*?);\s/ui', $grabberHelper->getHeaders($site->getHost(), $headers)['Cookie'], $userIdentity);
        $headers['Cookie'] .= trim($userIdentity[0]);

        $limitPage = 2;

        do {

            if ($limitPage <= 0 || $this->limitDuplicates <= 0) {
                return;
            }

            $limitPage--;

            $grabberHelper->log(Logger::DEBUG, 'Номер страницы', array('number' => $page, 'site_id' => $site->getId()));

            $demandContent = $grabberHelper->getContent($site, sprintf('/blurb/view/2/page%d/', $page), $headers);
            $page++;

            $crawler = new Crawler();
            $crawler->addHtmlContent($demandContent);
            $filter = $crawler->filter('.bt3 h1 a');
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
                    return;
                }

                $url = $content->getAttribute('href');
                preg_match('/\/(\d+)$/ui', $url, $matchDemandId);
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

                $demandContent = $grabberHelper->getContent($site, $url, $headers);
                usleep(500000);

                $demandCrawler = new Crawler();
                $demandCrawler->addHtmlContent($demandContent);
                if (!$demandCrawler->count()) {
                    $grabberHelper->log(Logger::NOTICE, 'Нет данных в заявке, пытаемся распарсить следующую', array('site_id' => $site->getId()));

                    $this->limitDuplicates--;

                    continue;
                }

                $grabberResult = new GrabberResult();

                $grabberResult->siteDemandPublicationDate = $demandCrawler->filter('.comment-cor-top span')->first()->text();
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

                $grabberResult->setInfo($content->textContent);

                $author = $demandCrawler->filter('dt.poster a[rel="author"]')->first();
                if ($author->count()) {
                    $grabberResult->person = trim($author->text());
                }

                $demandItem = new DemandItem();
                $demandItemFilter = $demandCrawler->filter('div.msg p#msg_text');
                if ($demandItemFilter->count()) {
                    $demandItem->setTitle(trim($demandItemFilter->text()));
                    $grabberHelper->log(Logger::INFO, 'Найдена позиция', array('demandItem' => $demandItem->getTitle(), 'site_id' => $site->getId()));
                }

                $cityTitleFilter = $demandCrawler->filter('div.postleft')->first()->filter('dd:nth-child(2)');
                if ($cityTitleFilter->count()) {
                    $grabberResult->cityTitle = trim($cityTitleFilter->text());
                }

                $contactsRow = $demandCrawler->filter('div.msg')->filter('div');
                $matchesRow = array(
                    'Телефон' => 'phone',
                    'Контактное лицо' => 'person'
                );
                for ($j = 1; $j <= $contactsRow->count(); $j++) {
                    foreach ($matchesRow as $key => $property) {
                        $currentRow = $contactsRow->eq($j)->filter('b');
                        if ($currentRow->count() && $currentRow->text() === $key) {
                            $grabberResult->$property = str_replace($key.':', '', $contactsRow->eq($j)->text());
                            $grabberHelper->log(Logger::INFO, 'Найдена информация', array($property => $grabberResult->$property, 'site_id' => $site->getId()));
                        }
                    }
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
