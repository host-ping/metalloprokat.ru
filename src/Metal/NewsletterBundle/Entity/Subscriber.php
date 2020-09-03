<?php

namespace Metal\NewsletterBundle\Entity;

use Spros\ProjectBundle\Entity\DemandSubscription;
use Metal\UsersBundle\Entity\User;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Metal\NewsletterBundle\Repository\SubscriberRepository")
 * @ORM\Table(
 *      name="UserSend",
 *      indexes={
 *          @ORM\Index(name="IDX_bounced_at", columns={"bounced_at"})
 *      }
 * )
 *
 * @UniqueEntity(
 *     fields={"email"},
 *     errorPath="email",
 *     message="Пользователь с таким адресом электронной почты уже существует"
 * )
 */
class Subscriber
{
    const DEMAND_SUBSCRIPTION_PERIODICITY_NONE = 0;
    const DEMAND_SUBSCRIPTION_PERIODICITY_DAILY = 1;
    const DEMAND_SUBSCRIPTION_PERIODICITY_HOURLY = 2;
    const DEMAND_SUBSCRIPTION_PERIODICITY_WEEKLY = 3;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer", name="ID")
     */
    protected $id;

    /**
     * @ORM\Column(length=255, name="Email")
     */
    protected $email;

    /** @ORM\Column(type="smallint", name="subscribed_for_demands", nullable=false, options={"default":0}) */
    protected $subscribedForDemands;

    /** @ORM\Column(type="boolean", name="deleted", nullable=false, options={"default":0}) */
    protected $deleted;

    /** @ORM\Column(type="integer", name="whereFrom") */
    protected $whereFrom;

    /**
     * @ORM\Column(type="datetime", name="Created")
     *
     * @var \DateTime
     */
    protected $createdAt;

    /**
     * @ORM\Column(type="datetime", name="Updated")
     *
     * @var \DateTime
     */
    protected $updatedAt;

    /**
     * @ORM\Column(type="datetime", name="demands_digest_sent_at", nullable=true)
     *
     * @var \DateTime
     */
    protected $demandsDigestSentAt;

    /**
     * @ORM\Column(type="boolean", name="is_invalid", nullable=false, options={"default":0})
     */
    protected $isInvalid;

    /**
     * @ORM\OneToOne(targetEntity="Metal\UsersBundle\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="User_ID", nullable=true)
     *
     * @var User
     */
    protected $user;

    /**
     * @ORM\Column(type="boolean", name="subscribed_on_news", nullable=false, options={"default":1})
     */
    protected $subscribedOnNews;

    /**
     * @ORM\Column(type="boolean", name="subscribed_on_index", nullable=false, options={"default":1})
     */
    protected $subscribedOnIndex;

    /**
     * @ORM\Column(type="string", length=255, name="source", nullable=true)
     */
    protected $source;

    /**
     * @ORM\OneToOne(targetEntity="Spros\ProjectBundle\Entity\DemandSubscription")
     * @ORM\JoinColumn(name="demand_subscription_id", referencedColumnName="id", nullable=true)
     *
     * @var DemandSubscription
     */
    protected $demandSubscription;

    /**
     * @ORM\Column(type="string", length=20, name="task_hash", nullable=true)
     */
    protected $taskHash;

    /**
     * @ORM\Column(type="datetime", name="task_hash_created_at", nullable=true)
     */
    protected $taskHashCreatedAt;

    /**
     * @ORM\Column(type="boolean", name="subscribed_on_recall_emails", nullable=false, options={"default":1})
     */
    protected $subscribedOnRecallEmails;

    /**
     * @ORM\Column(type="datetime", name="recall_email_sent_at", nullable=true)
     *
     * @var \DateTime
     */
    protected $recallEmailSentAt;

    /**
     * @ORM\Column(type="boolean", name="subscribed_on_demand_recall_emails", nullable=false, options={"default":1})
     */
    protected $subscribedOnDemandRecallEmails;

    /**
     * @ORM\Column(type="datetime", name="demand_recall_email_sent_at", nullable=true)
     *
     * @var \DateTime
     */
    protected $demandRecallEmailSentAt;

    /**
     * @ORM\Column(type="datetime", name="bounced_at", nullable=true)
     */
    protected $bouncedAt;

    /**
     * @ORM\Column(length=1000, name="bounce_log", nullable=true)
     */
    protected $bounceLog;

    /**
     * @ORM\Column(length=150, name="bounce_filename", nullable=true)
     */
    protected $bounceFilename;

    /**
     * @ORM\Column(type="boolean", name="subscribed_on_price_invite_emails", nullable=false, options={"default":1})
     */
    protected $subscribedOnPriceInviteEmails;

    /**
     * @ORM\Column(type="boolean", name="subscribed_on_products_update", nullable=false, options={"default":1})
     */
    protected $subscribedOnProductsUpdate;

    /**
     * @ORM\Column(type="datetime", name="price_invite_email_sent_at", nullable=true)
     *
     * @var \DateTime
     */
    protected $priceInviteEmailSentAt;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
        $this->whereFrom = 0;
        $this->userId = 0;
        $this->subscribedOnNews = false;
        $this->subscribedOnIndex = false;
        $this->isInvalid = false;
        $this->subscribedForDemands = self::DEMAND_SUBSCRIPTION_PERIODICITY_DAILY;
        $this->deleted = false;
        $this->subscribedOnRecallEmails = false;
        $this->subscribedOnDemandRecallEmails = false;
        $this->subscribedOnPriceInviteEmails = false;
        $this->subscribedOnProductsUpdate = false;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    public function getBounceLog()
    {
        return $this->bounceLog;
    }

    public function setBounceLog($bounceLog)
    {
        $this->bounceLog = $bounceLog;
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
     * @param \DateTime $demandsDigestSentAt
     */
    public function setDemandsDigestSentAt(\DateTime $demandsDigestSentAt)
    {
        $this->demandsDigestSentAt = $demandsDigestSentAt;
    }

    /**
     * @return \DateTime
     */
    public function getDemandsDigestSentAt()
    {
        return $this->demandsDigestSentAt;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param \DateTime $updatedAt
     */
    public function setUpdatedAt(\DateTime $updatedAt)
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    public function setWhereFrom($whereFrom)
    {
        $this->whereFrom = $whereFrom;
    }

    public function getWhereFrom()
    {
        return $this->whereFrom;
    }

    /**
     * @param User $user
     */
    public function setUser(User $user)
    {
        $this->user = $user;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    public function setIsInvalid($isInvalid)
    {
        $this->isInvalid = $isInvalid;
    }

    public function getIsInvalid()
    {
        return $this->isInvalid;
    }

    public function setSubscribedOnNews($subscribedOnNews)
    {
        $this->subscribedOnNews = $subscribedOnNews;
    }

    public function getSubscribedOnNews()
    {
        return $this->subscribedOnNews;
    }

    public function getSubscribedOnIndex()
    {
        return $this->subscribedOnIndex;
    }

    public function setSubscribedOnIndex($subscribedOnIndex)
    {
        $this->subscribedOnIndex = $subscribedOnIndex;
    }

    public function setSource($source)
    {
        $this->source = $source;
    }

    public function getSource()
    {
        return $this->source;
    }

    public function setDeleted($deleted)
    {
        $this->deleted = $deleted;
    }

    public function getDeleted()
    {
        return $this->deleted;
    }

    public function setSubscribedForDemands($subscribedForDemands)
    {
        $this->subscribedForDemands = $subscribedForDemands;
    }

    public function getSubscribedForDemands()
    {
        return $this->subscribedForDemands;
    }

    /**
     * @return DemandSubscription
     */
    public function getDemandSubscription()
    {
        return $this->demandSubscription;
    }

    /**
     * @param DemandSubscription $demandSubscription
     */
    public function setDemandSubscription(DemandSubscription $demandSubscription)
    {
        $this->demandSubscription = $demandSubscription;
    }

    public function getSubscribedOnRecallEmails()
    {
        return $this->subscribedOnRecallEmails;
    }

    public function setSubscribedOnRecallEmails($subscribedOnRecallEmails)
    {
        $this->subscribedOnRecallEmails = $subscribedOnRecallEmails;
    }

    /**
     * @return \DateTime
     */
    public function getRecallEmailSentAt()
    {
        return $this->recallEmailSentAt;
    }

    /**
     * @param \DateTime $recallEmailSentAt
     */
    public function setRecallEmailSentAt(\DateTime $recallEmailSentAt)
    {
        $this->recallEmailSentAt = $recallEmailSentAt;
    }

    public function getSubscribedOnDemandRecallEmails()
    {
        return $this->subscribedOnDemandRecallEmails;
    }

    public function setSubscribedOnDemandRecallEmails($subscribedOnDemandRecallEmails)
    {
        $this->subscribedOnDemandRecallEmails = $subscribedOnDemandRecallEmails;
    }

    /**
     * @return \DateTime
     */
    public function getDemandRecallEmailSentAt()
    {
        return $this->demandRecallEmailSentAt;
    }

    /**
     * @param \DateTime $demandRecallEmailSentAt
     */
    public function setDemandRecallEmailSentAt(\DateTime $demandRecallEmailSentAt)
    {
        $this->demandRecallEmailSentAt = $demandRecallEmailSentAt;
    }

    /**
     * @return \DateTime
     */
    public function getBouncedAt()
    {
        return $this->bouncedAt;
    }

    /**
     * @param \DateTime $bouncedAt
     */
    public function setBouncedAt(\DateTime $bouncedAt)
    {
        $this->bouncedAt = $bouncedAt;
    }

    public function setIsBounced($bouncedAt = null)
    {
        $this->bouncedAt = $bouncedAt ? new \DateTime() : null;
    }

    public function getIsBounced()
    {
        return $this->bouncedAt ? true : false;
    }

    /**
     * @return mixed
     */
    public function getBounceFilename()
    {
        return $this->bounceFilename;
    }

    public function setBounceFilename($bounceFilename)
    {
        $this->bounceFilename = $bounceFilename;
    }

    /**
     * @param \DateTime $priceInviteEmailSentAt
     */
    public function setPriceInviteEmailSentAt($priceInviteEmailSentAt)
    {
        $this->priceInviteEmailSentAt = $priceInviteEmailSentAt;
    }

    /**
     * @return \DateTime
     */
    public function getPriceInviteEmailSentAt()
    {
        return $this->priceInviteEmailSentAt;
    }

    public function setSubscribedOnPriceInviteEmails($subscribedOnPriceInviteEmails)
    {
        $this->subscribedOnPriceInviteEmails = $subscribedOnPriceInviteEmails;
    }

    public function getSubscribedOnPriceInviteEmails()
    {
        return $this->subscribedOnPriceInviteEmails;
    }

    public function getSubscribedOnProductsUpdate()
    {
        return $this->subscribedOnProductsUpdate;
    }

    public function setSubscribedOnProductsUpdate($subscribedOnProductsUpdate)
    {
        $this->subscribedOnProductsUpdate = $subscribedOnProductsUpdate;
    }

    public static function getDemandSubscriptionStatusesAsArray($keyValue = true)
    {
        $statuses = array(
            Subscriber::DEMAND_SUBSCRIPTION_PERIODICITY_NONE => 'Никогда',
            Subscriber::DEMAND_SUBSCRIPTION_PERIODICITY_DAILY => 'Раз в день',
            Subscriber::DEMAND_SUBSCRIPTION_PERIODICITY_HOURLY => 'Раз в час',
            Subscriber::DEMAND_SUBSCRIPTION_PERIODICITY_WEEKLY => 'Раз в неделю',
        );

        if ($keyValue) {
            return $statuses;
        }

        $arrHash = [];
        foreach ($statuses as $key => $value) {
            $arrHash[] = ['query' => $key, 'title' => $value];
        }

        return $arrHash;
    }
}
