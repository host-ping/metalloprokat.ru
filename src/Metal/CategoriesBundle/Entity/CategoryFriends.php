<?php

namespace Metal\CategoriesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use DateTime;

/**
 * @ORM\Entity(repositoryClass="Metal\CategoriesBundle\Repository\CategoryFriendsRepository")
 * @ORM\Table(name="Category_friends")
 */
class CategoryFriends
{
    const ANCHOR_CATEGORY_NAME = 0;
    const ANCHOR_CATEGORY_NAME_WITH_REGION = 1;
    const ANCHOR_CATEGORY_NAME_WITH_IN_REGION = 2;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer", name="ID")
     */
    protected $id;


    /** @ORM\Column(type="text", name="Value") */
    protected $friendIds;

    /** @ORM\Column(length=200, name="Links") */
    protected $flags;

    /** @ORM\Column(type="datetime", name="Created") */
    protected $createdAt;

    /**
     * @ORM\OneToOne(targetEntity="Category")
     * @ORM\JoinColumn(name="Category_ID", referencedColumnName="Message_ID", nullable=false)
     *
     * @var Category
     */
    protected $category;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    public function getId()
    {
        return $this->id;
    }

    /**
     * @param Category $category
     */
    public function setCategory(Category $category)
    {
        $this->category = $category;
    }

    /**
     * @return Category
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @param DateTime $createdAt
     */
    public function setCreatedAt(DateTime $createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    public function setFlags($flags)
    {
        $this->flags = $flags;
    }

    public function getFlags()
    {
        return $this->flags;
    }

    public function setFriendIds($friendIds)
    {
        $this->friendIds = $friendIds;
    }

    public function getFriendIds()
    {
        return $this->friendIds;
    }

    public function getCategoriesWithFlags()
    {
        $friendsIds = explode(',', $this->getFriendIds());
        $flags = array_map('intval', explode(',', $this->getFlags()));

        if (count($friendsIds) > count($flags)) {
            $friendsIds = array_slice($friendsIds, 0, count($flags));
        } elseif (count($flags) > count($friendsIds)) {
            $flags = array_slice($flags, 0, count($friendsIds));
        }

        return array_combine($friendsIds, $flags);
    }
}
