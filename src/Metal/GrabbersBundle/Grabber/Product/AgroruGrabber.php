<?php


namespace Metal\GrabbersBundle\Grabber\Product;

use Metal\DemandsBundle\Entity\DemandItem;
use Metal\GrabbersBundle\Entity\Site;
use Metal\GrabbersBundle\Grabber\GrabberInterface;
use Metal\GrabbersBundle\Grabber\GrabberHelper;
use Metal\GrabbersBundle\Grabber\GrabberResult;
use Monolog\Logger;
use Symfony\Component\DomCrawler\Crawler;

class AgroruGrabber implements GrabberInterface
{
    const MAX_DUPLICATES = 40;

    private $limitDuplicates = self::MAX_DUPLICATES;

    private $categoriesUri;

    private $code;

    public function __construct($code, $categoriesUri)
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
        foreach ($this->categoriesUri as $categoryUri) {
            $grabberHelper->log(Logger::DEBUG, 'Разбираем раздел', array('category_uri' => $categoryUri, 'site_id' => $site->getId()));
            $page = 1;
            $this->limitDuplicates = self::MAX_DUPLICATES;
            $limitPage = 2;
            do {

                if ($limitPage <= 0 || $this->limitDuplicates <= 0) {
                    continue 2;
                }

                $limitPage--;

                $demandContent = mb_convert_encoding($grabberHelper->getContent($site, sprintf('%s%sct-0-p%d.htm', $site->getHost(), $categoryUri, $page)), 'UTF-8', 'windows-1251');
                $page++;
                $crawler = new Crawler();
                $crawler->addHtmlContent($demandContent);

                $filter = $crawler->filter('div.dl_t_hdr a');

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

                    preg_match('/-(\d+)\.htm$/ui', $url, $matchDemandId);

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

                    $demandContent = mb_convert_encoding($grabberHelper->getContent($site, $url), 'UTF-8', 'windows-1251');
                    $demandCrawler = new Crawler();
                    $demandCrawler->addHtmlContent($demandContent);

                    if (!$demandCrawler->count()) {
                        $grabberHelper->log(Logger::NOTICE, 'Нет данных в заявке, пытаемся распарсить следующую', array('site_id' => $site->getId()));

                        $this->limitDuplicates--;

                        continue;
                    }

                    $grabberResult = new GrabberResult();

                    //Дата публикации
                    $datePublication = $demandCrawler->filter('.dd_date');
                    if ($datePublication->count()) {
                        $datePublication = preg_replace('/пожаловаться/ui', '', $datePublication->text());
                        $datePublication = preg_replace('/,\s+просмотры\:.+/ui', '', $datePublication);
                        $grabberResult->siteDemandPublicationDate = preg_replace('/^\s*/ui', '', $datePublication);
                        $grabberResult->createdAt = $grabberHelper->parseDate($site, $grabberResult->siteDemandPublicationDate);

                        $grabberHelper->log(Logger::INFO, 'Найдена дата публикации', array('date' => $grabberResult->siteDemandPublicationDate, 'site_id' => $site->getId()));
                    }

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

                    if ($demandCrawler->filter('.dd_cont_user')->first()->count()) {
                        $grabberResult->person = trim($demandCrawler->filter('.dd_cont_user')->first()->text());
                    }

                    $matchesRow = array(
                        'Город:' => 'cityTitle',
                        'тел.:' => 'phone'
                    );

                    $countRows = $demandCrawler->filter('.dd_cont_detail tr')->count();
                    foreach ($matchesRow as $key => $property) {
                        for ($j = 1; $j <= $countRows; $j++) {
                            $row = null;
                            $filter = $demandCrawler->filter(sprintf('.dd_cont_detail tr:nth-child(%d) td.dd_f_name span', $j));
                            if ($filter->count() && ($filter->text() === $key)) {

                                if ($property === 'cityTitle') {
                                    $row = trim($demandCrawler->filter(sprintf('.dd_cont_detail tr:nth-child(%d) td:nth-child(2)', $j))->text());
                                } else {
                                    $phonesFilter = $demandCrawler->filter(sprintf('.dd_cont_detail tr:nth-child(%d) td:nth-child(2) span.dd_cont_tel', $j));
                                    if ($phonesFilter->count()) {
                                        /* @var $phoneFilter \DOMElement */
                                        foreach($phonesFilter as $phoneFilter) {
                                            $phoneCrawler = new Crawler();
                                            $phoneCrawler->add($phoneFilter);
                                            $phonePart = str_replace('Показать телефон', '', $phoneCrawler->filter('span')->first()->text() ?: '');
                                            $urlPhone = sprintf('/?show_phone=%s', urlencode($phoneCrawler->filter('a')->attr('data')));
                                            $phoneContent = mb_convert_encoding($grabberHelper->getContent($site, $urlPhone), 'UTF-8', 'windows-1251');

                                            $phonePart .= preg_replace('/[^\d\(\)\+]/ui', '', $phoneContent);
                                            $row[] = preg_replace('/\s*/ui', '', $phonePart);
                                        }

                                        $row = implode(', ', $row);
                                    }
                                }

                                if ($row) {
                                    $grabberResult->$property = $row;
                                }

                                $grabberHelper->log(Logger::INFO, 'Найдена информация', array($property => $grabberResult->$property, 'site_id' => $site->getId()));
                            }
                        }
                    }

                    $demandItem = new DemandItem();
                    $demandItemTitleFilter = $demandCrawler->filter('p.dd_text');
                    if ($demandItemTitleFilter->count()) {
                        $demandItem->setTitle($demandItemTitleFilter->text());
                        $grabberHelper->log(Logger::INFO, 'Найдена позиция', array('demandItem' => $demandItem->getTitle(), 'site_id' => $site->getId()));
                    }

                    $demandInfoFilter = $demandCrawler->filter('h1.dd_hdr');
                    if ($demandInfoFilter->count()) {
                        $demandInfo = $demandInfoFilter->text();

                        $grabberResult->setInfo($demandInfo);

                        $grabberHelper->log(Logger::INFO, 'Найдена информация', array('demandInfo' => $demandInfo, 'site_id' => $site->getId()));
                    }

                    $grabberResult->demandItems[] = $demandItem;
                    $grabberResult->siteDemandUrl = $url;
                    $grabberResult->siteDemandId = $demandId;
                    $grabberResult->siteDemandHash = $hash;
                    $grabberResult->createdAt = $grabberHelper->parseDate($site, $grabberResult->siteDemandPublicationDate);

                    yield $grabberResult;
                }

            } while ($crawler->count() || $this->limitDuplicates > 0);
        }
    }
}
