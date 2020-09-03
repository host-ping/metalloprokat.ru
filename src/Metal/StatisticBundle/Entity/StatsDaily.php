<?php

namespace Metal\StatisticBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Metal\StatisticBundle\Repository\StatsDailyRepository", readOnly=true)
 * @ORM\Table(name="stats_daily", uniqueConstraints={
 *     @ORM\UniqueConstraint(name="UNIQ_company_date", columns={"company_id", "date"})
 * }, indexes={
 *   @ORM\Index(name="IDX_date", columns={"date"}),
 * })
 */
class StatsDaily extends ClientStats
{
    /**
     * @ORM\Column(name="updated_products_count", type="smallint", nullable=false, options={"default":0})
     */
    protected $updatedProductsCount;

    /**
     * @ORM\Column(name="added_products_count", type="smallint", nullable=false, options={"default":0})
     */
    protected $addedProductsCount;

    /**
     * @ORM\Column(name="users_on_site_count", type="smallint", nullable=false, options={"default":0})
     */
    protected $usersOnSiteCount;

    /**
     * @return integer
     */
    public function getUsersOnSiteCount()
    {
        return $this->usersOnSiteCount;
    }

    /**
     * @return integer
     */
    public function getAddedProductsCount()
    {
        return $this->addedProductsCount;
    }

    /**
     * @return integer
     */
    public function getUpdatedProductsCount()
    {
        return $this->updatedProductsCount;
    }
}
