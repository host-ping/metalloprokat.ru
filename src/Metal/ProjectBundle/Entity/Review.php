<?php

namespace Metal\ProjectBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Metal\CategoriesBundle\Entity\Category;
use Metal\ProjectBundle\Entity\Behavior\SoftDeleteable;
use Metal\UsersBundle\Entity\User;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\MappedSuperclass()
 */
class Review
{
    const TYPE_POSITIVE = 1;
    const TYPE_NEGATIVE = 0;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer", name="id")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Metal\UsersBundle\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="User_ID", nullable=true)
     *
     * @var User
     */
    protected $user;

    /** @ORM\Column(type="boolean", name="type", nullable=false) */
    protected $type;

    /** @ORM\Column(type="datetime", name="created_at"), nullable=false */
    protected $createdAt;

    /**
     * @ORM\Column(name="comment")
     * @Assert\NotBlank(groups={"anonymous", "authenticated"})
     * @Assert\Length(groups={"anonymous", "authenticated"}, min="2", max="255")
     */
    protected $comment;

    /**
     * @ORM\Column(length=255, name="mail", nullable=true)
     * @Assert\NotBlank(groups={"anonymous"})
     * @Assert\Email(groups={"anonymous"}, strict=true)
     */
    protected $email;

    /**
     * @ORM\Column(length=255, name="name", nullable=true)
     * @Assert\NotBlank(groups={"anonymous"})
     */
    protected $name;

    /**
     * @ORM\ManyToOne(targetEntity="Metal\UsersBundle\Entity\User")
     * @ORM\JoinColumn(name="deleted_by", referencedColumnName="User_ID", nullable=true)
     */
    protected $deletedBy;

    /**
     * @ORM\ManyToOne(targetEntity="Metal\CategoriesBundle\Entity\Category")
     * @ORM\JoinColumn(name="Category_ID", referencedColumnName="Message_ID", nullable=true)
     *
     * @var Category
     */
    protected $category;

    /**
     * @ORM\Column(type="datetime", name="moderated_at", nullable=true)
     *
     * @var \DateTime
     */
    protected $moderatedAt;

    /**
     * @ORM\ManyToOne(targetEntity="Metal\UsersBundle\Entity\User")
     * @ORM\JoinColumn(name="moderated_by", referencedColumnName="User_ID", nullable=true)
     */
    protected $moderatedBy;

    use SoftDeleteable;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    /**
     * @param \DateTime $createdAt
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    public function isCommentPositive()
    {
        return $this->type == self::TYPE_POSITIVE;
    }

    /**
     * @param \Metal\UsersBundle\Entity\User $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

    /**
     * @return \Metal\UsersBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }

    public function setComment($comment)
    {
        $this->comment = $comment;
    }

    public function getComment()
    {
        return $this->comment;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }

    /**
     * @param User $deletedBy
     */
    public function setDeletedBy(User $deletedBy)
    {
        $this->deletedBy = $deletedBy;
    }

    /**
     * @return User
     */
    public function getDeletedBy()
    {
        return $this->deletedBy;
    }

    /**
     * @param Category $category
     */
    public function setCategory(Category $category)
    {
        $this->category = $category;
    }

    /**
     * @return Category
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @param \DateTime $moderatedAt
     */
    public function setModeratedAt(\DateTime $moderatedAt)
    {
        $this->moderatedAt = $moderatedAt;
    }

    /**
     * @return \DateTime
     */
    public function getModeratedAt()
    {
        return $this->moderatedAt;
    }

    public function setModerated($moderatedAt = true)
    {
        $this->moderatedAt = $moderatedAt ? new \DateTime() : null;
    }

    public function isModerated()
    {
        return $this->moderatedAt !== null;
    }

    /**
     * @param User $moderatedBy
     */
    public function setModeratedBy(User $moderatedBy)
    {
        $this->moderatedBy = $moderatedBy;
    }

    /**
     * @return User
     */
    public function getModeratedBy()
    {
        return $this->moderatedBy;
    }

    public function getFixedEmail()
    {
        if ($this->email) {
            return $this->email;
        }

        if ($this->user) {
            return $this->user->getEmail();
        }

        return null;
    }

    public function getFixedUserTitle()
    {
        if ($this->name) {
            return $this->name;
        }

        if ($this->user) {
            return $this->user->getFirstName();
        }

        return null;
    }

    public static function getTypesAsSimpleArray()
    {
        return array(Review::TYPE_POSITIVE => 'Хорошо', Review::TYPE_NEGATIVE => 'Плохо');
    }
}
