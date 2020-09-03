<?php

namespace Metal\CallbacksBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Index;
use Metal\CategoriesBundle\Entity\Category;
use Metal\CompaniesBundle\Entity\Company;
use Metal\DemandsBundle\Entity\Demand;
use Metal\ProductsBundle\Entity\Product;
use Metal\ProductsBundle\Entity\ValueObject\ProductMeasure;
use Metal\ProductsBundle\Entity\ValueObject\ProductMeasureProvider;
use Metal\ProjectBundle\Entity\ValueObject\SourceType;
use Metal\ProjectBundle\Entity\ValueObject\SourceTypeProvider;
use Metal\TerritorialBundle\Entity\City;
use Metal\TerritorialBundle\Entity\Country;
use Metal\TerritorialBundle\Entity\Region;
use Metal\UsersBundle\Entity\User;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="Metal\CallbacksBundle\Repository\CallbackRepository")
 * @ORM\Table(name="callback",indexes={@Index(name="IDX_company_caller", columns={"company_id", "processed_by"})})
 * @ORM\HasLifecycleCallbacks
 */
class Callback
{
    const CALLBACK_TO_MODERATOR = 1;
    const CALLBACK_TO_SUPPLIER = 2;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer", name="id")
     */
    protected $id;

    /**
     * @ORM\Column(length=255, name="phone")
     * @Assert\NotBlank()
     * @Assert\Regex(
     *   pattern="/\d+/",
     *   message="Неправильный номер телефона"
     * )
     * @Assert\Length(
     *   min=6,
     *   max=255
     * )
     */
    protected $phone;

    /** @ORM\Column(type="datetime", name="created_at") */
    protected $createdAt;

    /**
     * @ORM\Column(type="datetime", name="processed_at", nullable=true)
     *
     * @var \DateTime
     */
    protected $processedAt;

    /**
     * @ORM\ManyToOne(targetEntity="Metal\UsersBundle\Entity\User")
     * @ORM\JoinColumn(name="processed_by", referencedColumnName="User_ID", nullable=true)
     *
     * @var User
     */
    protected $processedBy;

    /**
     * @ORM\ManyToOne(targetEntity="Metal\ProductsBundle\Entity\Product")
     * @ORM\JoinColumn(name="product_id", referencedColumnName="Message_ID", nullable=true)
     *
     * @var Product
     */
    protected $product;

    /** @ORM\Column(type="integer", name="kind", nullable=false) */
    protected $kind;

    /**
     * @ORM\ManyToOne(targetEntity="Metal\CompaniesBundle\Entity\Company")
     * @ORM\JoinColumn(name="company_id", referencedColumnName="Message_ID", nullable=true)
     *
     * @var Company
     */
    protected $company;

    /** @ORM\Column(type="integer", name="call_from_type") */
    protected $callFromTypeId;

    /**
     * @var \Metal\ProjectBundle\Entity\ValueObject\SourceType
     */
    protected $callFromType;

    /**
     * @ORM\ManyToOne(targetEntity="Metal\TerritorialBundle\Entity\City")
     * @ORM\JoinColumn(name="city_id", referencedColumnName="Region_ID", nullable=true)
     *
     * @var City
     */
    protected $city;

    /**
     * @ORM\ManyToOne(targetEntity="Metal\TerritorialBundle\Entity\Region")
     * @ORM\JoinColumn(name="region_id", referencedColumnName="Regions_ID", nullable=true)
     *
     * @var Region
     */
    protected $region;

    /**
     * @ORM\ManyToOne(targetEntity="Metal\TerritorialBundle\Entity\Country")
     * @ORM\JoinColumn(name="country_id", referencedColumnName="Country_ID", nullable=true)
     *
     * @var Country
     */
    protected $country;

    /**
     * @ORM\ManyToOne(targetEntity="Metal\CategoriesBundle\Entity\Category")
     * @ORM\JoinColumn(name="Category_ID", referencedColumnName="Message_ID", nullable=true)
     *
     * @var Category
     */
    protected $category;

    /**
     * @ORM\ManyToOne(targetEntity="Metal\UsersBundle\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="User_ID")
     *
     * @var User
     */
    protected $user;

    /**
     * @ORM\ManyToOne(targetEntity="Metal\DemandsBundle\Entity\AbstractDemand")
     * @ORM\JoinColumn(name="demand_id", referencedColumnName="id")
     */
    protected $demand;

    /** @ORM\Column(length=255, name="notation", nullable=true) */
    protected $notation;

    /**
     * @ORM\Column(type="integer", name="volume_type", nullable=true)
     */
    protected $volumeTypeId;

    /**
     * @ORM\Column(type="decimal", name="volume", scale=2, nullable=true)
     * @Assert\Type(
     *     type="numeric"
     * )
     */
    protected $volume;

    /**
     * @var ProductMeasure;
     */
    protected $volumeType;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->kind = self::CALLBACK_TO_MODERATOR;
    }

    /**
     * @ORM\PostLoad
     */
    public function postLoad()
    {
        $this->callFromType = null;
        if ($this->callFromTypeId) {
            $this->callFromType = SourceTypeProvider::create($this->callFromTypeId);
        }
        $this->volumeType = null;
        if ($this->volumeTypeId) {
            $this->volumeType = ProductMeasureProvider::create($this->volumeTypeId);
        }
    }

    public function getMeasureName()
    {
        if ($this->volumeType) {
            return $this->volumeType->getToken();
        }
    }

    public function getId()
    {
        return $this->id;
    }

    /**
     * @param SourceType $callFromType
     */
    public function setCallFromType(SourceType $callFromType)
    {
        $this->callFromType = $callFromType;
        $this->callFromTypeId = $callFromType->getId();
    }

    /**
     * @return SourceType
     */
    public function getCallFromType()
    {
        return $this->callFromType;
    }

    public function setCallFromTypeId($callFromTypeId)
    {
        $this->callFromTypeId = $callFromTypeId;
        $this->postLoad();
    }

    public function getCallFromTypeId()
    {
        return $this->callFromTypeId;
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
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTime $processedAt
     */
    public function setProcessedAt(\DateTime $processedAt)
    {
        $this->processedAt = $processedAt;
    }

    /**
     * @return \DateTime
     */
    public function getProcessedAt()
    {
        return $this->processedAt;
    }

    /**
     * @param User $processedBy
     */
    public function setProcessedBy(User $processedBy)
    {
        $this->processedBy = $processedBy;
    }

    /**
     * @return User
     */
    public function getProcessedBy()
    {
        return $this->processedBy;
    }

    public function setProcessed($processedAt = true)
    {
        $this->processedAt = $processedAt ? new \DateTime() : null;
        if (!$this->processedAt) {
            $this->processedBy = null;
        }
    }

    public function isProcessed()
    {
        return $this->processedAt !== null;
    }

    public function getProcessedAtTimestamp()
    {
        return $this->processedAt ? $this->processedAt->getTimestamp() : null;
    }

    public function setPhone($phone)
    {
        $this->phone = $phone;
    }

    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @param Product $product
     */
    public function setProduct(Product $product)
    {
        $this->product = $product;
        $this->setCompany($product->getCompany());
    }

    /**
     * @return Product
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * @param Company $company
     */
    public function setCompany(Company $company)
    {
        $this->company = $company;
    }

    /**
     * @return Company
     */
    public function getCompany()
    {
        return $this->company;
    }

    public function setKind($kind)
    {
        $this->kind = $kind;
    }

    public function getKind()
    {
        return $this->kind;
    }

    public function isPublic()
    {
        return $this->getKind() == self::CALLBACK_TO_MODERATOR;
    }

    public function isFromMiniSite()
    {
        return $this->callFromTypeId == SourceTypeProvider::MINISITE;
    }

    public function isFromProduct()
    {
        return $this->callFromTypeId == SourceTypeProvider::PRODUCT_VIEW;
    }

    /**
     * @param City $city
     */
    public function setCity(City $city = null)
    {
        $this->city = $city;
        $this->region = $city ? $city->getRegion() : null;
        $this->country = $city ? $city->getCountry() : null;
    }

    /**
     * @return City
     */
    public function getCity()
    {
        return $this->city;
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
        $this->country = $region ? $region->getCountry() : null;
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
    public function setCountry(Country $country)
    {
        $this->country = $country;
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

    public function setNotation($notation)
    {
        $this->notation = $notation;
    }

    public function getNotation()
    {
        return $this->notation;
    }

    /**
     * @param User $user
     */
    public function setUser(User $user = null)
    {
        $this->user = $user;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param Demand $demand
     */
    public function setDemand(Demand $demand)
    {
        $this->demand = $demand;
    }

    /**
     * @return Demand
     */
    public function getDemand()
    {
        return $this->demand;
    }

    public function setVolume($volume)
    {
        $volume = str_replace(',', '.', $volume);
        $this->volume = $volume;
    }

    public function getVolume()
    {
        return $this->volume;
    }

    /**
     * @param ProductMeasure $volumeType
     */
    public function setVolumeType(ProductMeasure $volumeType)
    {
        $this->volumeType = $volumeType;
        $this->volumeTypeId = $this->volumeType->getId();
    }

    /**
     * @return ProductMeasure
     */
    public function getVolumeType()
    {
        return $this->volumeType;
    }

    public function setVolumeTypeId($volumeTypeId)
    {
        $this->volumeTypeId = $volumeTypeId;
        $this->postLoad();
    }

    public function getVolumeTypeId()
    {
        return $this->volumeTypeId;
    }
}
