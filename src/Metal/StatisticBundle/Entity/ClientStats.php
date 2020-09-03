<?php

namespace Metal\StatisticBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Metal\CompaniesBundle\Entity\Company;

/**
 * @ORM\MappedSuperclass(repositoryClass="Metal\StatisticBundle\Repository\ClientStatsRepository")
 */
class ClientStats
{
    const GROUP_BY_DAILY_TABLE = 'stats_daily';
    const GROUP_BY_CITY_TABLE = 'stats_city';
    const GROUP_BY_CATEGORY_TABLE = 'stats_category';

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer", name="id")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Metal\CompaniesBundle\Entity\Company")
     * @ORM\JoinColumn(name="company_id", referencedColumnName="Message_ID", nullable=false)
     *
     * @var Company
     */
    protected $company;

    /**
     * @ORM\Column(name="demands_count", type="smallint", nullable=false, options={"default":0, "unsigned"=true})
     */
    protected $demandsCount;

    /**
     * @ORM\Column(name="demands_processed_count", type="smallint", nullable=false, options={"default":0, "unsigned"=true})
     */
    protected $demandsProcessedCount;

    /**
     * @ORM\Column(name="callback_count", type="smallint", nullable=false, options={"default":0, "unsigned"=true})
     */
    protected $callbacksCount;

    /**
     * @ORM\Column(name="callbacks_processed_count", type="smallint", nullable=false, options={"default":0, "unsigned"=true})
     */
    protected $callbacksProcessedCount;

    /**
     * @ORM\Column(name="reviews_count", type="smallint", nullable=false, options={"default":0, "unsigned"=true})
     */
    protected $reviewsCount;

    /**
     * @ORM\Column(name="complaints_count", type="smallint", nullable=false, options={"default":0, "unsigned"=true})
     */
    protected $complaintsCount;

    /**
     * @ORM\Column(name="reviews_phones_count", type="smallint", nullable=false, options={"default":0, "unsigned"=true})
     */
    protected $reviewsPhonesCount;

    /**
     * @ORM\Column(name="website_visits_count", type="smallint", nullable=false, options={"default":0, "unsigned"=true})
     */
    protected $websiteVisitsCount;

    /**
     * @ORM\Column(name="reviews_products_count", type="smallint", nullable=false, options={"default":0, "unsigned"=true})
     */
    protected $reviewsProductsCount;

    /**
     * @ORM\Column(name="show_products_count", type="integer", nullable=false, options={"default":0, "unsigned"=true})
     */
    protected $showProductsCount;

    /**
     * @ORM\Column(name="complaints_processed_count", type="smallint", nullable=false, options={"default":0, "unsigned"=true})
     */
    protected $complaintsProcessedCount;

    /**
     * @ORM\Column(name="demands_views_count", type="smallint", nullable=false, options={"default":0, "unsigned"=true})
     */
    protected $demandsViewsCount;

    /**
     * @ORM\Column(name="demands_to_favorite_count", type="smallint", nullable=false, options={"default":0, "unsigned"=true})
     */
    protected $demandsToFavoriteCount;

    /**
     * @ORM\Column(name="demands_answers_count", type="smallint", nullable=false, options={"default":0, "unsigned"=true})
     */
    protected $demandsAnswersCount;

    /**
     * @ORM\Column(type="date", name="date")
     *
     * @var \DateTime
     */
    protected $date;

    final private function __construct()
    {
        //
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return integer
     */
    public function getCallbacksCount()
    {
        return $this->callbacksCount;
    }

    /**
     * @return integer
     */
    public function getCallbacksProcessedCount()
    {
        return $this->callbacksProcessedCount;
    }

    /**
     * @return Company
     */
    public function getCompany()
    {
        return $this->company;
    }

    /**
     * @return integer
     */
    public function getComplaintsCount()
    {
        return $this->complaintsCount;
    }

    /**
     * @return integer
     */
    public function getComplaintsProcessedCount()
    {
        return $this->complaintsProcessedCount;
    }

    /**
     * @return integer
     */
    public function getDemandsCount()
    {
        return $this->demandsCount;
    }

    /**
     * @return integer
     */
    public function getDemandsProcessedCount()
    {
        return $this->demandsProcessedCount;
    }

    /**
     * @return integer
     */
    public function getReviewsCount()
    {
        return $this->reviewsCount;
    }

    /**
     * @return integer
     */
    public function getDemandsAnswersCount()
    {
        return $this->demandsAnswersCount;
    }

    /**
     * @return integer
     */
    public function getDemandsToFavoriteCount()
    {
        return $this->demandsToFavoriteCount;
    }

    /**
     * @return integer
     */
    public function getDemandsViewsCount()
    {
        return $this->demandsViewsCount;
    }

    /**
     * @return integer
     */
    public function getReviewsPhonesCount()
    {
        return $this->reviewsPhonesCount;
    }

    /**
     * @return integer
     */
    public function getReviewsProductsCount()
    {
        return $this->reviewsProductsCount;
    }

    /**
     * @return integer
     */
    public function getWebsiteVisitsCount()
    {
        return $this->websiteVisitsCount;
    }

    /**
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @return integer
     */
    public function getShowProductsCount()
    {
        return $this->showProductsCount;
    }
}
