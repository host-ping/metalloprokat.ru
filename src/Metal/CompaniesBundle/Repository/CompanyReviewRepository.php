<?php

namespace Metal\CompaniesBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Metal\CompaniesBundle\Entity\CompanyReview;
use Metal\UsersBundle\Entity\User;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;

class CompanyReviewRepository extends EntityRepository
{
    public function getCompanyReviewBySpecification($specification, $orderBy, $limit = null, $skip = null)
    {
        $pagerfanta = $this->getPagerfantaForCompanyReview($specification, $orderBy, $limit, ceil($skip / $limit) + 1);

        $reviews = iterator_to_array($pagerfanta->getIterator());

        return $reviews;
    }

    public function getPagerfantaForCompanyReview($specification, $orderBy, $perPage = null, $page = 1)
    {
        $qb = $this->createQueryBuilder('cr')
            ->leftJoin('cr.user', 'u')
            ->addSelect('u')
            ->leftJoin('cr.answer', 'a')
            ->addSelect('a');

        $this->applySpecificationToQueryBuilder($qb, $specification);
        $this->applyOrderByToQueryBuilder($qb, $orderBy);

        $adapter = new DoctrineORMAdapter($qb, false);
        $pagerfanta = new Pagerfanta($adapter);
        $pagerfanta->setMaxPerPage($perPage ?: 9999999);
        $pagerfanta ->setCurrentPage($page);

        return $pagerfanta;
    }

    /**
     * @param CompanyReview[] $reviews
     * @param User $user
     */
    public function setReviewsModerated($reviews, $user)
    {
        $notViewedReviews = array();
        $companiesIds = array();

        foreach ($reviews as $review) {
            if ($review->getViewedBy()) {
                continue;
            }

            $notViewedReviews[$review->getId()] = $review;
            $companiesIds[$review->getCompany()->getId()] = true;
        }

        if (!count($notViewedReviews)) {
            return;
        }

        $this->_em->createQueryBuilder()
            ->update('MetalCompaniesBundle:CompanyReview', 'r')
            ->set('r.viewedBy', ':user')
            ->set('r.viewedAt', ':date')
            ->where('r IN (:ids)')
            ->andWhere('r.viewedBy IS NULL')
            ->setParameter('user', $user)
            ->setParameter('date', new \DateTime())
            ->setParameter('ids', array_keys($notViewedReviews))
            ->getQuery()
            ->execute();

        $this->_em->getRepository('MetalCompaniesBundle:CompanyCounter')
            ->updateCompaniesNewReviewCount(array_keys($companiesIds));
    }

    private function applyOrderByToQueryBuilder(QueryBuilder $qb, $orderBy)
    {
        $orders = array(
            'created_at' => 'cr.createdAt'
        );
        foreach ($orderBy as $key => $order) {
            $qb->addOrderBy($orders[$key], $order);
        }
    }

    private function applySpecificationToQueryBuilder(QueryBuilder $qb, array $specification)
    {
        if (isset($specification['answers'])) {
            $qb->andWhere('cr.type IS NULL');
        } else {
            $qb->andWhere('cr.type IS NOT NULL');
        }

        if (isset($specification['company'])) {
            $qb->andWhere('cr.company = :company')
                ->setParameter('company', $specification['company']);
        }

        if (empty($specification['with_deleted'])) {
            $qb->andWhere('cr.deletedBy IS NULL');
        }

        if (empty($specification['with_no_moderated'])) {
            $qb->andWhere('cr.moderatedBy IS NOT NULL');
        }

        if (isset($specification['not_answered']) && $specification['not_answered']) {
            $qb->andWhere('cr.answer IS NULL');
        }
    }
}
