<?php

namespace Metal\ContentBundle\DataFetching;

use Doctrine\ORM\EntityManagerInterface;
use Metal\ContentBundle\DataFetching\Spec\ContentEntryLoadingSpec;
use Metal\ContentBundle\Entity\AbstractContentEntry;
use Metal\ProjectBundle\DataFetching\ConcreteEntityLoader;
use Metal\ProjectBundle\DataFetching\Spec\LoadingSpec;
use Metal\ProjectBundle\DataFetching\UnsupportedSpecException;

class ContentEntriesEntityLoader implements ConcreteEntityLoader
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function getEntitiesByRows(\Traversable $rows, LoadingSpec $options = null)
    {
        if (null === $options) {
            $options = new ContentEntryLoadingSpec();
        } elseif (!$options instanceof ContentEntryLoadingSpec) {
            throw UnsupportedSpecException::create(ContentEntryLoadingSpec::class, $options);
        }

        $entriesIds = array_column(iterator_to_array($rows), 'comments_count', 'id');

        $entriesRepository = $this->em->getRepository('MetalContentBundle:AbstractContentEntry');
        $entries = $entriesRepository->findByIds(array_keys($entriesIds));
        /* @var $entries AbstractContentEntry[] */

        if ($options->attachTags) {
            $this->em->getRepository('MetalContentBundle:ContentEntryTag')->attachTagsToContentEntries($entries);
        }

        $this->em->getRepository('MetalContentBundle:Category')->attachCategoriesToContentEntries($entries);

        foreach ($entries as $entry) {
            $entry->setAttribute('comments_count', $entriesIds[$entry->getContentEntryId()]);
        }

        return $entries;
    }
}
