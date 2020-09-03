<?php

namespace Metal\DemandsBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Metal\CompaniesBundle\Entity\Company;
use Metal\DemandsBundle\Entity\Demand;
use Metal\DemandsBundle\Entity\DemandView;
use Metal\UsersBundle\Entity\User;

class DemandViewRepository extends EntityRepository
{
    /**
     * @param $demand Demand|int
     * @param User $user
     * @param string $ip
     * @param bool $isExport
     *
     * @return bool
     */
    public function addView($demand, User $user, $ip = null, $isExport = false)
    {
        $demandView = new DemandView();

        $demandId = $demand instanceof Demand ? $demand->getId() : $demand;
        $demandView->setDemandId($demandId);

        $demandView->setUser($user);
        $demandView->setCompany($user->getCompany());
        $demandView->setIsExport($isExport);
        $demandView->setIp($ip);

        if ($this->insertDemandView($demandView)) {
            $this->_em
                ->createQuery('UPDATE MetalDemandsBundle:Demand d SET d.viewsCount = d.viewsCount + 1 WHERE d.id = :id')
                ->setParameter('id', $demandId)
                ->execute();

            return true;
        }

        return false;
    }

    public function getViewedDemandsForPromocode(Company $company)
    {
        if (!$company->getPromocode()) {
            return null;
        }

        $promocode = $company->getPromocode();
        $demandsView = $this->createQueryBuilder('demandView')
            ->select('IDENTITY(demandView.demand) AS id')
            ->where('demandView.viewedAt >= :promocodeActivated')
            ->setParameter('promocodeActivated', $promocode->getActivatedAt())
            ->andWhere('demandView.company = :company')
            ->setParameter('company', $company)
            ->getQuery()
            ->getArrayResult()
        ;

        $demandsIds = array();
        foreach ($demandsView as $demandView) {
            $demandsIds[$demandView['id']] = true;
        }

        return $demandsIds;
    }

    public function insertDemandView(DemandView $demandView)
    {
        $demandViewArray = $demandView->toArray();

        $id = $this->_em->getConnection()->fetchColumn('SELECT id FROM demand_view WHERE demand_id = :demand_id AND user_id = :user_id',
            array('demand_id' => $demandViewArray['demand_id'], 'user_id' => $demandViewArray['user_id']));

        if ($id) {
            return false;
        }

        $result = $this->_em->getConnection()->executeUpdate(
            'INSERT IGNORE INTO demand_view (
                        `user_id`,
                        `company_id`,
                        `demand_id`,
                        `viewed_at`, 
                        `is_export`,
                        `ip`) VALUES (
                        :user_id,
                        :company_id,
                        :demand_id,
                        :viewed_at,
                        :is_export,
                        :ip)',
            $demandViewArray
        );

        return (boolean) $result;
    }
}
