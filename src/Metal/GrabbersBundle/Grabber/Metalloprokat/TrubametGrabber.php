<?php

namespace Metal\GrabbersBundle\Grabber\Metalloprokat;

use Metal\DemandsBundle\Entity\DemandItem;
use Metal\GrabbersBundle\Entity\Site;
use Metal\GrabbersBundle\Grabber\GrabberHelper;
use Metal\GrabbersBundle\Grabber\GrabberInterface;
use Metal\GrabbersBundle\Grabber\GrabberResult;
use Symfony\Bridge\Monolog\Logger;
use Symfony\Component\DomCrawler\Crawler;

class TrubametGrabber implements GrabberInterface
{
    const MAX_DUPLICATE_COUNT = 25;

    protected $stopWords = '(Продаем|Продается|продам|Продадим|Продажа|Продаю|В наличии|Предлагаем|Оказываем услуги)';

    public function getCode()
    {
        return 'trubamet';
    }

    public function grab(Site $site, GrabberHelper $grabberHelper, $page = 0)
    {
        $categories = array(
            0 => 'Труба',
            1 => 'Балка',
            2 => 'Металлопрокат'
        );

        foreach ($categories as $categoryId => $categoryTitle) {

            $grabberHelper->log(Logger::DEBUG, 'Разбираем категорию '.$categoryTitle, array('page' => $page, 'site_id' => $site->getId()));

            $limit = 50;
            $duplicateCount = 0;

            do {

                $grabberHelper->log(Logger::DEBUG, 'Страница.', array('page' => $page, 'site_id' => $site->getId()));
                $uri = sprintf('/?p=doska&act=show&pos=%d&srt=1&razdId=%d', $page, $categoryId);
                $crawler = new Crawler();
                $crawler->addHtmlContent(
                    mb_convert_encoding($grabberHelper->getContent($site, $uri), 'UTF-8', 'windows-1251')
                );

                $page++;

                $demands = $crawler->filter('div#doska')->filter('div[id^="m"]');
                /* @var $demands \DOMElement[] */

                if (!count($demands)) {
                    $grabberHelper->log(Logger::ERROR, 'Список заявок не найден', array('site_id' => $site->getId()));

                    break;
                }

                foreach ($demands as $demand) {
                    if ($duplicateCount >= self::MAX_DUPLICATE_COUNT) {
                        $grabberHelper->log(Logger::ALERT, 'Лимит дубликатов исчерпан.', array('site_id' => $site->getId()));

                        break 2;
                    }

                    $demandId = (int)preg_replace('/\D/u', '', $demand->getAttribute('id'));

                    $grabberHelper->log(Logger::INFO, 'Найден id заявки.', array('siteDemandId' => $demandId, 'site_id' => $site->getId()));

                    $hash = md5($demandId);

                    if ($grabberHelper->isAdded(array('hash' => $hash, 'site' => $site->getId()))) {
                        $duplicateCount++;
                        $grabberHelper->log(Logger::INFO, GrabberInterface::MESSAGE_DEMAND_IS_ALREADY_EXIST, array('site_id' => $site->getId(), 'hash' => $hash));

                        continue;
                    }

                    $grabberResult = new GrabberResult();
                    $tdCrawler = (new Crawler($demand))->filter('td');
                    /* @var $tdCrawler \DOMElement[] */
                    foreach ($tdCrawler as $tdElement) {
                        if (!$grabberResult->info && $tdElement->getAttribute('class') === 'msgText') {
                            $grabberResult->setInfo($tdElement->textContent);
                            $grabberHelper->log(Logger::INFO, 'Найдено описание.', array('site_id' => $site->getId()));

                            if ($firstRow = explode("\n", $tdElement->textContent, 2)) {
                                $demandItem = new DemandItem();
                                $demandItem->setTitle(trim($firstRow[0]));
                                $grabberResult->demandItems[] = $demandItem;

                                $grabberHelper->log(
                                    Logger::INFO,
                                    'На основе описания создаем позицию.',
                                    array(
                                        'site_id' => $site->getId(),
                                        'text' => $demandItem->getTitle()
                                    )
                                );
                            }
                        }

                        if (!$grabberResult->companyTitle && $tdElement->textContent === 'Организация:') {
                            if ($nextSibling = $tdElement->nextSibling) {
                                $grabberResult->companyTitle = trim(str_replace('(отправить сообщение)', '', $nextSibling->textContent));
                                $grabberHelper->log(Logger::INFO, 'Найдена компания.', array('company_title' => $grabberResult->companyTitle, 'site_id' => $site->getId()));
                            }
                        }

                        if (!$grabberResult->address && $tdElement->textContent === 'Адрес:') {
                            if ($nextSibling = $tdElement->nextSibling) {
                                $grabberResult->address = $nextSibling->textContent;
                                $grabberResult->cityTitle = $nextSibling->textContent;
                                $grabberHelper->log(Logger::INFO, 'Найден адрес.', array('address' => $grabberResult->address, 'site_id' => $site->getId()));
                            }
                        }

                        if (!$grabberResult->person && $tdElement->textContent === 'Конт.лицо:') {
                            if ($nextSibling = $tdElement->nextSibling) {
                                $grabberResult->person = trim(str_replace('(отправить сообщение)', '', $nextSibling->textContent));
                                $grabberHelper->log(Logger::INFO, 'Найдено контактное лицо.', array('person' => $grabberResult->person, 'site_id' => $site->getId()));
                            }
                        }

                        if (!$grabberResult->phone && $tdElement->textContent === 'Тел.:') {
                            if ($nextSibling = $tdElement->nextSibling) {
                                $grabberResult->phone = $nextSibling->textContent;
                                $grabberHelper->log(Logger::INFO, 'Найден телефон.', array('person' => $grabberResult->phone, 'site_id' => $site->getId()));
                            }
                        }
                    }


                    if (preg_match_all(sprintf('/%s/ui', $this->stopWords), $grabberResult->info, $stopWordMatches)) {
                        $grabberHelper->log(Logger::INFO, 'В описании найдены стоп слова, пропускаем.', array('stop_words' => implode(', ', $stopWordMatches[1]), 'site_id' => $site->getId()));
                        continue;
                    }

                    $grabberResult->siteDemandId = $demandId;
                    $grabberResult->siteDemandHash = $hash;

                    yield $grabberResult;

                    $limit--;

                    if ($limit <= 0) {
                        $grabberHelper->log(Logger::NOTICE, 'Лимит для категории исчерпан, разбираем следующую', array('limit' => $limit, 'site_id' => $site->getId()));

                        break 2;
                    }
                }
            } while ($crawler->count() && $limit > 0);
        }

        $grabberHelper->log(Logger::INFO, 'Отправка данных', array('site_id' => $site->getId()));
    }
}
