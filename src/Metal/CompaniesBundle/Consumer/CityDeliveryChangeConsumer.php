<?php

namespace Metal\CompaniesBundle\Consumer;

use Metal\CompaniesBundle\Service\CompanyService;
use Sonata\NotificationBundle\Consumer\ConsumerEvent;
use Sonata\NotificationBundle\Consumer\ConsumerInterface;

class CityDeliveryChangeConsumer implements ConsumerInterface
{
    /**
     * @var CompanyService
     */
    protected $companyService;


    public function __construct(CompanyService $companyService)
    {
        $this->companyService = $companyService;
    }

    /**
     * {@inheritdoc}
     */
    public function process(ConsumerEvent $event)
    {
        $cityDeliveryData = $event->getMessage()->getBody();

        $addedCompanyCitiesIds = array_diff_key(
            $cityDeliveryData['company_cities_ids'],
            $cityDeliveryData['old_company_cities_ids']
        );

        $removedCompanyCitiesIds = array_diff_key(
            $cityDeliveryData['old_company_cities_ids'],
            $cityDeliveryData['company_cities_ids']
        );

        $data = array(
            'added'   => $addedCompanyCitiesIds,
            'removed' => $removedCompanyCitiesIds
        );

        if (count($addedCompanyCitiesIds) || count($removedCompanyCitiesIds)) {
            $this->companyService->updateRelativeDataCities($cityDeliveryData['old_company_as_array'], $data);
        }
    }
}
