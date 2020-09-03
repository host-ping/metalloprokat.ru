<?php

namespace Metal\ProjectBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Metal\CompaniesBundle\Entity\Company;
use Metal\DemandsBundle\Entity\AbstractDemand;
use Metal\NewsletterBundle\Entity\Subscriber;
use Metal\UsersBundle\Entity\User;

/**
 * @ORM\Entity
 * @ORM\Table(name="normalized_email",
 *  indexes={
 *      @ORM\Index(name="IDX_email", columns={"email"})
 *  },
 *  uniqueConstraints={
 *      @ORM\UniqueConstraint(name="UNIQ_company_email", columns={"company_id", "email"}),
 *      @ORM\UniqueConstraint(name="UNIQ_user_email", columns={"user_id", "email"}),
 *      @ORM\UniqueConstraint(name="UNIQ_demand_email", columns={"demand_id", "email"}),
 *      @ORM\UniqueConstraint(name="UNIQ_subscriber_email", columns={"subscriber_id", "email"})
 *  }
 * )
 */
class NormalizedEmail
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer", name="id")
     */
    protected $id;

    /**
     * @ORM\Column(length=255, name="email", nullable=false, options={"default": ""})
     */
    protected $email;

    /**
     * @ORM\ManyToOne(targetEntity="Metal\UsersBundle\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="User_ID", onDelete="CASCADE")
     *
     * @var User
     */
    protected $user;

    /**
     * @ORM\ManyToOne(targetEntity="Metal\CompaniesBundle\Entity\Company")
     * @ORM\JoinColumn(name="company_id", referencedColumnName="Message_ID", onDelete="CASCADE")
     *
     * @var Company
     */
    protected $company;

    /**
     * @ORM\ManyToOne(targetEntity="Metal\DemandsBundle\Entity\AbstractDemand")
     * @ORM\JoinColumn(name="demand_id", referencedColumnName="id", onDelete="CASCADE")
     *
     * @var AbstractDemand
     */
    protected $demand;

    /**
     * @ORM\ManyToOne(targetEntity="Metal\NewsletterBundle\Entity\Subscriber")
     * @ORM\JoinColumn(name="subscriber_id", referencedColumnName="ID", onDelete="CASCADE")
     *
     * @var Subscriber
     */
    protected $subscriber;


    public function __construct()
    {
        $this->email = '';
    }

    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @return Subscriber
     */
    public function getSubscriber()
    {
        return $this->subscriber;
    }

    /**
     * @param Subscriber $subscriber
     */
    public function setSubscriber(Subscriber $subscriber)
    {
        $this->subscriber = $subscriber;
    }

    /**
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = (string) $email;
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