<?php

namespace Metal\ComplaintsBundle\Entity;

use Doctrine\ORM\Mapping as Orm;
use Metal\ProductsBundle\Entity\Product;

/**
 * @ORM\Entity
 */
class ProductComplaint extends AbstractComplaint
{
    /**
     * @ORM\ManyToOne(targetEntity="Metal\ProductsBundle\Entity\Product")
     * @ORM\JoinColumn(name="product_id", referencedColumnName="Message_ID")
     */
    protected $product;

    /**
     * @param Product $product
     */
    public function setProduct(Product $product)
    {
        $this->product = $product;
        $this->setCompany($product->getCompany());
    }

    /**
     * @return Product
     */
    public function getProduct()
    {
        return $this->product;
    }

    public function setObject($object)
    {
        $this->setProduct($object);
    }

    public function getObjectKind()
    {
        return 'product';
    }
}
