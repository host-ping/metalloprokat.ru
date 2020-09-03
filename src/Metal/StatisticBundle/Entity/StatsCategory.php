<?php


namespace Metal\StatisticBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Metal\CategoriesBundle\Entity\Category;

/**
 * @ORM\Entity(repositoryClass="Metal\StatisticBundle\Repository\StatsCategoryRepository", readOnly=true)
 * @ORM\Table(name="stats_category", uniqueConstraints={
 *      @ORM\UniqueConstraint(name="UNIQ_comp_date_aggregation", columns={"company_id", "date", "aggregation_column"})
 * }, indexes={
 *   @ORM\Index(name="IDX_date", columns={"date"}),
 * })
 */
class StatsCategory extends ClientStats
{
    /**
     * @ORM\ManyToOne(targetEntity="Metal\CategoriesBundle\Entity\Category")
     * @ORM\JoinColumn(name="category_id", referencedColumnName="Message_ID", nullable=true)
     *
     * @var Category
     */
    protected $category;

    /**
     * @ORM\Column(name="aggregation_column", type="integer")
     */
    protected $aggregationColumn;

    /**
     * @ORM\Column(type="smallint", name="source_type_id")
     */
    protected $sourceTypeId;

    /**
     * @return Category
     */
    public function getCategory()
    {
        return $this->category;
    }

    public function getAggregationColumn()
    {
        return $this->aggregationColumn;
    }

    public function getSourceTypeId()
    {
        return $this->sourceTypeId;
    }
}
