<?php

namespace Metal\CategoriesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Metal\CategoriesBundle\Repository\ParameterGroupRepository")
 * @ORM\Table(name="Message157")
 */
class ParameterGroup
{
    const PARAMETER_MARKA = 1;
    const PARAMETER_GOST = 2;
    const PARAMETER_RAZMER = 3;
    const PARAMETER_KLASS = 4;
    const PARAMETER_TIP = 5;
    const PARAMETER_VID = 6;
    const PARAMETER_CONDITION = 7;
    const PARAMETER_PURPOSE = 8;
    const PARAMETER_SIDETYPE = 9;
    const PARAMETER_SHINETYPE = 10;
    const PARAMETER_MATERIAL = 11;
    const PARAMETER_COVERTYPE = 12;
    const PARAMETER_IZOLACIA = 13;

   /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer", name="Message_ID")
     */
    protected $id;

    /** @ORM\Column(length=255, name="Name") */
    protected $title;

    /** @ORM\Column(length=255, name="Keyword") */
    protected $slug;

    /**
     * @ORM\ManyToOne(targetEntity="Metal\CategoriesBundle\Entity\Category", inversedBy="parameterGroup")
     * @ORM\JoinColumn(name="Parent_Razd", referencedColumnName="Message_ID")
     */
    protected $category;

    /** @ORM\Column(type="integer", name="PriorityShow") */
    protected $priority;

    /** @ORM\Column(type="integer", name="Type", nullable=false) */
    protected $typeId;

    /** @ORM\Column(length=255, name="ParamsList", nullable=false) */
    protected $parametersString;

    /**
     * @ORM\OneToMany(targetEntity="Parameter", mappedBy="parameterGroup")
     * @ORM\OrderBy({"priority"="ASC"})
     */
    protected $parameters;

    public function __construct()
    {
        $this->priority = 0;
    }

    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setSlug($slug)
    {
        $this->slug = $slug;
    }

    public function getSlug()
    {
        return $this->slug;
    }

    public function getId()
    {
        return $this->id;
    }

    /**
     * @param Category $category
     */
    public function setCategory($category)
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
     * @param mixed $parametersString
     */
    public function setParametersString($parametersString)
    {
        $this->parametersString = $parametersString;
    }

    /**
     * @return mixed
     */
    public function getParametersString()
    {
        return $this->parametersString;
    }

    /**
     * @param mixed $priority
     */
    public function setPriority($priority)
    {
        $this->priority = $priority;
    }

    /**
     * @return mixed
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * @param mixed $typeId
     */
    public function setTypeId($typeId)
    {
        $this->typeId = $typeId;
    }

    /**
     * @return mixed
     */
    public function getTypeId()
    {
        return $this->typeId;
    }

    public function getParametersArray()
    {
        return explode('|', $this->parametersString);
    }

    /**
     * @return Parameter[]
     */
    public function getParameters()
    {
        return $this->parameters;
    }


}
