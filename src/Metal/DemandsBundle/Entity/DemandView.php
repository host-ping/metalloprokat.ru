<?php

namespace Metal\DemandsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Metal\CompaniesBundle\Entity\Company;
use Metal\UsersBundle\Entity\User;

/**
 * @ORM\Entity(repositoryClass="Metal\DemandsBundle\Repository\DemandViewRepository", readOnly=true)
 * @ORM\Table(name="demand_view", indexes={
 *     @ORM\Index(name="IDX_user_id", columns={"user_id"}),
 *     @ORM\Index(name="IDX_demand_id", columns={"demand_id"}),
 *     @ORM\Index(name="IDX_company_id", columns={"company_id"}),
 *     @ORM\Index(name="IDX_viewed_at", columns={"viewed_at"})
 *  }))
 */
class DemandView
{
    const MAX_CONTACTS_VIEW_COUNT_FOR_PROMOCODE = 30;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer", name="id")
     */
    protected $id;

    /**
     * @ORM\Column(type="datetime", name="viewed_at")
     *
     * @var AbstractDemand
     */
    protected $viewedAt;

    /**
     * @ORM\Column(type="integer", name="demand_id")
     */
    protected $demandId;

    /**
     * @ORM\ManyToOne(targetEntity="AbstractDemand")
     * @ORM\JoinColumn(name="demand_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     *
     * @var AbstractDemand
     */
    protected $demand;

    /**
     * @ORM\ManyToOne(targetEntity="Metal\UsersBundle\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="User_ID", nullable=false)
     *
     * @var User
     */
    protected $user;

    /**
     * @ORM\ManyToOne(targetEntity="Metal\CompaniesBundle\Entity\Company")
     * @ORM\JoinColumn(name="company_id", referencedColumnName="Message_ID")
     *
     * @var Company
     */
    protected $company;

    /**
     * @ORM\Column(length=25, name="ip", options={"default": ""})
     */
    protected $ip;

    /**
     * @ORM\Column(name="is_export", type="smallint", nullable=false, options={"default":0})
     */
    protected $isExport;

    public function __construct()
    {
        $this->viewedAt = new \DateTime();
        $this->isExport = false;
        $this->ip = '';
    }

    public function getId()
    {
        return $this->id;
    }

    public function setDemandId($demandId)
    {
        $this->demandId = $demandId;
    }

    /**
     * @param AbstractDemand $demand
     */
    public function setDemand(AbstractDemand $demand)
    {
        $this->demand = $demand;
        $this->demandId = $demand->getId();
    }

    /**
     * @return AbstractDemand
     */
    public function getDemand()
    {
        return $this->demand;
    }

    public function setUser(User $user)
    {
        $this->user = $user;
    }

    public function getUser()
    {
        return $this->user;
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
     * @param \DateTime $viewedAt
     */
    public function setViewedAt(\DateTime $viewedAt)
    {
        $this->viewedAt = $viewedAt;
    }

    public function getViewedAt()
    {
        return $this->viewedAt;
    }

    public function getIsExport()
    {
        return $this->isExport;
    }

    public function setIsExport($isExport)
    {
        $this->isExport = $isExport;
    }

    public function getIp()
    {
        return $this->ip;
    }

    public function setIp($ip)
    {
        $this->ip = (string) $ip;
    }

    public function toArray()
    {
        return array(
            'user_id' => $this->user->getId(),
            'company_id' => $this->company ? $this->company->getId() : null,
            'demand_id' => $this->demandId,
            'viewed_at' => $this->viewedAt->format('Y-m-d H:i:s'),
            'is_export' => $this->isExport,
            'ip' => $this->ip,
        );
    }
}
