<?php

namespace Metal\CompaniesBundle\Consumer;

use Brouzie\Sphinxy\Connection;
use Sonata\NotificationBundle\Consumer\ConsumerEvent;
use Sonata\NotificationBundle\Consumer\ConsumerInterface;

class CompanyAttributesChangeConsumer implements ConsumerInterface
{
    /**
     * @var Connection
     */
    protected $sphinxy;

    public function __construct(Connection $sphinxy)
    {
        $this->sphinxy = $sphinxy;
    }

    /**
     * {@inheritdoc}
     */
    public function process(ConsumerEvent $event)
    {
        $attributesChange = $event->getMessage()->getBody();

        $firstDiffCompanyAttributesTypesIds = array_diff(
            $attributesChange['company_attributes_types_ids'],
            $attributesChange['old_company_attributes_types_ids']
        );

        $secondDiffCompanyAttributesTypesIds = array_diff(
            $attributesChange['old_company_attributes_types_ids'],
            $attributesChange['company_attributes_types_ids']
        );

        if (count($firstDiffCompanyAttributesTypesIds) || count($secondDiffCompanyAttributesTypesIds)) {
            $this->sphinxy
                ->createQueryBuilder()
                ->update('products')
                ->set('company_attributes_ids', ':a')
                ->setParameter('a', $attributesChange['company_attributes_types_ids'])
                ->where('company_id = :c')
                ->setParameter('c', $attributesChange['company_id'])
                ->execute();
        }
    }
}
