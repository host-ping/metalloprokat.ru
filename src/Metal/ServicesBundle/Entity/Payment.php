<?php

namespace Metal\ServicesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Metal\CompaniesBundle\Entity\Company;
use Metal\ProjectBundle\Entity\Behavior\SoftDeleteable;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="Metal\ServicesBundle\Repository\PaymentRepository")
 * @ORM\Table(name="Payments")
 */
class Payment
{
    const STATUS_PENDING = 0;
    const STATUS_PAYED = 1;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer", name="ID")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=50, name="Order_NO", nullable=false)
     *
     * @Assert\NotBlank()
     */
    protected $orderNO;

    /**
     * @ORM\ManyToOne(targetEntity="Metal\CompaniesBundle\Entity\Company")
     * @ORM\JoinColumn(name="Company_ID", referencedColumnName="Message_ID", nullable=true)
     *
     * @var Company
     */
    protected $company;

    /**
     * @ORM\Column(type="string", length=150, name="ServName", nullable=false)
     *
     * @Assert\NotBlank()
     */
    protected $servName;

    /**
     * @ORM\Column(type="date", name="CreatedInCrm", nullable=false)
     */
    protected $createdInCrm;

    /**
     * @ORM\Column(type="integer", length=11, name="PSum", nullable=false)
     *
     * @Assert\NotBlank()
     */
    protected $pSum;

    /**
     * @ORM\Column(type="boolean", name="is_automatically_added", nullable=false, options={"default":0})
     */
    protected $isAutomaticallyAdded;

    /**
     * @ORM\Column(type="date", name="Created", nullable=false)
     */
    protected $created;

    /**
     * @ORM\Column(type="datetime", name="LastUpdated", nullable=false)
     *
     * @var \DateTime
     */
    protected $lastUpdated;

    use SoftDeleteable;

    /**
     * @ORM\Column(type="boolean", length=11, name="Status", nullable=false)
     */
    protected $status;

    /**
     * @ORM\ManyToOne(targetEntity="Metal\ServicesBundle\Entity\PackageOrder")
     * @ORM\JoinColumn(name="package_order_id", referencedColumnName="id", nullable=true)
     *
     * @var PackageOrder
     */
    protected $packageOrder;

    public static function createFromPackageOrder(PackageOrder $packageOrder)
    {
        $payment = new self();

        $payment->setPackageOrder($packageOrder);
        $payment->setCompany($packageOrder->getCompany());
        $payment->setPSum($packageOrder->getPackage()->getPriceByPeriod($packageOrder->getPackagePeriod()));
        $payment->setIsAutomaticallyAdded(true);
        $payment->setCreatedInCrm(new \DateTime());

        return $payment;
    }

    public function __construct()
    {
        $this->lastUpdated = new \DateTime();
        $this->created = new \DateTime();
        $this->status = 0;
        $this->isAutomaticallyAdded = false;
        $this->orderNO = '';
    }

    public function getId()
    {
        return $this->id;
    }

    public function getOrderNO()
    {
        return $this->orderNO;
    }

    public function setOrderNO($orderNO)
    {
        $this->orderNO = (string)$orderNO;
    }

    /**
     * @return Company
     */
    public function getCompany()
    {
        return $this->company;
    }

    /**
     * @param Company $company
     */
    public function setCompany(Company $company = null)
    {
        $this->company = $company;
    }

    public function setServName($servName)
    {
        $this->servName = trim(preg_replace('/\s+/u', ' ', $servName));
    }

    public function getServName()
    {
        return $this->servName;
    }

    /**
     * @param \DateTime $createdInCrm
     */
    public function setCreatedInCrm(\DateTime $createdInCrm)
    {
        $this->createdInCrm = $createdInCrm;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedInCrm()
    {
        return $this->createdInCrm;
    }

    public function setPSum($pSum)
    {
        $this->pSum = $pSum;
    }

    public function getPSum()
    {
        return $this->pSum;
    }

    /**
     * @return PackageOrder|null
     */
    public function getPackageOrder()
    {
        return $this->packageOrder;
    }

    /**
     * @param PackageOrder|null $packageOrder
     */
    public function setPackageOrder(PackageOrder $packageOrder = null)
    {
        $this->packageOrder = $packageOrder;
    }

    /**
     * @return bool
     */
    public function getIsAutomaticallyAdded()
    {
        return $this->isAutomaticallyAdded;
    }

    /**
     * @param bool $isAutomaticallyAdded
     */
    public function setIsAutomaticallyAdded($isAutomaticallyAdded)
    {
        $this->isAutomaticallyAdded = (bool)$isAutomaticallyAdded;
    }

    /**
     * @param \DateTime $created
     */
    public function setCreated(\DateTime $created)
    {
        $this->createdInCrm = $created;
    }

    /**
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * @param \DateTime $lastUpdated
     */
    public function setLastUpdated(\DateTime $lastUpdated)
    {
        $this->lastUpdated = $lastUpdated;
    }

    /**
     * @return \DateTime
     */
    public function getLastUpdated()
    {
        return $this->lastUpdated;
    }

    public function setStatus($status)
    {
        $this->status = $status;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function getPayedAt()
    {
        return $this->status == 0 ? null : $this->lastUpdated;
    }

    public function isPayed()
    {
        return $this->status == 1;
    }

    public function setPayed($status)
    {
        $this->status = $status;
    }
}
