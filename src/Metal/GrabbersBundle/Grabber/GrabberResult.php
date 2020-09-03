<?php

namespace Metal\GrabbersBundle\Grabber;

use Metal\DemandsBundle\Entity\DemandItem;

class GrabberResult
{
    public $siteDemandId;

    public $siteDemandUrl;

    public $siteDemandHash;

    /**
     * @var DemandItem[]
     */
    public $demandItems = array();

    public $info;

    public $cityTitle;

    public $address;

    public $person;

    public $companyTitle;

    public $email;

    public $emailImageUrl;

    public $phone;

    public $siteDemandPublicationDate;

    /**
     * @var \DateTime
     */
    public $createdAt;

    public function setInfo($info)
    {
        $this->info = trim($info);
    }
}
