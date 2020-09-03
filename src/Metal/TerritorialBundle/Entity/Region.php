<?php

namespace Metal\TerritorialBundle\Entity;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Mapping as ORM;
use Metal\ProjectBundle\Helper\TextHelperStatic;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * @ORM\Entity(repositoryClass="Metal\TerritorialBundle\Repository\RegionRepository")
 * @ORM\Table(name="Classificator_Regions")
 * @ORM\HasLifecycleCallbacks
 */
class Region implements TerritoryInterface
{
    const VIRTUAL_REGION_ID_ALL_RUSSIA = 100000208;
    const VIRTUAL_REGION_ID_ALL_UKRAINE = 100000215;
    const VIRTUAL_REGION_ID_ALL_BELORUSSIA = 100000280;
    const VIRTUAL_REGION_ID_ALL_KAZAKHSTAN = 100000213;

    protected static $countriesToVirtualRegions = array(
        Country::COUNTRY_ID_RUSSIA => self::VIRTUAL_REGION_ID_ALL_RUSSIA,
        Country::COUNTRY_ID_UKRAINE => self::VIRTUAL_REGION_ID_ALL_UKRAINE,
        Country::COUNTRY_ID_BELORUSSIA => self::VIRTUAL_REGION_ID_ALL_BELORUSSIA,
        Country::COUNTRY_ID_KAZAKHSTAN => self::VIRTUAL_REGION_ID_ALL_KAZAKHSTAN,
    );

    protected static $virtualRegionToESVirtualRegionMapping = [
        self::VIRTUAL_REGION_ID_ALL_RUSSIA => self::INT_SHORT_MAX - Country::COUNTRY_ID_RUSSIA,
        self::VIRTUAL_REGION_ID_ALL_UKRAINE => self::INT_SHORT_MAX - Country::COUNTRY_ID_UKRAINE,
        self::VIRTUAL_REGION_ID_ALL_BELORUSSIA => self::INT_SHORT_MAX - Country::COUNTRY_ID_BELORUSSIA,
        self::VIRTUAL_REGION_ID_ALL_KAZAKHSTAN => self::INT_SHORT_MAX - Country::COUNTRY_ID_KAZAKHSTAN,
    ];

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer", name="Regions_ID")
     */
    protected $id;

    /**
     * @ORM\Column(length=255, name="Regions_Name")
     * @Assert\NotBlank()
     */
    protected $title;

    /** @ORM\Column(length=50, name="title_locative", nullable=true) */
    protected $titleLocative;

    /** @ORM\Column(length=50, name="title_genitive", nullable=true) */
    protected $titleGenitive;

    /** @ORM\Column(length=50, name="title_accusative", nullable=true) */
    protected $titleAccusative;

    /** @ORM\Column(type="boolean", name="Checked") */
    protected $isEnabled;

    /**
     * @ORM\ManyToOne(targetEntity="Metal\TerritorialBundle\Entity\FederalDistrict")
     * @ORM\JoinColumn(name="parent", referencedColumnName="FO_ID")
     * @Assert\NotBlank()
     *
     * @var FederalDistrict
     */
    protected $federalDistrict;

    /**
     * @ORM\OneToOne(targetEntity="Metal\TerritorialBundle\Entity\City")
     * @ORM\JoinColumn(name="Capital", referencedColumnName="Region_ID")
     * @Assert\NotBlank()
     *
     * @var City
     */
    protected $administrativeCenter;

    /**
     * @ORM\ManyToOne(targetEntity="Metal\TerritorialBundle\Entity\Country")
     * @ORM\JoinColumn(name="country_id", referencedColumnName="Country_ID")
     * @Assert\NotBlank()
     *
     * @var Country
     */
    protected $country;

    /**
     * @return array
     */
    public static function getVirtualRegionsIds()
    {
        return array_values(self::$countriesToVirtualRegions);
    }

    public static function getVirtualRegionIdForCountry(int $countryId): ?int
    {
        return self::$countriesToVirtualRegions[$countryId] ?? null;
    }

    public static function getESVirtualRegionIdForVirtualRegionId(int $virtualRegionId): int
    {
        return self::$virtualRegionToESVirtualRegionMapping[$virtualRegionId] ?? $virtualRegionId;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getKind()
    {
        return 'region';
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function getSlug()
    {
        return (string) $this->getId();
    }

    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function getTitleLocative()
    {
        //TODO: нужно вручную проставить всем реионам title в этом падеже, авотматическое склонение плохо выглядит
        return $this->titleLocative ?: $this->title;
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

        return TextHelperStatic::declinePhraseGenitive($this->getTitle());
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

        return TextHelperStatic::declinePhraseAccusative($this->getTitle());
    }

    public function setTitleAccusative($titleAccusative)
    {
        $this->titleAccusative = $titleAccusative;
    }

    public function isEnabled()
    {
        return $this->isEnabled;
    }

    public function setIsEnabled($isEnabled)
    {
        $this->isEnabled = $isEnabled;
    }

    /**
     * @param FederalDistrict $federalDistrict
     */
    public function setFederalDistrict(FederalDistrict $federalDistrict)
    {
        $this->federalDistrict = $federalDistrict;
    }

    /**
     * @return FederalDistrict
     */
    public function getFederalDistrict()
    {
        return $this->federalDistrict;
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

    /**
     * @param Country $country
     */
    public function setCountry($country)
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
     * @ORM\PostUpdate()
     */
    public function normalizeCities(LifecycleEventArgs $lifecycleEventArgs)
    {
        $em = $lifecycleEventArgs->getEntityManager();

        $cities = $em->getRepository('MetalTerritorialBundle:City')->findBy(array('region' => $this->getId()));
        // обновляем дочерние города
        foreach ($cities as $city) {
            $city->setAdministrativeCenter($this->getAdministrativeCenter());
            $city->setCountry($this->getCountry());
        }

        $em->flush();
    }

    /**
     * @Assert\Callback()
     */
    public function validateFederalDistrict(ExecutionContextInterface $context)
    {
        if ($this->getCountry() && $this->getFederalDistrict()) {
            if ($this->getCountry()->getId() != $this->getFederalDistrict()->getCountry()->getId()) {
                $context
                    ->buildViolation('Выбранный федеральный округ не соответствует стране')
                    ->atPath('federalDistrict')
                    ->addViolation();
            }
        }
    }
}
