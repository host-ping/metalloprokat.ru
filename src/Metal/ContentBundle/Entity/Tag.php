<?php

namespace Metal\ContentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Metal\ContentBundle\Entity\ValueObject\StatusType;
use Metal\ContentBundle\Entity\ValueObject\StatusTypeProvider;
use Metal\ProjectBundle\Entity\Behavior\Updateable;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="Metal\ContentBundle\Repository\TagRepository")
 * @ORM\Table(name="content_tag")
 * @ORM\HasLifecycleCallbacks
 */
class Tag
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer", name="id")
     */
    protected $id;

    /**
     * @ORM\Column(length=100, name="title", nullable=false)
     * @Assert\NotBlank()
     */
    protected $title;

    /**
     * @ORM\Column(type="integer", name="status_type_id")
     */
    protected $statusTypeId;

    /**
     * @var StatusType
     */
    protected $statusType;

    /**
     * @ORM\Column(type="datetime", name="created_at", nullable=false)
     *
     * @var \DateTime
     */
    protected $createdAt;

    use Updateable;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
        $this->setStatusTypeId(StatusTypeProvider::NOT_CHECKED);
    }

    /**
     * @ORM\PostLoad
     */
    public function postLoad()
    {
        $this->statusType = StatusTypeProvider::create($this->statusTypeId);
    }

    public function getId()
    {
        return $this->id;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function getStatusTypeId()
    {
        return $this->statusTypeId;
    }

    public function setStatusTypeId($statusTypeId)
    {
        $this->statusTypeId = $statusTypeId;
        $this->postLoad();
    }

    /**
     * @return StatusType
     */
    public function getStatusType()
    {
        return $this->statusType;
    }

    /**
     * @param StatusType $statusType
     */
    public function setStatusType(StatusType $statusType)
    {
        $this->statusType = $statusType;
        $this->statusTypeId = $statusType->getId();
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
}
