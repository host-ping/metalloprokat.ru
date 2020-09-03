<?php

namespace Metal\AnnouncementsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Metal\CategoriesBundle\Entity\Category;

/**
 * @ORM\Entity()
 * @ORM\Table(
 *      name="announcement_category",
 *      uniqueConstraints={@ORM\UniqueConstraint(name="UNIQ_announcement_category", columns={"announcement_id", "category_id"})}
 * )
 */
class AnnouncementCategory
{
    /**
     * @ORM\Id
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Metal\AnnouncementsBundle\Entity\Announcement", inversedBy="announcementCategories")
     * @ORM\JoinColumn(name="announcement_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     *
     * @var Announcement
     */
    protected $announcement;

    /**
     * @ORM\ManyToOne(targetEntity="Metal\CategoriesBundle\Entity\Category")
     * @ORM\JoinColumn(name="category_id", referencedColumnName="Message_ID", nullable=false, onDelete="CASCADE")
     *
     * @var Category
     */
    protected $category;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return Announcement
     */
    public function getAnnouncement()
    {
        return $this->announcement;
    }

    /**
     * @param Announcement $announcement
     */
    public function setAnnouncement(Announcement $announcement)
    {
        $this->announcement = $announcement;
    }

    /**
     * @return Category
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @param Category $category
     */
    public function setCategory(Category $category)
    {
        $this->category = $category;
    }

    public function getCategoryTitle()
    {
        return $this->category ? $this->category->getTitle() : '';
    }

    public function setCategoryTitle($categoryTitle)
    {
        // dummy
    }
}
