<?php

namespace Metal\ProjectBundle\Entity\ValueObject;

class SiteSourceType
{
    protected $id;

    protected $title;

    protected $code;

    public function __construct($params)
    {
        $this->id = $params['id'];
        $this->title = $params['title'];
        $this->code = isset($params['code']) ? $params['code'] : null;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function getCode()
    {
        return $this->code;
    }
}
