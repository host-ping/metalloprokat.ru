<?php

namespace Metal\StatisticBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Metal\CompaniesBundle\Entity\Company;
use Metal\UsersBundle\Entity\User;

/**
 * @ORM\Entity(readOnly=true)
 * @ORM\Table(name="user_visiting", uniqueConstraints={
 * @ORM\UniqueConstraint(name="UNIQ_user_date", columns={"user_id", "date"})
 * })
 */
class UserVisiting
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(name="id", type="integer")
     */
    protected $id;

    /**
     * @ORM\Column(name="date", type="date")
     *
     * @var \DateTime
     */
    protected $date;

    /**
     * @ORM\Column(type="datetime", name="last_visit_at")
     *
     * @var \DateTime
     */
    protected $lastVisitAt;

    /**
     * @ORM\ManyToOne(targetEntity="Metal\UsersBundle\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="User_ID", nullable=false)
     *
     * @var User
     */
    protected $user;

    /**
     * @ORM\ManyToOne(targetEntity="Metal\CompaniesBundle\Entity\Company")
     * @ORM\JoinColumn(name="company_id", referencedColumnName="Message_ID", nullable=true)
     *
     * @var Company
     */
    protected $company;

    private function __construct()
    {
        // fully readonly
    }

    public function getId()
    {
        return $this->id;
    }

    /**
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @return \DateTime
     */
    public function getLastVisitAt()
    {
        return $this->lastVisitAt;
    }

    /**
     * @return User
     */
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
}
