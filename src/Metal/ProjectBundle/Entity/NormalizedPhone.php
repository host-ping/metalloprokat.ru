<?php

namespace Metal\ProjectBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Metal\CompaniesBundle\Entity\Company;
use Metal\DemandsBundle\Entity\AbstractDemand;
use Metal\UsersBundle\Entity\User;

/**
 * @ORM\Entity
 * @ORM\Table(name="normalized_phones", indexes={@ORM\Index(name="IDX_phone", columns={"phone"})})
 */
class NormalizedPhone
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer", name="id")
     */
    protected $id;

    /**
     * @ORM\Column(length=255, name="phone", nullable=false, options={"default": ""})
     */
    protected $phone;

    /**
     * @ORM\ManyToOne(targetEntity="Metal\CompaniesBundle\Entity\Company")
     * @ORM\JoinColumn(name="company_id", referencedColumnName="Message_ID", onDelete="CASCADE")
     *
     * @var Company
     */
    protected $company;

    /**
     * @ORM\ManyToOne(targetEntity="Metal\UsersBundle\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="User_ID", onDelete="CASCADE")
     *
     * @var User
     */
    protected $user;

    /**
     * @ORM\ManyToOne(targetEntity="Metal\DemandsBundle\Entity\AbstractDemand")
     * @ORM\JoinColumn(name="demand_id", referencedColumnName="id", onDelete="CASCADE")
     *
     * @var AbstractDemand
     */
    protected $demand;

    public function __construct()
    {
        $this->phone = '';
    }

    public function getId()
    {
        return $this->id;
    }

    public function getPhone()
    {
        return $this->phone;
    }

    public function setPhone($phone)
    {
        $this->phone = (string)$phone;
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
     * @return AbstractDemand
     */
    public function getDemand()
    {
        return $this->demand;
    }

    /**
     * @param AbstractDemand $demand
     */
    public function setDemand(AbstractDemand $demand)
    {
        $this->demand = $demand;
    }
}
