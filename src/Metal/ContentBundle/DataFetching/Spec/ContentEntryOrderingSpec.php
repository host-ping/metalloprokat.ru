<?php

namespace Metal\ContentBundle\DataFetching\Spec;

use Metal\ContentBundle\Entity\Tag;
use Metal\ProjectBundle\DataFetching\Spec\OrderingSpec;
use Symfony\Component\HttpFoundation\Request;

class ContentEntryOrderingSpec extends OrderingSpec
{
    public function createdAt()
    {
        return $this->pushOrder('createdAt');
    }

    public function tagsMatching($tags)
    {
        $tagsIds = array();
        foreach ($tags as $tag) {
            if ($tag instanceof Tag) {
                $tagsIds[] = $tag->getId();
            } else {
                $tagsIds[] = $tag['id'];
            }
        }

        return $this->pushOrder('tagsMatching', $tagsIds);
    }

    public function commentsCount()
    {
        return $this->pushOrder('commentsCount');
    }

    public function relevancy()
    {
        return $this->pushOrder('relevancy');
    }

    public function random($seed = null)
    {
        return $this->pushOrder('random', $seed);
    }

    public function applyFromRequest(Request $request)
    {
        $order = $request->query->get('sort');

        if (2 === (int)$order) {
            $this->commentsCount();

            return true;
        }

        if ('date' === $order) {
            $this->createdAt();

            return true;
        }

        return false;
    }
}
