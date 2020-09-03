<?php

namespace Metal\GrabbersBundle\Grabber\Metalloprokat;

use Metal\DemandsBundle\Entity\DemandItem;
use Metal\GrabbersBundle\Entity\Site;
use Metal\GrabbersBundle\Grabber\GrabberInterface;
use Metal\GrabbersBundle\Grabber\GrabberHelper;
use Metal\GrabbersBundle\Grabber\GrabberResult;
use Monolog\Logger;
use Symfony\Component\DomCrawler\Crawler;

class MetallorusGrabber implements GrabberInterface
{
    public function getCode()
    {
        return 'metallorus';
    }

    public function grab(Site $site, GrabberHelper $grabberHelper, $page = 1)
    {
        $beforeHeaders = $grabberHelper->getHeaders($site->getHost());

        if (!isset($beforeHeaders['Cookie'])) {
            $grabberHelper->log(Logger::ERROR, 'Нет первоначальных заголовков', array('site_id' => $site->getId()));
            return;
        }

        preg_match('/^.+\=.{32}\;/ui', $beforeHeaders['Cookie'], $beforeHeadersMatch);

        if (!$beforeHeadersMatch) {
            $grabberHelper->log(Logger::ERROR, 'Не найдена кука', array('cookie' => $beforeHeaders['Cookie'], 'site_id' => $site->getId()));
            return;
        }

        $beforeHeaders = array('Cookie' => $beforeHeadersMatch[0]);

        $headers = $grabberHelper->getAuthorizationHeaders(
            $site,
            '/login',
            array(
                'username' => $site->getLogin(),
                'password' => $site->getPassword(),
                'service' => 'login',
                'serviceButtonValue' => 'login'
            ),
            $beforeHeaders
        );

        if (!$headers) {
            $grabberHelper->log(Logger::INFO, 'Нет заголовков', array('site_id' => $site->getId()));

            return;
        }

        $associationSubdomains = array(
            'spb.' => 'Санкт-Петербург',
            'novosibirsk.' => 'Новосибирск',
            'sverdlovsk.' => 'Екатеринбург',
            'chel.' => 'Челябинск',
            '' => 'Москва'
        );

        foreach ($associationSubdomains as $subdomain => $cityTitle) {
            $grabberHelper->log(
                Logger::NOTICE,
                'Разбераем город: '.$cityTitle,
                array('site_id' => $site->getId())
            );

            $host = $this->getHost($site, $subdomain);
            $limit = 30;

            do {
                $content = $grabberHelper->getContent($site, $host.sprintf('/zayavki/page-%d', $page), $headers);

                $crawler = new Crawler();
                $crawler->addHtmlContent($content);

                $filter = $crawler->filter('.zayavka a.z_header');

                if (!iterator_count($filter)) {
                    $grabberHelper->log(Logger::ERROR, 'Список заявок не найден', array('site_id' => $site->getId()));

                    continue 2;
                }

                foreach ($filter as $content) {
                    $contentCrawler = new Crawler($content);

                    $href = $contentCrawler->attr('href');

                    preg_match('/\/(\d+)$/ui', $href, $matchDemandId);
                    if (!$matchDemandId) {
                        $grabberHelper->log(Logger::ERROR, 'Не найдена ссылка на заявку', array('site_id' => $site->getId()));

                        continue 2;
                    }

                    $demandId = $matchDemandId[1];
                    $hash = md5($demandId);
                    if ($grabberHelper->isAdded(array('hash' => $hash, 'site' => $site->getId()))) {
                        $grabberHelper->log(Logger::INFO, GrabberInterface::MESSAGE_DEMAND_IS_ALREADY_EXIST, array('site_id' => $site->getId(), 'hash' => $hash));

                        continue 3;
                    }

                    $demandContent = $grabberHelper->getContent($site, $href, $headers);

                    $demandCrawler = new Crawler();
                    $demandCrawler->addHtmlContent($demandContent);

                    if (!$demandCrawler->count()) {
                        $grabberHelper->log(Logger::NOTICE, 'Нет данных в заявке, пытаемся распарсить следующую', array('site_id' => $site->getId()));

                        continue;
                    }

                    $grabberResult = new GrabberResult();
                    $matchesRow = array(
                        'Заказчик:'    => 'person',
                        'Телефон:'     => 'phone',
                        'e-mail:'      => 'email',
                        'Комментарий:' => 'info'
                    );

                    $countRows = $demandCrawler->filter('table[class="zakazchik"] tr')->count();
                    foreach ($matchesRow as $key => $property) {
                        for ($i = 1; $i <= $countRows; $i++) {
                            if ($demandCrawler->filter(sprintf('table[class="zakazchik"] tr:nth-child(%d) td', $i))->first()->text() == $key) {
                                if ($property === 'email' && $demandCrawler->filter(sprintf('table[class="zakazchik"] tr:nth-child(%d) td', $i))->last()->text() == '') {
                                    $emailPath = $demandCrawler->filter(sprintf('table[class="zakazchik"] tr:nth-child(%d) td', $i))->last()
                                        ->filter('img')
                                        ->attr('src');
                                    $grabberResult->emailImageUrl = $emailPath;
                                    $grabberResult->$property = null;
                                    $grabberHelper->log(Logger::INFO, 'Найдена информация', array('emailImageUrl' => $grabberResult->emailImageUrl, 'site_id' => $site->getId()));
                                } else {
                                    $grabberResult->$property = $demandCrawler->filter(sprintf('table[class="zakazchik"] tr:nth-child(%d) td', $i))->last()->text();
                                }

                                $grabberHelper->log(Logger::INFO, 'Найдена информация', array($property => $grabberResult->$property, 'site_id' => $site->getId()));
                            }
                        }
                    }

                    $grabberResult->cityTitle = $cityTitle;

                    $demandItems = $demandCrawler->filter('span.z_metall_item');
                    foreach ($demandItems as $item) {
                        $demandItem = new DemandItem();
                        $demandItemCrawler = new Crawler($item);
                        $demandItem->setTitle(trim(preg_replace('/^\d+/ui', '', $demandItemCrawler->text())));
                        $grabberResult->demandItems[] = $demandItem;

                        $grabberHelper->log(
                            Logger::INFO,
                            'Найдена позиция',
                            array('title' => $demandItem->getTitle(), 'site_id' => $site->getId())
                        );
                    }

                    $grabberResult->siteDemandUrl = $href;
                    $grabberResult->siteDemandId = $demandId;
                    $grabberResult->siteDemandHash = $hash;

                    yield $grabberResult;

                    $limit--;

                    if ($limit === 0) {
                        $grabberHelper->log(Logger::NOTICE, 'Лимит исчерпан', array('limit' => $limit, 'site_id' => $site->getId()));

                        continue 3;
                    }

                }

                $page++;
            } while ($crawler->count() || $limit > 0);

        }
        $grabberHelper->log(Logger::INFO, 'Отправка данных', array('site_id' => $site->getId()));
    }

    private function getHost(Site $site, $subdomain)
    {
        return str_replace('http://', sprintf('http://%s', $subdomain), $site->getHost());
    }
}
