<?php

namespace Metal\CompaniesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Metal\UsersBundle\Entity\User;

/**
 * @ORM\Entity
 * @ORM\Table(name="company_log")
 */
class CompanyLog
{

    /**
     * @ORM\Id
     * @ORM\OneToOne(targetEntity="Metal\CompaniesBundle\Entity\Company")
     * @ORM\JoinColumn(name="company_id", referencedColumnName="Message_ID", nullable=false)
     *
     * @var Company
     */
    protected $company;

    /**
     * @ORM\ManyToOne(targetEntity="Metal\UsersBundle\Entity\User")
     * @ORM\JoinColumn(name="created_by", referencedColumnName="User_ID", nullable=true)
     *
     * @var User
     */
    protected $createdBy;

    /**
     * @ORM\ManyToOne(targetEntity="Metal\UsersBundle\Entity\User")
     * @ORM\JoinColumn(name="updated_by", referencedColumnName="User_ID", nullable=true)
     */
    protected $updatedBy;

    /** @ORM\Column(type="string", length=15, name="ip", nullable=false) */
    protected $ip;

    /** @ORM\Column(type="string", length=15, name="last_ip", nullable=true, options={"default":null}) */
    protected $lastIp;

    /** @ORM\Column(type="string", length=255, name="user_agent", nullable=false) */
    protected $userAgent;

    /** @ORM\Column(type="string", length=255, name="last_user_agent", nullable=false, options={"default":null}) */
    protected $lastUserAgent;

    public function __construct()
    {
        $this->ip = '';
        $this->lastIp = '';
        $this->userAgent = '';
        $this->lastUserAgent = '';
    }

    /**
     * @param Company $company
     */
    public function setCompany(Company $company)
    {
        $this->company = $company;
    }

    /**
     * @return Company
     */
    public function getCompany()
    {
        return $this->company;
    }

    /**
     * @param User $createdBy
     */
    public function setCreatedBy(User $createdBy = null)
    {
        // null required for validation
        $this->createdBy = $createdBy;
    }

    /**
     * @return User
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    public function setIp($ip)
    {
        $this->ip = (string)$ip;
    }

    public function getIp()
    {
        return $this->ip;
    }

    public function setLastIp($lastIp)
    {
        $this->lastIp = (string)$lastIp;
    }

    public function getLastIp()
    {
        return $this->lastIp;
    }

    public function setLastUserAgent($lastUserAgent)
    {
        $this->lastUserAgent = (string)$lastUserAgent;
    }

    public function getLastUserAgent()
    {
        return $this->lastUserAgent;
    }

    /**
     * @param User $updatedBy
     */
    public function setUpdatedBy(User $updatedBy)
    {
        $this->updatedBy = $updatedBy;
    }

    /**
     * @return User
     */
    public function getUpdatedBy()
    {
        return $this->updatedBy;
    }

    public function setUserAgent($userAgent)
    {
        $this->userAgent = (string)$userAgent;
    }

    public function getUserAgent()
    {
        return $this->userAgent;
    }


}
