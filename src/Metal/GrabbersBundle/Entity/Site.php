<?php

namespace Metal\GrabbersBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="grabber_site")
 */
class Site
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer", name="id")
     */
    protected $id;

    /**
     * @ORM\Column(length=255, name="title")
     */
    protected $title;

    /**
     * @ORM\Column(length=255, name="code")
     */
    protected $code;

    /**
     * @ORM\Column(length=255, name="login")
     */
    protected $login;

    /**
     * @ORM\Column(length=255, name="password")
     */
    protected $password;

    /**
     * @ORM\Column(length=255, name="host")
     */
    protected $host;

    /** @ORM\Column(type="boolean", name="use_proxy", nullable=false, options={"default":0}) */
    protected $useProxy;

    /**
     * @ORM\Column(length=255, name="test_proxy_uri", nullable=false, options={"default":""})
     */
    protected $testProxyUri;

    /**
     * @ORM\Column(type="boolean", name="manual_mode", nullable=false, options={"default":0})
     */
    protected $manualMode;

    /**
     * @ORM\Column(name="created_at", type="datetime")
     */
    protected $createdAt;

    /** @ORM\Column(type="boolean", name="is_enabled", nullable=false, options={"default":0}) */
    protected $isEnabled;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->isEnabled = false;
        $this->useProxy = false;
        $this->manualMode = false;
        $this->title = '';
        $this->host = '';
        $this->testProxyUri = '';
    }

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
        $this->title = (string)$title;
    }

    public function getHost()
    {
        return $this->host;
    }

    public function setHost($host)
    {
        $this->host = (string)$host;
    }

    /**
     * @return string
     */
    public function getTestProxyUri()
    {
        return $this->testProxyUri;
    }

    /**
     * @param string $testProxyUri
     */
    public function setTestProxyUri($testProxyUri)
    {
        $this->testProxyUri = (string)$testProxyUri;
    }


    public function getLogin()
    {
        return $this->login;
    }

    public function setLogin($login)
    {
        $this->login = (string)$login;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function setPassword($password)
    {
        $this->password = (string)$password;
    }

    public function getCode()
    {
        return $this->code;
    }

    public function setCode($code)
    {
        $this->code = (string)$code;
    }

    public function getUseProxy()
    {
        return $this->useProxy;
    }

    public function setUseProxy($useProxy)
    {
        $this->useProxy = (bool)$useProxy;
    }

    /**
     * @return bool
     */
    public function getManualMode()
    {
        return $this->manualMode;
    }

    /**
     * @param bool $manualMode
     */
    public function setManualMode($manualMode)
    {
        $this->manualMode = (bool)$manualMode;
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

    public function getIsEnabled()
    {
        return $this->isEnabled;
    }

    public function setIsEnabled($isEnabled)
    {
        $this->isEnabled = (bool)$isEnabled;
    }
}
