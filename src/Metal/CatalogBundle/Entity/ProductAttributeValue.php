<?php

namespace Metal\CatalogBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Metal\AttributesBundle\Entity\AttributeValue;

/**
 * @ORM\Entity(repositoryClass="Metal\CatalogBundle\Repository\ProductAttributeValueRepository")
 * @ORM\Table(
 *      name="catalog_product_attribute_value",
 *      uniqueConstraints={@ORM\UniqueConstraint(name="UNIQ_product_attribute_value", columns={"product_id", "attribute_value_id"} )}
 * )
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
     * @ORM\ManyToOne(targetEntity="Product", inversedBy="productAttributesValues")
     * @ORM\JoinColumn(name="product_id", referencedColumnName="id", nullable=false)
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

    public function getAttributeValueTitle()
    {
        if ($this->attributeValue) {
            return $this->attributeValue->getValue();
        }

        return '';
    }

    public function setAttributeValueTitle($cityTitle)
    {
        // do nothing. Readonly
    }
}
