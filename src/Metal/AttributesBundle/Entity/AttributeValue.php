<?php

namespace Metal\AttributesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Metal\ProjectBundle\Helper\TextHelperStatic;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="Metal\AttributesBundle\Repository\AttributeValueRepository")
 * @ORM\Table(name="attribute_value", indexes={@ORM\Index(name="IDX_output_priority", columns={"output_priority"})})
 * @UniqueEntity("slug")
 */
class AttributeValue
{
    const ORDER_OUTPUT_PRIORITY = 'av.outputPriority';

    protected static $sphinxRegexps = array();

    /**
     * @ORM\Id
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Attribute")
     * @ORM\JoinColumn(name="attribute_id", referencedColumnName="id", nullable=false)
     * @Assert\NotBlank()
     *
     * @var Attribute
     */
    protected $attribute;

    /**
     * @ORM\Column(length=100, name="value", nullable=false)
     * @Assert\NotBlank()
     */
    protected $value;

    /**
     * @ORM\Column(type="smallint", name="url_priority", nullable=false)
     * @Assert\NotBlank()
     */
    protected $urlPriority;

    /**
     * @ORM\Column(type="smallint", name="output_priority", nullable=false)
     * @Assert\NotBlank()
     */
    protected $outputPriority;

    /**
     * @ORM\Column(length=100, name="slug", nullable=true, unique=true)
     * @Assert\NotBlank()
     */
    protected $slug;

    /**
     * @ORM\Column(length=100, name="old_slug", nullable=true)
     */
    protected $oldSlug;

    /** @ORM\Column(length=1000, name="regex_match", nullable=true) */
    protected $regexMatch;

    /** @ORM\Column(length=1000, name="regex_exclude", nullable=true) */
    protected $regexExclude;

    /** @ORM\Column(length=100, name="additional_info", nullable=true) */
    protected $additionalInfo;

    /** @ORM\Column(length=100, name="value_accusative", nullable=true) */
    protected $valueAccusative;

    public function __construct()
    {
        $this->urlPriority = 0;
        $this->outputPriority = 0;
    }

    /**
     * @return array
     */
    public static function getSphinxRegexps()
    {
        return self::$sphinxRegexps;
    }

    /**
     * @param array $sphinxRegexps
     */
    public static function initializeSphinxRegexps(array $sphinxRegexps)
    {
        self::$sphinxRegexps = $sphinxRegexps;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getAdditionalInfo()
    {
        return $this->additionalInfo;
    }

    public function setAdditionalInfo($additionalInfo)
    {
        $this->additionalInfo = $additionalInfo;
    }

    public function getOutputPriority()
    {
        return $this->outputPriority;
    }

    public function setOutputPriority($outputPriority)
    {
        $this->outputPriority = $outputPriority;
    }

    public function getRegexExclude()
    {
        return $this->regexExclude;
    }

    public function setRegexExclude($regexExclude)
    {
        $this->regexExclude = $regexExclude;
    }

    public function getRegexMatch()
    {
        return $this->regexMatch;
    }

    public function setRegexMatch($regexMatch)
    {
        $this->regexMatch = $regexMatch;
    }

    public function getSlug()
    {
        return $this->slug;
    }

    public function setSlug($slug)
    {
        $this->slug = $slug;
    }

    public function getUrlPriority()
    {
        return $this->urlPriority;
    }

    public function setUrlPriority($urlPriority)
    {
        $this->urlPriority = $urlPriority;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * @return Attribute
     */
    public function getAttribute()
    {
        return $this->attribute;
    }

    /**
     * @param Attribute $attribute
     */
    public function setAttribute(Attribute $attribute)
    {
        $this->attribute = $attribute;
    }

    public function getValueAccusative()
    {
        return $this->valueAccusative;
    }

    public function setValueAccusative($valueAccusative)
    {
        $this->valueAccusative = $valueAccusative;
    }

    public function getValueAccusativeForEmbed()
    {
        return TextHelperStatic::normalizeTitleForEmbed($this->getValueAccusative());
    }

    public function getValueForEmbed()
    {
        return TextHelperStatic::normalizeTitleForEmbed($this->getValue());
    }

    public function getOldSlug()
    {
        return $this->oldSlug;
    }

    public function setOldSlug($oldSlug)
    {
        $this->oldSlug = $oldSlug;
    }

    public static function normalizeTitle($title)
    {
        $matches = self::getSphinxRegexps();

        if (!count($matches)) {
            return $title;
        }

        return preg_replace(array_keys($matches), array_values($matches), $title);
    }
}
