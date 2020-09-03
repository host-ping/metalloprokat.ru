<?php

namespace Metal\CompaniesBundle\Consumer;

use Doctrine\ORM\EntityManager;
use Metal\CompaniesBundle\Service\CompanyService;
use Sonata\NotificationBundle\Consumer\ConsumerEvent;
use Sonata\NotificationBundle\Consumer\ConsumerInterface;

class BranchOfficeRemovalConsumer implements ConsumerInterface
{
    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @var CompanyService
     */
    protected $companyService;

    public function __construct(EntityManager $em, CompanyService $companyService)
    {
        $this->em = $em;
        $this->companyService = $companyService;

    }

    /**
     * {@inheritdoc}
     */
    public function process(ConsumerEvent $event)
    {
        $branchOfficeCreation = $event->getMessage()->getBody();
        $data = array(
            'added'   => null,
            'removed' => $branchOfficeCreation['branch_office_id']
        );

        $this->companyService->updateRelativeDataCities($branchOfficeCreation['old_company_as_array'], $data);
    }
}
