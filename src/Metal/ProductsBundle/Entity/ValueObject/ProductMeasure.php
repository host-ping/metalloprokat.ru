<?php

namespace Metal\ProductsBundle\Entity\ValueObject;

class ProductMeasure
{
    protected $id;
    protected $token;
    protected $title;
    protected $tokenTransChoice;
    protected $tokenTransChoiceForWidget;

    public function __construct($params)
    {
        $this->id = $params['id'];
        $this->token = $params['token'];
        $this->title = isset($params['title']) ? $params['title'] : $params['token'];
        $this->tokenTransChoice = isset($params['tokenTransChoice']) ? $params['tokenTransChoice'] : $params['token'];
        $this->tokenTransChoiceForWidget = isset($params['tokenTransChoiceForWidget']) ? $params['tokenTransChoiceForWidget'] : $params['token'];
    }

    public function getId()
    {
        return $this->id;
    }

    public function getToken()
    {
        return $this->token;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function getTokenTransChoice()
    {
        return $this->tokenTransChoice;
    }

    public function getTokenPrice()
    {
        $explode = explode('|', $this->tokenTransChoice);

        return reset($explode);
    }

    public function getTokenPriceForWidget()
    {
        $explode = explode('|', $this->tokenTransChoiceForWidget);

        return reset($explode);
    }

    public function setTokenTransChoice($tokenTransChoice)
    {
        $this->tokenTransChoice = $tokenTransChoice;
    }

    public function getTokenTransChoiceForWidget()
    {
        return $this->tokenTransChoiceForWidget;
    }

    public function setTokenTransChoiceForWidget($tokenTransChoiceForWidget)
    {
        $this->tokenTransChoiceForWidget = $tokenTransChoiceForWidget;
    }
}
