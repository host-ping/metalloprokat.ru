<?php

namespace Metal\GrabbersBundle\Grabber\Product;

use Metal\DemandsBundle\Entity\DemandItem;
use Metal\GrabbersBundle\Entity\Site;
use Metal\GrabbersBundle\Grabber\GrabberInterface;
use Metal\GrabbersBundle\Grabber\GrabberHelper;
use Metal\GrabbersBundle\Grabber\GrabberResult;
use Monolog\Logger;
use Symfony\Component\DomCrawler\Crawler;

class InlineGrabber implements GrabberInterface
{
    const MAX_DUPLICATES = 10;

    protected $code;

    private $limitDuplicates = self::MAX_DUPLICATES;

    public function __construct($code)
    {
        $this->code = $code;
    }

    public function getCode()
    {
        return $this->code;
    }

    public function grab(Site $site, GrabberHelper $grabberHelper, $page = 1)
    {
        $headers = $grabberHelper->getAuthorizationHeaders(
            $site,
            '/auth?form=1&ref=/people',
            array('login' => $site->getLogin(), 'passwd' => $site->getPassword(), 'remember_me' => 'on')
        );

        if (!$headers) {
            $grabberHelper->log(Logger::ERROR, 'Не полученны заголовки авторизации.',
                array(
                    'site_id' => $site->getId()
                )
            );

            return;
        }

        $limitPage = 2;
        do {

            if ($limitPage <= 0 || $this->limitDuplicates <= 0) {
                return;
            }

            $limitPage--;

            $pageUri = null;

            if ($page > 1) {
                $pageUri = '&p='.$page;
            }

            $page++;
            $demandContent = $grabberHelper->getContent($site, sprintf('/trade?deal=buy%s', $pageUri), $headers);
            usleep(2500000);
            $crawler = new Crawler();
            $crawler->addHtmlContent($demandContent);

            $filter = $crawler->filter('tr[itemprop="itemListElement"]');

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

                $contentCrawler = new Crawler($content);
                $contentCrawler->addContent($content->textContent);

                $url = $contentCrawler->filter('td.td-name a.hproduct')->attr('href');
                preg_match('/-(\d+)$/ui', $url, $matchDemandId);

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
                $hash = md5($url);
                $dateStr = $contentCrawler->filter('.td-date span')->attr('title');
                $createdAt = $grabberHelper->parseDate($site, $dateStr) ?: null;
                if ($grabberHelper->isAdded(array('hash' => $hash, 'site' => $site->getId()), $createdAt)) {
                    if ($createdAt) {
                        $grabberHelper->log(
                            Logger::NOTICE,
                            GrabberInterface::MESSAGE_DEMAND_IS_ALREADY_EXIST,
                            array(
                                'site_id' => $site->getId(),
                                'hash' => $hash,
                                'page' => $page - 1,
                                'new_created_at' => $createdAt->format('d.m.Y H:i:s')
                            )
                        );
                    }

                    $this->limitDuplicates--;

                    continue;
                }

                $demandContent = $grabberHelper->getContent($site, $url, $headers);
                usleep(3000000);

                $demandCrawler = new Crawler();
                $demandCrawler->addHtmlContent($demandContent);
                if (!$demandCrawler->count()) {
                    $grabberHelper->log(Logger::NOTICE, 'Нет данных в заявке, пытаемся распарсить следующую', array('site_id' => $site->getId()));

                    $this->limitDuplicates--;

                    continue;
                }

                $grabberResult = new GrabberResult();

                $publicationDate = $demandCrawler->filter('.info-date')->first()->text();

                $publicationDate = preg_replace('/Размещено/', '', $publicationDate);
                $publicationDate = preg_replace('/^\s{2,}/', '', $publicationDate);
                $publicationDate = preg_replace('/\s{2,}$/', '', $publicationDate);
                $grabberResult->siteDemandPublicationDate = $publicationDate;
                $grabberResult->createdAt = $grabberHelper->parseDate($site, $publicationDate);

                if ($demandCrawler->filter('.tr-name strong')->first()->count()) {
                    $grabberResult->person = $demandCrawler->filter('.tr-name strong')->first()->text();
                }

                if ($demandCrawler->filter('.tr-name .tr-company')->first()->count()) {
                    $grabberResult->companyTitle = $demandCrawler->filter('.tr-name .tr-company')->first()->text();
                }

                $matchesRow = array(
                    'Моб. тел.:' => 'phone',
                    'Телефон:' => 'phone',
                    'Город:' => 'address'
                );

                $countRows = $demandCrawler->filter('div[class="tdcol tdcol-1"] p')->count();
                foreach ($matchesRow as $key => $property) {
                    for ($j = 1; $j <= $countRows; $j++) {
                        $row = null;
                        $filter = $demandCrawler->filter(sprintf('div[class="tdcol tdcol-1"] p:nth-child(%d) strong.label', $j));
                        if ($filter->count() && ($filter->text() === $key)) {
                            $row = $demandCrawler->filter(sprintf('div[class="tdcol tdcol-1"] p:nth-child(%d)', $j))->text();
                            $row = trim(str_replace('Телефон:', '', $row));
                            $row = trim(str_replace('Город:', '', $row));
                            $row = trim(str_replace('Моб. тел.:', '', $row));

                            if ($row === 'подождите...') {
                                $htmlPhone = $demandCrawler->filter(sprintf('div[class="tdcol tdcol-1"] p:nth-child(%d)', $j))->html();
                                preg_match('/data\-val\=\"(.+)\"\>подождите\.\.\./ui', $htmlPhone, $phoneMatch);
                                if ('скрыт настройками приватности' !== $phoneMatch[1]) {
                                    $row = $phoneMatch[1];
                                }
                            }

                            if ($row) {
                                $grabberResult->$property .= ' '.$row;
                            }

                            $grabberHelper->log(Logger::INFO, 'Найдена информация', array($property => $grabberResult->$property, 'site_id' => $site->getId()));
                        }
                    }
                }

                $demandItem = new DemandItem();
                if ($demandCrawler->filter('div[class="tdcol tdcol-text"]')->count()) {
                    $demandItem->setTitle(trim($demandCrawler->filter('div[class="tdcol tdcol-text"]')->text()));
                    $grabberHelper->log(Logger::INFO, 'Найдена позиция', array('demandItem' => $demandItem->getTitle(), 'site_id' => $site->getId()));
                }

                $itemTitle = $demandCrawler->filter('.trade-text h1')->text();
                preg_match('/в\s(.+)$/ui', $itemTitle, $matches);
                if ($matches) {
                    $grabberResult->cityTitle = trim($matches[1]);
                    $grabberResult->setInfo(str_replace($matches[0], '', $itemTitle));
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
