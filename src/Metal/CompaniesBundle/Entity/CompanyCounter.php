<?php

namespace Metal\CompaniesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Metal\CompaniesBundle\Repository\CompanyCounterRepository")
 * @ORM\Table(name="company_counter", uniqueConstraints={
 * @ORM\UniqueConstraint(name="UNIQ_company", columns={"company_id"} )})
 */
class CompanyCounter
{
    const DEFAULT_SCHEDULED_ACTUALIZATION_TIME = -1;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\Column(type="integer", name="id")
     */
    protected $id;

    /**
     * @ORM\OneToOne(targetEntity="Metal\CompaniesBundle\Entity\Company")
     * @ORM\JoinColumn(name="company_id", referencedColumnName="Message_ID", nullable=false)
     *
     * @var Company
     */
    protected $company;

    /**
     * Кол-во отзывов
     *
     * @ORM\Column(type="integer", name="reviews_count", nullable=false, options={"default":0})
     */
    protected $reviewsCount;

    /**
     * Кол-во продуктов со всеми статусами кроме удален и превышен лимит
     *
     * @ORM\Column(type="integer", name="all_products_count", nullable=false, options={"default":0})
     */
    protected $allProductsCount;

    /**
     * Кол-во непросмотренных жалоб
     *
     * @ORM\Column(type="integer", name="new_complaints_count", nullable=false, options={"default":0})
     */
    protected $newComplaintsCount;

    /**
     * Кол-во непросмотренных приватных заявок
     *
     * @ORM\Column(type="integer", name="new_demands_count", nullable=false), options={"default":0}
     */
    protected $newDemandsCount;

    /**
     * Кол-во непросмотренных отзывов о компании
     *
     * @ORM\Column(type="integer", name="new_company_reviews_count", nullable=false, options={"default":0})
     */
    protected $newCompanyReviewsCount;

    /**
     * Кол-во обратных звонков, ожидающих ответа
     *
     * @ORM\Column(type="integer", name="new_callbacks_count", nullable=false, options={"default":0})
     */
    protected $newCallbacksCount;

    /**
     * @ORM\Column(type="datetime", name="company_updated_at")
     *
     * @var \DateTime
     */
    protected $companyUpdatedAt;

    /**
     * @ORM\Column(type="date", name="products_actualized_at", nullable=true)
     *
     * @var \DateTime
     */
    protected $productsActualizedAt;

    /**
     * @ORM\Column(type="integer", name="scheduled_actualization_time", nullable=false, options={"default":-1})
     */
    protected $scheduledActualizationTime;

    /**
     * @ORM\Column(type="datetime", name="products_updated_at")
     *
     * @var \DateTime
     */
    protected $productsUpdatedAt;

    /**
     * @ORM\Column(type="datetime", name="minisite_colors_updated_at")
     *
     * @var \DateTime
     */
    protected $minisiteColorsUpdatedAt;

    /**
     * @ORM\Column(name="update_statistics", type="boolean", nullable=false, options={"default":0} )
     */
    protected $updateStatistics;

    public function __construct()
    {
        $this->reviewsCount = 0;
        $this->allProductsCount = 0;
        $this->newComplaintsCount = 0;
        $this->newDemandsCount = 0;
        $this->newCompanyReviewsCount = 0;
        $this->newCallbacksCount = 0;
        $this->scheduledActualizationTime = self::DEFAULT_SCHEDULED_ACTUALIZATION_TIME;
        $this->companyUpdatedAt = new \DateTime();
        $this->productsUpdatedAt = new \DateTime();
        $this->minisiteColorsUpdatedAt = new \DateTime();
        $this->updateStatistics = 0;
    }

    public static function getScheduledActualizationTimeValues()
    {
        return array_map(function ($el) {
            return str_pad($el, 2, 0, STR_PAD_LEFT).':00';
        }, range(0, 23));
    }

    /**
     * @param Company $company
     */
    public function setCompany(Company $company)
    {
        $this->company = $company;
        $this->id = $company->getId();
    }

    /**
     * @return Company
     */
    public function getCompany()
    {
        return $this->company;
    }

    public function getUpdateStatistics()
    {
        return $this->updateStatistics;
    }

    public function setUpdateStatistics($updateStatistics)
    {
        $this->updateStatistics = $updateStatistics;
    }

    public function setReviewsCount($reviewsCount)
    {
        $this->reviewsCount = $reviewsCount;
    }

    public function getReviewsCount()
    {
        return $this->reviewsCount;
    }

    public function setAllProductsCount($allProductsCount)
    {
        $this->allProductsCount = $allProductsCount;
    }

    public function getAllProductsCount()
    {
        return $this->allProductsCount;
    }

    public function setNewCallbacksCount($newCallbacksCount)
    {
        $this->newCallbacksCount = $newCallbacksCount;
    }

    public function getNewCallbacksCount()
    {
        return $this->newCallbacksCount;
    }

    public function setNewCompanyReviewsCount($newCompanyReviewsCount)
    {
        $this->newCompanyReviewsCount = $newCompanyReviewsCount;
    }

    public function getNewCompanyReviewsCount()
    {
        return $this->newCompanyReviewsCount;
    }

    public function setNewComplaintsCount($newComplaintsCount)
    {
        $this->newComplaintsCount = $newComplaintsCount;
    }

    public function getNewComplaintsCount()
    {
        return $this->newComplaintsCount;
    }

    public function setNewDemandsCount($newDemandsCount)
    {
        $this->newDemandsCount = $newDemandsCount;
    }

    public function getNewDemandsCount()
    {
        return $this->newDemandsCount;
    }

    /**
     * @param \DateTime $companyUpdatedAt
     */
    public function setCompanyUpdatedAt(\DateTime $companyUpdatedAt)
    {
        $this->companyUpdatedAt = $companyUpdatedAt;
    }

    /**
     * @return \DateTime
     */
    public function getCompanyUpdatedAt()
    {
        return $this->companyUpdatedAt;
    }

    public function setProductsActualizedAt($productsActualizedAt)
    {
        $this->productsActualizedAt = $productsActualizedAt;
    }

    public function getProductsActualizedAt()
    {
        return $this->productsActualizedAt;
    }

    public function isActualizationAvailable()
    {
        if (null === $this->productsActualizedAt || $this->company->getPackageChecker()->isScheduledActualizationAvailable()) {
            return true;
        }

        $date = new \DateTime();
        $actualValue = $date->diff(new \DateTime('tomorrow'));

        return $actualValue->days >= 1;
    }

    /**
     * @param \DateTime $productsUpdatedAt
     */
    public function setProductsUpdatedAt(\DateTime $productsUpdatedAt)
    {
        $this->productsUpdatedAt = $productsUpdatedAt;
    }

    /**
     * @return \DateTime
     */
    public function getProductsUpdatedAt()
    {
        return $this->productsUpdatedAt;
    }

    public function setScheduledActualizationTime($scheduledActualizationTime)
    {
        $this->scheduledActualizationTime = $scheduledActualizationTime ?: self::DEFAULT_SCHEDULED_ACTUALIZATION_TIME;
    }

    public function getScheduledActualizationTime()
    {
        return $this->scheduledActualizationTime;
    }

    /**
     * @param \DateTime $minisiteColorsUpdatedAt
     */
    public function setMinisiteColorsUpdatedAt(\DateTime $minisiteColorsUpdatedAt)
    {
        $this->minisiteColorsUpdatedAt = $minisiteColorsUpdatedAt;
    }

    /**
     * @return \DateTime
     */
    public function getMinisiteColorsUpdatedAt()
    {
        return $this->minisiteColorsUpdatedAt;
    }
}
