<?php

namespace Metal\CategoriesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Metal\ProjectBundle\Entity\Behavior\Attributable;
use Symfony\Component\Validator\Constraints as Assert;

use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass="Metal\CategoriesBundle\Repository\MenuItemRepository")
 * @ORM\Table(name="menu_item")
 * @ORM\HasLifecycleCallbacks
 * @Gedmo\Tree(type="closure")
 * @Gedmo\TreeClosure(class="Metal\CategoriesBundle\Entity\MenuItemClosure")
 */
class MenuItem implements MenuItemInterface
{
    const CLS = __CLASS__;
    const MODE_REFERENCE = 1;
    const MODE_LABEL = 2;
    const MODE_ADDITIONAL_REFERENCE = 3;
    const MODE_VIRTUAL_REFERENCE = 4;

    /**
     * @var self[]
     */
    public $loadedChildren = array();

    /**
     * @var self[]
     */
    public $loadedSiblings = array();

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer", name="id")
     */
    protected $id;

    /**
     * @Gedmo\TreeParent
     * @ORM\ManyToOne(targetEntity="MenuItem")
     * @ORM\JoinColumn(name="parent", referencedColumnName="id", nullable=true, onDelete="CASCADE")
     *
     * @var MenuItem|null
     */
    protected $parent;

    /** @ORM\Column(type="smallint", name="mode") */
    protected $mode;

    /**
     * @ORM\ManyToOne(targetEntity="Category")
     * @ORM\JoinColumn(name="category_id", referencedColumnName="Message_ID", nullable=true, onDelete="CASCADE")
     *
     * @var Category
     */
    protected $category;

    /**
     * @ORM\Column(length=255, name="title")
     * @Assert\NotBlank()
     */
    protected $title;

    /**
     * @ORM\Column(length=255, name="logo", nullable=true)
     */
    protected $logo;

    /**
     * @ORM\Column(length=1000, name="depends_from_menu_items", nullable=true)
     * @Assert\Regex(pattern="/^[\d\,\s]+$/", message="Можно использовать только цифры, пробел и запятую.")
     */
    protected $dependsFromMenuItems;

    /**
     * @ORM\Column(name="slug_combined", nullable=false, options={"default":""})
     */
    protected $slugCombined;

    /**
     * @ORM\Column(name="virtual_children_ids", nullable=true)
     * @Assert\Regex(pattern="/^[\d\,\s]+$/", message="Можно использовать только цифры, пробел и запятую.")
     */
    protected $virtualChildrenIds;

    /**
     * @ORM\Column(name="hide_if_not_active", type="boolean", nullable=false, options={"default":0})
     */
    protected $hideIfNotActive;

    /** @ORM\Column(type="integer", name="position") */
    protected $position;

    use Attributable;

    public function __construct()
    {
        $this->title = '';
        $this->slugCombined = '';
        $this->position = 0;
        $this->hideIfNotActive = false;
        $this->mode = self::MODE_REFERENCE;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getVirtualChildrenIds()
    {
        return $this->virtualChildrenIds;
    }

    public function getVirtualChildrenIdsAsArray()
    {
        return array_map('intval', array_filter(explode(',', $this->virtualChildrenIds)));
    }

    public function setVirtualChildrenIds($virtualChildrenIds)
    {
        $this->virtualChildrenIds = $virtualChildrenIds;
    }

    public function setHideIfNotActive($hideIfNotActive)
    {
        $this->hideIfNotActive = $hideIfNotActive;
    }

    public function getHideIfNotActive()
    {
        return $this->hideIfNotActive;
    }

    public function setDependsFromMenuItems($dependsFromMenuItems)
    {
        $this->dependsFromMenuItems = $dependsFromMenuItems;
    }

    public function getDependsFromMenuItems()
    {
        return $this->dependsFromMenuItems;
    }

    /**
     * @return array
     */
    public function getDependsFromMenuItemsIds()
    {
        return array_map('intval', array_filter(explode(',', $this->dependsFromMenuItems)));
    }

    public function setLogo($logo)
    {
        $this->logo = $logo;
    }

    public function getLogo()
    {
        return $this->logo;
    }

    public function setMode($mode)
    {
        if ($mode == self::MODE_LABEL) {
            $this->category = null;
        }

        $this->mode = $mode;
    }

    public function getMode()
    {
        return $this->mode;
    }

    public function isLabel()
    {
        return self::MODE_LABEL == $this->mode;
    }

    public function isReference()
    {
        return self::MODE_REFERENCE == $this->mode;
    }

    public function isAdditionalReference()
    {
        return self::MODE_ADDITIONAL_REFERENCE == $this->mode;
    }

    public function isVirtualReference()
    {
        return self::MODE_VIRTUAL_REFERENCE == $this->mode;
    }

    /**
     * @param Category|null $category
     */
    public function setCategory(Category $category = null)
    {
        $this->category = $category;
        $this->title = '';
        $this->slugCombined = '';

        if ($category) {
            $this->setTitle($category->getTitle());
            $this->setSlugCombined($category->getSlugCombined());
        }
    }

    /**
     * @return Category|null
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @param MenuItem $parent
     */
    public function setParent(MenuItem $parent = null)
    {
        $this->parent = $parent;
    }

    /**
     * @return MenuItem
     */
    public function getParent()
    {
        return $this->parent;
    }

    public function getParentId()
    {
        return $this->parent ? $this->parent->getId() : 0;
    }

    public function isRoot()
    {
        return null === $this->parent;
    }

    public function setPosition($position)
    {
        $this->position = $position;
    }

    public function getPosition()
    {
        return $this->position;
    }

    public function setTitle($title)
    {
        $this->title = (string)$title;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setSlugCombined($slugCombined)
    {
        $this->slugCombined = (string)$slugCombined;
    }

    public function getSlugCombined()
    {
        return $this->slugCombined;
    }

    /**
     * @return MenuItem[]
     */
    public function getLoadedChildren()
    {
        return $this->loadedChildren;
    }

    /**
     * @return MenuItem[]
     */
    public function getLoadedSiblings()
    {
        return $this->loadedSiblings;
    }

    static public function getCategoryModesAsSimpleArray()
    {
        return array(
            MenuItem::MODE_REFERENCE => 'Ссылка',
            MenuItem::MODE_ADDITIONAL_REFERENCE => 'Дополнительная ссылка',
            MenuItem::MODE_VIRTUAL_REFERENCE => 'Виртуальная ссылка',
            MenuItem::MODE_LABEL => 'Лейбл'
        );
    }

    public function getTitleWithParent()
    {
        return $this->parent ? $this->title.' / '.$this->parent->getTitle() : $this->title;
    }

    public function getNestedTitle()
    {
        $separator = '';
        for ($i = 2; $i <= $this->getAttribute('depth'); $i++) {
            $separator .= '—';
        }

        return $separator.$this->getTitle();
    }
}
