<?php

namespace Metal\CategoriesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;


/**
 * CategoryTestItem
 *
 * @ORM\Table(name="category_test_item")
 * @ORM\Entity
 */
class CategoryTestItem
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Category")
     * @ORM\JoinColumn(name="category", referencedColumnName="Message_ID")
     *
     * @var Category
     */
    private $category;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="size", type="string", length=255)
     */
    private $sizeString;

    /**
     * @ORM\ManyToOne(targetEntity="ParameterOption")
     * @ORM\JoinColumn(name="mark_id", referencedColumnName="Message_ID")
     *
     * @var ParameterOption
     */
    private $mark;

    /**
     * @ORM\ManyToOne(targetEntity="ParameterOption")
     * @ORM\JoinColumn(name="gost_id", referencedColumnName="Message_ID")
     *
     * @var ParameterOption
     */
    private $gost;

    /**
     * @ORM\ManyToOne(targetEntity="ParameterOption")
     * @ORM\JoinColumn(name="size_id", referencedColumnName="Message_ID")
     *
     * @var ParameterOption
     */
    private $size;

    /**
     * @ORM\ManyToOne(targetEntity="ParameterOption")
     * @ORM\JoinColumn(name="class_id", referencedColumnName="Message_ID")
     *
     * @var ParameterOption
     */
    private $class;

    /**
     * @ORM\ManyToOne(targetEntity="ParameterOption")
     * @ORM\JoinColumn(name="type_id", referencedColumnName="Message_ID")
     *
     * @var ParameterOption
     */
    private $type;

    /**
     * @ORM\ManyToOne(targetEntity="ParameterOption")
     * @ORM\JoinColumn(name="vid_id", referencedColumnName="Message_ID")
     *
     * @var ParameterOption
     */
    private $vid;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set category
     *
     * @param Category $category
     * @return CategoryTestItem
     */
    public function setCategory(Category $category)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Get category
     *
     * @return Category
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return CategoryTestItem
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $sizeString
     */
    public function setSizeString($sizeString)
    {
        $this->sizeString = $sizeString;
    }

    /**
     * @return string
     */
    public function getSizeString()
    {
        return $this->sizeString;
    }

    /**
     * @param ParameterOption $mark
     */
    public function setMark(ParameterOption $mark)
    {
        $this->mark = $mark;
    }

    /**
     * @return ParameterOption
     */
    public function getMark()
    {
        return $this->mark;
    }

    /**
     * @param ParameterOption $gost
     */
    public function setGost(ParameterOption $gost)
    {
        $this->gost = $gost;
    }

    /**
     * @return ParameterOption
     */
    public function getGost()
    {
        return $this->gost;
    }

    /**
     * @param ParameterOption $class
     */
    public function setClass(ParameterOption $class)
    {
        $this->class = $class;
    }

    /**
     * @return ParameterOption
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * @param ParameterOption $size
     */
    public function setSize(ParameterOption $size)
    {
        $this->size = $size;
    }

    /**
     * @return ParameterOption
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * @param ParameterOption $type
     */
    public function setType(ParameterOption $type)
    {
        $this->type = $type;
    }

    /**
     * @return ParameterOption
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param ParameterOption $vid
     */
    public function setVid(ParameterOption $vid)
    {
        $this->vid = $vid;
    }

    /**
     * @return ParameterOption
     */
    public function getVid()
    {
        return $this->vid;
    }

    public function setParam(ParameterOption $par)
    {
        switch ($par->getTypeId()) {
            case ParameterGroup::PARAMETER_MARKA:
                $this->setMark($par);
                break;
            case ParameterGroup::PARAMETER_GOST:
                $this->setGost($par);
                break;
            case ParameterGroup::PARAMETER_RAZMER:
                $this->setSize($par);
                break;
            case ParameterGroup::PARAMETER_KLASS:
                $this->setClass($par);
                break;
            case ParameterGroup::PARAMETER_TIP:
                $this->setType($par);
                break;
            case ParameterGroup::PARAMETER_VID:
                $this->setVid($par);
                break;
        }
    }


}
