<?php

namespace Metal\ContentBundle\Indexer;

use Brouzie\Sphinxy\Indexer\DoctrineQbIndexer;
use Metal\ContentBundle\Entity\ValueObject\StatusTypeProvider;

class ContentEntryIndexer extends DoctrineQbIndexer
{
    public function processItems(array $items)
    {
        $entriesIds = array_keys($items);
        $tags = $this->em->getRepository('MetalContentBundle:ContentEntryTag')
            ->getTagsByContentEntries($entriesIds);
        $commentsCountToEntries = $this->em->getRepository('MetalContentBundle:Comment')
            ->getCommentsCountByContentEntries($entriesIds);
        $categoriesToEntries = $this->em->getRepository('MetalContentBundle:AbstractContentEntry')
            ->getCategoriesIdsForEntries($entriesIds);

        $tagsToEntries = array();
        foreach ($tags as $tag) {
            $tagsToEntries[$tag['contentEntryId']][] = $tag['id'];
        }

        foreach ($items as $i => $entry) {
            $id = $entry['contentEntryId'];
            $description = $entry['description'].' '.$entry['shortDescription'];

            $items[$i] = array(
                'id' => $id,
                'categories_ids' => $categoriesToEntries[$id],
                'tags_ids' => isset($tagsToEntries[$id]) ? $tagsToEntries[$id] : array(),
                'entry_type' => $entry['entry_type'],
                'subject_id' => $entry['subjectTypeId'],
                'comments_count' => isset($commentsCountToEntries[$id]) ? $commentsCountToEntries[$id] : 0,
                'title' => $entry['title'],
                'title_field' => $entry['title'],
                'description' => $description,
                'description_field' => $description,
                'created_at' => $entry['createdAt']->getTimestamp(),
            );
        }

        return $items;
    }

    protected function getQueryBuilder()
    {
        return $this->em
            ->createQueryBuilder()
            ->select('e')
            ->from('MetalContentBundle:AbstractContentEntry', 'e', 'e.contentEntryId')
            ->andWhere('e.statusTypeId IN (:statuses)')
            //TODO: delete POTENTIAL_SPAM after moderation
            ->setParameter('statuses', array(StatusTypeProvider::POTENTIAL_SPAM, StatusTypeProvider::CHECKED));
    }
}
