<?php

namespace Metal\ContentBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Metal\ContentBundle\Entity\AbstractContentEntry;
use Metal\ContentBundle\Entity\ContentEntryTag;

class ContentEntryTagRepository extends EntityRepository
{
    /**
     * @param AbstractContentEntry[] $contentEntries
     */
    public function attachTagsToContentEntries($contentEntries)
    {
        $tags = $this->getTagsByContentEntries($contentEntries);

        $tagsToEntries = array();
        foreach ($tags as $tag) {
            $tagsToEntries[$tag['contentEntryId']][] = $tag;
        }

        foreach ($contentEntries as $contentEntry) {
            $id = $contentEntry->getContentEntryId();
            $contentEntry->setAttribute(
                'content_tags',
                isset($tagsToEntries[$id]) ? $tagsToEntries[$id] : array()
            );
        }
    }

    /**
     * @param AbstractContentEntry[]|int[] $contentEntries
     *
     * @return array
     */
    public function getTagsByContentEntries($contentEntries)
    {
        if (!count($contentEntries)) {
            return array();
        }

        return $this->_em->createQueryBuilder()
            ->select('t.id, t.title, IDENTITY(cet.contentEntry) AS contentEntryId')
            ->from('MetalContentBundle:ContentEntryTag', 'cet')
            ->join('cet.tag', 't')
            ->where('cet.contentEntry IN (:content_entries)')
            ->setParameter('content_entries', $contentEntries)
            ->getQuery()
            ->getResult();
    }
}
