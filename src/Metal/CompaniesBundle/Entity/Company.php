<?php

namespace Metal\CompaniesBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Mapping as ORM;

use Metal\CompaniesBundle\Entity\ValueObject\CompanyPackageTypeProvider;
use Metal\CompaniesBundle\Entity\ValueObject\CompanyType;
use Metal\CompaniesBundle\Validator\Constraints\CompanySlug;
use Metal\MiniSiteBundle\Entity\MiniSiteConfig;
use Metal\ProductsBundle\Entity\Product;
use Metal\ProjectBundle\Entity\Behavior\Attributable;
use Metal\ServicesBundle\Entity\Package;
use Metal\TerritorialBundle\Entity\City;
use Metal\TerritorialBundle\Entity\Coordinate;
use Metal\ProjectBundle\Validator\Constraints\Image;
use Metal\TerritorialBundle\Entity\Country;
use Metal\TerritorialBundle\Entity\Region;
use Metal\TerritorialBundle\Entity\TerritoryInterface;
use Metal\TerritorialBundle\Entity\ValueObject\MinisiteDomain;
use Metal\TerritorialBundle\Entity\ValueObject\MinisiteDomainsProvider;
use Metal\UsersBundle\Entity\User;

use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Vich\UploaderBundle\Entity\File as EmbeddableFile;

/**
 * @ORM\Entity(repositoryClass="Metal\CompaniesBundle\Repository\CompanyRepository")
 * @ORM\Table(name="Message75",
 *   indexes={
 *      @ORM\Index(name="IDX_deleted_at_ts", columns={"deleted_at_ts"}),
 *      @ORM\Index(name="IDX_code_access", columns={"code_access"}),
 *  },
 *   uniqueConstraints={@ORM\UniqueConstraint(name="UNIQ_slug_deleted_at_ts", columns={"slug", "deleted_at_ts"})}
 * )
 *
 * @ORM\HasLifecycleCallbacks
 * @Vich\Uploadable
 */
class Company implements ContactInfoInterface
{
    const VISIBILITY_STATUS_NORMAL = 0;
    const VISIBILITY_STATUS_ALL_CITIES = 1;
    const VISIBILITY_STATUS_OTHER_COUNTRIES = 2;
    const VISIBILITY_STATUS_ALL_COUNTRIES = 3;

    const MAX_LENGTH_SITES_JSON = 1000;

    const SLUG_REGEX = '[a-z0-9](?:(?!--)[-a-z0-9])*[a-z0-9]';

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer", name="Message_ID")
     */
    protected $id;

    /**
     * @ORM\Column(length=255, name="title")
     *
     * @Assert\NotBlank(groups={"company_edit"})
     * @Assert\Length(min=2, max=100, groups={"company_edit"})
     */
    protected $title;

    /**
     * @ORM\Column(type="integer", name="company_type")
     *
     * @Assert\NotBlank(groups={"company_edit"})
     */
    protected $companyTypeId;

    /**
     * @var CompanyType
     */
    protected $companyType;

    /**
     * @ORM\OneToMany(targetEntity="CompanyPhone", mappedBy="company", cascade={"persist"}, orphanRemoval=true)
     * @Assert\Count(min=1, minMessage="Необходимо указать телефон для компании", groups={"company_edit"})
     *
     * @var ArrayCollection|CompanyPhone[]
     */
    protected $phones;

    /** @ORM\Column(type="integer", name="company_buy_concerned", nullable=true) */
    protected $isBuyer;

    /** @ORM\Column(type="integer", name="company_sell_concerned", nullable=true) */
    protected $isSeller;

    /**
     * @ORM\OneToOne(targetEntity="Metal\CompaniesBundle\Entity\CompanyLog")
     * @ORM\JoinColumn(name="company_log_id", referencedColumnName="company_id")
     *
     * @var CompanyLog
     */
    protected $companyLog;

    /**
     * @ORM\OneToOne(targetEntity="Metal\CompaniesBundle\Entity\CompanyDescription")
     * @ORM\JoinColumn(name="company_description_id", referencedColumnName="company_id")
     *
     * @var CompanyDescription
     */
    protected $companyDescription;

    /**
     * @ORM\Column(length=100, name="address_new")
     *
     * @Assert\Length(min=4, max=200, groups={"company_edit"})
     */
    protected $address;

    /** @ORM\Column(type="boolean", name="optimize_logo", nullable=false, options={"default":1}) */
    protected $optimizeLogo;

    /**
     * @ORM\Embedded(class="Vich\UploaderBundle\Entity\File", columnPrefix="company_logo_")
     *
     * @var EmbeddableFile
     */
    protected $logo;

    /**
     * @Vich\UploadableField(mapping="company_logo", fileNameProperty="logo.name", originalName="logo.originalName", mimeType="logo.mimeType", size="logo.size")
     * @Image(maxSize="10M")
     *
     * @var File|UploadedFile
     */
    protected $uploadedLogo;

    /**
     * @ORM\ManyToOne(targetEntity="Metal\TerritorialBundle\Entity\Region")
     * @ORM\JoinColumn(name="company_region", referencedColumnName="Regions_ID", nullable=true)
     *
     * @var Region
     */
    protected $region;

    /**
     * @ORM\ManyToOne(targetEntity="Metal\TerritorialBundle\Entity\City")
     * @ORM\JoinColumn(name="company_city", referencedColumnName="Region_ID", nullable=true)
     * @Assert\NotBlank(groups={"company_city", "company_edit"})
     *
     * @var City
     */
    protected $city;

    /**
     * @ORM\OneToMany(targetEntity="Metal\CompaniesBundle\Entity\CompanyCity", mappedBy="company", cascade={"persist"}, orphanRemoval=true)
     * @ORM\OrderBy({"kind" = "ASC"}))
     *
     *
     * @var ArrayCollection|CompanyCity[]
     */
    protected $companyCities;

    /**
     * @ORM\Column(length=200, name="delivery_description", nullable=true)
     * @Assert\Length(max=200, groups={"company_edit"})
     */
    protected $deliveryDescription;


    /**
     * @ORM\Column(type="json_array", length=1000, name="company_url", nullable=true)
     *
     * @var array
     */
    protected $sites;

    /** @ORM\Column(type="datetime", name="Created") */
    protected $createdAt;

    /**
     * @ORM\Column(type="smallint", name="company_rating", options={"default":0})
     */
    protected $companyRating;

    /**
     * @ORM\Column(type="smallint", name="code_access", options={"default":0})
     */
    protected $codeAccess;

    /**
     * @ORM\Column(type="json_array", length=5000, name="code_access_territory", nullable=true)
     *
     * @var array
     */
    protected $codeAccessTerritory;

    /** @ORM\Column(type="smallint", name="visibility_status", options={"default":0}) */
    protected $visibilityStatus;

    /** @ORM\Column(type="boolean", name="has_territorial_rules", nullable=false, options={"default":0}) */
    protected $hasTerritorialRules;

    /**
     * @ORM\ManyToOne(targetEntity="Metal\UsersBundle\Entity\User")
     * @ORM\JoinColumn(name="Manager", referencedColumnName="User_ID", nullable=true)
     *
     * @var User
     */
    protected $manager;

    /**
     * @ORM\OneToMany(targetEntity="CompanyCategory", indexBy="categoryId", mappedBy="company", cascade={"persist"}, orphanRemoval=true)
     *
     * @var ArrayCollection|CompanyCategory[]
     */
    protected $companyCategories;

    /**
     * @ORM\Column(length=255, name="slug", nullable=true)
     * @Assert\NotBlank(groups={"company_address"})
     * @Assert\Length(min="2", max="35", groups={"company_address"})
     * @CompanySlug(groups={"company_address"})
     */
    protected $slug;

    /**
     * @ORM\Column(type="datetime", name="slug_changed_at", nullable=true)
     */
    protected $slugChangedAt;

    /** @ORM\Column(length=255, name="slogan", nullable=true) */
    protected $slogan;

    /** @ORM\Column(length=255, name="domain", nullable=true) */
    protected $domain;

    /**
     * @ORM\OneToOne(targetEntity="CompanyCounter")
     * @ORM\JoinColumn(name="counter_id", referencedColumnName="id", nullable=true)
     *
     * @var CompanyCounter
     */
    protected $counter;

    /**
     * @ORM\OneToOne(targetEntity="PaymentDetails")
     * @ORM\JoinColumn(name="payment_details_id", referencedColumnName="id", nullable=true)
     *
     * @var PaymentDetails
     */
    protected $paymentDetails;

    /** @ORM\Column(type="datetime", name="LastUpdated") */
    protected $updatedAt;

    /** @ORM\Column(type="datetime", name="coordinates_updated_at", nullable=true) */
    protected $coordinatesUpdatedAt;

    use Coordinate;

    /** @ORM\Column(type="datetime", name="last_visit_at") */
    protected $lastVisitAt;

    /**
     * @ORM\OneToMany(targetEntity="CompanyAttribute", mappedBy="company", indexBy="typeId", cascade={"persist"}, orphanRemoval=true)
     *
     * @var ArrayCollection|CompanyAttribute[]
     */
    protected $companyAttributes;

    /**
     * @ORM\ManyToOne(targetEntity="Metal\TerritorialBundle\Entity\Country")
     * @ORM\JoinColumn(name="country_id", referencedColumnName="Country_ID", nullable=false)
     *
     * @var Country
     */
    protected $country;

    /** @ORM\Column(name="phones_as_string", nullable=true) */
    protected $phonesAsString;

    /** @ORM\Column(type="boolean", name="ReplicationStatus", nullable=false, options={"default":0}) */
    protected $unsynchronizedStatus;

    /** @ORM\Column(type="boolean", name="inCrm", nullable=false, options={"default":0}) */
    protected $inCrm;

    /**
     * @ORM\Column(type="boolean", name="Checked", nullable=false, options={"default":0})
     */
    protected $isModerated;

    protected $mainBranchOffice;

    /**
     * @ORM\Column(length=255, name="normalized_title")
     */
    protected $normalizedTitle;

    /**
     * @ORM\Column(name="deleted_at_ts", type="integer", nullable=false, options={"default":0, "unsigned"=true})
     */
    protected $deletedAtTS;

    /**
     * @var EntityManagerInterface|null
     */
    protected $em;

    /**
     * @ORM\Column(name="domain_id", type="integer", nullable=false, options={"default":0})
     */
    protected $domainId;

    /**
     * @var MinisiteDomain
     */
    protected $domainType;

    /**
     * @ORM\Column(type="date", name="spros_end", nullable=true)
     *
     * @var \DateTime
     */
    protected $sprosEndsAt;

    /**
     * @ORM\Column(type="csv", name="company_categories_titles", nullable=true, options={"default":""})
     *
     * @var array
     */
    protected $companyCategoriesTitles;

    /**
     * @ORM\Column(type="csv", name="company_delivery_titles", nullable=true, options={"default":""})
     *
     * @var array
     */
    protected $companyDeliveryTitles;

    /**
     * @ORM\ManyToOne(targetEntity="Metal\ProductsBundle\Entity\Product")
     * @ORM\JoinColumn(name="virtual_product_id", referencedColumnName="Message_ID", nullable=true)
     *
     * @var Product
     */
    protected $virtualProduct;

    /**
     * @ORM\ManyToOne(targetEntity="Metal\CompaniesBundle\Entity\Promocode")
     * @ORM\JoinColumn(name="promocode_id", referencedColumnName="id", nullable=true)
     *
     * @var Promocode
     */
    protected $promocode;

    /**
     * @ORM\Column(type="boolean", name="enabled_auto_association_with_photos", nullable=false, options={"default":1})
     */
    protected $enabledAutoAssociationWithPhotos;

    /**
     * @ORM\Column(type="boolean", name="minisite_enabled", nullable=false, options={"default":1})
     */
    protected $minisiteEnabled;

    /**
     * @ORM\Column(type="boolean", name="index_minisite", nullable=false, options={"default":1})
     */
    protected $indexMinisite;

    /**
     * @ORM\Column(type="boolean", name="is_allowed_extra_cities", nullable=false, options={"default":0})
     */
    protected $isAllowedExtraCities;

    /**
     * @ORM\Column(type="boolean", name="is_added_to_cloudflare", nullable=true)
     */
    protected $isAddedToCloudflare;

    /**
     * @ORM\Column(type="boolean", name="main_user_all_sees", nullable=false, options={"default":1})
     */
    protected $mainUserAllSees;

    /**
     * @ORM\OneToOne(targetEntity="Metal\MiniSiteBundle\Entity\MiniSiteConfig")
     * @ORM\JoinColumn(name="minisite_config_id", referencedColumnName="company_id")
     *
     * @var MiniSiteConfig
     */
    protected $minisiteConfig;

    protected $packageChecker;

    protected $packageCheckersByTerritory = array(
        'cities' => array(),
        'regions' => array()
    );

    use Attributable;

    public function __construct()
    {
        $this->companyCategories = new ArrayCollection();
        $this->phones = new ArrayCollection();
        $this->companyCities = new ArrayCollection();
        $this->companyAttributes = new ArrayCollection();
        $this->logo = new EmbeddableFile();

        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
        $this->lastVisitAt = new \DateTime();

        $this->title = '';
        $this->address = '';
        $this->optimizeLogo = true;

        $this->inCrm = true;
        $this->isAllowedExtraCities = false;
        $this->isModerated = false;
        $this->unsynchronizedStatus = false;
        $this->hasTerritorialRules = false;
        $this->enabledAutoAssociationWithPhotos = true;
        $this->minisiteEnabled = true;
        $this->indexMinisite = true;
        $this->mainUserAllSees = true;

        $this->deletedAtTS = 0;
        $this->domainId = 0;
        $this->companyRating = 0;
        $this->codeAccess = Package::BASE_PACKAGE;
        $this->codeAccessTerritory = array();

        $this->sites = array();
        $this->companyCategoriesTitles = array();
        $this->companyDeliveryTitles = array();

        $this->visibilityStatus = self::VISIBILITY_STATUS_NORMAL;
        $this->isAddedToCloudflare = false;
    }

    public static function getVisibilityStatusesAsArray()
    {
        return array(
            self::VISIBILITY_STATUS_NORMAL => 'Обычно',
            self::VISIBILITY_STATUS_ALL_CITIES => 'В своей стране',
            self::VISIBILITY_STATUS_OTHER_COUNTRIES => 'В других странах',
            self::VISIBILITY_STATUS_ALL_COUNTRIES => 'Во всех странах',
        );
    }

    public static function normalizeTitle($title)
    {
        $charlist = '\!\"&\*\'\-\.\s\/\?@<«\\“”¬';

        $title = trim(preg_replace('/\t\r\n\v/ui', ' ', $title));
        $title = trim(preg_replace('/\s{2,}/ui', ' ', $title));

        $title = trim(preg_replace('/['.$charlist.']+$/u', '', $title));
        $title = trim(preg_replace('/^['.$charlist.']+/u', '', $title));

        return $title;
    }

    /**
     * @return Promocode|null
     */
    public function getPromocode()
    {
        return $this->promocode;
    }

    /**
     * @param Promocode|null $promocode
     */
    public function setPromocode(Promocode $promocode = null)
    {
        $this->promocode = $promocode;
    }

    public function getMinisiteEnabled()
    {
        return $this->minisiteEnabled;
    }

    public function setMinisiteEnabled($minisiteEnabled)
    {
        $this->minisiteEnabled = $minisiteEnabled;
    }

    public function getIndexMinisite()
    {
        return $this->indexMinisite;
    }

    public function setIndexMinisite($indexMinisite)
    {
        $this->indexMinisite = $indexMinisite;
    }

    /**
     * @return Product
     */
    public function getVirtualProduct()
    {
        return $this->virtualProduct;
    }

    /**
     * @param Product $virtualProduct
     */
    public function setVirtualProduct(Product $virtualProduct)
    {
        $this->virtualProduct = $virtualProduct;
    }

    public function setPhonesAsString($phonesAsString)
    {
        $this->phonesAsString = $phonesAsString;
    }

    public function getPhonesAsString()
    {
        return $this->phonesAsString;
    }

    /**
     * @ORM\PreFlush()
     */
    public function normalizePhonesAsString()
    {
        $phoneAsStringArray = array();
        foreach ($this->getPhones() as $phone) {
            if ($phone->getAdditionalCode()) {
                $phoneAsStringArray[] = $phone->getPhone().' доб. '.$phone->getAdditionalCode();
            } else {
                $phoneAsStringArray[] = $phone->getPhone();
            }
        }

        $this->setPhonesAsString(implode(', ', $phoneAsStringArray));
    }

    /**
     * @Assert\Callback(groups={"company_edit"})
     */
    public function canUpdateSites(ExecutionContextInterface $context)
    {
        if (mb_strlen(json_encode($this->sites)) > self::MAX_LENGTH_SITES_JSON) {
            $context
                ->buildViolation('Ошибка при добавлении сайта, удалите некоторые сайты и попробуйте снова.')
                ->atPath('sites')
                ->addViolation();
        }
    }

    /**
     * @ORM\PostLoad
     */
    public function postLoad(LifecycleEventArgs $eventArgs)
    {
        $this->em = $eventArgs->getEntityManager();
        $this->initializeCompanyType();
        $this->initializeDomainType(true);
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
        $this->title = preg_replace('/\s+/', ' ', (string)$title);
        $this->normalizedTitle = self::normalizeTitle($title);
    }

    public function getDeliveryDescription()
    {
        return $this->deliveryDescription;
    }

    public function setDeliveryDescription($deliveryDescription)
    {
        $this->deliveryDescription = $deliveryDescription;
    }

    /**
     * @param \Metal\CompaniesBundle\Entity\ValueObject\CompanyType $companyType
     */
    public function setCompanyType(ValueObject\CompanyType $companyType)
    {
        $this->companyType = $companyType;
        $this->companyTypeId = $companyType->getId();
    }

    /**
     * @return \Metal\CompaniesBundle\Entity\ValueObject\CompanyType
     */
    public function getCompanyType()
    {
        return $this->companyType;
    }

    public function getCompanyTypeId()
    {
        return $this->companyTypeId;
    }

    public function setCompanyTypeId($companyTypeId)
    {
        $this->companyTypeId = $companyTypeId;
        $this->initializeCompanyType();
    }

    /**
     * @return CompanyPhone[]|ArrayCollection
     */
    public function getCompanyPhones()
    {
        return $this->phones;
    }

    public function addCompanyPhone(CompanyPhone $companyPhone)
    {
        $companyPhone->setCompany($this);
        $this->phones->add($companyPhone);
    }

    public function removeCompanyPhone(CompanyPhone $companyPhone)
    {
        $this->phones->removeElement($companyPhone);
    }

    /**
     * @Assert\Valid()
     *
     * @return CompanyPhone[]|ArrayCollection
     */
    public function getPhones()
    {
        return $this->phones->filter(function(CompanyPhone $companyPhone) {
            return $companyPhone->getBranchOffice() === null;
        });
    }

    public function addPhone(CompanyPhone $phone)
    {
        $this->addCompanyPhone($phone);
    }

    public function removePhone(CompanyPhone $phone)
    {
        $this->removeCompanyPhone($phone);
    }

    public function getIsBuyer()
    {
        return $this->isBuyer;
    }

    public function setIsBuyer($isBuyer)
    {
        $this->isBuyer = $isBuyer;
    }

    /**
     * @return mixed
     */
    public function getIsAllowedExtraCities()
    {
        return $this->isAllowedExtraCities;
    }

    /**
     * @param mixed $isAllowedExtraCities
     */
    public function setIsAllowedExtraCities($isAllowedExtraCities)
    {
        $this->isAllowedExtraCities = $isAllowedExtraCities;
    }

    public function getIsSeller()
    {
        return $this->isSeller;
    }

    public function setIsSeller($isSeller)
    {
        $this->isSeller = $isSeller;
    }

    /**
     * @return EmbeddableFile
     */
    public function getLogo()
    {
        return $this->logo;
    }

    /**
     * @param EmbeddableFile $logo
     */
    public function setLogo(EmbeddableFile $logo)
    {
        $this->logo = $logo;
    }

    public function getOptimizeLogo()
    {
        return $this->optimizeLogo;
    }

    public function setOptimizeLogo($optimizeLogo)
    {
        $this->optimizeLogo = $optimizeLogo;
    }

    /**
     * @return array
     */
    public function getSites()
    {
        return $this->sites;
    }

    /**
     * @return string|false
     */
    public function getSite()
    {
        return current($this->sites);
    }

    public function addSite($site)
    {
        $this->sites = array_unique(array_merge($this->sites, array($site)));
        $this->sites = array_values($this->sites);
    }

    public function removeSite($site)
    {
        $key = array_search($site, $this->sites);
        if ($key !== false) {
            unset($this->sites[$key]);
        }

        $this->sites = array_values($this->sites);
    }

    public function setCompanyRating($companyRating)
    {
        $this->companyRating = $companyRating;
    }

    public function getCompanyRating()
    {
        return $this->companyRating;
    }

    /**
     * @return User
     */
    public function getManager()
    {
        return $this->manager;
    }

    public function setManager(User $manager = null)
    {
        $this->manager = $manager;
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
    protected function setRegion(Region $region)
    {
        $this->region = $region;
    }

    /**
     * {@inheritdoc}
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
        $this->setRegion($city->getRegion());
        $this->setCountry($city->getCountry());
    }

    public function setSlug($slug)
    {
        if (null === $this->country) {
            throw new \LogicException('Country should be set before calling this method.');
        }

        $slug = mb_strtolower($slug);
        if ($this->slug && ($slug != $this->slug)) {
            $this->slugChangedAt = new \DateTime();
        }

        $this->slug = $slug;
        $this->initializeDomainType();
    }

    public function getSlug()
    {
        return $this->slug;
    }

    public function setDomainId($domainId)
    {
        $this->domainId = $domainId;
        $this->initializeDomainType();
    }

    public function getDomainId()
    {
        return $this->domainId;
    }

    public function setSlugChangedAt($slugChangedAt)
    {
        $this->slugChangedAt = $slugChangedAt;
    }

    public function isSlugChanged()
    {
        return $this->slugChangedAt !== null;
    }

    public function getSlugChangedAt()
    {
        return $this->slugChangedAt;
    }

    public function setDomain($domain)
    {
        $this->domain = $domain;
    }

    public function getDomain()
    {
        if (!$this->domain) {
            return $this->slug.'.'.'metalloprokat.ru';
        }

        return $this->domain;
    }

    public function getCodeAccess()
    {
        return $this->codeAccess;
    }

    public function setCodeAccess($codeAccess)
    {
        $this->codeAccess = $codeAccess;
        // повторно иницализируем Package Checker
        $this->getPackageChecker()->__construct($this);
    }

    /**
     * @return array
     */
    public function getCodeAccessTerritory()
    {
        return $this->codeAccessTerritory;
    }

    /**
     * @param array $codeAccessTerritory
     */
    public function setCodeAccessTerritory($codeAccessTerritory)
    {
        $this->codeAccessTerritory = $codeAccessTerritory;
    }

    public function hasCodeAccessTerritory()
    {
        return !empty($this->codeAccessTerritory);
    }

    /**
     * @param PaymentDetails $paymentDetails
     */
    public function setPaymentDetails(PaymentDetails $paymentDetails)
    {
        $this->paymentDetails = $paymentDetails;
        $this->paymentDetails->setCompany($this);
    }

    /**
     * @return PaymentDetails
     */
    public function getPaymentDetails()
    {
        return $this->paymentDetails;
    }

    /**
     * @param CompanyCounter $counter
     */
    public function setCounter(CompanyCounter $counter)
    {
        $this->counter = $counter;
        $this->counter->setCompany($this);
    }

    /**
     * @return CompanyCounter
     */
    public function getCounter()
    {
        return $this->counter;
    }

    public function setSlogan($slogan)
    {
        $this->slogan = $slogan;
    }

    public function getSlogan()
    {
        return $this->slogan;
    }

    /**
     * @param \DateTime $updatedAt
     */
    public function setUpdatedAt(\DateTime $updatedAt)
    {
        $this->updatedAt = $updatedAt;
    }

    public function setUpdated()
    {
        $this->updatedAt = new \DateTime();
        $this->scheduleSynchronization();
    }

    /**
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    public function setCoordinatesUpdatedAt($coordinatesUpdatedAt)
    {
        $this->coordinatesUpdatedAt = $coordinatesUpdatedAt;
    }

    public function getCoordinatesUpdatedAt()
    {
        return $this->coordinatesUpdatedAt;
    }

    public function setAddress($address)
    {
        $this->address = (string)$address;
    }

    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @return CompanyCategory[]|ArrayCollection
     */
    public function getCompanyCategories()
    {
        return $this->companyCategories;
    }

    /**
     * @return CompanyCategory[]
     */
    public function getEnabledCompanyCategories()
    {
        $companyCategories = array();
        foreach ($this->companyCategories as $companyCategory) {
            if ($companyCategory->getEnabled()) {
                $companyCategories[] = $companyCategory;
            }
        }

        return $companyCategories;
    }

    public function getCategoriesIds()
    {
        $ids = array();
        foreach ($this->getEnabledCompanyCategories() as $companyCategory) {
            $ids[] = $companyCategory->getCategory()->getId();
        }

        return $ids;
    }

    public function addEnabledCompanyCategory(CompanyCategory $companyCategory)
    {
        $this->addCompanyCategory($companyCategory);
    }

    public function removeEnabledCompanyCategory(CompanyCategory $companyCategory)
    {
        $this->removeCompanyCategory($companyCategory);
    }

    public function getCategoriesToCompanyCategoriesIds()
    {
        $ids = array();
        foreach ($this->getCompanyCategories() as $companyCategory) {
            $ids[$companyCategory->getCategory()->getId()] = $companyCategory->getId();
        }

        return $ids;
    }

    public function addCompanyCategory(CompanyCategory $companyCategory)
    {
        $companyCategory->setCompany($this);

        if ($companyCategory->getCategory()) {
            if (!$this->companyCategories->containsKey($companyCategory->getCategoryId())) {
                $this->companyCategories->set($companyCategory->getCategoryId(), $companyCategory);
                $this->companyCategoriesTitles[$companyCategory->getCategory()->getId()] = $companyCategory->getCategory()->getTitleAblativeForEmbed();
            }
        } else {
            $this->companyCategories->add($companyCategory);
        }
    }

    public function removeCompanyCategory(CompanyCategory $companyCategory)
    {
        $this->companyCategories->removeElement($companyCategory);
        if ($companyCategory->getCategory()) {
            unset($this->companyCategoriesTitles[$companyCategory->getCategory()->getId()]);
        }
    }

    public function addCompanyCategories(CompanyCategory $companyCategory)
    {
        $this->addCompanyCategory($companyCategory);
    }

    public function removeCompanyCategories(CompanyCategory $companyCategory)
    {
        $this->removeCompanyCategory($companyCategory);
    }

    /**
     * @return CompanyCity[]|ArrayCollection
     */
    public function getCompanyCities()
    {
        return $this->companyCities;
    }

    public function addCompanyCity(CompanyCity $companyCity)
    {
        $companyCity->setCompany($this);
        $this->companyCities->add($companyCity);

        if ($city = $companyCity->getCity()) {
            $this->companyDeliveryTitles[$city->getId()] = $city->getTitleAccusative();
        }
    }

    public function addCompanyCities(CompanyCity $companyCity)
    {
        $this->addCompanyCity($companyCity);
    }

    public function removeCompanyCity(CompanyCity $companyCity)
    {
        $this->companyCities->removeElement($companyCity);
        unset($this->companyDeliveryTitles[$companyCity->getCity()->getId()]);
    }

    /**
     * @return CompanyCity[]|ArrayCollection
     */
    public function getCompanyCitiesDelivery()
    {
        $companyCities = array();

        foreach ($this->companyCities as $companyCity) {
            if ($companyCity->getKind() == CompanyCity::KIND_DELIVERY) {
                $companyCities[] = $companyCity;
            }
        }

        return new ArrayCollection($companyCities);
    }

    /**
     * Возвращает список айдишников городов, которые как-то связаны с компанией:
     *  - центральный офис
     *  - филиалы
     *  - города доставки
     *
     * @return array
     */
    public function getCitiesIds()
    {
        $ids = array();
        foreach ($this->getCompanyCities() as $companyCity) {
            $ids[] = $companyCity->getCity()->getId();
        }

        return $ids;
    }

    public function addCompanyCitiesDelivery(CompanyCity $companyCity)
    {
        $this->addCompanyCity($companyCity);
    }

    public function removeCompanyCitiesDelivery(CompanyCity $companyCity)
    {
        $this->removeCompanyCity($companyCity);
    }

    /**
     * @param \DateTime $lastVisitAt
     */
    public function setLastVisitAt(\DateTime $lastVisitAt)
    {
        $this->lastVisitAt = $lastVisitAt;
    }

    /**
     * @return \DateTime
     */
    public function getLastVisitAt()
    {
        return $this->lastVisitAt;
    }

    /**
     * @return ArrayCollection|CompanyAttribute[]
     */
    public function getCompanyAttributes()
    {
        return $this->companyAttributes;
    }

    public function getCompanyAttributesTypesIds()
    {
        $ids = array();
        foreach ($this->companyAttributes as $companyAttribute) {
            $ids[] = $companyAttribute->getTypeId();
        }

        return $ids;
    }

    public function addCompanyAttributesTypesId($companyAttributeTypeId)
    {
        $companyAttribute = new CompanyAttribute();
        $companyAttribute->setTypeId($companyAttributeTypeId);
        $companyAttribute->setCompany($this);
        $this->companyAttributes->set($companyAttribute->getTypeId(), $companyAttribute);
    }

    public function removeCompanyAttributesTypesId($companyAttributeTypeId)
    {
        $this->companyAttributes->remove($companyAttributeTypeId);
    }

    public function setCreatedAt($createdAt)
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
     * @return \DateTime
     */
    public function getSprosEndsAt()
    {
        return $this->sprosEndsAt;
    }

    /**
     * @param \DateTime $sprosEndsAt
     */
    public function setSprosEndsAt($sprosEndsAt)
    {
        $this->sprosEndsAt = $sprosEndsAt;
    }

    public function getCityTitle()
    {
        if ($this->city) {
            return $this->city->getTitle();
        }

        return '';
    }

    public function setCityTitle($title)
    {
        // do nothing
    }

    /**
     * @param Country $country
     */
    protected function setCountry(Country $country)
    {
        $this->country = $country;
        $this->initializeDomainType();
    }

    /**
     * @return Country
     */
    public function getCountry()
    {
        return $this->country;
    }

    public function setInCrm($inCrm)
    {
        $this->inCrm = $inCrm;
    }

    public function getInCrm()
    {
        return $this->inCrm;
    }

    public function setUnsynchronizedStatus($unsynchronizedStatus)
    {
        $this->unsynchronizedStatus = $unsynchronizedStatus;
    }

    public function getUnsynchronizedStatus()
    {
        return $this->unsynchronizedStatus;
    }

    public function scheduleSynchronization()
    {
        if ($this->inCrm) {
            $this->unsynchronizedStatus = true;
        }
    }

    /**
     * @return ContactInfoInterface
     */
    public function getContactInfo()
    {
        if (!array_key_exists('company_city', $this->attributes)) {
            throw new \LogicException('You should call CompanyCityRepository::attachCompanyCities before calling this method.');
        }

        $companyCity = $this->attributes['company_city'];
        /* @var $companyCity CompanyCity */
        if ($companyCity && !$companyCity->getIsMainOffice()) {
            return $companyCity;
        }

        return $this;
    }

    public function getCompanyToArray()
    {
        return array(
            'companyId' => $this->getId(),
            'cityId' => $this->getCity() ? $this->getCity()->getId() : null,
            'citiesIds' => $this->getCitiesIds(),
            'categoriesIds' => $this->getCategoriesIds(),
            'address' => $this->getAddress(),
        );
    }

    /**
     * @return PackageChecker
     */
    public function getPackageChecker()
    {
        if (null === $this->packageChecker) {
            $this->packageChecker = new PackageChecker($this);
        }

        return $this->packageChecker;
    }

    /**
     * @param TerritoryInterface $territory
     *
     * @return PackageChecker
     */
    public function getPackageCheckerByTerritory(TerritoryInterface $territory)
    {
        $territoryKind = $territory->getKind();
        if ($territoryKind === 'country') {
            return $this->getPackageChecker();
        }

        $associationTerritories = array(
            'city' => 'cities',
            'region' => 'regions'
        );

        $territoryId = $territory->getId();

        if (isset($this->packageCheckersByTerritory[$associationTerritories[$territoryKind]][$territoryId])) {

            return $this->packageCheckersByTerritory[$associationTerritories[$territoryKind]][$territoryId];
        }

        if (isset($this->codeAccessTerritory[$associationTerritories[$territoryKind]][$territoryId])) {
            $this->packageCheckersByTerritory[$associationTerritories[$territoryKind]][$territoryId] = new PackageChecker(
                $this,
                $territory
            );

            return $this->packageCheckersByTerritory[$associationTerritories[$territoryKind]][$territoryId];
        }

        return $this->getPackageChecker();
    }

    public function hasPackageTerritory(TerritoryInterface $territory)
    {
        if (!$this->codeAccessTerritory) {
            return false;
        }

        $kindToKeys = array(
            'city' => 'cities',
            'region' => 'regions'
        );
        $key = $kindToKeys[$territory->getKind()];

        return isset($this->codeAccessTerritory[$key][$territory->getId()]);
    }

    public function isContactsShouldBeVisible()
    {
        return $this->getPackageChecker()->isContactsShouldBeVisible();
    }

    public function getMaxPossibleCompanyCitiesCount()
    {
        return $this->getPackageChecker()->getMaxPossibleCompanyCitiesCount();
    }

    public function getCompanyCitiesCount()
    {
        $criteria = Criteria::create()->where(Criteria::expr()->eq('enabled', true));

        // вычитаем головной офис
        return count($this->getCompanyCities()->matching($criteria)) - 1;
    }

    public function getCompanyCitiesFromCurrentCountryCount()
    {
        $count = 0;
        foreach ($this->getCompanyCities() as $companyCity) {
            if ($companyCity->getEnabled() &&
                !$companyCity->getIsMainOffice()
                && $companyCity->getCity() && $companyCity->getCity()->getCountry() == $this->getCountry()
            ) {
                $count++;
            }
        }

        return $count;
    }

    public function getCompanyCitiesFromOtherCountriesCount()
    {
        $count = 0;
        foreach ($this->getCompanyCities() as $companyCity) {
            if ($companyCity->getEnabled() && $companyCity->getCity() && $companyCity->getCity()->getCountry() != $this->getCountry()) {
                $count++;
            }
        }

        return $count;
    }

    public function canCreateCompanyCity()
    {
        $n = $this->getMaxPossibleCompanyCitiesCount();

        if (null === $n) {
            return true;
        }

        return $this->getCompanyCitiesCount() <= $n;
    }

    public function canCreateCompanyCityInCurrentCountry()
    {
        $n = $this->getPackageChecker()->getMaxPossibleCitiesCountFromCurrentCountry();

        if (null === $n) {
            return true;
        }

        return $this->getCompanyCitiesFromCurrentCountryCount() <= $n;
    }

    public function canCreateCompanyCityInOtherCountries()
    {
        $n = $this->getPackageChecker()->getMaxPossibleCitiesCountFromOtherCountries();

        if (null === $n) {
            return true;
        }

        return $this->getCompanyCitiesFromOtherCountriesCount() <= $n;
    }

    public function getMaxPossibleCategoriesCount()
    {
        return $this->getPackageChecker()->getMaxPossibleCategoriesCount();
    }

    /**
     * @Assert\Callback(groups={"company_edit_admin"})
     */
    public function isAllowedAddCities(ExecutionContextInterface $context)
    {
        if (!$this->canCreateCompanyCity()) {
            $n = $this->getMaxPossibleCompanyCitiesCount();
            $violationMessage = 'add_company_city_error_no_package';
            if ($n) {
                $violationMessage = 'add_company_city_error_limit_exceeded';
            }

            $context
                ->buildViolation($violationMessage)
                ->setParameters(array('%count%' => $n))
                ->setPlural($n)
                ->atPath('companyCities')
                ->addViolation();

            return;
        }

        if (!$this->canCreateCompanyCityInCurrentCountry()) {
            $n = $this->getPackageChecker()->getMaxPossibleCitiesCountFromCurrentCountry();

            $context
                ->buildViolation('add_company_city_error_limit_exceeded_current_country')
                ->setParameters(array('%count%' => $n))
                ->setPlural($n)
                ->atPath('companyCities')
                ->addViolation();

            return;
        }

        if (!$this->canCreateCompanyCityInOtherCountries()) {
            $n = $this->getPackageChecker()->getMaxPossibleCitiesCountFromOtherCountries();

            $context
                ->buildViolation('add_company_city_error_limit_exceeded_other_countries')
                ->setParameters(array('%count%' => $n))
                ->setPlural($n)
                ->atPath('companyCities')
                ->addViolation();

            return;
        }
    }

    /**
     * @return CompanyCity[]|ArrayCollection
     */
    public function getBranchOffices()
    {
        return $this->companyCities->filter(function(CompanyCity $companyCity) {
            return $companyCity->isBranchOffice() && $companyCity->getEnabled();
        });
    }

    /**
     * @Assert\Callback(groups={"company_edit"})
     */
    public function canCreateCategory(ExecutionContextInterface $context)
    {
        if ($this->getMaxPossibleCategoriesCount() === null) {
            return;
        }

        $categoriesIds = array();
        foreach ($this->getCompanyCategories() as $companyCategory) {
            if (!$companyCategory->getIsAutomaticallyAdded() && $companyCategory->getCategory() && $companyCategory->getEnabled() && $companyCategory->getCategory()->getAllowProducts()) {
                $categoriesIds[] = $companyCategory->getCategory()->getId();
            }
        }

        $shouldValidate = true;
        if ($this->em) {
            $oldCountCategories = $this->em
                ->getRepository('MetalCompaniesBundle:CompanyCategory')
                ->createQueryBuilder('cc')
                ->select('IDENTITY(cc.category) as categoryId')
                ->where('cc.company = :company')
                ->andWhere('cc.isAutomaticallyAdded = 0')
                ->andWhere('cc.enabled = true')
                ->setParameter('company', $this)
                ->getQuery()
                ->getResult();

            $oldCategoriesIds = array();
            foreach ($oldCountCategories as $oldCountCategory){
                $oldCategoriesIds[] = $oldCountCategory['categoryId'];
            }

            $shouldValidate = count(array_diff($categoriesIds, $oldCategoriesIds)) > 0;
        }

        if ($shouldValidate && count($categoriesIds) > $this->getMaxPossibleCategoriesCount()) {
            $context
                ->buildViolation('В Вашем пакете можно добавить максимум категорий: '.$this->getMaxPossibleCategoriesCount())
                ->atPath('companyCategories')
                ->addViolation();
        }
    }

    public function setIsModerated($isModerated)
    {
        $this->isModerated = $isModerated;
    }

    public function getIsModerated()
    {
        return $this->isModerated;
    }

    public function getEnabledAutoAssociationWithPhotos()
    {
        return $this->enabledAutoAssociationWithPhotos;
    }

    public function setEnabledAutoAssociationWithPhotos($enabledAutoAssociationWithPhotos)
    {
        $this->enabledAutoAssociationWithPhotos = $enabledAutoAssociationWithPhotos;
    }

    /**
     * @return CompanyCity
     */
    public function getMainOffice()
    {
        if ($this->mainBranchOffice) {
            return $this->mainBranchOffice;
        }

        $criteria = Criteria::create()
            ->where(Criteria::expr()->eq('isMainOffice', true));

        return $this->mainBranchOffice = $this->getBranchOffices()->matching($criteria)->first();
    }

    public function getDeletedAtTS()
    {
        return $this->deletedAtTS;
    }

    public function setDeletedAtTS($deletedAtTS)
    {
        $this->deletedAtTS = $deletedAtTS;
    }

    public function isDeleted()
    {
        return $this->deletedAtTS ? true : false;
    }

    public function setDeleted($deleted = true)
    {
        $this->setDeletedAtTS($deleted ? time() : 0);
    }

    /**
     * @param CompanyDescription $companyDescription
     */
    public function setCompanyDescription(CompanyDescription $companyDescription)
    {
        $this->companyDescription = $companyDescription;
        $this->companyDescription->setCompany($this);
    }

    /**
     * @return CompanyDescription
     */
    public function getCompanyDescription()
    {
        return $this->companyDescription;
    }

    /**
     * @return MiniSiteConfig
     */
    public function getMinisiteConfig()
    {
        return $this->minisiteConfig;
    }

    /**
     * @param MiniSiteConfig $minisiteConfig
     */
    public function setMinisiteConfig(MiniSiteConfig $minisiteConfig)
    {
        $this->minisiteConfig = $minisiteConfig;
        $this->minisiteConfig->setCompany($this);
    }

    /**
     * @param CompanyLog $companyLog
     */
    public function setCompanyLog(CompanyLog $companyLog)
    {
        $this->companyLog = $companyLog;
        $this->companyLog->setCompany($this);
    }

    /**
     * @return CompanyLog
     */
    public function getCompanyLog()
    {
        return $this->companyLog;
    }

    public function getNormalizedTitle()
    {
        return $this->normalizedTitle;
    }

    /**
     * @return array
     */
    public function getCompanyCategoriesTitles()
    {
        return array_values(array_filter($this->companyCategoriesTitles));
    }

    /**
     * @return array
     */
    public function getCompanyDeliveryTitles()
    {
        return array_values(array_filter($this->companyDeliveryTitles));
    }

    protected function refreshDomain()
    {
        if ($this->domainId) {
            $this->domain = $this->slug.'.'.$this->domainType->getTitle();
        } else {
            $this->domain = $this->slug.'.'.$this->country->getBaseHost();
        }
    }

    public function getSubDir()
    {
        return 'companies';
    }

    /**
     * @return File|UploadedFile
     */
    public function getUploadedLogo()
    {
        return $this->uploadedLogo;
    }

    public function setUploadedLogo(File $uploadedLogo = null)
    {
        $this->uploadedLogo = $uploadedLogo;
        if ($this->uploadedLogo) {
            $this->setUpdatedAt(new \DateTime());
        }
    }

    protected function initializeDomainType($postLoad = false)
    {
        $this->domainType = null;
        if ($this->domainId) {
            $this->domainType = MinisiteDomainsProvider::create($this->domainId);
            if (!$postLoad && $this->domainType->getCountryId() != $this->country->getId()) {
                $this->domainId = 0;
                $this->domainType = null;
            }
        }

        if (!$postLoad) {
            $this->refreshDomain();
        }
    }

    protected function initializeCompanyType()
    {
        //TODO: create company type with id = 6 by default?
        if ($this->companyTypeId) {
            $this->companyType = ValueObject\CompanyTypeProvider::create($this->companyTypeId);
        }
    }

    public function getVisibilityStatus()
    {
        return $this->visibilityStatus;
    }

    public function setVisibilityStatus($visibilityStatus)
    {
        $this->visibilityStatus = $visibilityStatus;
    }

    public function getHasTerritorialRules()
    {
        return $this->hasTerritorialRules;
    }

    public function setHasTerritorialRules($hasTerritorialRules)
    {
        $this->hasTerritorialRules = $hasTerritorialRules;
    }

    public function getIsAddedToCloudflare()
    {
        return $this->isAddedToCloudflare;
    }

    public function setIsAddedToCloudflare($isAddedToCloudflare)
    {
        $this->isAddedToCloudflare = $isAddedToCloudflare;
    }

    public function getMainUserAllSees()
    {
        return $this->mainUserAllSees;
    }

    public function setMainUserAllSees($mainUserAllSees)
    {
        $this->mainUserAllSees = $mainUserAllSees;
    }

    public function isPromo()
    {
        return self::VISIBILITY_STATUS_ALL_CITIES == $this->visibilityStatus
            || self::VISIBILITY_STATUS_OTHER_COUNTRIES == $this->visibilityStatus
            || self::VISIBILITY_STATUS_ALL_COUNTRIES == $this->visibilityStatus;
    }

    public function isVisibleInAllCities()
    {
        return $this->visibilityStatus == self::VISIBILITY_STATUS_ALL_CITIES;
    }

    public function isVisibleInOtherCountries()
    {
        return $this->visibilityStatus == self::VISIBILITY_STATUS_OTHER_COUNTRIES;
    }

    public function isVisibleInAllCountries()
    {
        return $this->visibilityStatus == self::VISIBILITY_STATUS_ALL_COUNTRIES;
    }

    public function getPackage()
    {
        return CompanyPackageTypeProvider::create($this->codeAccess);
    }
}
