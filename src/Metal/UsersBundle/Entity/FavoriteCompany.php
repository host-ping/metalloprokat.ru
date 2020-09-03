<?php

namespace Metal\UsersBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use Metal\CompaniesBundle\Entity\Company;
use Metal\ProjectBundle\Entity\Behavior\Attributable;

/**
 * @ORM\Entity(repositoryClass="Metal\UsersBundle\Repository\FavoriteCompanyRepository")
 * @ORM\Table(name="favorite_company", uniqueConstraints={
 *     @ORM\UniqueConstraint(name="UNIQ_user_company", columns={"user_id", "company_id"} )})
 */
class FavoriteCompany
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer", name="id")
     */
    protected $id;

    /**
     * @ORM\Column(type="datetime", name="created_at")
     *
     * @var \DateTime
     */
    protected $createdAt;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="User_ID", nullable=false)
     *
     * @var User
     */
    protected $user;

    /** @ORM\Column(type="text", name="comment", nullable=false) */
    protected $comment;

    /**
     * @ORM\ManyToOne(targetEntity="Metal\CompaniesBundle\Entity\Company")
     * @ORM\JoinColumn(name="company_id", referencedColumnName="Message_ID", nullable=false)
     *
     * @var Company
     */
    protected $company;

    /**
     * @ORM\Column(type="datetime", name="comment_updated_at", nullable=true)
     *
     * @var \DateTime
     */
    protected $commentUpdatedAt;

    use Attributable;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->comment = '';
    }

    public function getId()
    {
        return $this->id;
    }

    public function getCreatedAt()
    {
        return $this->createdAt;
    }

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
    public function setUser(User $user)
    {
        $this->user = $user;
    }

    public function getComment()
    {
        return $this->comment;
    }

    public function setComment($comment)
    {
        $this->comment = (string)$comment;
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
     * @param \DateTime $commentUpdatedAt
     */
    public function setCommentUpdatedAt(\DateTime $commentUpdatedAt)
    {
        $this->commentUpdatedAt = $commentUpdatedAt;
    }

    /**
     * @return \DateTime
     */
    public function getCommentUpdatedAt()
    {
        return $this->commentUpdatedAt;
    }
}
