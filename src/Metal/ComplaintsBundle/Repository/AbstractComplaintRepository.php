<?php


namespace Metal\ComplaintsBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Metal\ComplaintsBundle\Entity\AbstractComplaint;
use Metal\ComplaintsBundle\Entity\ProductComplaint;

class AbstractComplaintRepository extends EntityRepository
{
    public function attachProductsToComplaints($complaints)
    {
        $directedComplaints = array();
        $products = array();
        foreach ($complaints as $complaint) {
            if ($complaint instanceof ProductComplaint) {
                $directedComplaints[$complaint->getId()] = $complaint;
                $products[$complaint->getProduct()->getId()] = true;
            }
        }
        if (!count($directedComplaints)) {
            return;
        }
        $this->_em->createQueryBuilder()
            ->select('p')
            ->from('MetalProductsBundle:Product', 'p')
            ->where('p IN (:ids)')
            ->setParameter('ids', array_keys($products))
            ->getQuery()
            ->getResult();
    }

    /**
     * @param AbstractComplaint[] $complaints
     * @param $user
     */
    public function setComplaintsViewed(array $complaints, $user)
    {
        $notViewedComplaints = array();
        $companiesIds = array();
        foreach ($complaints as $complaint) {
            if ($complaint->getViewedBy()) {
                continue;
            }

            $notViewedComplaints[$complaint->getId()] = $complaint;
            $companiesIds[$complaint->getCompany()->getId()] = true;
        }

        if (!count($notViewedComplaints)) {
            return;
        }

        $this->_em->createQueryBuilder()
            ->update('MetalComplaintsBundle:AbstractComplaint', 'c')
            ->set('c.viewedBy', ':user')
            ->set('c.viewedAt', ':date')
            ->where('c IN (:ids)')
            ->andWhere('c.viewedBy IS NULL')
            ->setParameter('user', $user)
            ->setParameter('date', new \DateTime())
            ->setParameter('ids', array_keys($notViewedComplaints))
            ->getQuery()
            ->execute();

        $this->_em->getRepository('MetalCompaniesBundle:CompanyCounter')
            ->updateCompaniesComplaintCount(array_keys($companiesIds));
    }

}
