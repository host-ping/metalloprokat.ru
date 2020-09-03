<?php

namespace Metal\ProductsBundle\Entity\ValueObject;

class Currency
{
    protected $id;

    protected $token;

    protected $tokenEn;

    protected $symbolClass;

    protected $fallbackToken;

    public function __construct($params)
    {
        $this->id = $params['id'];
        $this->token = $params['token'];
        $this->tokenEn = $params['tokenEn'];
        $this->symbolClass = $params['symbolClass'];
        $this->fallbackToken = $params['fallbackToken'];
    }

    public function getId()
    {
        return $this->id;
    }

    public function getToken()
    {
        return $this->token;
    }

    public function  getTokenEn()
    {
        return $this->tokenEn;
    }

    public function getSymbolClass()
    {
        return $this->symbolClass;
    }

    public function getFallbackToken()
    {
        return $this->fallbackToken;
    }

}
