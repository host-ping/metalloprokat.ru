<?php

namespace Metal\ContentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Metal\ContentBundle\Repository\InstagramCommentRepository")
 * @ORM\Table(name="instagram_comment")
 */
class InstagramComment extends AbstractComment
{
    /**
     * @ORM\ManyToOne(targetEntity="InstagramPhoto")
     * @ORM\JoinColumn(name="photo_id", referencedColumnName="id")
     *
     * @var InstagramPhoto
     */
    protected $instagramPhoto;

    /**
     * @ORM\ManyToOne(targetEntity="InstagramComment")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id", onDelete="CASCADE")
     *
     * @var InstagramComment
     */
    protected $parent;

    /**
     * @return InstagramPhoto
     */
    public function getInstagramPhoto()
    {
        return $this->instagramPhoto;
    }

    /**
     * @param InstagramPhoto $instagramPhoto
     */
    public function setInstagramPhoto(InstagramPhoto $instagramPhoto)
    {
        $this->instagramPhoto = $instagramPhoto;
    }
}
