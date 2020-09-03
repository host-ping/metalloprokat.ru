<?php

namespace Metal\NewsletterBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Metal\NewsletterBundle\Repository\RecipientRepository")
 * @ORM\Table(name="newsletter_recipient")
 */
class Recipient
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer", name="id")
     */
    protected $id;

    /**
     * @ORM\Column(type="datetime", name="sent_at", nullable=true)
     *
     * @var \DateTime
     */
    protected $sentAt;

    /**
     * @ORM\Column(type="datetime", name="processed_at", nullable=true)
     *
     * @var \DateTime
     */
    protected $processedAt;

    /**
     * @ORM\ManyToOne(targetEntity="Newsletter")
     * @ORM\JoinColumn(name="newsletter_id", referencedColumnName="id", nullable=false)
     *
     * @var Newsletter
     */
    protected $newsletter;

    /**
     * @ORM\ManyToOne(targetEntity="Subscriber")
     * @ORM\JoinColumn(name="subscriber_id", referencedColumnName="ID", nullable=false)
     */
    protected $subscriber;

    /**
     * @ORM\Column(type="string", length=40, name="hash_key", nullable=true)
     *
     * @var string
     */
    protected $hashKey;

    public function getId()
    {
        return $this->id;
    }

    /**
     * @param Newsletter $newsletter
     */
    public function setNewsletter(Newsletter $newsletter)
    {
        $this->newsletter = $newsletter;
    }

    /**
     * @return Newsletter
     */
    public function getNewsletter()
    {
        return $this->newsletter;
    }

    /**
     * @param \DateTime $sentAt
     */
    public function setSentAt(\DateTime $sentAt)
    {
        $this->sentAt = $sentAt;
        $this->processedAt = $sentAt;
    }

    public function send()
    {
        $this->setSentAt(new \DateTime());
    }

    /**
     * @param \DateTime $processedAt
     */
    public function setProcessedAt($processedAt)
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

    public function setProcessed()
    {
        $this->processedAt = new \DateTime();
    }

    /**
     * @return \DateTime
     */
    public function getSentAt()
    {
        return $this->sentAt;
    }

    /**
     * @param Subscriber $subscriber
     */
    public function setSubscriber(Subscriber $subscriber)
    {
        $this->subscriber = $subscriber;
    }

    /**
     * @return Subscriber
     */
    public function getSubscriber()
    {
        return $this->subscriber;
    }

    /**
     * @return string
     */
    public function getHashKey()
    {
        return $this->hashKey;
    }

    /**
     * @param string $hashKey
     */
    public function setHashKey($hashKey)
    {
        $this->hashKey = $hashKey;
    }
}
