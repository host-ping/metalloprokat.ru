<?php

namespace Metal\DemandsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use Metal\TerritorialBundle\Entity\City;
use Metal\UsersBundle\Entity\User;
use Symfony\Component\Validator\Constraints as Assert;
use DateTime;

/**
 * @ORM\Entity(repositoryClass="Metal\DemandsBundle\Repository\DemandAnswerRepository")
 * @ORM\Table(name="demand_answer")
 */
class DemandAnswer
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer", name="id")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Metal\DemandsBundle\Entity\AbstractDemand")
     * @ORM\JoinColumn(name="demand_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    protected $demand;

    /**
     * @ORM\ManyToOne(targetEntity="Metal\UsersBundle\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="User_ID", nullable=true)
     *
     * @var User
     */
    protected $user;

    /**
     * @ORM\Column(length=255, name="company", nullable=true)
     * @Assert\NotBlank(groups={"anonymous"})
     */
    protected $company;

    /**
     * @ORM\Column(length=255, name="email", nullable=true)
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
     * @ORM\Column(length=255, name="phone", nullable=true)
     * @Assert\NotBlank(groups={"anonymous"})
     * @Assert\Regex(
     *   pattern="/\d+/",
     *   match=true,
     *   message="not valid phone number",
     *   groups={"anonymous"}
     * )
     * @Assert\Length(
     *   min=7,
     *   max=18,
     *   groups={"anonymous"}
     * )
     */
    protected $phone;

    /**
     * @ORM\Column(type="text", name="description")
     * @Assert\NotBlank(groups={"anonymous", "authenticated"})
     */
    protected $description;

    /** @ORM\Column(type="datetime", name="created_at", nullable=false) */
    protected $createdAt;

    /**
     * @ORM\ManyToOne(targetEntity="Metal\TerritorialBundle\Entity\City")
     * @ORM\JoinColumn(name="city_id", referencedColumnName="Region_ID", nullable=true)
     *
     * @var City
     */
    protected $city;

    public $cityTitle;

    public function __construct()
    {
        $this->createdAt = new DateTime();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getDemand()
    {
        return $this->demand;
    }

    public function setDemand($demand)
    {
        $this->demand = $demand;
    }

    public function getUser()
    {
        return $this->user;
    }

    public function setUser($user)
    {
        $this->user = $user;
    }

    public function getCompany()
    {
        return $this->company;
    }

    public function setCompany($company)
    {
        $this->company = $company;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setDescription($description)
    {
        $this->description = $description;
    }

    public function getPhone()
    {
        return $this->phone;
    }

    public function setPhone($phone)
    {
        $this->phone = $phone;
    }

    /**
     * @param City $city
     */
    public function setCity(City $city)
    {
        $this->city = $city;
    }

    /**
     * @return City
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @param DateTime $createdAt
     */
    public function setCreatedAt(DateTime $createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    public function getFixedCompanyTitle()
    {
        if ($this->user) {
            if ($this->user->getCompanyTitle()) {
                return $this->user->getCompanyTitle();
            }
            if ($company = $this->user->getCompany()) {
                return $company->getTitle();
            }
        }

        return $this->company;
    }

    public function getFixedEmail()
    {
        if ($this->email) {
            return $this->email;
        }

        if ($this->user) {
            $company = $this->user->getCompany();
            if ($company && $company->getCompanyLog()->getCreatedBy()->getEmail()) {
                return $company->getCompanyLog()->getCreatedBy()->getEmail();
            }

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
            return $this->user->getFullName();
        }

        return null;
    }

}
