<?php

namespace Metal\ContentBundle\EventListener;

use Brouzie\Bundle\HelpersBundle\Helper\HelperFactory;
use Doctrine\ORM\EntityManager;
use Metal\ContentBundle\Entity\Category;
use Metal\ContentBundle\Entity\ValueObject\StatusTypeProvider;
use Metal\ProjectBundle\Repository\SiteRepository;
use Presta\SitemapBundle\Event\SitemapPopulateEvent;
use Presta\SitemapBundle\Service\SitemapListenerInterface;
use Presta\SitemapBundle\Sitemap\Url\UrlConcrete;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;

class ContentSitemapListener implements SitemapListenerInterface
{
    private $router;

    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var Request
     */
    private $request;

    private $hostnameMap;

    /**
     * @var HelperFactory
     */
    private $helperFactory;

    private $baseHost;

    private $projectFamily;

    private $section = '';

    private $container;

    public function __construct(RouterInterface $router, EntityManager $em, HelperFactory $helperFactory, $hostnameMap, $baseHost, $projectFamily, ContainerInterface $container)
    {
        $this->router = $router;
        $this->em = $em;
        $this->helperFactory = $helperFactory;
        $this->hostnameMap = $hostnameMap;
        $this->baseHost = $baseHost;
        $this->projectFamily = $projectFamily;
        $this->container = $container;
    }

    public function setRequest(Request $request = null)
    {
        $this->request = $request;
    }

    public function populateSitemap(SitemapPopulateEvent $event)
    {
        if ($this->projectFamily !== 'stroy') {
            return;
        }

        list($subdomain, $domain) = explode('.', $this->request->getHost(), 2);
        preg_match('/^(\w+)\./ui', $this->baseHost, $matches);
        $this->section = $matches[1];

        if (0 !== strpos($domain, $this->section)) {
            return;
        }

        $siteRepository = $this->em->getRepository('MetalProjectBundle:Site');
        /* @var $siteRepository SiteRepository */

        $siteRepository->disableLogging();

        $now = new \DateTime();

        $this->buildMainPage($event, $now);

        $this->buildCategories($event, $now);

        $this->buildTopics($event);

        $this->buildQuestions($event);

        $siteRepository->restoreLogging();
    }

    private function buildMainPage(SitemapPopulateEvent $event, $now)
    {
        $event->getGenerator()->addUrl(
            new UrlConcrete(
                $this->getUrl(),
                $now,
                UrlConcrete::CHANGEFREQ_DAILY,
                0.8
            ),
            $this->section
        );
    }

    private function buildTopics(SitemapPopulateEvent $event)
    {
        $topicsRepository = $this->em->getRepository('MetalContentBundle:Topic');

        $topics = $topicsRepository->createQueryBuilder('t')
            ->select('t.id, c.slugCombined, cs.slugCombined AS secondarySlug, t.updatedAt')
            ->join('t.category', 'c')
            ->join('t.categorySecondary', 'cs')
            ->andWhere('t.statusTypeId = :status')
            ->setParameter('status', StatusTypeProvider::CHECKED)
            ->groupBy('t.id')
            ->getQuery()
            ->iterate();

        $batchSize = 1000;
        $i = 0;
        foreach ($topics as $row) {
            $topic = current($row);
            $event->getGenerator()->addUrl(
                new UrlConcrete(
                    $this->router->generate('MetalContentBundle:Topic:view',  array('category_slug' => $topic['slugCombined'] ?: $topic['secondarySlug'], 'id' => $topic['id']), true),
                    $topic['updatedAt'],
                    UrlConcrete::CHANGEFREQ_WEEKLY,
                    0.6
                ),
                $this->section
            );
            if (($i % $batchSize) === 0) {
                $this->em->clear();
            }
            $i++;
        }
        $this->em->clear();

    }

    private function buildQuestions(SitemapPopulateEvent $event)
    {
        $questionsRepository = $this->em->getRepository('MetalContentBundle:Question');

        $questions = $questionsRepository->createQueryBuilder('q')
            ->select('q.id, c.slugCombined, cs.slugCombined AS secondarySlug, q.updatedAt')
            ->join('q.category', 'c')
            ->join('q.categorySecondary', 'cs')
            ->andWhere('q.statusTypeId = :status')
            ->setParameter('status', StatusTypeProvider::CHECKED)
            ->groupBy('q.id')
            ->getQuery()
            ->iterate();

        $batchSize = 1000;
        $i = 0;
        foreach ($questions as $row) {
            $question = current($row);
            $event->getGenerator()->addUrl(
                new UrlConcrete(
                    $this->router->generate('MetalContentBundle:Question:view',  array('category_slug' => $question['slugCombined'] ?: $question['secondarySlug'], 'id' => $question['id']), true),
                    $question['updatedAt'],
                    UrlConcrete::CHANGEFREQ_WEEKLY,
                    0.6
                ),
                $this->section
            );
            if (($i % $batchSize) === 0) {
                $this->em->clear();
            }
            $i++;
        }
        $this->em->clear();

    }

    private function buildCategories(SitemapPopulateEvent $event, $now)
    {
        $categories = $this->em
            ->getRepository('MetalContentBundle:Category')
            ->createQueryBuilder('c')
            ->andWhere('c.isEnabled = true')
            ->orderBy('c.title', 'ASC')
            ->getQuery()
            ->getResult();

        foreach ($categories as $category) {
            /* @var $category Category */
            $event->getGenerator()->addUrl(
                new UrlConcrete(
                    $this->router->generate('MetalContentBundle:Topics:list', array('category_slug' => $category->getSlugCombined()), true),
                    $this->randomizeDate($now, 'hours'),
                    UrlConcrete::CHANGEFREQ_DAILY,
                    0.8
                ),
                $this->section
            );

            $event->getGenerator()->addUrl(
                new UrlConcrete(
                    $this->router->generate('MetalContentBundle:Questions:list', array('category_slug' => $category->getSlugCombined()), true),
                    $this->randomizeDate($now, 'hours'),
                    UrlConcrete::CHANGEFREQ_DAILY,
                    0.8
                ),
                $this->section
            );
        }
    }

    private function randomizeDate(\DateTime $date, $dimension)
    {
        $newDate = clone $date;
        $newDate->modify(sprintf('%d %s', mt_rand(-10, 10), $dimension));

        return $newDate;
    }

    private function getUrl()
    {
        return $this->router->generate('MetalProjectBundle:Default:index', array(), true);
    }
}
