<?php

namespace Metal\ContentBundle\Entity\ValueObject;

class StatusType
{
    protected $id;

    protected $title;

    public function __construct($params)
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
