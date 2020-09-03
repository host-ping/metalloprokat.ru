<?php

namespace Metal\NewsletterBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use Metal\ProjectBundle\Entity\Behavior\Attributable;
use Metal\ProjectBundle\Entity\Behavior\Updateable;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="Metal\NewsletterBundle\Repository\NewsletterRepository")
 * @ORM\Table(name="newsletter")
 */
class Newsletter
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer", name="id")
     */
    protected $id;

    /** @ORM\Column(length=255, name="title") */
    protected $title;

    /** @ORM\Column(type="datetime", name="created_at")
     *
     * @var \DateTime
     */
    protected $createdAt;

    /** @ORM\Column(type="datetime", name="start_at", nullable=true)
     *
     * @var \DateTime
     */
    protected $startAt;

    /** @ORM\Column(type="datetime", name="processed_at", nullable=true)
     *
     * @var \DateTime
     */
    protected $processedAt;

    /**
     * @ORM\Column(length=255, name="template", nullable=false)
     * @Assert\NotBlank()
     * @Assert\Length(
     *   min=6,
     *   max=255,
     *   maxMessage="Слишком длинный путь"
     * )
     */
    protected $template;

    use Attributable;

    use Updateable;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
        $this->startAt = new \DateTime('+2 hours');
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
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

    /**
     * @param mixed $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
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
     * @param \DateTime $startAt
     */
    public function setStartAt(\DateTime $startAt)
    {
        $this->startAt = $startAt;
    }

    /**
     * @return \DateTime
     */
    public function getStartAt()
    {
        return $this->startAt;
    }

    public function getTemplate()
    {
        return $this->template;
    }

    public function setTemplate($template)
    {
        $this->template = $template;
    }
}
