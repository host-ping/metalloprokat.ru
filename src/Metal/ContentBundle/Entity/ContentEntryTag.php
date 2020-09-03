<?php

namespace Metal\ContentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="Metal\ContentBundle\Repository\ContentEntryTagRepository")
 * @ORM\Table(
 *     name="content_entry_tag",
 *     uniqueConstraints={@ORM\UniqueConstraint(name="UNIQ_content_entry_tag", columns={"content_entry_id", "tag_id"} )}
 * )
 */
class ContentEntryTag
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer", name="id")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="AbstractContentEntry", inversedBy="contentEntryTags")
     * @ORM\JoinColumn(name="content_entry_id", referencedColumnName="content_entry_id", onDelete="CASCADE", nullable=false)
     *
     * @var AbstractContentEntry
     */
    protected $contentEntry;

    /**
     * @ORM\ManyToOne(targetEntity="Tag")
     * @ORM\JoinColumn(name="tag_id", referencedColumnName="id", onDelete="CASCADE", nullable=false)
     *
     * @var Tag
     */
    protected $tag;

    /**
     * @ORM\Column(type="datetime", name="created_at", nullable=false)
     *
     * @var \DateTime
     */
    protected $createdAt;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    public function getId()
    {
        return $this->id;
    }

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

    /**
     * @return Tag
     */
    public function getTag()
    {
        return $this->tag;
    }

    /**
     * @param Tag $tag
     */
    public function setTag(Tag $tag)
    {
        $this->tag = $tag;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTime $createdAt
     */
    public function setCreatedAt(\DateTime $createdAt)
    {
        $this->createdAt = $createdAt;
    }

    public function getTagTitle()
    {
        if ($this->tag) {
            return $this->tag->getTitle();
        }

        return null;
    }

}
