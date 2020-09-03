<?php

namespace Metal\ComplaintsBundle\Entity\ValueObject;

class ComplaintType
{
    protected $id;

    protected $title;

    protected $kind;

    public function __construct($params)
    {
        $this->id = $params['id'];
        $this->title = $params['title'];
        $this->kind = $params['kind'];
    }

    public function getId()
    {
        return $this->id;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setKind($kind)
    {
        $this->kind = $kind;
    }

    public function getKind()
    {
        return $this->kind;
    }
}
