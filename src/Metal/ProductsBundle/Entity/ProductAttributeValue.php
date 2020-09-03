<?php

namespace Metal\ProductsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Metal\AttributesBundle\Entity\AttributeValue;

/**
 * @ORM\Entity(repositoryClass="Metal\ProductsBundle\Repository\ProductAttributeValueRepository")
 * @ORM\Table(name="product_attribute_value")
 */
class ProductAttributeValue
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer", name="id")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Product")
     * @ORM\JoinColumn(name="product_id", referencedColumnName="Message_ID", nullable=false)
     *
     * @var Product
     */
    protected $product;

    /**
     * @ORM\ManyToOne(targetEntity="Metal\AttributesBundle\Entity\AttributeValue")
     * @ORM\JoinColumn(name="attribute_value_id", referencedColumnName="id", nullable=false)
     *
     * @var AttributeValue
     */
    protected $attributeValue;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

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
     * @return AttributeValue
     */
    public function getAttributeValue()
    {
        return $this->attributeValue;
    }

    /**
     * @param AttributeValue $attributeValue
     */
    public function setAttributeValue(AttributeValue $attributeValue)
    {
        $this->attributeValue = $attributeValue;
    }
}
