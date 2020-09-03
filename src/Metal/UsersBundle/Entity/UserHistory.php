<?php

namespace Metal\UsersBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Metal\UsersBundle\Entity\ValueObject\ActionType;
use Metal\UsersBundle\Entity\ValueObject\ActionTypeProvider;

/**
 * @ORM\Entity()
 * @ORM\Table(name="user_history")
 * @ORM\HasLifecycleCallbacks
 */
class UserHistory
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer", name="id")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="User_ID", nullable=false)
     *
     * @var User
     */
    protected $user;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="author_id", referencedColumnName="User_ID", nullable=false)
     *
     * @var User
     */
    protected $author;

    /**
     * @ORM\Column(type="datetime", name="created_at")
     *
     * @var \DateTime
     */
    protected $createdAt;

    /** @ORM\Column(type="text", name="comment", nullable=true) */
    protected $comment;

    /**
     * @ORM\Column(type="integer", name="action_id")
     */
    protected $actionId;

    /**
     * @var ActionType
     */
    protected $action;


    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    /**
     * @ORM\PostLoad
     */
    public function postLoad()
    {
        $this->action = ActionTypeProvider::create($this->actionId);
    }

    public function getId()
    {
        return $this->id;
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
    public function setUser(User $user)
    {
        $this->user = $user;
    }

    /**
     * @return User
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * @param User $author
     */
    public function setAuthor(User $author)
    {
        $this->author = $author;
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

    public function getComment()
    {
        return $this->comment;
    }

    public function setComment($comment)
    {
        $this->comment = $comment;
    }

    /**
     * @return mixed
     */
    public function getActionId()
    {
        return $this->actionId;
    }

    /**
     * @param mixed $actionId
     */
    public function setActionId($actionId)
    {
        $this->actionId = $actionId;
        $this->postLoad();
    }

    /**
     * @return ActionType
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * @param ActionType $action
     */
    public function setAction(ActionType $action)
    {
        $this->action = $action;
        $this->actionId = $action->getId();
    }

}