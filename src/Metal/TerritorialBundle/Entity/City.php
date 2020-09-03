<?php

namespace Metal\TerritorialBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Metal\ProjectBundle\Entity\Behavior\Attributable;
use Metal\ProjectBundle\Helper\TextHelperStatic;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * @ORM\Entity(repositoryClass="Metal\TerritorialBundle\Repository\CityRepository")
 * @ORM\Table(name="Classificator_Region")
 * @UniqueEntity("slug")
 */
class City implements TerritoryInterface
{
    const VIRTUAL_CITY_ID_ALL_RUSSIA = 100000165;
    const VIRTUAL_CITY_ID_ALL_UKRAINE = 100000209;
    const VIRTUAL_CITY_ID_ALL_BELORUSSIA = 100000019;
    const VIRTUAL_CITY_ID_ALL_KAZAKHSTAN = 100000083;

    //Москва
    const SEO_TOP_CITY_ID = 1123;

    protected static $countriesToVirtualCities = array(
        Country::COUNTRY_ID_RUSSIA => self::VIRTUAL_CITY_ID_ALL_RUSSIA,
        Country::COUNTRY_ID_UKRAINE => self::VIRTUAL_CITY_ID_ALL_UKRAINE,
        Country::COUNTRY_ID_BELORUSSIA => self::VIRTUAL_CITY_ID_ALL_BELORUSSIA,
        Country::COUNTRY_ID_KAZAKHSTAN => self::VIRTUAL_CITY_ID_ALL_KAZAKHSTAN,
    );

    protected static $virtualCityToESVirtualCityMapping = [
        self::VIRTUAL_CITY_ID_ALL_RUSSIA => self::INT_SHORT_MAX - Country::COUNTRY_ID_RUSSIA,
        self::VIRTUAL_CITY_ID_ALL_UKRAINE => self::INT_SHORT_MAX - Country::COUNTRY_ID_UKRAINE,
        self::VIRTUAL_CITY_ID_ALL_BELORUSSIA => self::INT_SHORT_MAX - Country::COUNTRY_ID_BELORUSSIA,
        self::VIRTUAL_CITY_ID_ALL_KAZAKHSTAN => self::INT_SHORT_MAX - Country::COUNTRY_ID_KAZAKHSTAN,
    ];

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer", name="Region_ID")
     */
    protected $id;

    /**
     * @ORM\Column(length=255, name="Region_Name")
     * @Assert\NotBlank()
     */
    protected $title;

    /**
     * @ORM\Column(length=255, name="Keyword", nullable=true, unique=true)
     * @Assert\Length(min="3")
     * @Assert\Regex(pattern="/[-0-9a-z\_]+?/u", message="Недопустимые символы, слаг должен соответствовать регулярному выражению: [-0-9a-z\_]+?")
     */
    protected $slug;

    /**
     * @ORM\ManyToOne(targetEntity="Metal\TerritorialBundle\Entity\Region")
     * @ORM\JoinColumn(name="parent", referencedColumnName="Regions_ID")
     * @Assert\NotBlank()
     *
     * @var Region
     */
    protected $region;

    /** @ORM\Column(type="integer", name="population", nullable=true) */
    protected $population;

    /** @ORM\Column(length=32, name="title_locative", nullable=true) */
    protected $titleLocative;

    /** @ORM\Column(length=32, name="title_genitive", nullable=true) */
    protected $titleGenitive;

    /** @ORM\Column(length=32, name="title_accusative", nullable=true) */
    protected $titleAccusative;

    /**
     * @ORM\ManyToOne(targetEntity="Metal\TerritorialBundle\Entity\Country")
     * @ORM\JoinColumn(name="country_id", referencedColumnName="Country_ID")
     * @Assert\NotBlank()
     *
     * @var Country
     */
    protected $country;

    /**
     * @ORM\ManyToOne(targetEntity="Metal\TerritorialBundle\Entity\Country")
     * @ORM\JoinColumn(name="display_in_country_id", referencedColumnName="Country_ID")
     *
     * @var Country
     */
    protected $displayInCountry;

    /**
     * @ORM\OneToOne(targetEntity="Metal\TerritorialBundle\Entity\City")
     * @ORM\JoinColumn(name="administrative_center", referencedColumnName="Region_ID")
     *
     * @var City
     */
    protected $administrativeCenter;

    /** @ORM\Column(type="boolean", name="is_capital", nullable=false, options={"default": 0}) */
    protected $isCapital;

    use Coordinate;

    /** @ORM\Column(type="datetime", name="coordinates_updated_at", nullable=true) */
    protected $coordinatesUpdatedAt;

    /**
     * @ORM\Column(type="text", name="robots_text", nullable=true)
     *
     * @Assert\Length(max="2000")
     */
    protected $robotsText;

    /** @ORM\Column(length=50, name="onesignal_code", nullable=true) */
    protected $onesignalCode;

    use Attributable;

    public static function checkIsPrimary($countryId, $population)
    {
        if ($countryId == Country::COUNTRY_ID_UKRAINE) {
            return $population > 1010000;
        }

        return $population > 1500000;
    }

    public static function getVirtualCitiesIds()
    {
        return array_values(self::$countriesToVirtualCities);
    }

    public static function getVirtualCityIdForCountry(int $countryId): ?int
    {
        return self::$countriesToVirtualCities[$countryId] ?? null;
    }

    public static function getESVirtualCityIdForVirtualCityId(int $virtualCityId): int
    {
        return self::$virtualCityToESVirtualCityMapping[$virtualCityId] ?? $virtualCityId;
    }

    public function __construct()
    {
        $this->isCapital = false;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getIsCapital()
    {
        return $this->isCapital;
    }

    public function setIsCapital($isCapital)
    {
        $this->isCapital = $isCapital;
    }

    public function getKind()
    {
        return 'city';
    }

    /**
     * @param City $administrativeCenter
     */
    public function setAdministrativeCenter(City $administrativeCenter)
    {
        $this->administrativeCenter = $administrativeCenter;
    }

    /**
     * @return City
     */
    public function getAdministrativeCenter()
    {
        return $this->administrativeCenter;
    }

    public function isAdministrativeCenter()
    {
        return $this == $this->getRegion()->getAdministrativeCenter();
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
        $this->slug = $slug ?: null;
    }

    public function getSlugWithFallback()
    {
        if ($this->slug) {
            return $this->slug;
        }

        return $this->administrativeCenter->getSlug();
    }

    /**
     * @return City
     */
    public function getCityWithFallback()
    {
        return $this->slug ? $this : $this->administrativeCenter;
    }

    /**
     * @return Region
     */
    public function getRegion()
    {
        return $this->region;
    }

    public function getRegionTitle()
    {
        return $this->region->getTitle();
    }

    public function setRegion($region)
    {
        $this->region = $region;
    }

    public function getTitleLocative()
    {
        if ($this->titleLocative) {
            return $this->titleLocative;
        }

        return $this->titleLocative = TextHelperStatic::declinePhraseLocative($this->getTitle());
    }

    public function setTitleLocative($titleLocative)
    {
        $this->titleLocative = $titleLocative;
    }

    public function getTitleGenitive()
    {
        if ($this->titleGenitive) {
            return $this->titleGenitive;
        }

        return $this->titleGenitive = TextHelperStatic::declinePhraseGenitive($this->getTitle());
    }

    public function setTitleGenitive($titleGenitive)
    {
        $this->titleGenitive = $titleGenitive;
    }

    public function getTitleAccusative()
    {
        if ($this->titleAccusative) {
            return $this->titleAccusative;
        }

        return $this->titleAccusative = TextHelperStatic::declinePhraseAccusative($this->getTitle());
    }

    public function setTitleAccusative($titleAccusative)
    {
        $this->titleAccusative = $titleAccusative;
    }

    public function setPopulation($population)
    {
        $this->population = $population;
    }

    public function getPopulation()
    {
        return $this->population;
    }

    public function isPrimary()
    {
        return self::checkIsPrimary($this->country->getId(), $this->population);
    }

    /**
     * @param Country $country
     */
    public function setCountry(Country $country)
    {
        $this->country = $country;
    }

    /**
     * @return Country
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * @return Country
     */
    public function getDisplayInCountry()
    {
        return $this->displayInCountry;
    }

    /**
     * @param Country $displayInCountry
     */
    public function setDisplayInCountry(Country $displayInCountry)
    {
        $this->displayInCountry = $displayInCountry;
    }

    /**
     * @Assert\Callback()
     */
    public function validateCity(ExecutionContextInterface $context)
    {
        if ($this->getCountry() && $this->getRegion()) {
            if ($this->getCountry()->getId() != $this->getRegion()->getCountry()->getId()) {
                $context
                    ->buildViolation('Выбранная область не соответствует стране')
                    ->atPath('region')
                    ->addViolation();
            }
        }
    }

    public function getRobotsText()
    {
        return $this->robotsText;
    }

    public function setRobotsText($robotsText)
    {
        $this->robotsText = $robotsText;
    }

    /**
     * @return mixed
     */
    public function getOnesignalCode()
    {
        return $this->onesignalCode;
    }

    /**
     * @param mixed $onesignalCode
     */
    public function setOnesignalCode($onesignalCode)
    {
        $this->onesignalCode = $onesignalCode;
    }

}
