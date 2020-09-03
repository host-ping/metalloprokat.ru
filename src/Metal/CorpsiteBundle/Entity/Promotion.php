<?php

namespace Metal\CorpsiteBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="Metal\CorpsiteBundle\Repository\PromotionRepository")
 * @ORM\Table(name="promotion")
 * @Assert\Expression(
 *   "this.getStartsAt() <= this.getEndsAt()",
 *   message="Дата старта не может быть больше даты окончания"
 * )
 */
class Promotion
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer", name="id")
     */
    protected $id;

    /**
     * @ORM\Column(length=255, name="title")
     * @Assert\NotBlank()
     */
    protected $title;

    /**
     * @ORM\Column(type="text", name="description")
     * @Assert\NotBlank()
     */
    protected $description;

    /**
     * @ORM\Column(type="date", name="starts_at")
     * @Assert\NotBlank()
     *
     * @var \DateTime
     */
    protected $startsAt;

    /**
     * @ORM\Column(type="date", name="ends_at")
     * @Assert\NotBlank()
     *
     * @var \DateTime
     */
    protected $endsAt;

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
        $this->title = $title;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setDescription($description)
    {
        $this->description = $description;
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
        $this->startsAt = $startsAt;
    }

    /**
     * @return \DateTime
     */
    public function getEndsAt()
    {
        return $this->endsAt;
    }

    /**
     * @param \DateTime $endsAt
     */
    public function setEndsAt(\DateTime $endsAt = null)
    {
        $this->endsAt = $endsAt;
    }
}
