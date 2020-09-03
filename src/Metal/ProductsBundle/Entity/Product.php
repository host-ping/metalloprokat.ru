<?php

namespace Metal\ProductsBundle\Entity;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Mapping as ORM;

use Metal\CategoriesBundle\Entity\Category;
use Metal\CompaniesBundle\Entity\Company;
use Metal\CompaniesBundle\Entity\CompanyCity;
use Metal\CompaniesBundle\Entity\CustomCompanyCategory;
use Metal\ProductsBundle\Entity\ValueObject\Currency;
use Metal\ProductsBundle\Entity\ValueObject\CurrencyProvider;
use Metal\ProductsBundle\Entity\ValueObject\ProductMeasure;
use Metal\ProductsBundle\Entity\ValueObject\ProductMeasureProvider;
use Metal\ProjectBundle\Entity\Behavior\Attributable;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * @ORM\Entity(repositoryClass="Metal\ProductsBundle\Repository\ProductRepository")
 * @ORM\Table(name="Message142")
 * @ORM\HasLifecycleCallbacks
 */
class Product
{
    const STATUS_NOT_CHECKED = 0;
    const STATUS_CHECKED = 1;
    const STATUS_DELETED = 2;
    const STATUS_PENDING_CATEGORY_DETECTION = 3;
    const STATUS_PROCESSING = 4;
    const STATUS_LIMIT_EXCEEDING = 5;

    const PRICE_CONTRACT = 9999999;

    const MAX_RESULT_IMAGES_IN_ADMINPANEL = 50;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer", name="Message_ID")
     */
    protected $id;

    /**
     * @ORM\Column(length=255, name="Name")
     * @Assert\NotBlank()
     * @Assert\Length(max=120)
     */
    protected $title;

    /**
     * @ORM\Column(length=255, name="Memo", nullable=false)
     */
    protected $size;

    /**
     * @ORM\Column(type="decimal", scale=2, name="Price")
     * @Assert\Type(type="numeric")
     * @Assert\Length(min=1, max=11)
     * @Assert\Range(min="0")
     */
    protected $price;

    /** @ORM\Column(type="smallint", name="currency_id") */
    protected $currencyId;

    /**
     * @var Currency
     */
    protected $currency;

    /** @ORM\Column(type="decimal", scale=2, name="normalized_price", nullable=true) */
    protected $normalizedPrice;

    /**
     * @ORM\ManyToOne(targetEntity="Metal\CompaniesBundle\Entity\Company")
     * @ORM\JoinColumn(name="Company_ID", referencedColumnName="Message_ID")
     *
     * @var Company
     */
    protected $company;


    /**
     * @ORM\OneToOne(targetEntity="Metal\ProductsBundle\Entity\ProductLog", cascade={"persist"})
     * @ORM\JoinColumn(name="product_log_id", referencedColumnName="product_id")
     *
     * @var ProductLog
     */
    protected $productLog;

    protected $newProductLog;

    /**
     * @ORM\OneToOne(targetEntity="Metal\ProductsBundle\Entity\ProductDescription", cascade={"persist"})
     * @ORM\JoinColumn(name="product_description_id", referencedColumnName="product_id")
     *
     * @var ProductDescription
     */
    protected $productDescription;

    protected $newProductDescription;

    /**
     * @ORM\ManyToOne(targetEntity="Metal\CategoriesBundle\Entity\Category")
     * @ORM\JoinColumn(name="Category_ID", referencedColumnName="Message_ID", nullable=true, onDelete="SET NULL")
     *
     * @var Category
     */
    protected $category;

    /**
     * @ORM\ManyToOne(targetEntity="Metal\CompaniesBundle\Entity\CustomCompanyCategory")
     * @ORM\JoinColumn(name="custom_category_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     *
     * @var CustomCompanyCategory
     */
    protected $customCategory;

    /**
     * @ORM\ManyToOne(targetEntity="Metal\CompaniesBundle\Entity\CompanyCity")
     * @ORM\JoinColumn(name="branch_office_id", referencedColumnName="id", nullable=true)
     *
     * @var CompanyCity
     */
    protected $branchOffice;

    /**
     * @ORM\ManyToOne(targetEntity="Metal\ProductsBundle\Entity\ProductImage")
     * @ORM\JoinColumn(name="Image_ID", referencedColumnName="ID", nullable=true, onDelete="SET NULL")
     *
     * @var ProductImage
     */
    protected $image;

    /**
     * @ORM\ManyToOne(targetEntity="Metal\CategoriesBundle\Entity\Category")
     * @ORM\JoinColumn(name="P_Category_ID", referencedColumnName="Message_ID", nullable=true, onDelete="SET NULL")
     */
    protected $categoryParent;

    /** @ORM\Column(type="datetime", name="Created") */
    protected $createdAt;

    /**
     * @ORM\Column(type="integer", name="Pts")
     * @Assert\NotBlank(
     *     message="Выберите единицы измерения либо поставьте прочерк"
     * )
     */
    protected $measureId;

    /**
     * @var ProductMeasure
     */
    protected $measure;

    /** @ORM\Column(type="datetime", name="LastUpdated") */
    protected $updatedAt;

    /** @ORM\Column(type="datetime", name="moderated_at", nullable=true) */
    protected $moderatedAt;

    /**
     * @ORM\Column(type="smallint", name="Checked", nullable=false, options={"default":0})
     */
    protected $checked;

    /** @ORM\Column(type="boolean", name="is_contract_price", options={"default":false}) */
    protected $isContractPrice;

    /** @ORM\Column(type="boolean", name="is_special_offer", options={"default":0}) */
    protected $isSpecialOffer;

    /** @ORM\Column(type="boolean", name="is_price_from", options={"default":0}) */
    protected $isPriceFrom;

    /** @ORM\Column(type="boolean", name="Memo2Title", options={"default":0}) */
    protected $isTitleNonUnique;

    /** @ORM\Column(type="string", length=40, name="item_hash", nullable=true) */
    protected $itemHash;

    /** @ORM\Column(type="boolean", name="is_virtual", options={"default":0}) */
    protected $isVirtual;

    /** @ORM\Column(type="boolean", name="show_on_portal", options={"default":1}) */
    protected $showOnPortal;

    /**
     * @ORM\Column(length=400, name="external_url", nullable=false, options={"default":""})
     * @Assert\Length(max=400)
     * @Assert\Url()
     */
    protected $externalUrl;

    /**
     * @ORM\Column(type="smallint", name="previous_status", nullable=false, options={"default":0})
     */
    protected $previousStatus;

    /**
     * @ORM\Column(type="smallint", name="position", nullable=false, options={"default":1})
     * @Assert\NotBlank()
     * @Assert\GreaterThanOrEqual(1)
     * @Assert\Type(type="integer")
     */
    protected $position;

    /**
     * @ORM\Column(type="boolean", name="is_hot_offer", nullable=false, options={"default":false})
     */
    protected $isHotOffer;

    /**
     * @ORM\Column(type="smallint", name="hot_offer_position", nullable=false, options={"default":1})
     * @Assert\NotBlank()
     * @Assert\GreaterThanOrEqual(1)
     * @Assert\Type(type="integer")
     */
    protected $hotOfferPosition;

    use Attributable;

    protected $batchInsertingMode;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
        $this->checked = self::STATUS_NOT_CHECKED;
        $this->previousStatus = self::STATUS_NOT_CHECKED;
        $this->isContractPrice = false;
        $this->isSpecialOffer = false;
        $this->isPriceFrom = false;
        $this->isTitleNonUnique = false;
        $this->size = '';
        $this->newProductLog = new ProductLog();
        $this->newProductDescription = new ProductDescription();
        $this->batchInsertingMode = false;
        $this->isVirtual = false;
        $this->showOnPortal = true;
        $this->externalUrl = '';
        $this->position = 1;
        $this->isHotOffer = false;
        $this->hotOfferPosition = 1;
    }

    public static function createVirtualProduct(Company $company)
    {
        $product = new self();
        $product->setTitle(sprintf('virtual-product-%d', $company->getId()));
        $product->setMeasure(ProductMeasureProvider::createByPattern('-'));
        $product->setIsVirtual(true);
        $product->setCompany($company);
        $product->setBranchOffice($company->getMainOffice());
        $product->setChecked(self::STATUS_CHECKED);
        $product->setPrice(self::PRICE_CONTRACT);
        $product->setCurrency($company->getCountry()->getCurrency());
        $product->getProductLog()->setCreatedBy($company->getCompanyLog()->getCreatedBy());

        return $product;
    }

    /**
     * @ORM\PostLoad
     */
    public function postLoad()
    {
        $this->initializeCurrency();
        $this->initializeMeasure();
    }

    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function initializeItemHash()
    {
        $itemHash = self::calculateItemHash($this->branchOffice->getId(), $this->title, $this->size);
        $this->setItemHash($itemHash);
    }

    /**
     * @ORM\PostPersist()
     */
    public function postPersist(LifecycleEventArgs $arg)
    {
        $flushRequired = $this->processPostPersist();

        if (!$this->batchInsertingMode && $flushRequired) {
            $arg->getEntityManager()->flush();
        }
    }

    public function processPostPersist()
    {
        $flushRequired = false;

        if ($this->newProductLog) {
            $this->productLog = $this->newProductLog;
            $this->productLog->setProduct($this);
            $this->newProductLog = null;
            $flushRequired = true;
        }

        if ($this->newProductDescription) {
            $this->productDescription = $this->newProductDescription;
            $this->productDescription->setProduct($this);
            $this->newProductDescription = null;
            $flushRequired = true;
        }

        return $flushRequired;
    }

    /**
     * @return boolean
     */
    public function isBatchInsertingMode()
    {
        return $this->batchInsertingMode;
    }

    /**
     * @param boolean $batchInsertingMode
     */
    public function setBatchInsertingMode($batchInsertingMode)
    {
        $this->batchInsertingMode = $batchInsertingMode;
    }

    public function setItemHash($itemHash)
    {
        $this->itemHash = $itemHash;
    }

    public function getItemHash()
    {
        $this->initializeItemHash();

        return $this->itemHash;
    }

    /**
     * @return ProductDescription
     */
    public function getProductDescription()
    {
        return $this->productDescription ?: $this->newProductDescription;
    }

    /**
     * @return ProductLog
     */
    public function getProductLog()
    {
        return $this->productLog ?: $this->newProductLog;
    }

    /**
     * @param ProductLog $productLog
     */
    public function setProductLog(ProductLog $productLog)
    {
        $this->productLog = $productLog;
    }

    /**
     * @param ProductDescription $productDescription
     */
    public function setProductDescription(ProductDescription $productDescription)
    {
        $this->productDescription = $productDescription;
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
    }

    /**
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @return \DateTime
     */
    public function getModeratedAt()
    {
        return $this->moderatedAt;
    }

    /**
     * @param \DateTime $moderatedAt
     */
    public function setModeratedAt(\DateTime $moderatedAt = null)
    {
        $this->moderatedAt = $moderatedAt;
    }

    public function getIsVirtual()
    {
        return $this->isVirtual;
    }

    public function setIsVirtual($isVirtual)
    {
        $this->isVirtual = $isVirtual;
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
        $this->title = self::normalizeTitle($title);
    }

    public function getNormalizedPrice()
    {
        return $this->normalizedPrice;
    }

    public function setNormalizedPrice($normalizedPrice)
    {
        $this->normalizedPrice = $normalizedPrice;
    }

    /**
     * @return Company
     */
    public function getCompany()
    {
        return $this->company;
    }

    public function setCompany($company)
    {
        $this->company = $company;
    }

    /**
     * @return Category
     */
    public function getCategory()
    {
        return $this->category;
    }

    public function setCategory(Category $category = null)
    {
        if ($this->category != $category && $this->image && $this->image->isCommon()) {
            $this->image = null;
        }

        $this->category = $category;
        $this->categoryParent = null;
        if ($this->category) {
            $this->categoryParent = $this->category->getParent();
        }
    }

    /**
     * @return CustomCompanyCategory
     */
    public function getCustomCategory()
    {
        return $this->customCategory;
    }

    /**
     * @param CustomCompanyCategory $customCategory
     */
    public function setCustomCategory(CustomCompanyCategory $customCategory)
    {
        $this->customCategory = $customCategory;
    }

    public function getImage()
    {
        return $this->image;
    }

    public function setImage($image)
    {
        $this->image = $image;
    }

    public function getCategoryParent()
    {
        return $this->categoryParent;
    }

    public function setCategoryParent($categoryParent)
    {
        $this->categoryParent = $categoryParent;
    }

    public function getSize()
    {
        return $this->size;
    }

    public function setSize($size)
    {
        $this->size = self::normalizeTitle($size);
    }

    public function getCutSize()
    {
        if (!$this->size) {
            return '';
        }

        $size = trim($this->size);
        $limit = 16;

        if (mb_strlen($size) < $limit) {
            return $this->size;
        }

        return mb_substr($size, 0, $limit).'...';
    }

    public function getPrice()
    {
        return round($this->price, 1);
    }

    public function setPrice($price)
    {
        $this->price = $price;
        $this->initializePrice();
        //$this->normalizedPrice = $this->getCompany()->getCountry()->getExchangeRates() * $this->price;
    }

    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }

    public function setMeasureId($measureId)
    {
        $this->measureId = $measureId;
        $this->initializeMeasure();
    }

    public function getMeasureId()
    {
        return $this->measureId;
    }

    public function isModerated()
    {
        return $this->checked == self::STATUS_CHECKED;
    }

    public function isWaitingForModeration()
    {
        return $this->checked == self::STATUS_NOT_CHECKED;
    }

    public function isDeleted()
    {
        return $this->checked == self::STATUS_DELETED;
    }

    public function isPending()
    {
        return $this->checked == self::STATUS_PENDING_CATEGORY_DETECTION;
    }

    public function isProcessing()
    {
        return $this->checked == self::STATUS_PROCESSING;
    }

    public function isLimitExceeding()
    {
        return $this->checked == self::STATUS_LIMIT_EXCEEDING;
    }

    public function isLockedForEditing()
    {
        return $this->isPending() || $this->isProcessing();
    }

    public function setChecked($checked)
    {
        $this->checked = $checked;
        if ($this->isModerated() && !$this->getModeratedAt()) {
            $this->moderatedAt = new \DateTime();
        }
    }

    public function getChecked()
    {
        return $this->checked;
    }

    public function getPreviousStatus()
    {
        return $this->previousStatus;
    }

    public function setPreviousStatus($previousStatus)
    {
        $this->previousStatus = $previousStatus;
    }

    public function setCurrencyId($currencyId)
    {
        $this->currencyId = $currencyId;
        $this->initializeCurrency();
    }

    public function getCurrencyId()
    {
        return $this->currencyId;
    }

    /**
     * @param Currency $currency
     */
    public function setCurrency(Currency $currency)
    {
        $this->currency = $currency;
        $this->currencyId = $currency->getId();
    }

    /**
     * @return Currency
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * @param ProductMeasure $measure
     */
    public function setMeasure(ProductMeasure $measure)
    {
        $this->measure = $measure;
        $this->measureId = $measure->getId();
    }

    /**
     * @return ProductMeasure
     */
    public function getMeasure()
    {
        return $this->measure;
    }

    public function setIsContractPrice($isContractPrice)
    {
        $this->isContractPrice = $isContractPrice;
    }

    public function isContractPrice()
    {
        return $this->isContractPrice;
    }

    public function setIsSpecialOffer($isSpecialOffer)
    {
        $this->isSpecialOffer = $isSpecialOffer;
    }

    public function getIsSpecialOffer()
    {
        return $this->isSpecialOffer;
    }

    /**
     * @ORM\PrePersist
     */
    public function initializePrice()
    {
        if (!$this->price || (is_string($this->price) && false !== mb_stripos($this->price, 'дог'))) {
            $this->price = Product::PRICE_CONTRACT;
        }

        $this->isContractPrice = self::PRICE_CONTRACT == $this->price;
        if ($this->isContractPrice && !$this->measureId) {
            $this->setMeasureId(ProductMeasureProvider::WITHOUT_VOLUME);
        }

        //this.normalizedPrice = this.company.country.exchangeRates[this.currency] * this.price
    }

    public function setIsPriceFrom($isPriceFrom)
    {
        $this->isPriceFrom = $isPriceFrom;
    }

    public function getIsPriceFrom()
    {
        return $this->isPriceFrom;
    }

    public function getIsContractPrice()
    {
        return $this->isContractPrice;
    }

    public function setIsTitleNonUnique($isTitleNonUnique)
    {
        $this->isTitleNonUnique = $isTitleNonUnique;
    }

    public function getIsTitleNonUnique()
    {
        return $this->isTitleNonUnique;
    }

    public function getCategoryId()
    {
        return $this->category ? $this->category->getId() : null;
    }

    /**
     * @param CompanyCity $branchOffice
     */
    public function setBranchOffice(CompanyCity $branchOffice)
    {
        $this->branchOffice = $branchOffice;
    }

    /**
     * @return CompanyCity
     */
    public function getBranchOffice()
    {
        return $this->branchOffice;
    }

    /**
     * @return string
     */
    public function getExternalUrl()
    {
        return $this->externalUrl;
    }

    /**
     * @param string $externalUrl
     */
    public function setExternalUrl($externalUrl)
    {
        $this->externalUrl = (string)$externalUrl;
    }

    public function getPosition()
    {
        return $this->position;
    }

    public function setPosition($position)
    {
        $this->position = $position;
    }

    public function getIsHotOffer()
    {
        return $this->isHotOffer;
    }

    public function setIsHotOffer($isHotOffer)
    {
        $this->isHotOffer = $isHotOffer;
    }

    public function getHotOfferPosition()
    {
        return $this->hotOfferPosition;
    }

    public function setHotOfferPosition($hotOfferPosition)
    {
        $this->hotOfferPosition = $hotOfferPosition;
    }

    public static function getProductStatusesAsSimpleArray($filterByLimitExceeding = false)
    {
        if ($filterByLimitExceeding) {
            return array(
                Product::STATUS_LIMIT_EXCEEDING => 'Превышен лимит',
                Product::STATUS_DELETED => 'Удален'
            );
        }

        return array(
            Product::STATUS_NOT_CHECKED => 'Не промодерирован',
            Product::STATUS_CHECKED => 'Промодерирован',
            Product::STATUS_PENDING_CATEGORY_DETECTION => 'Ожидает обработки',
            Product::STATUS_PROCESSING => 'Обрабатывается',
            Product::STATUS_DELETED => 'Удален',
            Product::STATUS_LIMIT_EXCEEDING => 'Превышен лимит'
        );
    }

    public function hasAttribute($key)
    {
        return isset($this->attributes[$key]);
    }

    public static function calculateItemHash($branchOfficeId, $title, $size)
    {
        return sha1(serialize(array((int)$branchOfficeId, mb_strtolower((string)$title), mb_strtolower((string)$size))));
    }

    static public function normalizeTitle($string, $cutNewlines = true)
    {
        $fractions = array(
            '/\xBC/u' => '1/4',
            '/\xBD/u' => '1/2',
            '/\xBE/u' => '3/4',
            '/\x2153/u' => '1/3',
            '/\x2154/u' => '2/3',
            '/\x2155/u' => '1/5',
            '/\x2156/u' => '2/5',
            '/\x2157/u' => '3/5',
            '/\x2158/u' => '4/5',
            '/\x2159/u' => '1/6',
            '/\x215A/u' => '5/6',
            '/\x215B/u' => '1/8',
            '/\x215C/u' => '3/8',
            '/\x215D/u' => '5/8',
            '/\x215E/u' => '7/8'
        );

        $string = preg_replace(array_keys($fractions), array_values($fractions), $string);
        $string = preg_replace('/[ \xC2\xA0'.($cutNewlines ? '\n\r\v' : '').'\t]+/ui', ' ', $string);
        //delete 'Æ Δ Ω Ç' .....
        $string = preg_replace('/[^а-яёa-z0-9\-\(\)\s ~@\"\';:\?.,#%№\$\/\\\\&\n\r\v\*_\+=]/ui', '', $string);
        $string = trim($string);

        return (string) $string;
    }

    public static function getAvailableStatusesForEdit()
    {
        return array(
            self::STATUS_PENDING_CATEGORY_DETECTION => 'Ожидает автоопределения категории',
            self::STATUS_CHECKED => 'Промодерирован',
            self::STATUS_NOT_CHECKED => 'Новый',
            self::STATUS_DELETED => 'Удален'
        );
    }

    private function initializeCurrency()
    {
        $this->currency = CurrencyProvider::create($this->currencyId);
    }

    /**
     * @Assert\Callback()
     */
    public function isCategoryValid(ExecutionContextInterface $context)
    {
        if ($this->getCategory() && !$this->getCategory()->getAllowProducts()) {
            $context
                ->buildViolation(sprintf('К категории "%s" нельзя прикреплять товары.', $this->getCategory()->getTitle()))
                ->atPath('category')
                ->addViolation();
        }
    }

    private function initializeMeasure()
    {
        $this->measure = null;
        if ($this->measureId !== null) {
            $this->measure = ProductMeasureProvider::create($this->measureId);
        }
    }
}
