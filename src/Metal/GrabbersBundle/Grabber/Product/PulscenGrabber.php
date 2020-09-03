<?php

namespace Metal\GrabbersBundle\Grabber\Product;

use Metal\DemandsBundle\Entity\DemandItem;
use Metal\GrabbersBundle\Entity\Site;
use Metal\GrabbersBundle\Grabber\GrabberInterface;
use Metal\GrabbersBundle\Grabber\GrabberHelper;
use Metal\GrabbersBundle\Grabber\GrabberResult;
use Monolog\Logger;
use Symfony\Component\DomCrawler\Crawler;

class PulscenGrabber implements GrabberInterface
{
    const MAX_DUPLICATES = 40;

    private $limitDuplicates = self::MAX_DUPLICATES;

    private $categoriesUri;

    private $code;

    private $onlyTodayDemands;

    public function __construct($code, array $categoriesUri, $onlyTodayDemands = false)
    {
        $this->code = $code;
        $this->categoriesUri = $categoriesUri;
        $this->onlyTodayDemands = $onlyTodayDemands;
    }

    public function getCode()
    {
        return $this->code;
    }

    public function grab(Site $site, GrabberHelper $grabberHelper, $page = 1)
    {
        $grabberResults = array();
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

                $grabberHelper->log(Logger::DEBUG, 'Номер страницы', array('number' => $page, 'site_id' => $site->getId()));
                $demandContent = $grabberHelper->getContent($site, sprintf('%s%d', $allowedCategoryUri, $page), $headers);
                $page++;
                usleep(2500000);
                $crawler = new Crawler();
                $crawler->addHtmlContent($demandContent);

                $filter = $crawler->filter('a.js-tender-name');
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
                    preg_match('/\/(\d+)$/ui', $url, $matchDemandId);

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
                    $grabberResult->siteDemandPublicationDate = $demandCrawler->filter('li[class="sl-item important"]')->first()->text();

                    $grabberResult->createdAt = $grabberHelper->parseDate($site, $grabberResult->siteDemandPublicationDate);
                    $grabberHelper->log(Logger::NOTICE, 'Дата заявки', array('site_id' => $site->getId(), 'date' => $grabberResult->createdAt->format('d.m.Y')));

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

                    if ($this->onlyTodayDemands && !$grabberHelper->isToday($grabberResult->createdAt)){
                        $grabberHelper->log(Logger::NOTICE, 'Дата заявки устаревшая', array('site_id' => $site->getId(), 'date' => $grabberResult->createdAt->format('d.m.Y')));
                        $this->limitDuplicates -= 5;

                        continue;
                    }

                    $title = $demandCrawler->filter('H1.mb20');
                    if ($title->count()) {
                        $demandItem->setTitle(trim(str_replace('Требуется:', '', $title->text())));
                        $grabberHelper->log(Logger::INFO, 'Найдена позиция', array('demandItem' => $demandItem->getTitle(), 'site_id' => $site->getId()));
                    }

                    $ulRowsCount = $demandCrawler->filter('ul[class="vertical-list thin"]')->count();

                    if (!$ulRowsCount) {
                        $grabberHelper->log(Logger::ERROR, 'Не найдены корневые ul елементы для получения детальной информации.', array('site_id' => $site->getId(), 'date' => $grabberResult->createdAt->format('d.m.Y')));
                        $this->limitDuplicates--;

                        continue;
                    }

                    $ulRowsCount--;

                    $associateProperties = array(
                        'Регион закупки:'  => 'cityTitle',
                        'Комментарий:'     => 'info',
                        'Контактное лицо:' => 'person',
                        'Телефон:' => array(
                            'property'   => 'phone',
                            'attribute'  => 'data-phone',
                            'node_index' => 0
                        )
                    );

                    for ($j = 0; $j <= $ulRowsCount; $j++) {
                        $liRowsCount = $demandCrawler
                            ->filter('ul[class="vertical-list thin"]')
                            ->eq($j)
                            ->filter('li.vl-item')
                            ->count();

                        if ($liRowsCount) {
                            $liRowsCount--;

                            for ($h = 0; $h <= $liRowsCount; $h++) {
                                $label = $demandCrawler
                                    ->filter('ul[class="vertical-list thin"]')
                                    ->eq($j)
                                    ->filter('li.vl-item')
                                    ->eq($h)
                                    ->filter('div.vl-label')
                                ;

                                if (!$label->count() ) {
                                    continue; //TODO Записать в лог
                                }

                                foreach ($associateProperties as $key => $property) {
                                    if ($label->text() !== $key) {
                                        continue;
                                    }

                                    $propertyText = $demandCrawler
                                        ->filter('ul[class="vertical-list thin"]')
                                        ->eq($j)
                                        ->filter('li.vl-item')
                                        ->eq($h)
                                        ->filter('div.vl-content')
                                    ;

                                    if (!$propertyText->count()) {
                                        $grabberHelper->log(
                                            Logger::NOTICE,
                                            'Неудалось получить информацию',
                                            array(
                                                'bad_row' => is_array($property) ? $property['property'] : $property,
                                                'site_id' => $site->getId(),
                                                'date' => $grabberResult->createdAt->format('d.m.Y')
                                            )
                                        );

                                        continue;
                                    }

                                    if (is_array($property)) {
                                        $propertyText = $propertyText->filter('span')->getNode($property['node_index'])->getAttribute($property['attribute']);
                                        $property = $property['property'];
                                    } else {
                                        $propertyText = $propertyText->text();
                                    }

                                    $grabberResult->$property = trim(str_replace($key, '', $propertyText));
                                    $grabberHelper->log(Logger::INFO, 'Найдена информация', array($property => $grabberResult->$property, 'site_id' => $site->getId()));
                                }
                            }
                        }
                    }

                    $grabberResult->demandItems[] = $demandItem;
                    $grabberResult->siteDemandUrl = $url;
                    $grabberResult->siteDemandId = $demandId;
                    $grabberResult->siteDemandHash = $hash;

                    $grabberResults[] = $grabberResult;
                }

            } while ($crawler->count() || $this->limitDuplicates > 0);
        }

        return $grabberResults;
    }
}
