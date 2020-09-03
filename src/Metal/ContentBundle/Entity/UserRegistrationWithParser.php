<?php

namespace Metal\ContentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use Metal\CompaniesBundle\Entity\Company;
use Metal\UsersBundle\Entity\User;

/**
 * @ORM\Entity()
 * @ORM\Table(name="user_registration_with_parser")
 */
class UserRegistrationWithParser
{
    /**
     * @ORM\Id
     * @ORM\OneToOne(targetEntity="Metal\UsersBundle\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="User_ID", nullable=false)
     *
     * @var User
     */
    protected $user;

    /**
     * @ORM\OneToOne(targetEntity="Metal\CompaniesBundle\Entity\Company")
     * @ORM\JoinColumn(name="company_id", referencedColumnName="Message_ID", nullable=false)
     *
     * @var Company
     */
    protected $company;

    /** @ORM\Column(length=50, name="site_code") */
    protected $siteCode;

    /** @ORM\Column(length=255, name="url") */
    protected $url;

    /**
     * @ORM\Column(type="integer", name="page", nullable=false, options={"default":0})
     */
    protected $page;

    /**
     * @ORM\Column(type="datetime", name="created_at")
     *
     * @var \DateTime
     */
    protected $createdAt;

    /**
     * @ORM\Column(type="boolean", name="notified", nullable=false, options={"default":0})
     */
    protected $notified;

    public function __construct()
    {
        $this->siteCode = '';
        $this->createdAt = new \DateTime();
        $this->page = 0;
        $this->notified = false;
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

    public function getSiteCode()
    {
        return $this->siteCode;
    }

    public function setSiteCode($siteCode)
    {
        $this->siteCode = $siteCode;
    }

    public function getUrl()
    {
        return $this->url;
    }

    public function setUrl($url)
    {
        $this->url = $url;
    }

    public function getPage()
    {
        return $this->page;
    }

    public function setPage($page)
    {
        $this->page = $page;
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

    public function getNotified()
    {
        return $this->notified;
    }

    public function setNotified($notified)
    {
        $this->notified = $notified;
    }
}
