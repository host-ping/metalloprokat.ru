<?php

namespace Metal\ContentBundle\Parsers;

abstract class ParserAbstract implements ParserInterface 
{
    protected $nextPage;

    protected $currentPage;

    
    public function hasNextPage()
    {
        return null !== $this->nextPage;
    }

    public function getCurrentPage()
    {
        return $this->currentPage;
    }

    public function getNextPage()
    {
        return $this->nextPage;
    }

    public function setNextPage($nextPage)
    {
        $this->nextPage = $nextPage;
    }
    
    public function getSleep()
    {
        return mt_rand(5, 8);
    }
}
