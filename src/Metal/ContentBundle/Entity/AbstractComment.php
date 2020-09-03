<?php

namespace Metal\ContentBundle\Entity;

use Metal\ContentBundle\Entity\ValueObject\StatusType;
use Metal\ContentBundle\Entity\ValueObject\StatusTypeProvider;
use Metal\ProjectBundle\Entity\Behavior\Updateable;
use Metal\UsersBundle\Entity\User;

use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\MappedSuperclass(repositoryClass="Metal\ContentBundle\Repository\AbstractCommentRepository")
 */
abstract class AbstractComment
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer", name="id")
     */
    protected $id;

    //NB: Add mapping in the child class
    protected $parent;

    /**
     * @ORM\ManyToOne(targetEntity="Metal\UsersBundle\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="User_ID")
     *
     * @var User
     */
    protected $user;

    /**
     * @ORM\Column(type="text", name="description", nullable=false)
     * @Assert\NotBlank()
     * @Assert\Length(min=5)
     */
    protected $description;

    /**
     * @ORM\Column(length=255, name="email", nullable=true)
     * @Assert\Email(strict=true)
     * @Assert\NotBlank(groups={"anonymous"})
     */
    protected $email;

    /**
     * @ORM\Column(length=255, name="name", nullable=true)
     * @Assert\Length(min=3)
     * @Assert\NotBlank(groups={"anonymous"})
     */
    protected $name;

    /**
     * @ORM\Column(type="boolean", name="notify", nullable=false, options={"default":0})
     */
    protected $notify;

    /**
     * @ORM\Column(type="datetime", name="created_at", nullable=false)
     *
     * @var \DateTime
     */
    protected $createdAt;

    /**
     * @ORM\Column(type="integer", name="status_type_id")
     */
    protected $statusTypeId;

    /**
     * @var StatusType
     */
    protected $statusType;

    use Updateable;

    public function __construct()
    {
        $this->parentId = 0;
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
        $this->notify = false;
        $this->setStatusTypeId(StatusTypeProvider::NOT_CHECKED);

    }

    public function getId()
    {
        return $this->id;
    }

    /**
     * @param AbstractComment|null $parent
     */
    public function setParent(AbstractComment $parent = null)
    {
        $this->parent = $parent;
    }

    /**
     * @return AbstractComment
     */
    public function getParent()
    {
        return $this->parent;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setDescription($description)
    {
        $this->description = $description;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getNotify()
    {
        return $this->notify;
    }

    public function setNotify($notify)
    {
        $this->notify = $notify;
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

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param User $user
     */
    public function setUser(User $user = null)
    {
        $this->user = $user;
    }

    public function getStatusTypeId()
    {
        return $this->statusTypeId;
    }

    public function setStatusTypeId($statusTypeId)
    {
        $this->statusTypeId = $statusTypeId;
        $this->statusType = null;
    }

    /**
     * @return StatusType
     */
    public function getStatusType()
    {
        if (null !== $this->statusType) {
            return $this->statusType;
        }

        return $this->statusType = StatusTypeProvider::create($this->statusTypeId);
    }

    /**
     * @param StatusType $statusType
     */
    public function setStatusType(StatusType $statusType)
    {
        $this->statusType = $statusType;
        $this->statusTypeId = $statusType->getId();
    }

}
