<?php

namespace Metal\ContentBundle\Repository;

use Doctrine\ORM\QueryBuilder;
use Metal\ContentBundle\Entity\AbstractContentEntry;
use Metal\ContentBundle\Entity\ValueObject\StatusTypeProvider;

class CommentRepository extends AbstractCommentRepository
{
    /**
     * @param AbstractContentEntry[]|int[] $contentEntries
     *
     * @return array
     */
    public function getCommentsCountByContentEntries($contentEntries)
    {
        if (!count($contentEntries)) {
            return array();
        }

        $contentEntriesToCommentsCount = $this->_em->createQueryBuilder()
            ->select('COUNT(c.id) AS _count, IDENTITY(c.contentEntry) AS contentEntryId')
            ->from('MetalContentBundle:Comment', 'c')
            ->where('c.contentEntry IN (:content_entries)')
            ->andWhere('c.statusTypeId IN (:statuses)')
            ->setParameter('content_entries', $contentEntries)
            //TODO: delete POTENTIAL_SPAM after moderation
            ->setParameter('statuses', array(StatusTypeProvider::NOT_CHECKED, StatusTypeProvider::CHECKED, StatusTypeProvider::POTENTIAL_SPAM))
            ->groupBy('contentEntryId')
            ->getQuery()
            ->getResult();

        return array_column($contentEntriesToCommentsCount, '_count', 'contentEntryId');
    }

    protected function filterQueryByObject(QueryBuilder $qb, $object)
    {
        $qb
            ->andWhere('c.contentEntry = :content_entry')
            ->setParameter('content_entry', $object);
    }
}
