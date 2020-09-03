<?php

namespace Metal\ContentBundle\Parsers;

interface ParserInterface
{
    public function initMainCrawler($content);
    
    public function initPostCrawler($content);

    /**
     * @return string
     */
    public function getCode();

    /**
     * @return array
     */
    public function getPostsLinks();

    /**
     * @return string
     */
    public function getTitle();

    /**
     * @return string
     */
    public function getUserName();

    /**
     * @return string
     */
    public function getDescription();

    /**
     * @return string
     */
    public function getAddress();
    
    /**
     * @return string
     */
    public function getCity();

    /**
     * @return string
     */
    public function getEmail();

    /**
     * @return string
     */
    public function getPhone();

    /**
     * @return string
     */
    public function getSite();

    /**
     * @return string
     */
    public function getSleep();

    /**
     * @return bool
     */
    public function hasNextPage();

    /**
     * @return integer
     */
    public function getCurrentPage();

    /**
     * @return integer|null
     */
    public function getNextPage();

    /**
     * @param integer
     */
    public function setNextPage($nextPage);

    public function getCatalogUrl();

    /**
     * @return array
     */
    public function getCategoriesTitles();
}