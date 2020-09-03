<?php

namespace Metal\ContentBundle\Parsers;


use Symfony\Component\DomCrawler\Crawler;

class ParserStroyfirm extends ParserAbstract
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
    protected $companyInfoCrawler;

    public function __construct($siteUrl, $catalogUri)
    {
        $this->siteUrl = $siteUrl;
        $this->catalogUri = $catalogUri;
    }

    public function initMainCrawler($content)
    {
        $this->mainCrawler = new Crawler($content);

        $activePageCrawler = $this->mainCrawler->filter('#paginator b');
        $this->nextPage = null;
        $this->currentPage = 1;
        if ($activePageCrawler->count()) {
            $this->currentPage = (int)$activePageCrawler->text();
            foreach ($activePageCrawler->siblings()->filter('a') as $sibling) {
                if ((int)$sibling->textContent > $this->currentPage) {
                    $this->setNextPage($sibling->textContent);
                    break;
                }
            }
        }
    }

    public function setNextPage($nextPage)
    {
        $this->nextPage = (int)(($nextPage - 1) * 10);
    }

    public function initPostCrawler($content)
    {
        $this->postCrawler = new Crawler($content);
    }

    public function getCode()
    {
        return 'stroyfirm';
    }

    public function getPostsLinks()
    {
        if (!$this->mainCrawler->filter('div.firmcard div.fname')->count()) {
            return null;
        }

        return $this->mainCrawler->filter('div.firmcard div.fname')->each(function (Crawler $node, $i) {
            return $this->siteUrl . $node->filter('a[target="_blank"]')->attr('href');
        });
    }

    public function getTitle()
    {
        if (!$this->postCrawler->filter('span[itemprop="name"]')->first()->count()) {
            return null;
        }

        return $this->postCrawler->filter('span[itemprop="name"]')->first()->text();
    }

    public function getUserName()
    {
        return 'Менеджер';
    }

    public function getDescription()
    {
        if (!$this->postCrawler->filter('p.desc')->first()->count()) {
            return null;
        }

        return $this->postCrawler->filter('p.desc')->first()->text();
    }

    public function getAddress()
    {
        if (!$this->postCrawler->filter('span[itemprop="streetAddress"]')->first()->count()) {
            return null;
        }

        return $this->postCrawler->filter('span[itemprop="streetAddress"]')->first()->text();
    }

    public function getCity()
    {
        if (!$this->postCrawler->filter('span[itemprop="addressLocality"]')->first()->count()) {
            return null;
        }

        return $this->postCrawler->filter('span[itemprop="addressLocality"]')->first()->text();
    }

    public function getEmail()
    {
        if (!$this->postCrawler->filter('span[itemprop="email"]')->first()->count()) {
            return null;
        }

        return $this->postCrawler->filter('span[itemprop="email"]')->first()->text();
    }

    public function getPhone()
    {
        if (!$this->postCrawler->filter('span[itemprop="telephone"]')->first()->count()) {
            return null;
        }

        return $this->postCrawler->filter('span[itemprop="telephone"]')->first()->text();
    }

    public function getSite()
    {
        if (!$this->postCrawler->filter('span[itemprop="telephone"]')->first()->count()) {
            return null;
        }

        return $this->postCrawler->filter('span[itemprop="telephone"]')->first()->text();
    }

    public function getCatalogUrl()
    {
        return sprintf(
            '%s%s%s',
            $this->siteUrl,
            $this->catalogUri,
            $this->nextPage ? '&l1='.$this->nextPage : null
        );
    }

    public function getCategoriesTitles()
    {
        return array_filter($this->postCrawler->filter('ul[style="margin: 0; padding: 0 0 0 10px;"]')
            ->filter('li')
            ->each(function (Crawler $node, $i) {
                return $node->filter('a')->text();
            }));
    }

    public function getSleep()
    {
        return mt_rand(5, 10);
    }
}
