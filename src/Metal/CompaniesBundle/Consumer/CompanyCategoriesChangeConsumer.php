<?php

namespace Metal\CompaniesBundle\Consumer;

use Brouzie\Sphinxy\IndexManager;
use Doctrine\ORM\EntityManager;
use Sonata\NotificationBundle\Consumer\ConsumerEvent;
use Sonata\NotificationBundle\Consumer\ConsumerInterface;

class CompanyCategoriesChangeConsumer implements ConsumerInterface
{
    /**
     * @var IndexManager
     */
    private $indexManager;

    /**
     * @var EntityManager
     */
    private $em;

    public function __construct(IndexManager $indexManager, EntityManager $em)
    {
        $this->indexManager = $indexManager;
        $this->em = $em;
    }

    /**
     * {@inheritdoc}
     */
    public function process(ConsumerEvent $event)
    {
        $categoriesChangeData = $event->getMessage()->getBody();
        $currentCategoriesIds = $categoriesChangeData['company_categories_ids'];
        $oldCategoriesIds = $categoriesChangeData['old_company_categories_ids'];

        $addedCompanyCategoriesIds = array_diff($currentCategoriesIds, $oldCategoriesIds);
        $deletedCompanyCategoriesIds = array_diff($oldCategoriesIds, $currentCategoriesIds);

        if (count($addedCompanyCategoriesIds) || count($deletedCompanyCategoriesIds)) {
            $companyCategoryRepository = $this->em->getRepository('MetalCompaniesBundle:CompanyCategory');

            $companyId = $categoriesChangeData['company_id'];

            if ($addedCompanyCategoriesIds) {
                $companyCategoryRepository->onInsertCompanyCategory(array($companyId));
            }

            if ($deletedCompanyCategoriesIds) {
                $companyCategoryRepository->onDeleteCompanyCategory($companyId, $deletedCompanyCategoriesIds);
            }

            $company = $this->em->getRepository('MetalCompaniesBundle:Company')->find($companyId);
            $this->indexManager->reindexItems('products', array($company->getVirtualProduct()->getId()));
        }
    }
}
