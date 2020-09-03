<?php

namespace Metal\CompaniesBundle\Consumer;

use Metal\CompaniesBundle\Service\CompanyService;

use Sonata\NotificationBundle\Consumer\ConsumerEvent;
use Sonata\NotificationBundle\Consumer\ConsumerInterface;

class BranchOfficeCreationConsumer implements ConsumerInterface
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
        $branchOfficeCreation = $event->getMessage()->getBody();

        $data = array(
            'added'   => $branchOfficeCreation['branch_office_id'],
            'removed' => null
        );

        $this->companyService->updateRelativeDataCities($branchOfficeCreation['old_company_as_array'], $data);
    }
}
