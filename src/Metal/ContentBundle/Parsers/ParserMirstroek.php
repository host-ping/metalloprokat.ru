<?php

namespace Metal\ContentBundle\Parsers;

use Symfony\Component\DomCrawler\Crawler;

class ParserMirstroek extends ParserAbstract
{
    protected $siteUrl;
    protected $catalogUri;

    /**
     * @var Crawler
     */
    protected $mainCrawler;

    /**
     * @var Crawler
     */
    protected $postCrawler;

    /**
     * @var Crawler
     */
    protected $contentCrawler;

    /**
     * @var Crawler
     */
    protected $companyInfoCrawler;

    public function __construct($siteUrl, $catalogUri)
    {
        $this->siteUrl = $siteUrl;
        $this->catalogUri = $catalogUri;
    }

    public function initMainCrawler($content)
    {
        $this->mainCrawler = new Crawler($content);

        $activePageCrawler = $this->mainCrawler->filter('ul.nav li.active');
        $this->currentPage = (int)$activePageCrawler->filter('a')->text();
        $this->nextPage = null;
        
        foreach ($activePageCrawler->siblings()->filter('a') as $sibling) {
            if ((int)$sibling->textContent > $this->currentPage) {
                $this->nextPage = (int)$sibling->textContent;
                break;
            }
        }
    }

    public function initPostCrawler($content)
    {
        $this->postCrawler = new Crawler($content);
        
        $this->contentCrawler = $this->postCrawler->filter('div.company_detail');
        
        $this->companyInfoCrawler = $this->postCrawler
            ->filter('div#content')
            ->filter('table.stripy')
            ->first()
            ->filter('tr')
        ;
    }

    public function getPostsLinks()
    {
        if (!$this->mainCrawler->filter('ul.company_list li')->count()) {
            return null;
        }

        return $this->mainCrawler->filter('ul.company_list li')->each(function (Crawler $node, $i) {
            return $node->filter('a')->attr('href');
        });
    }

    public function getTitle()
    {
        if (!$this->contentCrawler->filter('div h1')->count()) {
            return null;
        }
        
       return $this->contentCrawler->filter('div h1')->text();
    }

    public function getUserName()
    {
        return 'Менеджер';
    }

    public function getDescription()
    {
        if (!$this->contentCrawler->filter('div .description')->count()) {
            return null;
        }
        
        return $this->contentCrawler->filter('div .description')->text();
    }

    public function getCity()
    {
        $founds = $this->companyInfoCrawler
            ->each(function (Crawler $node, $i) {
                if (!$node->filter('td')->first()->filter('span')->count()) {
                    return null;
                }

                if ('Город' === $node->filter('td')->first()->filter('span')->text())
                {
                    return $node->filter('td')->last()->text();
                }
            });

        $founds = array_filter($founds);

        return reset($founds);
    }

    public function getAddress()
    {
        $founds = $this->companyInfoCrawler
            ->each(function (Crawler $node, $i) {
                if (!$node->filter('td')->first()->filter('span')->count()) {
                    return null;
                }

                if ('Адрес' === $node->filter('td')->first()->filter('span')->text())
                {
                    return $node->filter('td')->last()->text();
                }
            });

        $founds = array_filter($founds);

        return reset($founds);
    }

    public function getEmail()
    {
        $founds = $this->companyInfoCrawler
            ->each(function (Crawler $node, $i) {
                if (!$node->filter('td')->first()->filter('span')->count()) {
                    return null;
                }
                
                if ('E-mail' === $node->filter('td')->first()->filter('span')->text())
                {
                    return $node->filter('td')->last()->text();
                }
            });
        
        $founds = array_filter($founds);
        
        return reset($founds);
    }

    public function getPhone()
    {
        $founds = $this->companyInfoCrawler
            ->each(function (Crawler $node, $i) {
                if (!$node->filter('td')->first()->filter('span')->count()) {
                    return null;
                }

                if ('Телефон' === $node->filter('td')->first()->filter('span')->text())
                {
                    return $node->filter('td')->last()->text();
                }
            });

        $founds = array_filter($founds);

        return reset($founds);
    }

    public function getSite()
    {
        $founds = $this->companyInfoCrawler
            ->each(function (Crawler $node, $i) {
                if (!$node->filter('td')->first()->filter('span')->count()) {
                    return null;
                }

                if ('Веб сайт' === $node->filter('td')->first()->filter('span')->text())
                {
                    return $node->filter('td')->last()->text();
                }
            });

        $founds = array_filter($founds);

        return reset($founds);   
    }

    public function getCatalogUrl()
    {
        return sprintf(
            '%s%s%s',
            $this->siteUrl,
            $this->catalogUri,
            $this->nextPage ? '/page'.$this->nextPage : null
        );
    }

    public function getCode()
    {
        return 'mirstroek';
    }

    public function getCategoriesTitles()
    {
        $pos = $this->postCrawler->filter('h2')->first();
        $titles = array();
        if ($pos->count() && $pos->text() === 'Категории компании:') {
            $titles =  $this->postCrawler->filter('table.stripy')->eq(1)->filter('tr')->each(
                function (Crawler $node, $i) {
                    if ($node->filter('td')->filter('span')->count()) {
                        return trim($node->filter('td')->filter('span')->text());
                    }
                }
            );
        }
        
        return array_filter($titles);
    }

    public function getSleep()
    {
        return mt_rand(5, 12);
    }
}
