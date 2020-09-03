<?php

namespace Metal\ContentBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Metal\ContentBundle\Entity\ContentImageAlbum;

class ContentImageRepository extends EntityRepository
{
    /**
     * @param ContentImageAlbum[] $albums
     */
    public function attachImagesToAlbums($albums)
    {
        if (!count($albums)) {
            return array();
        }

         $images = $this->_em->createQueryBuilder()
            ->select('ci AS image, IDENTITY(ci.album) AS albumId')
            ->from('MetalContentBundle:ContentImage', 'ci')
            ->where('ci.album IN (:albums)')
            ->setParameter('albums', $albums)
            ->getQuery()
            ->getResult();

        $imagesToAlbums = array();
        foreach ($images as $image) {
            $imagesToAlbums[$image['albumId']][] = $image['image'];
        }

        foreach ($albums as $album) {
            $id = $album->getId();
            $album->setAttribute(
                'content_images',
                isset($imagesToAlbums[$id]) ? $imagesToAlbums[$id] : array()
            );
        }
    }
}