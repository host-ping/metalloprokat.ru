<?php

namespace Metal\ProjectBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use Metal\ProductsBundle\Entity\ValueObject\Currency;
use Metal\ProductsBundle\Entity\ValueObject\CurrencyProvider;
use Metal\ProjectBundle\Entity\Behavior\Updateable;
use Metal\TerritorialBundle\Entity\Country;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="exchange_rate", uniqueConstraints={
 *     @ORM\UniqueConstraint(name="UNIQ_country_currency_date", columns={"country_id", "currency_id", "updated_at"})
 * })
 */
class ExchangeRate
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer", name="id")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Metal\TerritorialBundle\Entity\Country")
     * @ORM\JoinColumn(name="country_id", referencedColumnName="Country_ID")
     *
     * @var Country
     */
    protected $country;

    /**
     * @var Currency
     */
    protected $currency;

    /** @ORM\Column(type="smallint", name="currency_id", nullable=true) */
    protected $currencyId;

    /** @ORM\Column(type="decimal", scale=4, name="course") */
    protected $course;

    /** @ORM\Column(type="boolean", name="is_last", options={"default":0}) */
    protected $isLast;

    use Updateable;

    public function __construct()
    {
        $this->isLast = false;
        $this->updatedAt = new \DateTime();
    }

    /**
     * @ORM\PostLoad
     */
    public function postLoad()
    {
        $this->initializeCurrency();
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
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

    public function getCourse()
    {
        return $this->course;
    }

    public function setCourse($course)
    {
        $this->course = $course;
    }

    /**
     * @return Currency
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * @param Currency $currency
     */
    public function setCurrency(Currency $currency)
    {
        $this->currency = $currency;
        $this->currencyId = $currency->getId();
    }

    public function getCurrencyId()
    {
        return $this->currencyId;
    }

    public function setCurrencyId($currencyId)
    {
        $this->currencyId = $currencyId;
        $this->initializeCurrency();
    }

    public function getIsLast()
    {
        return $this->isLast;
    }

    public function setIsLast($isLast)
    {
        $this->isLast = $isLast;
    }

    private function initializeCurrency()
    {
        $this->currency = CurrencyProvider::create($this->currencyId);
    }
}
