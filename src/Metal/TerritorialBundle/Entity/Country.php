<?php

namespace Metal\TerritorialBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Metal\ProductsBundle\Entity\ValueObject\Currency;
use Metal\ProductsBundle\Entity\ValueObject\CurrencyProvider;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="Metal\TerritorialBundle\Repository\CountryRepository")
 * @ORM\Table(name="Classificator_Country")
 * @ORM\HasLifecycleCallbacks
 */
class Country implements TerritoryInterface
{
    const COUNTRY_ID_RUSSIA = 165;
    const COUNTRY_ID_UKRAINE = 209;
    const COUNTRY_ID_BELORUSSIA = 19;
    const COUNTRY_ID_KAZAKHSTAN = 83;

    protected static $enabledCountriesIds = array();

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer", name="Country_ID")
     */
    protected $id;

    /**
     * @ORM\Column(length=255, name="Country_Name")
     *
     * @Assert\NotBlank()
     */
    protected $title;

    /**
     * @ORM\ManyToOne(targetEntity="Metal\TerritorialBundle\Entity\City")
     * @ORM\JoinColumn(name="capital_id", referencedColumnName="Region_ID", onDelete="SET NULL")
     *
     * @var City
     */
    protected $capital;

    /** @ORM\Column(type="boolean", name="Checked") */
    protected $isEnabled;

    /** @ORM\Column(type="boolean", name="secure", nullable=true) */
    protected $secure;

    /** @ORM\Column(length=50,name="base_host") */
    protected $baseHost;

    /** @ORM\Column(length=32, name="title_locative", nullable=true) */
    protected $titleLocative;

    /** @ORM\Column(length=32, name="title_genitive", nullable=true) */
    protected $titleGenitive;

    /** @ORM\Column(length=32, name="title_accusative", nullable=true) */
    protected $titleAccusative;

    /**
     * @var Currency
     */
    protected $currency;

    /** @ORM\Column(type="smallint", name="currency_id", nullable=true) */
    protected $currencyId;

    /** @ORM\Column(length=50, name="domain_title", nullable=true) */
    protected $domainTitle;

    /** @ORM\Column(length=30, name="support_phone", nullable=true) */
    protected $supportPhone;

    /** @ORM\Column(length=30, name="callback_phone", nullable=true) */
    protected $callbackPhone;

    /**
     * @return array
     */
    public static function getEnabledCountriesIds()
    {
        return self::$enabledCountriesIds;
    }

    /**
     * @param array $enabledCountriesIds
     */
    public static function initializeEnabledCountriesIds(array $enabledCountriesIds)
    {
        self::$enabledCountriesIds = $enabledCountriesIds;
    }

    /**
     * @ORM\PostLoad
     */
    public function postLoad()
    {
        $this->currency = null;
        if ($this->currencyId) {
            $this->currency = CurrencyProvider::create($this->currencyId);
        }
    }

    public function getId()
    {
        return $this->id;
    }

    public function getKind()
    {
        return 'country';
    }

    public function getSlug()
    {
        return 'www';
    }

    public function getSecure()
    {
        return $this->secure;
    }

    public function setSecure($secure)
    {
        $this->secure = $secure;
    }

    /**
     * @return City
     */
    public function getCapital()
    {
        return $this->capital;
    }

    public function getCapitalTitle()
    {
        if ($this->capital) {
            return $this->capital->getTitle();
        }

        return '';
    }

    public function setCapitalTitle($title)
    {
        // do nothing
    }

    /**
     * @param City $capital
     */
    public function setCapital(City $capital)
    {
        $this->capital = $capital;
    }

    public function setCallbackPhone($callbackPhone)
    {
        $this->callbackPhone = $callbackPhone;
    }

    public function getCallbackPhone()
    {
        return $this->callbackPhone;
    }

    public function setIsEnabled($isEnabled)
    {
        $this->isEnabled = $isEnabled;
    }

    public function getIsEnabled()
    {
        return $this->isEnabled;
    }

    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setBaseHost($baseHost)
    {
        $this->baseHost = $baseHost;
    }

    public function getBaseHost()
    {
        return $this->baseHost;
    }

    public function setTitleAccusative($titleAccusative)
    {
        $this->titleAccusative = $titleAccusative;
    }

    public function getTitleAccusative()
    {
        return $this->titleAccusative;
    }

    public function setTitleGenitive($titleGenitive)
    {
        $this->titleGenitive = $titleGenitive;
    }

    public function getTitleGenitive()
    {
        return $this->titleGenitive;
    }

    public function setTitleLocative($titleLocative)
    {
        $this->titleLocative = $titleLocative;
    }

    public function getTitleLocative()
    {
        return $this->titleLocative ?: $this->title;
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

    public function setCurrencyId($currencyId)
    {
        $this->currencyId = $currencyId;
        $this->postLoad();
    }

    public function getCurrencyId()
    {
        return $this->currencyId;
    }

    public function setDomainTitle($domainTitle)
    {
        $this->domainTitle = $domainTitle;
    }

    public function getDomainTitle()
    {
        return $this->domainTitle;
    }

    public function setSupportPhone($supportPhone)
    {
        $this->supportPhone = $supportPhone;
    }

    public function getSupportPhone()
    {
        return $this->supportPhone;
    }

    public function getTextForCountry()
    {
        return self::COUNTRY_ID_KAZAKHSTAN == $this->id ? 'Весь' : 'Вся';
    }

    public function getCountryCode()
    {
        $codes = self::countriesCodes();

        return isset($codes[$this->id]) ? $codes[$this->id] : 'ru';
    }

    public static function countriesCodes()
    {
        return array(
            self::COUNTRY_ID_RUSSIA => 'ru',
            self::COUNTRY_ID_BELORUSSIA => 'by',
            self::COUNTRY_ID_KAZAKHSTAN => 'kz',
            self::COUNTRY_ID_UKRAINE => 'ua'
        );
    }
}
