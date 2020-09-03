<?php

namespace Metal\NewsletterBundle\Repository;

use Doctrine\ORM\EntityRepository;

class NewsletterRepository extends EntityRepository
{
    public function updateProcessedAt()
    {
        //TODO: add check for Subscriber.isInvalid
        $this->_em->createQueryBuilder()
            ->update($this->_entityName, 'n')
            ->set('n.processedAt', ':now')
            ->where('(SELECT COUNT(nr) FROM MetalNewsletterBundle:Recipient nr WHERE nr.newsletter = n.id AND nr.sentAt IS NULL) = 0')
            ->setParameter('now', new \DateTime())
            ->getQuery()
            ->execute()
        ;

        $this->_em->createQueryBuilder()
            ->update($this->_entityName, 'n')
            ->set('n.processedAt', 'NULL')
            ->where('(SELECT COUNT(nr) FROM MetalNewsletterBundle:Recipient nr WHERE nr.newsletter = n.id AND nr.sentAt IS NULL) > 0')
            ->getQuery()
            ->execute()
        ;
    }

    public function attachStatisticsToNewsletter(array $newsletters)
    {
        if (!count($newsletters)) {
            return;
        }

        $directedNewsletters = array();
        foreach ($newsletters as $newsletter) {
            $directedNewsletters[$newsletter->getId()] = $newsletter;
            $newsletter->setAttribute('recipientsCount', 0);
            $newsletter->setAttribute('processedRecipientsCount', 0);
            $newsletter->setAttribute('sentRecipientsCount', 0);
            $newsletter->setAttribute('views', 0);
        }

        $newsletterStatistics = $this->_em->createQueryBuilder()
            ->select('IDENTITY(r.newsletter) AS newsletterId, COUNT(r.id) AS recipientsCount, COUNT(r.processedAt) AS processedRecipientsCount, COUNT(r.sentAt) AS sentRecipientsCount')
            ->from('MetalNewsletterBundle:Recipient', 'r')
            ->andWhere('r.newsletter IN (:newsletters_ids)')
            ->addGroupBy('r.newsletter')
            ->setParameter('newsletters_ids', array_keys($directedNewsletters))
            ->getQuery()
            ->getResult();

        foreach ($newsletterStatistics as $newsletterStatistic) {
            $newsletter = $directedNewsletters[$newsletterStatistic['newsletterId']];
            $newsletter->setAttribute('recipientsCount', $newsletterStatistic['recipientsCount']);
            $newsletter->setAttribute('processedRecipientsCount', $newsletterStatistic['processedRecipientsCount']);
            $newsletter->setAttribute('sentRecipientsCount', $newsletterStatistic['sentRecipientsCount']);
        }

        $viewStatistics = $this->_em->createQueryBuilder()
            ->from('MetalNewsletterBundle:NewsletterViewer', 'newsletterViewer')
            ->select('IDENTITY(recipient.newsletter) AS newsletterId')
            ->addSelect('COUNT(newsletterViewer) AS _count')
            ->join('newsletterViewer.recipient', 'recipient')
            ->where('recipient.newsletter IN (:newsletterIds)')
            ->setParameter('newsletterIds', array_keys($directedNewsletters))
            ->groupBy('recipient.newsletter')
            ->getQuery()
            ->getResult();

        foreach ($viewStatistics as $viewStatistic) {
            $newsletter = $directedNewsletters[$viewStatistic['newsletterId']];
            $newsletter->setAttribute('viewsCount', $viewStatistic['_count']);
        }
    }
}
