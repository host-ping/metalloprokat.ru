<?php

namespace Metal\UsersBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use Metal\DemandsBundle\Entity\Demand;
use Metal\ProductsBundle\Entity\Product;

/**
 * @ORM\Entity(repositoryClass="Metal\UsersBundle\Repository\FavoriteRepository")
 * @ORM\Table(name="favorite", uniqueConstraints={
 *  @ORM\UniqueConstraint(name="UNIQ_user_demand", columns={"user_id", "demand_id"}),
 *  @ORM\UniqueConstraint(name="UNIQ_user_product", columns={"user_id", "product_id"})
 * })
 */
class Favorite
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer", name="id")
     */
    protected $id;

    /**
     * @ORM\Column(type="datetime", name="created_at")
     */
    protected $createdAt;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="User_ID", nullable=false)
     *
     * @var User
     */
    protected $user;

    /**
     * @ORM\ManyToOne(targetEntity="Metal\DemandsBundle\Entity\Demand")
     * @ORM\JoinColumn(name="demand_id", referencedColumnName="id", nullable=true)
     *
     * @var Demand
     */
    protected $demand;

    /**
     * @ORM\ManyToOne(targetEntity="Metal\ProductsBundle\Entity\Product")
     * @ORM\JoinColumn(name="product_id", referencedColumnName="Message_ID", nullable=true)
     */
    protected $product;

    /**
     * @ORM\ManyToOne(targetEntity="FavoriteCompany")
     * @ORM\JoinColumn(name="favorite_company_id", referencedColumnName="id", nullable=true, onDelete="CASCADE")
     */
    protected $favoriteCompany;

    /** @ORM\Column(type="text", name="comment", nullable=false) */
    protected $comment;

    /**
     * @ORM\Column(type="datetime", name="comment_updated_at", nullable=true)
     */
    protected $commentUpdatedAt;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->comment = '';
    }

    public function getId()
    {
        return $this->id;
    }

    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTime $createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param User $user
     */
    public function setUser(User $user)
    {
        $this->user = $user;
    }

    /**
     * @return Demand
     */
    public function getDemand()
    {
        return $this->demand;
    }

    public function setDemand(Demand $demand = null)
    {
        $this->demand = $demand;
    }

    public function getProduct()
    {
        return $this->product;
    }

    public function setProduct(Product $product = null)
    {
        $this->product = $product;
    }

    public function getFavoriteCompany()
    {
        return $this->favoriteCompany;
    }

    public function setFavoriteCompany($favoriteCompany)
    {
        $this->favoriteCompany = $favoriteCompany;
    }

    public function getComment()
    {
        return $this->comment;
    }

    public function setComment($comment)
    {
        $this->comment = (string)$comment;
    }

    /**
     * @param \DateTime $commentUpdatedAt
     */
    public function setCommentUpdatedAt(\DateTime $commentUpdatedAt)
    {
        $this->commentUpdatedAt = $commentUpdatedAt;
    }

    /**
     * @return \DateTime
     */
    public function getCommentUpdatedAt()
    {
        return $this->commentUpdatedAt;
    }
}
