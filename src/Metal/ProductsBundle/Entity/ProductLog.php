<?php

namespace Metal\ProductsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Metal\UsersBundle\Entity\User;

/**
 * @ORM\Entity
 * @ORM\Table(name="product_log")
 */
class ProductLog
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
     * @ORM\ManyToOne(targetEntity="Metal\UsersBundle\Entity\User")
     * @ORM\JoinColumn(name="created_by", referencedColumnName="User_ID", nullable=false)
     */
    protected $createdBy;

    /**
     * @ORM\ManyToOne(targetEntity="Metal\UsersBundle\Entity\User")
     * @ORM\JoinColumn(name="updated_by", referencedColumnName="User_ID", nullable=true)
     */
    protected $updatedBy;

    /** @ORM\Column(type="string", length=15, name="ip", nullable=false) */
    protected $ip;

    /** @ORM\Column(type="string", length=255, name="user_agent", nullable=false) */
    protected $userAgent;

    /** @ORM\Column(type="string", length=15, name="last_ip", nullable=true, options={"default":null}) */
    protected $lastIp;

    /** @ORM\Column(type="string", length=255, name="last_user_agent", nullable=false, options={"default":null}) */
    protected $lastUserAgent;

    public function __construct()
    {
        $this->ip = '';
        $this->lastIp = '';
        $this->userAgent = '';
        $this->lastUserAgent = '';
    }

    /**
     * @param mixed $ip
     */
    public function setIp($ip)
    {
        $this->ip = (string)$ip;
    }

    /**
     * @return mixed
     */
    public function getIp()
    {
        return $this->ip;
    }

    /**
     * @param mixed $lastIp
     */
    public function setLastIp($lastIp)
    {
        $this->lastIp = (string)$lastIp;
    }

    /**
     * @return mixed
     */
    public function getLastIp()
    {
        return $this->lastIp;
    }

    /**
     * @param mixed $lastUserAgent
     */
    public function setLastUserAgent($lastUserAgent)
    {
        $this->lastUserAgent = (string)$lastUserAgent;
    }

    /**
     * @return mixed
     */
    public function getLastUserAgent()
    {
        return $this->lastUserAgent;
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

    /**
     * @param mixed $userAgent
     */
    public function setUserAgent($userAgent)
    {
        $this->userAgent = (string)$userAgent;
    }

    public function getUserAgent()
    {
        return $this->userAgent;
    }

    /**
     * @param User $createdBy
     */
    public function setCreatedBy(User $createdBy)
    {
        $this->createdBy = $createdBy;
    }

    /**
     * @return User
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * @param User $updatedBy
     */
    public function setUpdatedBy(User $updatedBy)
    {
        $this->updatedBy = $updatedBy;
    }

    /**
     * @return User
     */
    public function getUpdatedBy()
    {
        return $this->updatedBy;
    }
}

