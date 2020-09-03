<?php

namespace Metal\CategoriesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Metal\ProjectBundle\Entity\Behavior\Attributable;
use Metal\ProjectBundle\Entity\Behavior\Updateable;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\MappedSuperclass
 */
abstract class CategoryAbstract
{
    //NB! add mapping in children
    protected $id;

    //NB! add mapping in children
    protected $parent;

    //NB! add mapping in children
    protected $title;

    //TODO: добавить валидацию на регулярку из Company::SLUG_REGEX
    //NB! add mapping in children
    protected $slug;

    //NB! add mapping in children
    protected $slugCombined;

    /**
     * @ORM\Column(name="branch_ids", nullable=false)
     */
    protected $branchIds;

    /**
     * @ORM\Column(type="integer", name="Priority", nullable=false, options={"default":0})
     * @Assert\NotBlank()
     */
    protected $priority;

    /**
     * @ORM\Column(type="datetime", name="created_at")
     */
    protected $createdAt;

    use Updateable;

    use Attributable;

    public static function getClosureTableName()
    {
        throw new \BadMethodCallException('Override this method in children.');
    }

    public static function getSlugColumnName()
    {
        return 'slug';
    }

    public function __construct()
    {
        $this->title = '';
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
        $this->slug = '';
        $this->slugCombined = '';
        $this->branchIds = '';
        $this->priority = 0;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function getSlug()
    {
        return $this->slug;
    }

    public function getSlugCombined()
    {
        if ($this->slugCombined) {
            return $this->slugCombined;
        }

        return $this->getRealSlugCombined();
    }

    public function getRealSlugCombined()
    {
        if ($this->getParent()) {
            return $this->getParent()->getRealSlugCombined().'/'.$this->getSlug();
        }

        return $this->getSlug();
    }

    public function setSlug($slug)
    {
        $this->slug = $slug;
        $this->slugCombined = $this->getRealSlugCombined();
    }

    public function setSlugCombined($slugCombined)
    {
        $this->slugCombined = $slugCombined;
    }

    /**
     * @return static|null
     */
    public function getParent()
    {
        return $this->parent;
    }

    public function getSuperParent()
    {
        $superParent = $this;
        while ($superParent->getParent()) {
            $superParent = $superParent->getParent();
        }

        return $superParent;
    }

    public function setParent(self $parent = null)
    {
        $this->parent = $parent;
    }

    /**
     * @return integer|null
     */
    public function getParentId()
    {
        return $this->parent ? $this->getParent()->getId() : null;
    }

    public function isRoot()
    {
        return null === $this->parent;
    }

    /**
     * @return static[]
     */
    public function getBranch()
    {
        $parentCategory = $this;
        $branchCategories = array($parentCategory);

        while ($parentCategory = $parentCategory->getParent()) {
            array_unshift($branchCategories, $parentCategory);
        }

        return $branchCategories;
    }

    /**
     * @return static
     */
    public function getRootCategory()
    {
        //TODO: если не ошибаюсь, этот метод дублирует getSuperParent, переименовать в getRoot, заменить везде getSuperParent на getRoot
        $branch = $this->getBranch();

        return $branch[0];
    }

    /**
     * @param \DateTime $updatedAt
     */
    public function setUpdatedAt(\DateTime $updatedAt)
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    public function setPriority($priority)
    {
        $this->priority = $priority;
    }

    public function getPriority()
    {
        return $this->priority;
    }

    public function getTitleWithParent()
    {
        $parent = $this->getParent();

        return $parent ? $this->title.' / '.$parent->getTitle() : $this->title;
    }

    /**
     * @param \DateTime $createdAt
     */
    public function setCreatedAt(\DateTime $createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @return int[]
     */
    public function getBranchIds(): array
    {
        return explode(',', $this->branchIds);
    }

    public function isChildOf(self $category)
    {
        return in_array($category->getId(), $this->getBranchIds());
    }

    public function isParentOf(self $category)
    {
        return in_array($this->getId(), $category->getBranchIds());
    }

    public function getNestedTitle()
    {
        $separator = '';
        for ($i = 2; $i <= $this->getAttribute('depth'); $i++) {
            $separator .= '—';
        }

        return $separator.$this->getTitle();
    }

    public function getBranchTitle()
    {
        $fullPath[] = $this->getTitle();
        $parentCategory = $this;
        while ($parentCategory = $parentCategory->getParent()) {
            $fullPath[] = $parentCategory->getTitle();
        }

        return implode(' / ', $fullPath);
    }
}
