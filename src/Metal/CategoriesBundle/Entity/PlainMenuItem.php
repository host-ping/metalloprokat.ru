<?php

namespace Metal\CategoriesBundle\Entity;

class PlainMenuItem implements MenuItemInterface
{
    /**
     * @var MenuItemInterface[]
     */
    public $loadedSiblings = array();

    /**
     * @var MenuItemInterface[]
     */
    public $loadedChildren = array();

    protected $id;

    protected $title;

    protected $slugCombined;

    protected $isLabel;

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function isLabel()
    {
        return $this->isLabel;
    }

    public function setIsLabel($isReference)
    {
        $this->isLabel = $isReference;
    }

    public function getSlugCombined()
    {
        return $this->slugCombined;
    }

    public function setSlugCombined($slugCombined)
    {
        $this->slugCombined = $slugCombined;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * {@inheritdoc}
     */
    public function getLoadedChildren()
    {
        return $this->loadedChildren;
    }

    /**
     * {@inheritdoc}
     */
    public function getLoadedSiblings()
    {
        return $this->loadedSiblings;
    }
}
