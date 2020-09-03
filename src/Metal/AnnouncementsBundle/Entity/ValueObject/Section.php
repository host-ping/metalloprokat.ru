<?php

namespace Metal\AnnouncementsBundle\Entity\ValueObject;

class Section
{
    protected $id;

    protected $title;

    protected $longTitle;

    public function __construct($params)
    {
        $this->id = $params['id'];
        $this->title = $params['title'];
        $this->longTitle = $params['longTitle'];
    }

    public function getId()
    {
        return $this->id;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function getLongTitle()
    {
        return $this->longTitle;
    }
}
