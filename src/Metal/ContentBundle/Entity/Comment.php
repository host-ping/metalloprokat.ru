<?php

namespace Metal\ContentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Metal\ContentBundle\Repository\CommentRepository")
 * @ORM\Table(name="content_comment")
 */
class Comment extends AbstractComment
{
    /**
     * @ORM\ManyToOne(targetEntity="AbstractContentEntry")
     * @ORM\JoinColumn(name="content_entry_id", referencedColumnName="content_entry_id", nullable=false, onDelete="CASCADE")
     *
     * @var AbstractContentEntry
     */
    protected $contentEntry;

    /**
     * @ORM\ManyToOne(targetEntity="Comment")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id", onDelete="CASCADE")
     *
     * @var Comment
     */
    protected $parent;

    /**
     * @return AbstractContentEntry
     */
    public function getContentEntry()
    {
        return $this->contentEntry;
    }

    /**
     * @param AbstractContentEntry $contentEntry
     */
    public function setContentEntry(AbstractContentEntry $contentEntry)
    {
        $this->contentEntry = $contentEntry;
    }
}
