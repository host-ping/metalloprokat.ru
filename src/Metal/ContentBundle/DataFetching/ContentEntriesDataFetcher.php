<?php

namespace Metal\ContentBundle\DataFetching;

use Brouzie\Sphinxy\Query\ResultSet;
use Brouzie\Sphinxy\QueryBuilder;
use Metal\ContentBundle\DataFetching\Spec\ContentEntryFilteringSpec;
use Metal\ContentBundle\DataFetching\Spec\ContentEntryOrderingSpec;
use Metal\ProjectBundle\DataFetching\Sphinxy\ConcreteDataFetcher;
use Metal\ProjectBundle\DataFetching\Spec\FilteringSpec;
use Metal\ProjectBundle\DataFetching\Spec\OrderingSpec;
use Metal\ProjectBundle\DataFetching\UnsupportedSpecException;

class ContentEntriesDataFetcher implements ConcreteDataFetcher
{
    public function initializeQueryBuilder(QueryBuilder $qb)
    {
        $qb
            ->select('id, title, comments_count')
            ->from('content_entry');
    }

    public function applyFilteringSpec(QueryBuilder $qb, FilteringSpec $criteria)
    {
        if (!$criteria instanceof ContentEntryFilteringSpec) {
            throw UnsupportedSpecException::create(ContentEntryFilteringSpec::class, $criteria);
        }

        if (null !== $criteria->categoryId) {
            $qb
                ->andWhere('categories_ids = :category_id')
                ->setParameter('category_id', $criteria->categoryId);
        }

        if ($criteria->entryTypeId) {
            $qb
                ->andWhere('entry_type = :entry_type_id')
                ->setParameter('entry_type_id', $criteria->entryTypeId);
        }

        if ($criteria->tagsIds) {
            $qb
                ->andWhere('tags_ids IN :tags_ids')
                ->setParameter('tags_ids', $criteria->tagsIds);
        }

        if ($criteria->createdAtId) {
            $periods = array(
                1 => '-1 day',
                2 => '-1 week',
                3 => '-1 month',
            );

            $qb
                ->andWhere('created_at >= :date_period')
                ->setParameter('date_period', (new \DateTime($periods[$criteria->createdAtId]))->getTimestamp());
        }

        if ($criteria->subjectTypeId) {
            $qb
                ->andWhere('subject_id = :subject_id')
                ->setParameter('subject_id', $criteria->subjectTypeId);
        }

        if ($criteria->exceptEntriesIds) {
            $qb->andWhere('id NOT IN :excluded_ids')
                ->setParameter('excluded_ids', $criteria->exceptEntriesIds);
        }

        if ($criteria->match) {
            $match = $qb->getEscaper()->halfEscapeMatch($criteria->match);

            $qb->andWhere('MATCH (:match_title)')
                ->setParameter('match_title', "(@title_field $match) | (@description_field $match)")
                ->setOption('field_weights', '(title_field = 100, description_field = 10)');
        }
    }

    public function applyOrderingSpec(QueryBuilder $qb, OrderingSpec $orderBy = null)
    {
        if (null === $orderBy) {
            $orderBy = new ContentEntryOrderingSpec();
        } elseif (!$orderBy instanceof ContentEntryOrderingSpec) {
            throw UnsupportedSpecException::create(ContentEntryOrderingSpec::class, $orderBy);
        }

        $orders = $orderBy->getOrders();
        foreach ($orders as $order => $orderAttributes) {
            switch ($order) {
                case 'createdAt':
                    $qb->addOrderBy('created_at', 'DESC');
                    break;

                case 'commentsCount':
                    $qb->addOrderBy('comments_count', 'DESC');
                    break;

                case 'relevancy':
                    $qb->addOrderBy('WEIGHT()', 'DESC');
                    break;

                case 'random':
                    if ($orderAttributes) {
                        $qb->addSelect('DISORDERLY(:random_seed) random_order')
                            ->setParameter('random_seed', $orderAttributes);
                    } else {
                        $qb->addSelect('DISORDERLY() AS random_order');
                    }
                    $qb->addOrderBy('random_order');
                    break;

                case 'tagsMatching':
                    $parts = array();
                    foreach ($orderAttributes as $tagId) {
                        $parts[] = sprintf('IN (tags_ids, %s)', $qb->createParameter($tagId, 'tag_'));
                    }

                    $qb
                        ->addSelect(sprintf('(%s) AS matched_tags_count', implode(' + ', $parts)))
                        ->addOrderBy('matched_tags_count', 'DESC');
                    break;

                default:
                    throw new \InvalidArgumentException(sprintf('Wrong order "%s"', $order));
            }
        }
    }

    public function filterResultSet(ResultSet $resultSet, FilteringSpec $criteria, $offset, $length)
    {
    }
}
