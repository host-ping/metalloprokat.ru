<?php

namespace Metal\ProjectBundle\Entity\ValueObject;

class AdminSourceType 
{
    protected $id;

    protected $title;

    function __construct($params)
    {
        $this->id = $params['id'];
        $this->title = $params['title'];
    }

    public function getId()
    {
        return $this->id;
    }

    public function getTitle()
    {
        return $this->title;
    }
}