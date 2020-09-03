<?php

namespace Metal\CatalogBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Metal\ProjectBundle\Entity\Review;
use Metal\TerritorialBundle\Entity\City;

/**
 * @ORM\Entity()
 * @ORM\Table(name="catalog_product_review", indexes={
 * @ORM\Index(name="IDX_product_deleted_by", columns={"product_id", "deleted_by"})})
 */
class ProductReview extends Review
{
    /**
     * @ORM\ManyToOne(targetEntity="Product")
     * @ORM\JoinColumn(name="product_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     *
     * @var Product
     */
    protected $product;

    /**
     * @ORM\ManyToOne(targetEntity="Metal\TerritorialBundle\Entity\City")
     * @ORM\JoinColumn(name="city_id", referencedColumnName="Region_ID", nullable=true)
     *
     * @var City
     */
    protected $city;

    /**
     * @return Product
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * @param Product $product
     */
    public function setProduct(Product $product)
    {
        $this->product = $product;
    }

    /**
     * @param City $city
     */
    public function setCity(City $city = null)
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
