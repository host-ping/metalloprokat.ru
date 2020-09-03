<?php

namespace Metal\CompaniesBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Metal\ProjectBundle\Entity\Behavior\Attributable;
use Metal\ProjectBundle\Entity\Behavior\Updateable;
use Metal\TerritorialBundle\Entity\City;

use Metal\TerritorialBundle\Entity\Coordinate;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Doctrine\ORM\Mapping\Index;

/**
 * @ORM\Entity(repositoryClass="Metal\CompaniesBundle\Repository\CompanyCityRepository")
 * @ORM\Table(
 *     name="company_delivery_city",
 *     uniqueConstraints={@ORM\UniqueConstraint(name="UNIQ_company_city", columns={"company_id", "city_id"} )},
 *     indexes={@Index(name="IDX_company_is_main_office", columns={"company_id", "is_main_office"} )}
 * )
 *
 * @UniqueEntity(
 *     fields={"city", "company"},
 *     groups={"company_edit", "branch_office", "batch_branch_office"},
 *     errorPath="cityTitle",
 *     message="Филиал или регион доставки в этом городе уже добавлен"
 * )
 */
class CompanyCity implements ContactInfoInterface
{
    const KIND_BRANCH_OFFICE = 0;
    const KIND_DELIVERY = 1;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer", name="id")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Metal\CompaniesBundle\Entity\Company", inversedBy="companyCities")
     * @ORM\JoinColumn(name="company_id", referencedColumnName="Message_ID", onDelete="CASCADE")
     *
     * @var Company
     */
    protected $company;

    /**
     * @ORM\ManyToOne(targetEntity="Metal\TerritorialBundle\Entity\City")
     * @ORM\JoinColumn(name="city_id", referencedColumnName="Region_ID", onDelete="SET NULL")
     *
     * @var City
     */
    protected $city;

    /**
     * @ORM\Column(type="smallint", name="kind", nullable=true)
     */
    protected $kind;

    /**
     * @ORM\Column(length=255, name="phone")
     *
     * @deprecated use CompanyCity::$phonesAsString or CompanyCity::$phones
     */
    protected $phone;

    /**
     * @ORM\OneToMany(targetEntity="CompanyPhone", mappedBy="branchOffice", cascade={"persist"}, orphanRemoval=true)
     *
     * @var ArrayCollection|CompanyPhone[]
     */
    protected $phones;

    /**
     * @ORM\Column(length=50, name="mail", nullable=false, options={"default":""})
     * @Assert\Email(groups={"branch_office"}, strict=true)
     */
    protected $email;

    /** @ORM\Column(length=255, name="site", nullable=false, options={"default":""}) */
    protected $site;

    /**
     * @ORM\Column(length=255, name="adress", options={"default": ""})
     * @deprecated
     */
    protected $addressOld;

    /** @ORM\Column(length=100, name="address_new", options={"default": ""})) */
    protected $address;

    /** @ORM\Column(type="text", name="branch_about", nullable=true) */
    protected $description;

    /**
     * @ORM\Column(type="smallint", name="display_position", nullable=false, options={"default":1})
     * @Assert\GreaterThanOrEqual(1)
     * @Assert\Type(type="integer")
     */
    protected $displayPosition;

    use Coordinate;

    /** @ORM\Column(type="datetime", name="coordinates_updated_at", nullable=true) */
    protected $coordinatesUpdatedAt;

    /** @ORM\Column(name="phones_as_string", nullable=true) */
    protected $phonesAsString;

    /** @ORM\Column(type="boolean", name="is_main_office", nullable=false, options={"default":0}) */
    protected $isMainOffice;

    /** @ORM\Column(type="boolean", name="has_products", nullable=false, options={"default":0}) */
    protected $hasProducts;

    /** @ORM\Column(type="boolean", name="is_associated_with_city_code", nullable=false, options={"default":0}) */
    protected $isAssociatedWithCityCode;

    /** @ORM\Column(type="datetime", name="created_at") */
    protected $createdAt;

    /** @ORM\Column(type="boolean", name="enabled", options={"default":1}) */
    protected $enabled;

    use Updateable;

    use Attributable;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
        $this->phones = new ArrayCollection();
        $this->email = '';
        $this->site = '';
        $this->phone = '';
        $this->addressOld = '';
        $this->address = '';
        $this->isMainOffice = false;
        $this->hasProducts = false;
        $this->isAssociatedWithCityCode = false;
        $this->enabled = true;
        $this->kind = self::KIND_DELIVERY;
        $this->displayPosition = 1;
    }

    public function getId()
    {
        return $this->id;
    }

    /**
     * @param City $city
     */
    public function setCity(City $city)
    {
        $this->city = $city;
    }

    /**
     * @return City
     */
    public function getCity()
    {
        return $this->city;
    }

    public function getKind()
    {
        return $this->kind;
    }

    public function setKind($kind)
    {
        $this->kind = $kind;
    }

    public function getDisplayPosition()
    {
        return $this->displayPosition;
    }

    public function setDisplayPosition($displayPosition)
    {
        $this->displayPosition = $displayPosition;
    }

    /**
     * @param Company $company
     */
    public function setCompany(Company $company)
    {
        $this->company = $company;
        foreach ($this->phones as $phone) {
            $phone->setCompany($company);
        }
    }

    /**
     * @return Company
     */
    public function getCompany()
    {
        return $this->company;
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

    public function getEnabled()
    {
        return $this->enabled;
    }

    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;
    }

    public function setEmail($email)
    {
        $this->email = (string)$email;
    }

    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @deprecated
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;
    }

    /**
     * @deprecated
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @return ArrayCollection|CompanyPhone[]
     */
    public function getPhones()
    {
        return $this->phones;
    }

    public function addPhone(CompanyPhone $phone)
    {
        $phone->setBranchOffice($this);
        $phone->setCompany($this->company);
        $this->phones->add($phone);
    }

    public function removePhone(CompanyPhone $phone)
    {
        $this->phones->removeElement($phone);
    }


    public function setSite($site)
    {
        $this->site = (string)$site;
    }

    public function getSite()
    {
        return $this->site;
    }

    public function setDescription($description)
    {
        $this->description = $description;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setAddress($address)
    {
        $this->address = (string)$address;

        if (!$this->addressOld) {
            $this->addressOld = $this->address;
        }

        if (!$this->isMainOffice) {
            $this->kind = $this->address ? self::KIND_BRANCH_OFFICE : self::KIND_DELIVERY;
        }
    }

    public function getAddress()
    {
        return $this->address;
    }

    public function setAddressOld($addressOld)
    {
        $this->addressOld = (string)$addressOld;
    }

    public function getAddressOld()
    {
        return $this->addressOld;
    }

    public function isBranchOffice()
    {
        return $this->kind == self::KIND_BRANCH_OFFICE;
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
        // do nothing. Readonly
    }

    public function setPhonesAsString($phonesAsString)
    {
        $this->phonesAsString = $phonesAsString;
    }

    public function getPhonesAsString()
    {
        return $this->phonesAsString;
    }

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
     * @Assert\Callback(groups={"branch_office", "batch_branch_office"})
     */
    public function isCityValid(ExecutionContextInterface $context)
    {
        if (null === $this->city) {
            $context
                ->buildViolation('Нужно выбрать город из списка')
                ->atPath('cityTitle')
                ->addViolation();
        }
    }

    /**
     * @Assert\Callback(groups={"branch_office", "batch_branch_office"})
     */
    public function canCreateCompanyCity(ExecutionContextInterface $context)
    {
        $this->company->isAllowedAddCities($context);
    }

    public function isContactsShouldBeVisible()
    {
        return $this->company->getPackageChecker()->isContactsShouldBeVisible();
    }

    public function setIsMainOffice($isMainOffice)
    {
        $this->isMainOffice = $isMainOffice;
        $this->kind = self::KIND_BRANCH_OFFICE;
    }

    public function getIsMainOffice()
    {
        return $this->isMainOffice;
    }

    public function setCoordinatesUpdatedAt($coordinatesUpdatedAt)
    {
        $this->coordinatesUpdatedAt = $coordinatesUpdatedAt;
    }

    public function getCoordinatesUpdatedAt()
    {
        return $this->coordinatesUpdatedAt;
    }

    public function getHasProducts()
    {
        return $this->hasProducts;
    }

    public function setHasProducts($hasProducts)
    {
        $this->hasProducts = $hasProducts;
    }

    public function getIsAssociatedWithCityCode()
    {
        return $this->isAssociatedWithCityCode;
    }

    public function setIsAssociatedWithCityCode($isAssociatedWithCityCode)
    {
        $this->isAssociatedWithCityCode = $isAssociatedWithCityCode;
    }
}
