<?php

namespace Metal\ContentBundle\DataFetching\Spec;

use Metal\ContentBundle\Entity\AbstractContentEntry;
use Metal\ContentBundle\Entity\Category;
use Metal\ContentBundle\Entity\Tag;
use Metal\ContentBundle\Entity\ValueObject\SubjectTypeProvider;
use Metal\ProjectBundle\DataFetching\Spec\CacheableSpec;
use Metal\ProjectBundle\DataFetching\Spec\FilteringSpec;
use Symfony\Component\HttpFoundation\Request;

class ContentEntryFilteringSpec extends FilteringSpec implements CacheableSpec
{
    public $entryTypeId;
    public $categoryId;
    public $createdAtId;
    public $subjectTypeId;
    public $tagsIds = array();
    public $exceptEntriesIds = array();
    public $match;

    private $availableTimePeriods = array(1, 2, 3);

    public function categoryId($categoryId)
    {
        $this->categoryId = $categoryId;

        return $this;
    }

    public function category(Category $category = null)
    {
        if ($category) {
            $this->categoryId($category->getId());
        }

        return $this;
    }

    public function createdAtId($createdAtId)
    {
        $this->createdAtId = $createdAtId;
        if (!in_array($createdAtId, $this->availableTimePeriods)) {
            $this->createdAtId = null;
        }

        return $this;
    }

    public function subjectTypeId($subjectTypeId)
    {
        $this->subjectTypeId = $subjectTypeId;
        if (!in_array($subjectTypeId, SubjectTypeProvider::getAllTypesIds())) {
            $this->subjectTypeId = null;
        }

        return $this;
    }

    public function entryType($entryType)
    {
        switch ($entryType) {
            case 'ENTRY_TYPE_TOPIC':
            case AbstractContentEntry::ENTRY_TYPE_TOPIC:
                $this->entryTypeId = AbstractContentEntry::ENTRY_TYPE_TOPIC;
                break;

            case 'ENTRY_TYPE_QUESTION':
            case AbstractContentEntry::ENTRY_TYPE_QUESTION:
                $this->entryTypeId = AbstractContentEntry::ENTRY_TYPE_QUESTION;
                break;
        }

        return $this;
    }

    public function tags($tags)
    {
        foreach ($tags as $tag) {
            if ($tag instanceof Tag) {
                $this->tagsIds[] = $tag->getId();
            } else {
                $this->tagsIds[] = $tag['id'];
            }
        }

        return $this;
    }

    public function match($match)
    {
        $this->match = $match;

        return $this;
    }

    public function exceptEntryId($id)
    {
        $this->exceptEntriesIds[] = $id;

        return $this;
    }

    /**
     * @param Request $request
     *
     * @return static
     */
    public static function createFromRequest(Request $request)
    {
        $specification = (new static())
            ->category($request->attributes->get('content_category'));

        if ($subjectId = $request->query->get('subject')) {
            $specification->subjectTypeId($subjectId);
        }

        if ($timePeriodId = $request->query->get('filter')) {
            $specification->createdAtId($timePeriodId);
        }

        if ($entryType = $request->query->get('type')) {
            $specification->entryType($entryType);
        }

        if ($q = $request->query->get('q')) {
            $specification->match($q);
        }

        return $specification;
    }

    public function getCacheKey()
    {
        if (array() !== $this->exceptEntriesIds) {
            return null;
        }

        return sha1(
            serialize(
                array(
                    'class' => __CLASS__,
                    'entryTypeId' => $this->entryTypeId,
                    'categoryId' => $this->categoryId,
                )
            )
        );
    }
}
