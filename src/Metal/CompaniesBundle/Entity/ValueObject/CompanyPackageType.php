<?php

namespace Metal\CompaniesBundle\Entity\ValueObject;

//TODO: rename to PackageType or even delete
class CompanyPackageType
{
    protected $id;
    protected $title;
    protected $titleGenitive;

    public function __construct($params)
    {
        $this->id = $params['id'];
        $this->title = $params['title'];
        $this->titleGenitive = $params['titleGenitive'];
    }

    public function getId()
    {
        return $this->id;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function getTitleGenitive()
    {
        return $this->titleGenitive;
    }
}
