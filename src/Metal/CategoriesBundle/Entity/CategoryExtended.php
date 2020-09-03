<?php

namespace Metal\CategoriesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="Metal\CategoriesBundle\Repository\CategoryExtendedRepository")
 * @ORM\Table(name="category_extended")
 */
class CategoryExtended
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\Column(type="integer", name="id")
     */
    protected $id;

    /**
     * @ORM\OneToOne(targetEntity="Category")
     * @ORM\JoinColumn(name="category_id", referencedColumnName="Message_ID", nullable=false, onDelete="CASCADE")
     *
     * @var Category
     */
    protected $category;

    /**
     * @ORM\Column(type="string", length=1000, name="pattern")
     * @Assert\Length(max=1000)
     */
    protected $pattern;

    /**
     * @ORM\Column(type="string", length=2000, name="extended_pattern")
     * @Assert\Length(max=2000)
     */
    protected $extendedPattern;

    /**
     * @ORM\Column(type="string", length=1000, name="test_pattern")
     * @Assert\Length(max=1000)
     */
    protected $testPattern;

    /**
     * @ORM\Column(type="integer", name="matching_priority", nullable=false, options={"default":0})
     * @Assert\NotBlank()
     */
    protected $matchingPriority;

    /**
     * @ORM\Column(type="string", length=2000, name="description")
     * @Assert\Length(max=2000)
     */
    protected $description;

    public function __construct()
    {
        $this->pattern = '';
        $this->extendedPattern = '';
        $this->testPattern = '';
        $this->matchingPriority = 0;
        $this->description = '';
    }

    public function getId()
    {
        return $this->id;
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
        $this->id = $category->getId();
    }

    public function getPattern()
    {
        return $this->pattern;
    }

    public function setPattern($pattern)
    {
        $this->pattern = (string)$pattern;
    }

    public function getExtendedPattern()
    {
        return $this->extendedPattern;
    }

    public function setExtendedPattern($extendedPattern)
    {
        $this->extendedPattern = (string)$extendedPattern;
    }

    public function getTestPattern()
    {
        return $this->testPattern;
    }

    public function setTestPattern($testPattern)
    {
        $this->testPattern = (string)$testPattern;
    }

    public function setMatchingPriority($matchingPriority)
    {
        $this->matchingPriority = (int)$matchingPriority;
    }

    public function getMatchingPriority()
    {
        return $this->matchingPriority;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setDescription($description)
    {
        $this->description = (string)$description;
    }
}
