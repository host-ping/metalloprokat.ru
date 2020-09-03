<?php

namespace Metal\GrabbersBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Metal\ProjectBundle\Entity\Behavior\Updateable;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity()
 * @ORM\Table(name="grabber_proxy",uniqueConstraints={
 *      @ORM\UniqueConstraint(name="UNIQ_proxy", columns={"proxy"})
 * })
 * @UniqueEntity("proxy")
 */
class Proxy
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer", name="id")
     */
    protected $id;

    /**
     * @ORM\Column(length=255, name="proxy")
     */
    protected $proxy;

    /**
     * @ORM\Column(name="created_at", type="datetime")
     */
    protected $createdAt;

    /** @ORM\Column(name="disabled_at", type="datetime", nullable=true) */
    protected $disabledAt;

    use Updateable;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getProxy()
    {
        return $this->proxy;
    }

    /**
     * @param mixed $proxy
     */
    public function setProxy($proxy)
    {
        $this->proxy = $proxy;
    }

    /**
     * @return mixed
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param mixed $createdAt
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return mixed
     */
    public function getDisabledAt()
    {
        return $this->disabledAt;
    }

    /**
     * @param mixed $disabledAt
     */
    public function setDisabledAt($disabledAt)
    {
        $this->disabledAt = $disabledAt;
    }
}
