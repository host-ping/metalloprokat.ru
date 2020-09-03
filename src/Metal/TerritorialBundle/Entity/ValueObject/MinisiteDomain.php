<?php

namespace Metal\TerritorialBundle\Entity\ValueObject;

class MinisiteDomain
{
    protected $id;

    protected $title;

    protected $countryId;

    public function __construct($data)
    {
        $this->id = $data['id'];
        $this->title = $data['title'];
        $this->countryId = $data['country_id'];
    }

    public function getId()
    {
        return $this->id;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function getCountryId()
    {
        return $this->countryId;
    }
}
