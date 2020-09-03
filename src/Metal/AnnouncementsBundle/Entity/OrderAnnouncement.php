<?php

namespace Metal\AnnouncementsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Metal\UsersBundle\Entity\User;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="announcement_order")
 */
class OrderAnnouncement
{
    /**
     * @ORM\Id
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue
     */
    protected $id;

    /**
     * @ORM\Column(name="starts_at", type="date", nullable=false)
     * @Assert\NotBlank()
     */
    protected $startsAt;

    /**
     * @ORM\Column(length=255, name="name", nullable=false)
     * @Assert\NotBlank()
     */
    protected $name;

    /**
     * @ORM\Column(length=120, name="phone", nullable=true)
     * @Assert\NotBlank()
     * @Assert\Regex(
     *   pattern="/\d+/",
     *   message="Неправильный номер телефона"
     * )
     * @Assert\Length(
     *   min=6,
     *   max=120
     * )
     */
    protected $phone;

    /**
     * @ORM\Column(length=255, name="email", nullable=false)
     * @Assert\NotBlank()
     * @Assert\Email(strict=true)
     */
    protected $email;

    /**
     * @ORM\Column(type="boolean", name="create_announcement", nullable=false, options={"default":0})
     */
    protected $createAnnouncement;

    /**
     * @ORM\ManyToOne(targetEntity="Metal\AnnouncementsBundle\Entity\Zone")
     * @ORM\JoinColumn(name="zone_id", referencedColumnName="id", nullable=false)
     *
     * @var Zone
     */
    protected $zone;

    /**
     * @ORM\ManyToOne(targetEntity="Metal\UsersBundle\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="User_ID", nullable=true)
     *
     * @var User
     */
    protected $user;

    /**
     * @ORM\Column(length=255, name="comment", nullable=true)
     * @Assert\Length(
     *    max=255
     * )
     */
    protected $comment;

    /** @ORM\Column(type="datetime", name="created_at") */
    protected $createdAt;

    /**
     * @ORM\Column(type="datetime", name="processed_at", nullable=true)
     *
     * @var \DateTime
     */
    protected $processedAt;

    /**
     * @ORM\ManyToOne(targetEntity="Metal\UsersBundle\Entity\User")
     * @ORM\JoinColumn(name="processed_by", referencedColumnName="User_ID", nullable=true)
     *
     * @var User
     */
    protected $processedBy;

    public function __construct()
    {
        $this->createAnnouncement = true;
        $this->createdAt = new \DateTime();
        $this->startsAt = new \DateTime('tomorrow');
    }

    public function getId()
    {
        return $this->id;
    }

    /**
     * @param \DateTime $processedAt
     */
    public function setProcessedAt(\DateTime $processedAt)
    {
        $this->processedAt = $processedAt;
    }

    /**
     * @return \DateTime
     */
    public function getProcessedAt()
    {
        return $this->processedAt;
    }

    /**
     * @param User $processedBy
     */
    public function setProcessedBy(User $processedBy)
    {
        $this->processedBy = $processedBy;
    }

    /**
     * @return User
     */
    public function getProcessedBy()
    {
        return $this->processedBy;
    }

    public function setProcessed($processedAt = true)
    {
        $this->processedAt = $processedAt ? new \DateTime() : null;
        if (!$this->processedAt) {
            $this->processedBy = null;
        }
    }

    public function isProcessed()
    {
        return $this->processedAt !== null;
    }

    public function getProcessedAtTimestamp()
    {
        return $this->processedAt ? $this->processedAt->getTimestamp() : null;
    }

    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }


    public function getCreateAnnouncement()
    {
        return $this->createAnnouncement;
    }

    public function setCreateAnnouncement($createAnnouncement)
    {
        $this->createAnnouncement = $createAnnouncement;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getPhone()
    {
        return $this->phone;
    }

    public function setPhone($phone)
    {
        $this->phone = $phone;
    }

    /**
     * @return \DateTime
     */
    public function getStartsAt()
    {
        return $this->startsAt;
    }

    /**
     * @param \DateTime $startsAt
     */
    public function setStartsAt(\DateTime $startsAt = null)
    {
        // null разрешен для того, что б срабатывала валидация, когда отправляется пустая форма
        $this->startsAt = $startsAt;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param User $user
     */
    public function setUser(User $user)
    {
        $this->user = $user;
    }

    /**
     * @return Zone
     */
    public function getZone()
    {
        return $this->zone;
    }

    /**
     * @param Zone $zone
     */
    public function setZone(Zone $zone)
    {
        $this->zone = $zone;
    }

    public function getComment()
    {
        return $this->comment;
    }

    public function setComment($comment)
    {
        $this->comment = $comment;
    }
}
