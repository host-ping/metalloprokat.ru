<?php

namespace Metal\DemandsBundle\Repository;

use Doctrine\ORM\EntityRepository;

class DemandAnswerRepository extends EntityRepository
{
    public function attachDemandAnswers($demands)
    {
        if (empty($demands)) {
            return;
        }

        $qb = $this->_em->createQueryBuilder()
            ->select('da')
            ->from('MetalDemandsBundle:DemandAnswer', 'da')
            ->andWhere('da.demand IN (:demands_ids)')
            ->setParameter('demands_ids', $demands)
            ->orderBy('da.createdAt', 'DESC');

        $demandsAnswers = $qb
            ->getQuery()
            ->getResult();

        foreach ($demandsAnswers as $demandAnswer) {
            $answers = $demandAnswer->getDemand()->getAttribute('demandAnswers');
            $answers[] = $demandAnswer;
            $demandAnswer->getDemand()->setAttribute('demandAnswers', $answers);
        }
    }
}
