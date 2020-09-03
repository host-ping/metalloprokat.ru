<?php

namespace Metal\UsersBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Metal\UsersBundle\Repository\UserAutoLoginRepository")
 * @ORM\Table(name="user_auto_login")
 */
class UserAutoLogin
{
    const MAX_AUTHENTICATION_COUNT = 2;

    const TARGET_EMAIL = 1;
    const TARGET_EXTERNAL_SITE = 2;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer", name="id")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="User_ID", nullable=false)
     *
     * @var User
     */
    protected $user;

    /**
     * @ORM\Column(type="string", length=40, name="token", unique=true, options={"fixed" = true})
     */
    protected $token;

    /**
     * @ORM\Column(type="datetime", name="created_at", nullable=false)
     *
     * @var \DateTime
     */
    protected $createdAt;

    /**
     * @ORM\Column(type="smallint", name="authentications_count", options={"default":0})
     */
    protected $authenticationsCount;

    /**
     * @ORM\Column(type="smallint", name="target", options={"default":1})
     */
    protected $target;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->authenticationsCount = 0;
        $this->token = sha1(microtime(true).mt_rand(0, 9999));
        $this->target = self::TARGET_EMAIL;
    }

    public function getId()
    {
        return $this->id;
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

    public function getToken()
    {
        return $this->token;
    }

    public function setToken($token)
    {
        $this->token = $token;
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

    public function getAuthenticationsCount()
    {
        return $this->authenticationsCount;
    }

    public function setAuthenticationsCount($authenticationsCount)
    {
        $this->authenticationsCount = $authenticationsCount;
    }

    public function isAlive()
    {
        $dateFrom = new \DateTime('-7 days');

        return $this->createdAt > $dateFrom && ($this->authenticationsCount <= self::MAX_AUTHENTICATION_COUNT);
    }

    public function getTarget()
    {
        return $this->target;
    }

    public function setTarget($target)
    {
        $this->target = $target;
    }
}
