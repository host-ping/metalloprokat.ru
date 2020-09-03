<?php

namespace Metal\ProjectBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use Doctrine\ORM\Mapping\Index;
use Metal\UsersBundle\Entity\User;

/**
 * @ORM\Entity(readOnly=true)
 * @ORM\Table(name="ban_request", indexes={@Index(name="IDX_created_ip", columns={"int_ip", "int_created_at"})})
 */
class BanRequest
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer", name="id")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Metal\UsersBundle\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="User_ID", nullable=true)
     */
    protected $user;

    /**
     * @ORM\Column(name="int_ip", type="integer", options={"unsigned"=true})
     */
    protected $intIp;

    /**
     * @ORM\Column(name="int_created_at", type="integer", options={"unsigned"=true})
     */
    protected $intCreatedAt;

    /**
     * @ORM\Column(name="ip", length=15)
     */
    protected $ip;

    /**
     * @ORM\Column(name="created_at", type="datetime")
     */
    protected $createdAt;

    /**
     * @ORM\Column(name="uri", length=255)
     */
    protected $uri;

    /**
     * @ORM\Column(name="referer", length=255, nullable=true)
     */
    protected $referer;

    /**
     * @ORM\Column(name="method", length=5)
     */
    protected $method;

    /**
     * @ORM\Column(name="code", type="smallint", nullable=true)
     */
    protected $code;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->method = '';
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

    public function getIntCreatedAt()
    {
        return $this->intCreatedAt;
    }

    public function setIntCreatedAt($intCreatedAt)
    {
        $this->intCreatedAt = $intCreatedAt;
    }

    public function getIntIp()
    {
        return $this->intIp;
    }

    public function setIntIp($intIp)
    {
        $this->intIp = $intIp;
    }

    public function getIp()
    {
        return $this->ip;
    }

    public function setIp($ip)
    {
        $this->ip = $ip;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTime $createdAt
     */
    public function setCreatedAt(\DateTime $createdAt)
    {
        $this->createdAt = $createdAt;
    }

    public function getUri()
    {
        return $this->uri;
    }

    public function setUri($uri)
    {
        $this->uri = $uri;
    }

    public function getReferer()
    {
        return $this->referer;
    }

    public function setReferer($referer)
    {
        $this->referer = $referer;
    }

    public function getMethod()
    {
        return $this->method;
    }

    public function setMethod($method)
    {
        $this->method = (string)$method;
    }

    public function getCode()
    {
        return $this->code;
    }

    public function setCode($code)
    {
        $this->code = $code;
    }
}
