<?php

namespace Metal\ServicesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Metal\ServicesBundle\Entity\ValueObject\ServicePeriodTypesProvider;
use Metal\UsersBundle\Entity\User;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="Metal\ServicesBundle\Repository\PackageRepository")
 * @ORM\Table(name="Message180")
 */
class Package
{
    const BASE_PACKAGE = 1;
    const ADVANCED_PACKAGE = 2;
    const FULL_PACKAGE = 3;
    const STANDARD_PACKAGE = 4;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer", name="Message_ID")
     */
    protected $id;

    /**
     * @ORM\Column(length=255, name="Name", nullable=true )
     * @Assert\NotBlank()
     */
    protected $title;

    /**
     * @ORM\ManyToOne(targetEntity="Metal\UsersBundle\Entity\User")
     * @ORM\JoinColumn(name="User_ID", referencedColumnName="User_ID", nullable=false)
     *
     * @var User
     */
    protected $user;

    /**
     * @ORM\Column(type="smallint", name="Checked", nullable=false, options={"default":1})
     */
    protected $checked;

    /** @ORM\Column(type="integer", name="Priority", nullable=false, options={"default":0}) */
    protected $priority;

    /** @ORM\Column(type="integer", name="Price1", nullable=false, options={"default":0}) */
    protected $priceForOneMonth;

    /** @ORM\Column(type="integer", name="Price3", nullable=false, options={"default":0}) */
    protected $priceForThreeMonth;

    /** @ORM\Column(type="integer", name="Price6", nullable=false, options={"default":0}) */
    protected $priceForSixMonth;

    /** @ORM\Column(type="integer", name="Price12", nullable=false, options={"default":0}) */
    protected $priceForTwelveMonth;

    /**
     * @ORM\Column(type="datetime", name="Created", nullable=false)
     *
     * @var \DateTime
     */
    protected $createdAt;

    // в базе тут установлено дефалтовое значение CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    /**
     * @ORM\Column(type="datetime", name="LastUpdated", nullable=false)
     *
     * @var \DateTime
     */
    protected $updatedAt;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
        $this->checked = true;
        $this->priority = 0;

        $this->priceForOneMonth = 0;
        $this->priceForThreeMonth = 0;
        $this->priceForSixMonth = 0;
        $this->priceForTwelveMonth = 0;
    }

    public function getId()
    {
        return $this->id;
    }

    /**
     * @param User $user
     */
    public function setUser(User $user)
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
     * @param \DateTime $updatedAt
     */
    public function setUpdatedAt(\DateTime $updatedAt)
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setPriority($priority)
    {
        $this->priority = $priority;
    }

    public function getPriority()
    {
        return $this->priority;
    }

    public function setPriceForTwelveMonth($priceForTwelveMonth)
    {
        $this->priceForTwelveMonth = $priceForTwelveMonth;
    }

    public function getPriceForTwelveMonth()
    {
        return $this->priceForTwelveMonth;
    }

    public function setPriceForThreeMonth($priceForThreeMonth)
    {
        $this->priceForThreeMonth = $priceForThreeMonth;
    }

    public function getPriceForThreeMonth()
    {
        return $this->priceForThreeMonth;
    }

    public function setPriceForSixMonth($priceForSixMonth)
    {
        $this->priceForSixMonth = $priceForSixMonth;
    }

    public function getPriceForSixMonth()
    {
        return $this->priceForSixMonth;
    }

    public function getPriceForOneMonth()
    {
        return $this->priceForOneMonth;
    }

    public function setPriceForOneMonth($priceForOneMonth)
    {
        $this->priceForOneMonth = $priceForOneMonth;
    }

    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }

    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    public function setChecked($checked)
    {
        $this->checked = $checked;
    }

    public function getChecked()
    {
        return $this->checked;
    }

    public function isBasePackage()
    {
        return self::BASE_PACKAGE == $this->id;
    }

    public function getPriceByPeriod($periodId)
    {
        switch ($periodId) {
            case ServicePeriodTypesProvider::MONTH:
                return $this->priceForOneMonth;

            case ServicePeriodTypesProvider::QUARTER:
                return $this->priceForThreeMonth;

            case ServicePeriodTypesProvider::HALF_YEAR:
                return $this->priceForSixMonth;

            case ServicePeriodTypesProvider::YEAR:
                return $this->priceForTwelveMonth;

        }
    }

    public function getAllPricesByPeriod()
    {
        return array(
            ServicePeriodTypesProvider::MONTH => $this->priceForOneMonth,
            ServicePeriodTypesProvider::QUARTER => $this->priceForThreeMonth,
            ServicePeriodTypesProvider::HALF_YEAR => $this->priceForSixMonth,
            ServicePeriodTypesProvider::YEAR => $this->priceForTwelveMonth
        );
    }

    public function getTitleForPromotions()
    {
        $packages =  array(
            self::BASE_PACKAGE => 'Не продвигать товары',
            self::ADVANCED_PACKAGE => 'Рекомендованное продвижение',
            self::FULL_PACKAGE => 'Максимальное продвижение'
        );

        return isset($packages[$this->getId()]) ? $packages[$this->getId()] : null;
    }
}
