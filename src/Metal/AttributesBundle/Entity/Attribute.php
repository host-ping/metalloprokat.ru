<?php

namespace Metal\AttributesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @UniqueEntity("code")
 * @ORM\Table(name="attribute")
 */
class Attribute
{
    const ORDER_OUTPUT_PRIORITY = 'a.outputPriority';

    /**
     * @ORM\Id
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue
     */
    protected $id;

    /**
     * @ORM\Column(length=100, name="title", nullable=false)
     * @Assert\NotBlank()
     */
    protected $title;

    /**
     * @ORM\Column(length=20, name="code", nullable=false, unique=true)
     * @Assert\NotBlank()
     * @Assert\Regex(pattern="/^[a-z0-9-]+$/ui", message="Неправильный код атрибута")
     */
    protected $code;

    /**
     * @ORM\Column(type="smallint", name="columns_count", nullable=false, options={"default":1})
     * @Assert\NotBlank()
     * @Assert\Range(min="1", max="3")
     */
    protected $columnsCount;

    /**
     * @ORM\Column(type="smallint", name="url_priority", nullable=false, options={"default":0})
     * @Assert\NotBlank()
     */
    protected $urlPriority;

    /**
     * @ORM\Column(type="smallint", name="output_priority", nullable=false, options={"default":0})
     * @Assert\NotBlank()
     */
    protected $outputPriority;

    /**
     * @ORM\Column(type="boolean", name="indexable_for_seo", options={"default":0})
     */
    protected $indexableForSeo;

    /**
     * @ORM\Column(length=255, name="suffix", nullable=false, options={"default": ""})
     */
    protected $suffix;

    /**
     * @ORM\Column(type="smallint", name="max_matches", nullable=false, options={"default":1})
     * @Assert\NotBlank()
     */
    protected $maxMatches;

    public function __construct()
    {
        $this->urlPriority = 0;
        $this->outputPriority = 0;
        $this->indexableForSeo = false;
        $this->columnsCount = 1;
        $this->suffix = '';
        $this->maxMatches = 1;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getColumnsCount()
    {
        return $this->columnsCount;
    }

    public function setColumnsCount($columnsCount)
    {
        $this->columnsCount = $columnsCount;
    }

    public function getCode()
    {
        return $this->code;
    }

    public function setCode($code)
    {
        $this->code = $code;
    }

    public function getSuffix()
    {
        return $this->suffix;
    }

    public function setSuffix($suffix)
    {
        $this->suffix = (string)$suffix;
    }

    public function getOutputPriority()
    {
        return $this->outputPriority;
    }

    public function setOutputPriority($outputPriority)
    {
        $this->outputPriority = $outputPriority;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function getUrlPriority()
    {
        return $this->urlPriority;
    }

    public function setUrlPriority($urlPriority)
    {
        $this->urlPriority = $urlPriority;
    }

    public function getIndexableForSeo()
    {
        return $this->indexableForSeo;
    }

    public function setIndexableForSeo($indexableForSeo)
    {
        $this->indexableForSeo = $indexableForSeo;
    }

    public function getMaxMatches()
    {
        return $this->maxMatches;
    }

    public function setMaxMatches($maxMatches)
    {
        $this->maxMatches = $maxMatches;
    }

}
