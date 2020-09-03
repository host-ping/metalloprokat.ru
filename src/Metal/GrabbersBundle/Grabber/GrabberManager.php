<?php

namespace Metal\GrabbersBundle\Grabber;

use Doctrine\ORM\EntityManagerInterface;
use Metal\CategoriesBundle\Service\CategoryDetectorInterface;
use Metal\DemandsBundle\Entity\Demand;
use Metal\DemandsBundle\Entity\DemandItem;
use Metal\DemandsBundle\Entity\ValueObject\ConsumerTypeProvider;
use Metal\DemandsBundle\Entity\ValueObject\DemandPeriodicityProvider;
use Metal\GrabbersBundle\Command\ParseCommand;
use Metal\GrabbersBundle\Entity\ParsedDemand;
use Metal\GrabbersBundle\Entity\Site;
use Metal\ProductsBundle\Entity\ValueObject\ProductMeasureProvider;
use Metal\ProjectBundle\Entity\ValueObject\AdminSourceTypeProvider;
use Metal\TerritorialBundle\Entity\City;
use Metal\TerritorialBundle\Service\CityService;
use Symfony\Bridge\Monolog\Logger;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\Process\Process;

class GrabberManager
{
    /**
     * @var GrabberInterface[]
     */
    protected $grabbers;

    /**
     * @var CityService
     */
    protected $cityService;

    /**
     * @var CategoryDetectorInterface
     */
    protected $categoryDetector;

    protected $grabberHelper;

    protected $projectFamily;

    protected $webDir;

    protected $rootDir;

    protected $envariment;

    /**
     * @param GrabberInterface[] $grabbers
     * @param EntityManagerInterface $em
     * @param CityService $cityService
     * @param CategoryDetectorInterface $categoryDetector
     * @param GrabberHelper $grabberHelper
     * @param string $projectFamily
     * @param string $webDir
     * @param string $rootDir
     * @param string $envariment
     */
    public function __construct(
        $grabbers,
        EntityManagerInterface $em,
        CityService $cityService,
        CategoryDetectorInterface $categoryDetector,
        GrabberHelper $grabberHelper,
        $projectFamily,
        $webDir,
        $rootDir,
        $envariment
    ) {
        $this->grabbers = $grabbers;
        $this->em = $em;
        $this->cityService = $cityService;
        $this->categoryDetector = $categoryDetector;
        $this->grabberHelper = $grabberHelper;
        $this->projectFamily = $projectFamily;
        $this->webDir = $webDir;
        $this->rootDir = $rootDir;
        $this->envariment = $envariment;
    }

    public function manager($force = false)
    {
        $siteRepository = $this->em->getRepository('MetalGrabbersBundle:Site');

        //FIXME: Придумать как обрабатывать сразу несколько категорий одновременно, хотя без проксей может быть напряжно
        $running = array();
        foreach ($this->grabbers as $grabber) {
            $site = $siteRepository->findOneBy(array('code' => $grabber->getCode()));

            if (!$site || (!$site->getIsEnabled() && !$force) || $site->getManualMode()) {
                continue;
            }

            if (isset($running[$grabber->getCode()])) {
                continue;
            }

            $running[$grabber->getCode()] = true;

            $this->runCommandInBackground(ParseCommand::COMMAND_NAME, array('site-code' => $site->getCode()));
        }
    }

    public function grab(array $enabledSites = array(), $force = false)
    {
        $siteRepository = $this->em->getRepository('MetalGrabbersBundle:Site');
        foreach ($this->grabbers as $grabber) {
            if ($enabledSites && !in_array($grabber->getCode(), $enabledSites)) {
                continue;
            }

            $site = $siteRepository->findOneBy(array('code' => $grabber->getCode()));
            if (!$site || (!$site->getIsEnabled() && !$force)) {
                continue;
            }

            $this->processSite($grabber, $site);
        }
    }

    private function processSite(GrabberInterface $grabber, Site $site)
    {
        foreach ($grabber->grab($site, $this->grabberHelper) as $grabberResult) {
            /* @var $grabberResult GrabberResult */
            $demand = new Demand();
            $demandItems = $grabberResult->demandItems;
            /* @var $demandItems DemandItem[] */
            foreach ($demandItems as $demandItem) {
                $demandItem->setTitle($this->clearTextBadWords($demandItem->getTitle(), $grabberResult->cityTitle));

                if (!$demandItem->getTitle()) {
                    continue;
                }

                if ($category = $this->categoryDetector->getCategoryByTitle($demandItem->getTitle())) {
                    $demandItem->setCategory($category);

                    $this->grabberHelper->log(
                        Logger::INFO,
                        'Найдена категория: '. $category->getTitle(),
                        array(
                            'title' => $category->getTitle(),
                            'category_id' => $category->getId(),
                            'subject' => $demandItem->getTitle(),
                            'site_id' => $site->getId()
                        )
                    );

                } else {
                    $this->grabberHelper->log(Logger::INFO, 'Категория не найдена', array('demand_item_title' => $demandItem->getTitle(), 'site_id' => $site->getId()));
                }

                if (!$demandItem->getVolumeType() || !$demandItem->getVolume()) {
                    $this->grabberHelper->log(Logger::NOTICE, 'Убираем объем', array('site_id' => $site->getId()));
                    $demandItem->setVolume(null);
                    $demandItem->setVolumeType(ProductMeasureProvider::create(ProductMeasureProvider::WITHOUT_VOLUME));
                }

                $demand->addDemandItem($demandItem);
            }

            if (!count($demand->getDemandItems())) {
                continue;
            }

            $city = null;
            if ($city = $this->cityService->findTerritory($grabberResult->cityTitle)) {
                $demand->appendBody(sprintf('Город определился по названию города: %s', $grabberResult->cityTitle));
            } elseif ($city = $this->cityService->findTerritory($grabberResult->address)) {
                $demand->appendBody(sprintf('Город определился по адресу: %s', $grabberResult->address));
            } elseif ($grabberResult->phone) {
                $cityTitle = $this->getCityByPhones($grabberResult->phone, $site);
                if ($cityTitle && $city = $this->cityService->findTerritory($cityTitle, $cityTitle)) {
                    $demand->appendBody(sprintf('Город определился по номеру телефона: %s', $city->getTitle()));
                }
            }

            if ($city instanceof City) {
                $this->grabberHelper->log(
                    Logger::INFO,
                    'Определен город: '.$city->getTitle(),
                    array('title' => $city->getTitle(), 'city_id' => $city->getId(), 'site_id' => $site->getId())
                );

                $demand->setCity($city);
            } elseif ($grabberResult->cityTitle || $grabberResult->address) {
                $cityTitle = $grabberResult->cityTitle ?: $grabberResult->address;
                $this->grabberHelper->log(
                    Logger::NOTICE,
                    'Город не определился',
                    array('subject' => $cityTitle, 'site_id' => $site->getId())
                );

                $demand->appendBody(sprintf('Город не определился: %s', $cityTitle));
            }

            if ($grabberResult->siteDemandPublicationDate) {
                $demand->appendBody(trim($grabberResult->siteDemandPublicationDate));
            }

            if ($grabberResult->createdAt instanceof \DateTime) {
                $demand->setCreatedAt($grabberResult->createdAt);
            }

            $demand->setAdminSourceTypeId(AdminSourceTypeProvider::ADMIN_SOURCE_GRABBER);
            $demand->setConsumerTypeId(ConsumerTypeProvider::CONSUMER);
            $demand->setAddress($grabberResult->address);
            $demand->setCompanyTitle($grabberResult->companyTitle);

            if ($email = $grabberResult->email) {
                $demand->setEmail($email);
            } elseif ($grabberResult->emailImageUrl) {
                $this->downloadEmail($demand, $site, $grabberResult->emailImageUrl);
            }

            $demand->setPerson('Менеджер');
            if ($grabberResult->person) {
                $demand->setPerson($grabberResult->person);
            }

            $demand->setInfo($this->clearTextBadWords($grabberResult->info, $grabberResult->cityTitle));
            $demand->setPhone($grabberResult->phone);

            if ($this->projectFamily === 'product') {
                $demand->setDemandPeriodicityId(DemandPeriodicityProvider::PERMANENT);
            }

            $this->em->persist($demand);

            $parsedDemand = new ParsedDemand();

            $url = $grabberResult->siteDemandUrl;
            if (!preg_match('/^https?:\/\//ui', $grabberResult->siteDemandUrl)) {
                $url = $site->getHost().$grabberResult->siteDemandUrl;
            }
            $parsedDemand->setUrl($url);

            $parsedDemand->setDemand($demand);
            $parsedDemand->setParsedDemandId($grabberResult->siteDemandId);
            $parsedDemand->setSite($site);
            $parsedDemand->setHash($grabberResult->siteDemandHash);

            $this->em->persist($parsedDemand);
            $this->em->flush();
        }
    }

    private function downloadEmail(Demand $demand, Site $site, $emailImageUrl)
    {
        $emailFilePath = $site->getHost().$emailImageUrl;

        $dir = $this->webDir.'/'.$demand->getEmailSubDir().'/';
        if (!is_dir($dir)) {
            mkdir($dir);
        }
        $name = sha1(microtime(true).mt_rand(0, 9999)).'.png';

        copy($emailFilePath, $dir.$name);

        $demand->setEmailFilePath($name);
    }

    private function getCityByPhones($phone, Site $site)
    {
        $matches = array();
        preg_match('/((?P<country_code>\+?8|\+?7|\+?3)[\- ]?)?(\(?(?P<code>\d{3,5})\)?[\- ]?)?(?P<phone_number>[\d\- ]{7,10})/ui', $phone, $matches);
        if ($matches) {
            //Сначала пробуем найти по коду.
            if (!empty($matches['code'])) {
                $results = $this->em->getRepository('MetalTerritorialBundle:CityCode')
                    ->findBy(array('code' => (int)$matches['code']));

                foreach ($results as $result) {
                    if ($result->getCity()) {
                        return $result->getCity()->getTitle();
                    }
                }
            }

            $phoneReplace = preg_replace('/\D/ui', '', $matches[0]);
            if (strlen($phoneReplace) === 10) {
                $phoneReplace .= '7'.$phoneReplace;
            } elseif (strlen($phoneReplace) < 10) {
                return null;
            }

            $phoneContent = $this->grabberHelper->getContent($site, sprintf('http://sbinfo.ru/operator.php?number=%d', $phoneReplace));

            $phoneCrawler = new Crawler();

            $phoneCrawler->addContent($phoneContent);
            $filter = $phoneCrawler->filter('body > table > tr > td:nth-child(2) > table > tr > td > table > tr:nth-child(1) > td > table:nth-child(13) > tr > td:nth-child(1) > table > tr:nth-child(3) > td:nth-child(2) > b');

            if ($filter->count()) {
                return $filter->text();
            }
        }

        $start = 0;
        $firstNumber = substr($phone, 0, 1);
        if ($firstNumber == '+') {
            $start = 2;
        }

        if ($firstNumber == 7) {
            $start = 1;
        }

        $phoneInt = preg_replace('/\D/ui', '', $phone);
        $i = 5;
        while ($i >= 3) {
            $virtCode = substr($phoneInt, $start, $i);
            $results = $this->em->getRepository('MetalTerritorialBundle:CityCode')
                ->findBy(array('code' => (int)$virtCode));
            foreach ($results as $result) {
                if ($result->getCity()) {
                    return $result->getCity()->getTitle();
                }
            }
            $i--;
        }

        return null;
    }

    protected function runCommandInBackground($command, $options = array())
    {
        $appDir = realpath($this->rootDir.'/../'.'app');

        $cmd = sprintf('%s/console %s', $appDir, $command);

        if (count($options) > 0) {
            $prepareOptions = array();
            foreach ($options as $key => $option) {
                if (is_array($option)) {
                    foreach ($option as $item) {
                        $prepareOptions[] = '--'.$key.'='.$item;
                    }
                } else {
                    $prepareOptions[] = '--'.$key.'='.$option;
                }
            }

            $cmd = sprintf($cmd.' %s', implode(' ', $prepareOptions));
        }

        $cmd = sprintf(
            "php %s --env=%s > /dev/null 2>&1 &",
            $cmd,
            $this->envariment
        );

        $this->grabberHelper->log(Logger::INFO, sprintf('Запуск комманды %s.', $cmd));

        $process = new Process($cmd);
        $process->run();
        if (!$process->isSuccessful()) {
            throw new \RuntimeException($process->getErrorOutput());
        }
    }

    private function clearTextBadWords($text, $cityTitle)
    {
        $patterns = array(
            '/(^|\s|\W)(НЕ ДОРОГО|Нужна|Срочно куплю|Куплю|Купим|Покупаю|Спрос|Покупаем|Покупка|Закупаем|Закупаю|Закупим|Приобретем):?\s?[!,\.]{0,5}($|\s|\W)/ui',
            '/Спрос, куплю:\s?/ui'
        );

        foreach ($patterns as $pattern) {
            $text = preg_replace($pattern, '', $text);
        }

        if ($cityTitle) {
            $text = preg_replace(sprintf('/\sв\s%s$/ui', $cityTitle), '', $text);
        }

        $text = preg_replace('#\\\\#u', '\\', $text);

        $text = preg_replace('#//#u', '/', $text);

        return trim($text);
    }
}
