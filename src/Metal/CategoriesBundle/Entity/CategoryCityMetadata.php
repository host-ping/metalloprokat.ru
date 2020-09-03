<?php

namespace Metal\CategoriesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use Metal\ProjectBundle\Entity\Metadata;
use Metal\TerritorialBundle\Entity\City;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(
 *     name="category_city_metadata",
 *     uniqueConstraints={@ORM\UniqueConstraint(name="UNIQ_category_city", columns={"category_id", "city_id"} )}
 * )
 */
class CategoryCityMetadata
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer", name="id")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Category", inversedBy="categoryCityMetadatas")
     * @ORM\JoinColumn(name="category_id", referencedColumnName="Message_ID", nullable=false, onDelete="CASCADE")
     * @Assert\NotBlank()
     *
     * @var Category
     */
    protected $category;

    /**
     * @ORM\ManyToOne(targetEntity="Metal\TerritorialBundle\Entity\City")
     * @ORM\JoinColumn(name="city_id", referencedColumnName="Region_ID", onDelete="SET NULL")
     *
     * @var City
     */
    protected $city;

    /**
     * @ORM\Embedded(class="Metal\ProjectBundle\Entity\Metadata", columnPrefix=false)
     */
    protected $metadata;

    /**
     * @ORM\Column(length=1000, name="description", nullable=true)
     * @Assert\Length(max="1000")
     */
    protected $description;

    /**
     * @return Metadata
     */
    public function getMetadata()
    {
        return $this->metadata;
    }

    /**
     * @param Metadata $metadata
     */
    public function setMetadata(Metadata $metadata)
    {
        $this->metadata = $metadata;
    }

    public function __construct()
    {
        $this->metadata = new Metadata();
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
    }

    /**
     * @return City
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @param City $city
     */
    public function setCity(City $city)
    {
        $this->city = $city;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setDescription($description)
    {
        $this->description = $description;
    }

    public function getCityTitle()
    {
        if ($this->city) {
            return $this->city->getTitle();
        }

        return '';
    }

    public function setCityTitle($cityTitle)
    {
        // dummy
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
