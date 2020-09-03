<?php

namespace Metal\ProjectBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity
 * @ORM\Table(name="ban_ip")
 * @UniqueEntity(fields={"ip"})
 */
class BanIP
{
    const STATUS_BLOCKED_AUTO = 1;
    const STATUS_BLOCKED_MANUALLY = 2;
    const STATUS_WHITELISTED_AUTO = 3;
    const STATUS_WHITELISTED_MANUALLY = 4;
    const STATUS_WHITELISTED_CAPTCHA = 5;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\Column(name="int_ip", type="integer", options={"unsigned"=true}))
     */
    protected $intIp;

    /**
     * @ORM\Column(name="ip", length=15)
     * @Assert\NotBlank()
     * @Assert\Ip()
     */
    protected $ip;

    /**
     * @ORM\Column(name="created_at", type="datetime")
     */
    protected $createdAt;

    /**
     * @ORM\Column(name="hostname")
     */
    protected $hostname;

    /**
     * @ORM\Column(name="status", type="integer")
     */
    protected $status;

    /**
     * @ORM\Column(name="note", type="string", nullable=true)
     */
    protected $note;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
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
        $this->intIp = ip2long($ip);
        $this->hostname = @gethostbyaddr($ip);
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

    public function getHostname()
    {
        return $this->hostname;
    }

    public function setHostname($hostname)
    {
        $this->hostname = $hostname;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function setStatus($status)
    {
        $this->status = $status;
    }

    public function getNote()
    {
        return $this->note;
    }

    public function setNote($note)
    {
        $this->note = $note;
    }

    public static function getIpStatusAsSimpleArray()
    {
        return array(
            self::STATUS_BLOCKED_AUTO => 'Забанен автоматически',
            self::STATUS_BLOCKED_MANUALLY => 'Забанен администратором',
            self::STATUS_WHITELISTED_AUTO => 'Разрешен автоматически',
            self::STATUS_WHITELISTED_MANUALLY => 'Разрешен администратором',
            self::STATUS_WHITELISTED_CAPTCHA => 'Прошел капчу',
        );
    }
}
