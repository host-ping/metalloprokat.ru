<?php

namespace Metal\CompaniesBundle\Repository;

use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityRepository;
use Metal\CompaniesBundle\Entity\PaymentDetails;

class PaymentDetailsRepository extends EntityRepository
{
    public function synchronizePaymentDetails(array $companiesIds = null)
    {
        if (is_array($companiesIds) && !$companiesIds) {
            return null;
        }

        $connection = $this->_em->getConnection();

        $subQb = $connection->createQueryBuilder()
            ->select('Message_ID')
            ->addSelect('Message_ID')
            ->addSelect('Created')
            ->addSelect(sprintf('%d AS display_on_minisite', (new PaymentDetails())->getDisplayOnMiniSite()))
            ->from('Message75', 'c')
        ;

        $uQb = $connection->createQueryBuilder()
            ->update('Message75')
            ->set('payment_details_id', 'Message_ID')
        ;

        $params = array();
        $paramsType = array();
        if (null !== $companiesIds) {
            $subQb->where('c.Message_ID IN(:companiesIds)');
            $params = array('companiesIds' => $companiesIds);
            $paramsType = array('companiesIds' => Connection::PARAM_INT_ARRAY);

            $uQb->where('Message_ID IN (:companiesIds)')
                ->setParameter('companiesIds', $companiesIds, Connection::PARAM_INT_ARRAY)
            ;
        }

        $connection->executeQuery('
            INSERT INTO company_payment_details (id, company_id, updated_at, display_on_minisite)
            (
              '.$subQb->getSQL().'
            ) ON DUPLICATE KEY UPDATE
            company_payment_details.updated_at = IF(company_payment_details.updated_at > c.Created, company_payment_details.updated_at, c.Created)',
            $params,
            $paramsType
        );

        $uQb->execute();
    }
}
