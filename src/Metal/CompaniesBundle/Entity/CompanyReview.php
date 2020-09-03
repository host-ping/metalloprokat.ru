<?php

namespace Metal\CompaniesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Metal\ProjectBundle\Entity\Review;
use Metal\TerritorialBundle\Entity\City;
use Metal\UsersBundle\Entity\User;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="Metal\CompaniesBundle\Repository\CompanyReviewRepository")
 * @ORM\Table(name="company_review", indexes={
 * @ORM\Index(name="IDX_company_viewer", columns={"company_id", "viewed_by"}),
 * @ORM\Index(name="IDX_company_deleted_by", columns={"company_id", "deleted_by"})})
 */
class CompanyReview extends Review
{
    /**
     * @ORM\ManyToOne(targetEntity="Metal\CompaniesBundle\Entity\Company")
     * @ORM\JoinColumn(name="company_id", referencedColumnName="Message_ID", nullable=false)
     *
     * @var Company
     */
    protected $company;

    /**
     * @ORM\ManyToOne(targetEntity="Metal\TerritorialBundle\Entity\City")
     * @ORM\JoinColumn(name="city_id", referencedColumnName="Region_ID", nullable=false)
     *
     * @var City
     */
    protected $city;

    /**
     * @ORM\OneToOne(targetEntity="ReviewAnswer")
     * @ORM\JoinColumn(name="answer_id", referencedColumnName="id", nullable=true)
     * @var ReviewAnswer
     */
    protected $answer;

    /**
     * @ORM\Column(type="datetime", name="viewed_at", nullable=true)
     */
    protected $viewedAt;

    /**
     * @ORM\ManyToOne(targetEntity="Metal\UsersBundle\Entity\User")
     * @ORM\JoinColumn(name="viewed_by", referencedColumnName="User_ID", nullable=true)
     */
    protected $viewedBy;

    /**
     * @param \Metal\CompaniesBundle\Entity\Company $company
     */
    public function setCompany($company)
    {
        $this->company = $company;
    }

    /**
     * @return \Metal\CompaniesBundle\Entity\Company
     */
    public function getCompany()
    {
        return $this->company;
    }


    /**
     * @param ReviewAnswer $answer
     */
    public function setAnswer(ReviewAnswer $answer)
    {
        $this->answer = $answer;
    }

    /**
     * @return ReviewAnswer
     */
    public function getAnswer()
    {
        return $this->answer;
    }

    /**
     * @param \DateTime $viewedAt
     */
    public function setViewedAt(\DateTime $viewedAt)
    {
        $this->viewedAt = $viewedAt;
    }

    /**
     * @return \DateTime|null
     */
    public function getViewedAt()
    {
        return $this->viewedAt;
    }

    /**
     * @param User $viewedBy
     */
    public function setViewedBy(User $viewedBy)
    {
        $this->viewedBy = $viewedBy;
    }

    /**
     * @return User
     */
    public function getViewedBy()
    {
        return $this->viewedBy;
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
}
