<?php

namespace Metal\ProductsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="product_description")
 */
class ProductDescription
{
    /**
     * @ORM\Id
     * @ORM\OneToOne(targetEntity="Metal\ProductsBundle\Entity\Product")
     * @ORM\JoinColumn(name="product_id", referencedColumnName="Message_ID", nullable=false)
     *
     * @var Product
     */
    protected $product;

    /**
     * @ORM\Column(type="string", length=5000, name="description", options={"default":""})
     * @Assert\Length(max=5000)
     */
    protected $description;

    public function __construct()
    {
        $this->description = '';
    }

    /**
     * @param mixed $description
     */
    public function setDescription($description)
    {
        $this->description = (string)$description;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param Product $product
     */
    public function setProduct(Product $product)
    {
        $this->product = $product;
    }

    /**
     * @return Product
     */
    public function getProduct()
    {
        return $this->product;
    }
}

