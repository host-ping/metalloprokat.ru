<?php

namespace Metal\ProductsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Metal\CategoriesBundle\Entity\ParameterOption;

/**
 * @ORM\Entity(repositoryClass="Metal\ProductsBundle\Repository\ProductParameterValueRepository")
 * @ORM\Table(name="Message159")
 */
class ProductParameterValue
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer", name="Message_ID")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Product")
     * @ORM\JoinColumn(name="Price_ID", referencedColumnName="Message_ID")
     *
     * @var Product
     */
    protected $product;

    /**
     * @ORM\ManyToOne(targetEntity="Metal\CategoriesBundle\Entity\ParameterOption")
     * @ORM\JoinColumn(name="GostM_ID", referencedColumnName="Message_ID")
     *
     * @var ParameterOption
     */
    protected $parameterOption;

    //TODO: уникальный составной ключ, в который входит Type - крайне сомнительная затея. По идее достаточно было бы только  product-parameterOption

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param ParameterOption $parameterOption
     */
    public function setParameterOption(ParameterOption $parameterOption)
    {
        $this->parameterOption = $parameterOption;
    }

    /**
     * @return ParameterOption
     */
    public function getParameterOption()
    {
        return $this->parameterOption;
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
