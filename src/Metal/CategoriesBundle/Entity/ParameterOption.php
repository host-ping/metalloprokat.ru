<?php

namespace Metal\CategoriesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Metal\CategoriesBundle\Repository\ParameterOptionRepository")
 * @ORM\Table(name="Message155")
 */
class ParameterOption
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer", name="Message_ID")
     */
    protected $id;

    /** @ORM\Column(length=255, name="name") */
    protected $title;

    /** @ORM\Column(length=255, name="title_accusative", nullable=true)   */
    protected $titleAccusative;

    /** @ORM\Column(length=255, name="sinonym") */
    protected $pattern;

    /** @ORM\Column(length=255, name="Keyword") */
    protected $keyword;

    /** @ORM\Column(length=55, name="slug") */
    protected $slug;

    /** @ORM\Column(type="integer", name="Type") */
    protected $typeId;

    /** @ORM\Column(type="integer", name="minisite_priority", nullable=false, options={"default":0}) */
    protected $minisitePriority;

    /**
     * @ORM\ManyToOne(targetEntity="Metal\CategoriesBundle\Entity\ParameterTypes")
     * @ORM\JoinColumn(name="Type", referencedColumnName="id")
     */
    protected $type;

    /** @ORM\Column(type="integer", name="Steel") */
    protected $markType;

    public function __construct()
    {
        $this->minisitePriority = 0;
    }

    public function getId()
    {
        return $this->id;
    }

    /**
     * @return ParameterTypes
     */
    public function getType()
    {
        return $this->type;
    }

    public function getMarkType()
    {
        return $this->markType;
    }

    public function setMarkType($markType)
    {
        $this->markType = $markType;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function getPattern()
    {
        return $this->pattern;
    }

    public function setPattern($pattern)
    {
        $this->pattern = $pattern;
    }

    public function getSlug()
    {
        return $this->slug;
    }

    public function setSlug($slug)
    {
        $this->slug = $slug;
    }

    public function getTypeId()
    {
        return $this->typeId;
    }

    public function setTypeId($typeId)
    {
        $this->typeId = $typeId;
    }

    public function getKeyword()
    {
        return $this->keyword;
    }

    public function setKeyword($keyword)
    {
        $this->keyword = $keyword;
    }

    /**
     * @return int
     */
    public function getMinisitePriority()
    {
        return $this->minisitePriority;
    }

    /**
     * @param int $minisitePriority
     */
    public function setMinisitePriority($minisitePriority)
    {
        $this->minisitePriority = $minisitePriority;
    }

    public function getTitleAccusative()
    {
        return $this->titleAccusative;
    }

    public function setTitleAccusative($titleAccusative)
    {
        $this->titleAccusative = $titleAccusative;
    }

    public function setType(ParameterTypes $parameterType)
    {
        $this->type = $parameterType;
    }

}
