<?php

namespace Metal\ContentBundle\Repository;

use Metal\ContentBundle\Entity\ValueObject\StatusTypeProvider;
use Metal\ProjectBundle\Doctrine\EntityRepository;

class ContentEntryRepository extends EntityRepository
{
    public function loadContentEntry($id)
    {
        $contentEntry = $this->findOneBy(array(
            'id' => $id,
            'statusTypeId' => array(StatusTypeProvider::CHECKED, StatusTypeProvider::POTENTIAL_SPAM)
        ));

        if ($contentEntry) {
            $this
                ->_em
                ->getRepository('MetalContentBundle:ContentEntryTag')
                ->attachTagsToContentEntries(array($contentEntry));
        }

        return $contentEntry;
    }

    /**
     * @param array $entriesIds
     *
     * @return array [entryId => [categoryId, categoryId, ...]]
     */
    public function getCategoriesIdsForEntries(array $entriesIds = array())
    {
        if (!$entriesIds) {
            return array();
        }

        $entriesToCategories = array_fill_keys($entriesIds, array());

        $entryCategories = $this->_em->getRepository('MetalContentBundle:AbstractContentEntry')
            ->createQueryBuilder('ace')
            ->select('ace.contentEntryId as entryId')
            ->addSelect('IDENTITY(ace.category) AS categoryId')
            ->addSelect('IDENTITY(ace.categorySecondary) AS categorySecondaryId')
            ->where('ace.contentEntryId IN (:entries_ids)')
            ->setParameter('entries_ids', $entriesIds)
            ->getQuery()
            ->getArrayResult();

        $categoriesIds = array_unique(array_merge(array_column($entryCategories, 'categoryId'), array_column($entryCategories, 'categorySecondaryId')));

        $categories = $this->_em->createQueryBuilder()
            ->from('MetalContentBundle:CategoryClosure', 'cc')
            ->select('IDENTITY(cc.descendant) as descendant')
            ->addSelect('IDENTITY(cc.ancestor) as ancestor')
            ->where('cc.descendant IN (:categories_ids)')
            ->setParameter('categories_ids', $categoriesIds)
            ->getQuery()
            ->getResult();

        $categoriesToDescendant = array();
        foreach ($categories as $category) {
            $categoriesToDescendant[$category['descendant']][] = $category['ancestor'];
        }

        foreach ($entryCategories as $entryCategory) {
            if (!isset($entriesToCategories[$entryCategory['entryId']])) {
                $entriesToCategories[$entryCategory['entryId']] = array();
            }

            $entriesToCategories[$entryCategory['entryId']] = array_unique(
                array_merge(
                    $entriesToCategories[$entryCategory['entryId']],
                    $categoriesToDescendant[$entryCategory['categoryId']],
                    isset($categoriesToDescendant[$entryCategory['categorySecondaryId']]) ? $categoriesToDescendant[$entryCategory['categorySecondaryId']] : array()
                )
            );
        }

        return $entriesToCategories;
    }
}
