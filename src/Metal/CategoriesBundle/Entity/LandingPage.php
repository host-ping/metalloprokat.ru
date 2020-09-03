<?php

namespace Metal\CategoriesBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Metal\AttributesBundle\Entity\DTO\AttributesCollection;
use Metal\CategoriesBundle\Entity\ValueObject\LandingPageTerritory;
use Metal\CategoriesBundle\Entity\ValueObject\LandingPageTerritoryProvider;
use Metal\CompaniesBundle\Entity\ValueObject\CompanyServiceProvider;
use Metal\ProductsBundle\DataFetching\Spec\ProductsFilteringSpec;
use Metal\ProjectBundle\Entity\Behavior\Attributable;
use Metal\ProjectBundle\Entity\Metadata;
use Metal\TerritorialBundle\Entity\City;
use Metal\TerritorialBundle\Entity\Country;
use Metal\TerritorialBundle\Entity\Region;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * @ORM\Entity(repositoryClass="Metal\CategoriesBundle\Repository\LandingPageRepository")
 * @ORM\Table(name="landing_page")
 * @UniqueEntity(fields={"title"})
 * @UniqueEntity(fields={"slug"}, errorPath="slug")
 * @ORM\HasLifecycleCallbacks
 */
class LandingPage
{
    const MIN_PRODUCTS_COUNT = 7;

    /**
     * @ORM\Id
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue
     */
    protected $id;

    /**
     * @ORM\Column(length=255, name="title")
     * @Assert\NotBlank()
     */
    protected $title;

    /**
     * @ORM\Column(length=255, name="slug", unique=true)
     * @Assert\NotBlank()
     */
    protected $slug;

    /**
     * @ORM\ManyToOne(targetEntity="Category")
     * @ORM\JoinColumn(name="category_id", referencedColumnName="Message_ID", onDelete="CASCADE")
     *
     * @var Category
     */
    protected $category;

    /**
     * @ORM\ManyToOne(targetEntity="Category")
     * @ORM\JoinColumn(name="breadcrumb_category_id", referencedColumnName="Message_ID", onDelete="CASCADE")
     *
     * @var Category
     */
    protected $breadcrumbCategory;

    /**
     * @ORM\ManyToOne(targetEntity="Metal\TerritorialBundle\Entity\City")
     * @ORM\JoinColumn(name="city_id", referencedColumnName="Region_ID")
     *
     * @var City
     */
    protected $city;

    /**
     * @ORM\ManyToOne(targetEntity="Metal\TerritorialBundle\Entity\Region")
     * @ORM\JoinColumn(name="region_id", referencedColumnName="Regions_ID")
     *
     * @var Region
     */
    protected $region;

    /**
     * @ORM\ManyToOne(targetEntity="Metal\TerritorialBundle\Entity\Country")
     * @ORM\JoinColumn(name="country_id", referencedColumnName="Country_ID")
     *
     * @var Country
     */
    protected $country;

    /**
     * @ORM\OneToMany(targetEntity="LandingPageAttributeValue", mappedBy="landingPage", cascade={"persist"}, orphanRemoval=true)
     *
     * @var ArrayCollection|LandingPageAttributeValue[]
     */
    protected $landingPageAttributesValues;

    /** @ORM\Column(type="boolean", name="enabled", nullable=false, options={"default":1}) */
    protected $enabled;

    /**
     * @ORM\Column(type="integer", name="landing_page_territory_id")
     */
    protected $landingPageTerritoryId;

    /**
     * @var LandingPageTerritory
     */
    protected $landingPageTerritory;

    /**
     * @ORM\OneToMany(targetEntity="LandingPageCityCount", mappedBy="landingPage", cascade={"persist"}, orphanRemoval=true)
     *
     * @var ArrayCollection|LandingPageCityCount[]
     */
    protected $landingPageCityCounts;

    /**
     * @ORM\OneToMany(targetEntity="LandingPageCountryCount", mappedBy="landingPage", cascade={"persist"}, orphanRemoval=true)
     *
     * @var ArrayCollection|LandingPageCountryCount[]
     */
    protected $landingPageCountryCounts;

    /**
     * Кол-во найденых результатов по поисковому запросу
     *
     * @ORM\Column(type="integer", name="results_count", nullable=false, options={"default":0})
     */
    protected $resultsCount;

    /**
     * @ORM\Column(type="datetime", name="created_at")
     *
     * @var \DateTime
     */
    protected $createdAt;

    /**
     * @ORM\Column(type="datetime", name="results_count_updated_at")
     *
     * @var \DateTime
     */
    protected $resultsCountUpdatedAt;

    /**
     * @ORM\Column(type="array", name="company_attributes", nullable=true)
     *
     * @var array
     */
    protected $companyAttributes;

    /**
     * @ORM\Column(length=255, name="search_query", nullable=true)
     */
    protected $searchQuery;

    /**
     * @ORM\Embedded(class="Metal\ProjectBundle\Entity\Metadata", columnPrefix=false)
     */
    protected $metadata;

    use Attributable;

    public function __construct()
    {
        $this->landingPageAttributesValues = new ArrayCollection();
        $this->createdAt = new \DateTime();
        $this->resultsCountUpdatedAt = new \DateTime();
        $this->enabled = true;
        $this->landingPageTerritoryId = ValueObject\LandingPageTerritoryProvider::SELECTED_TERRITORY;
        $this->resultsCount = 0;
        $this->metadata = new Metadata();
        $this->landingPageCityCounts = new ArrayCollection();
        $this->landingPageCountryCounts = new ArrayCollection();
    }

    /**
     * @ORM\PostLoad
     */
    public function postLoad()
    {
        $this->landingPageTerritory = ValueObject\LandingPageTerritoryProvider::create($this->landingPageTerritoryId);
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

    public function setSlug($slug)
    {
        $this->slug = $slug;
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
    public function setCategory(Category $category = null)
    {
        $this->category = $category;
    }

    /**
     * @return Category
     */
    public function getBreadcrumbCategory()
    {
        return $this->breadcrumbCategory;
    }

    /**
     * @param Category $breadcrumbCategory
     */
    public function setBreadcrumbCategory(Category $breadcrumbCategory = null)
    {
        $this->breadcrumbCategory = $breadcrumbCategory;
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
    public function setCity(City $city = null)
    {
        $this->city = $city;
    }

    /**
     * @return Region
     */
    public function getRegion()
    {
        return $this->region;
    }

    /**
     * @param Region $region
     */
    public function setRegion(Region $region = null)
    {
        $this->region = $region;
    }

    /**
     * @return Country
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * @param Country $country
     */
    public function setCountry(Country $country = null)
    {
        $this->country = $country;
    }

    /**
     * @return ArrayCollection|LandingPageCityCount[]
     */
    public function getLandingPageCityCounts()
    {
        return $this->landingPageCityCounts;
    }

    public function addLandingPageCityCount(LandingPageCityCount $landingPageCityCount)
    {
        $landingPageCityCount->setLandingPage($this);
        $this->landingPageCityCounts->add($landingPageCityCount);
    }

    public function addLandingPageCityCounts(LandingPageCityCount $landingPageCityCount)
    {
        $this->addLandingPageCityCount($landingPageCityCount);
    }

    public function removeLandingPageCityCount(LandingPageCityCount $landingPageCityCount)
    {
        $this->landingPageCityCounts->removeElement($landingPageCityCount);
    }

    /**
     * @return ArrayCollection|LandingPageCountryCount[]
     */
    public function getLandingPageCountryCounts()
    {
        return $this->landingPageCountryCounts;
    }

    public function addLandingPageCountryCount(LandingPageCountryCount $landingPageCountryCount)
    {
        $landingPageCountryCount->setLandingPage($this);
        $this->landingPageCountryCounts->add($landingPageCountryCount);
    }

    public function addLandingPageCountryCounts(LandingPageCountryCount $landingPageCountryCount)
    {
        $this->addLandingPageCountryCount($landingPageCountryCount);
    }

    public function removeLandingPageCountryCount(LandingPageCountryCount $landingPageCountryCount)
    {
        $this->landingPageCountryCounts->removeElement($landingPageCountryCount);
    }

    public function getCompanyAttributes()
    {
        return $this->companyAttributes;
    }

    public function setCompanyAttributes(array $companyAttributes)
    {
        $this->companyAttributes = $companyAttributes;
    }

    public function getSearchQuery()
    {
        return $this->searchQuery;
    }

    public function setSearchQuery($searchQuery)
    {
        $this->searchQuery = $searchQuery;
    }

    /**
     * @return ArrayCollection|LandingPageAttributeValue[]
     */
    public function getLandingPageAttributesValues()
    {
        return $this->landingPageAttributesValues;
    }

    public function getAttributesValues()
    {
        $attributesValues = array();
        foreach ($this->getLandingPageAttributesValues() as $landingPageAttributeValue) {
            $attributeValue = $landingPageAttributeValue->getAttributeValue();
            $attributesValues[$attributeValue->getId()] = $attributeValue;
        }

        return $attributesValues;
    }

    public function addLandingPageAttributeValue(LandingPageAttributeValue $landingPageAttributeValue)
    {
        $landingPageAttributeValue->setLandingPage($this);
        $this->landingPageAttributesValues->add($landingPageAttributeValue);
    }

    public function removeLandingPageAttributeValue(LandingPageAttributeValue $landingPageAttributeValue)
    {
        $this->landingPageAttributesValues->removeElement($landingPageAttributeValue);
    }

    public function getEnabled()
    {
        return $this->enabled;
    }

    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;
    }

    public function getLandingPageTerritoryId()
    {
        return $this->landingPageTerritoryId;
    }

    public function setLandingPageTerritoryId($landingPageTerritoryId)
    {
        $this->landingPageTerritoryId = $landingPageTerritoryId;
        $this->postLoad();
    }

    /**
     * @return LandingPageTerritory
     */
    public function getLandingPageTerritory()
    {
        return $this->landingPageTerritory;
    }

    public function getLandingPageTerritoryTitle()
    {
        return $this->landingPageTerritory->getTitle();
    }

    /**
     * @param LandingPageTerritory $landingPageTerritory
     */
    public function setLandingPageTerritory(ValueObject\LandingPageTerritory $landingPageTerritory)
    {
        $this->landingPageTerritory = $landingPageTerritory;
        $this->landingPageTerritoryId = $landingPageTerritory->getId();
    }

    public function getResultsCount()
    {
        return $this->resultsCount;
    }

    public function setResultsCount($resultsCount)
    {
        $this->resultsCount = $resultsCount;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
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
    public function getResultsCountUpdatedAt()
    {
        return $this->resultsCountUpdatedAt;
    }

    /**
     * @param \DateTime $resultsCountUpdatedAt
     */
    public function setResultsCountUpdatedAt(\DateTime $resultsCountUpdatedAt)
    {
        $this->resultsCountUpdatedAt = $resultsCountUpdatedAt;
    }

    public function getAttributesCollection()
    {
        $attributesCollection = new AttributesCollection();
        $attributesCollection->appendAttributeValues($this->getAttributesValues());

        return $attributesCollection;
    }

    public function getProductFilteringSpec()
    {
        $specification = new ProductsFilteringSpec();

        $specification
            ->category($this->getCategory())
            ->companyAttrs($this->getCompanyAttributes())
            ->matchTitle($this->getSearchQuery());

        $specification
            ->attributesCollection($this->getAttributesCollection());

        $specification
            ->city($this->getCity())
            ->region($this->getRegion());

        if ($this->getCountry()) {
            $specification->country($this->getCountry());
        }

        return $specification;
    }

    /**
     * @Assert\Callback()
     */
    public function canUpdateSites(ExecutionContextInterface $context)
    {
        if (!$this->searchQuery && !$this->category && ($this->landingPageAttributesValues->count() == 0)) {
            $context
                ->buildViolation('Нужно добавить категорию или строку поиска или атрибуты')
                ->atPath('category')
                ->addViolation();
        }
    }

    public function getCompanyAttributesObjects()
    {
        if (!$this->companyAttributes) {
            return array();
        }

        $allCompanyServices = CompanyServiceProvider::getAllServices();

        return array_intersect_key($allCompanyServices, array_flip($this->companyAttributes));
    }

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

    /**
     * Возвращает ненастоящую категорию, у которой есть title. Нужно для того, что б сгенерировать метаданные
     * на просмотре лендинг странице как на странице с категорией
     *
     * @return Category
     */
    public function getFakeCategory()
    {
        $category = new Category();
        $category->setTitle($this->title);

        return $category;
    }

    /**
     * Режим лендинг-страницы, когда она ведет себя подобно категории (виртуальная категория)
     *
     * @return bool
     */
    public function isVisibleEverywhere()
    {
        return $this->landingPageTerritoryId == LandingPageTerritoryProvider::ALL_COUNTRIES_AND_CITIES;
    }

    /**
     * Режим лендинг-страницы, когда она привязанк к конкретной территории (страна, город, область)
     *
     * @return bool
     */
    public function isModeSelectedTerritory()
    {
        return $this->landingPageTerritoryId == LandingPageTerritoryProvider::SELECTED_TERRITORY;
    }
}
