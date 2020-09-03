<?php

namespace Metal\ServicesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Metal\UsersBundle\Entity\User;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity()
 * @ORM\Table(name="Message179")
 */
class ServiceItem
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer", name="Message_ID")
     */
    protected $id;

    /**
     * @ORM\Column(length=255, name="Name", nullable=false)
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

    /** @ORM\Column(length=255, name="Keyword", nullable=true) */
    protected $keyword;

    /** @ORM\Column(length=255, name="link_pattern", nullable=true) */
    protected $linkPattern;

    /**
     * @ORM\Column(type="smallint", name="Checked", nullable=false, options={"default":1})
     */
    protected $checked;

    /** @ORM\Column(type="integer", name="Priority", nullable=false, options={"default":0}) */
    protected $priority;

    /**
     * @ORM\ManyToOne(targetEntity="ServiceItem")
     * @ORM\JoinColumn(name="parent", referencedColumnName="Message_ID", nullable=true)
     */
    protected $parent;

    /** @ORM\Column(type="integer", name="parent", nullable=true)) */
    protected $parentId;

    /** @ORM\Column(type="boolean", name="type1", nullable=false, options={"default":0}) */
    protected $type1;

    /** @ORM\Column(type="boolean", name="type2", nullable=false, options={"default":0}) */
    protected $type2;

    /** @ORM\Column(type="boolean", name="type3", nullable=false, options={"default":0}) */
    protected $type3;

    /** @ORM\Column(type="boolean", name="type4", nullable=false, options={"default":0}) */
    protected $type4;

    /** @ORM\Column(type="boolean", name="additional_payment", nullable=false, options={"default":0}) */
    protected $additionalPayment;

    /** @ORM\Column(type="text", name="Descr", nullable=true) */
    protected $description;

    /**
     * @ORM\Column(type="datetime", name="Created", nullable=false)
     *
     * @var \DateTime
     */
    protected $createdAt;

    /**
     * @ORM\Column(type="datetime", name="LastUpdated", nullable=false)
     *
     * @var \DateTime
     */
    protected $updatedAt;

    /** @ORM\Column(length=100, name="type1Val", nullable=true ) */
    protected $type1Val;

    /** @ORM\Column(length=100, name="type2Val", nullable=true ) */
    protected $type2Val;

    /** @ORM\Column(length=100, name="type3Val", nullable=true ) */
    protected $type3Val;

    /** @ORM\Column(length=100, name="type4Val", nullable=true ) */
    protected $type4Val;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
        $this->parentId = 0;
        $this->checked = true;
        $this->priority = 0;
        $this->additionalPayment = false;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setChecked($checked)
    {
        $this->checked = $checked;
    }

    public function getChecked()
    {
        return $this->checked;
    }

    public function getLinkPattern()
    {
        return $this->linkPattern;
    }

    public function setLinkPattern($linkPattern)
    {
        $this->linkPattern = $linkPattern;
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

    public function setDescription($description)
    {
        $this->description = $description;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setKeyword($keyword)
    {
        $this->keyword = $keyword;
    }

    public function getKeyword()
    {
        return $this->keyword;
    }

    public function setPriority($priority)
    {
        $this->priority = $priority;
    }

    public function getPriority()
    {
        return $this->priority;
    }


    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setType1($type1)
    {
        $this->type1 = $type1;
    }

    public function getType1()
    {
        return $this->type1;
    }

    public function setType2($type2)
    {
        $this->type2 = $type2;
    }

    public function getType2()
    {
        return $this->type2;
    }

    public function setType3($type3)
    {
        $this->type3 = $type3;
    }

    public function getType3()
    {
        return $this->type3;
    }

    public function getType4()
    {
        return $this->type4;
    }

    public function setType4($type4)
    {
        $this->type4 = $type4;
    }

    public function getAdditionalPayment()
    {
        return $this->additionalPayment;
    }

    public function setAdditionalPayment($additionalPayment)
    {
        $this->additionalPayment = $additionalPayment;
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

    public function setParent($parent)
    {
        $this->parent = $parent;
        $this->parentId = 0;
        if ($parent) {
            $this->parentId = $parent->getId();
        }
    }

    public function getParent()
    {
        if (!$this->parentId) {
            return null;
        }

        return $this->parent;
    }

    /**
     * @return integer|null
     */
    public function getParentId()
    {
        return $this->parentId ? $this->parentId : null;
    }

    public function __toString()
    {
        $level = 0;
        $prefix = "";
        $parent = $this->getParent();
        while ($parent && $parent = $parent->getParent()) {
            $level++;
        }

        for ($i = 1; $i <= $level; $i++) {
            $prefix .= "---";
        }

        return $prefix.$this->title;
    }

    public function getType1Val()
    {
        return $this->type1Val;
    }

    public function setType1Val($type1Val)
    {
        $this->type1Val = $type1Val;
    }

    public function getType3Val()
    {
        return $this->type3Val;
    }

    public function setType3Val($type3Val)
    {
        $this->type3Val = $type3Val;
    }

    public function getType2Val()
    {
        return $this->type2Val;
    }

    public function setType2Val($type2Val)
    {
        $this->type2Val = $type2Val;
    }

    public function getType4Val()
    {
        return $this->type4Val;
    }

    public function setType4Val($type4Val)
    {
        $this->type4Val = $type4Val;
    }

    public function isAvailableForPackage(Package $package)
    {
        $property = 'type'.$package->getId();

        return $this->$property;
    }

    public function getValueForPackage(Package $package)
    {
        $property = 'type'.$package->getId().'Val';

        return $this->$property;
    }
}
