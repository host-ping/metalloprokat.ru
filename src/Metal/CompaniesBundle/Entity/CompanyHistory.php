<?php

namespace Metal\CompaniesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use Metal\CompaniesBundle\Entity\ValueObject\ActionType;
use Metal\CompaniesBundle\Entity\ValueObject\ActionTypeProvider;
use Metal\UsersBundle\Entity\User;

/**
 * @ORM\Entity()
 * @ORM\Table(name="company_history")
 * @ORM\HasLifecycleCallbacks
 */
class CompanyHistory
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer", name="id")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Metal\CompaniesBundle\Entity\Company")
     * @ORM\JoinColumn(name="company_id", referencedColumnName="Message_ID", nullable=true)
     *
     * @var Company
     */
    protected $company;

    /**
     * @ORM\ManyToOne(targetEntity="Metal\CompaniesBundle\Entity\Company")
     * @ORM\JoinColumn(name="related_company_id", referencedColumnName="Message_ID", nullable=true)
     *
     * @var Company
     */
    protected $relatedCompany;

    /**
     * @ORM\ManyToOne(targetEntity="Metal\UsersBundle\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="User_ID", nullable=true)
     *
     * @var User
     */
    protected $user;

    /**
     * @ORM\ManyToOne(targetEntity="Metal\UsersBundle\Entity\User")
     * @ORM\JoinColumn(name="author_id", referencedColumnName="User_ID", nullable=true)
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

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
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

    public function getComment()
    {
        return $this->comment;
    }

    public function setComment($comment)
    {
        $this->comment = $comment;
    }

    /**
     * @return Company
     */
    public function getCompany()
    {
        return $this->company;
    }

    /**
     * @param Company $company
     */
    public function setCompany(Company $company)
    {
        $this->company = $company;
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
     * @return Company
     */
    public function getRelatedCompany()
    {
        return $this->relatedCompany;
    }

    /**
     * @param Company $relatedCompany
     */
    public function setRelatedCompany(Company $relatedCompany)
    {
        $this->relatedCompany = $relatedCompany;
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


} 