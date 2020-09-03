<?php

namespace Metal\NewsletterBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(
 *      name="email_deferred",
 *      indexes={
 *          @ORM\Index(name="IDX_email", columns={"email"}),
 *          @ORM\Index(name="IDX_date", columns={"date_send"})
 *      },
 *      uniqueConstraints={
 *          @ORM\UniqueConstraint(name="UNIQ_email_date_send", columns={"email", "date_send"})
 *      }
 * )
 */
class EmailDeferred
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer", name="id")
     */
    protected $id;

    /**
     * @ORM\Column(length=255, name="email")
     */
    protected $email;

    /**
     * @ORM\Column(length=1000, name="log")
     */
    protected $log;

    /** @ORM\Column(type="date", name="date_send") */
    protected $dateSend;

    /**
     * @ORM\Column(type="boolean", name="unsubscribed", nullable=false, options={"default":0})
     */
    protected $unsubscribed;

    public function __construct()
    {
        $this->unsubscribed = false;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function getLog()
    {
        return $this->log;
    }

    public function setLog($log)
    {
        $this->log = $log;
    }

    public function getDateSend()
    {
        return $this->dateSend;
    }

    public function setDateSend($dateSend)
    {
        $this->dateSend = $dateSend;
    }

    public function getUnsubscribed()
    {
        return $this->unsubscribed;
    }

    public function setUnsubscribed($unsubscribed)
    {
        $this->unsubscribed = $unsubscribed;
    }
}
