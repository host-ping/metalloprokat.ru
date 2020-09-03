<?php

namespace Metal\SupportBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Metal\CompaniesBundle\Entity\Company;
use Metal\UsersBundle\Entity\User;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="Metal\SupportBundle\Repository\TopicRepository")
 * @ORM\Table(name="support_topic")
 */
class Topic
{
    const SOURCE_CORPSITE = 1;
    const SOURCE_PRIVATE_OFFICE = 2;
    const SOURCE_ADMIN = 3;

    const STATUS_NEW = 1;
    const STATUS_RESOLVED = 2;
    const STATUS_REOPENED = 3;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer", name="id")
     */
    protected $id;

    /**
     * @ORM\Column(length=255, name="title", options={"default"=""})
     * @Assert\NotBlank()
     * @Assert\Length(min=5, max=255)
     */
    protected $title;

    /**
     * @ORM\Column(length=7150, name="description")
     * @Assert\NotBlank(groups={"corp_site", "private_office"})
     * @Assert\Length(min="5", max="7150", groups={"corp_site", "private_office"})
     */
    protected $description;

    /**
     * @ORM\Column(type="smallint", name="sent_from", nullable=false)
     */
    protected $sentFrom;

    /**
     * @ORM\ManyToOne(targetEntity="Metal\UsersBundle\Entity\User")
     * @ORM\JoinColumn(name="author_id", referencedColumnName="User_ID")
     *
     * @var User
     */
    protected $author;

    /**
     * @ORM\ManyToOne(targetEntity="Metal\UsersBundle\Entity\User")
     * @ORM\JoinColumn(name="receiver_id", referencedColumnName="User_ID")
     *
     * @var User
     */
    protected $receiver;

    /**
     * @ORM\ManyToOne(targetEntity="Metal\CompaniesBundle\Entity\Company")
     * @ORM\JoinColumn(name="company_id", referencedColumnName="Message_ID")
     *
     * @var Company
     */
    protected $company;

    /**
     * @ORM\ManyToOne(targetEntity="Metal\UsersBundle\Entity\User")
     * @ORM\JoinColumn(name="resolved_by", referencedColumnName="User_ID", nullable=true)
     */
    protected $resolvedBy;

    /**
     * @ORM\Column(length=255, name="user_name", nullable=true)
     * @Assert\NotBlank(groups={"corp_site"})
     * @Assert\Length(min="3", max="100", groups={"corp_site", "private_office"})
     */
    protected $userName;

    /**
     * @ORM\Column(length=40, name="email", nullable=true)
     * @Assert\NotBlank(groups={"corp_site"})
     * @Assert\Email(groups={"corp_site"}, strict=true)
     */
    protected $email;

    /**
     * @ORM\Column(type="smallint", name="answers_count", nullable=false, options={"default":0})
     */
    protected $answersCount;

    /**
     * @ORM\Column(type="smallint", name="unread_answers_count", nullable=false, options={"default":0})
     */
    protected $unreadAnswersCount;

    /**
     * @ORM\OneToOne(targetEntity="Answer")
     * @ORM\JoinColumn(name="last_answer_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     *
     * @var Answer
     */
    protected $lastAnswer;

    /**
     * @ORM\Column(type="datetime", name="last_answer_at")
     * @var \DateTime
     */
    protected $lastAnswerAt;

    /**
     * @ORM\Column(type="datetime", name="created_at")
     * @var \DateTime
     */
    protected $createdAt;

    /**
     * @ORM\Column(type="datetime", name="resolved_at", nullable=true)
     *
     * @var \DateTime
     */
    protected $resolvedAt;

    /**
     * @ORM\Column(type="datetime", name="viewed_at", nullable=true)
     *
     * @var \DateTime
     */
    protected $viewedAt;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->lastAnswerAt = new \DateTime();
        $this->answersCount = 0;
        $this->unreadAnswersCount = 0;
        $this->title = '';
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
        $this->title = (string) $title;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function getSentFrom()
    {
        return $this->sentFrom;
    }

    public function setSentFrom($sentFrom)
    {
        $this->sentFrom = $sentFrom;
    }

    public function getUserName()
    {
        return $this->userName;
    }

    public function setUserName($userName)
    {
        $this->userName = $userName;
    }

    /**
     * @param User $author
     */
    public function setAuthor(User $author)
    {
        $this->author = $author;
    }

    /**
     * @return User
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * @return User
     */
    public function getReceiver()
    {
        return $this->receiver;
    }

    /**
     * @param User $receiver
     */
    public function setReceiver(User $receiver)
    {
        $this->receiver = $receiver;
    }

    /**
     * @param Company $company
     */
    public function setCompany(Company $company)
    {
        $this->company = $company;
    }

    /**
     * @return Company
     */
    public function getCompany()
    {
        return $this->company;
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
     * @return \DateTime
     */
    public function getViewedAt()
    {
        return $this->viewedAt;
    }

    public function setViewedAt()
    {
        $this->viewedAt = new \DateTime();
    }

    public function setDescription($description)
    {
        $this->description = $description;
    }

    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param \DateTime $lastAnswerAt
     */
    public function setLastAnswerAt(\DateTime $lastAnswerAt)
    {
        $this->lastAnswerAt = $lastAnswerAt;
    }

    /**
     * @return \DateTime
     */
    public function getLastAnswerAt()
    {
        return $this->lastAnswerAt;
    }

    /**
     * @param int $answersCount
     */
    public function setAnswersCount($answersCount)
    {
        $this->answersCount = $answersCount;
    }

    public function getAnswersCount()
    {
        return $this->answersCount;
    }

    public function setUnreadAnswersCount($unreadAnswersCount)
    {
        $this->unreadAnswersCount = $unreadAnswersCount;
    }

    public function getUnreadAnswersCount()
    {
        return $this->unreadAnswersCount;
    }

    /**
     * @param Answer $lastAnswer
     */
    public function setLastAnswer(Answer $lastAnswer)
    {
        $this->lastAnswer = $lastAnswer;
        $this->setLastAnswerAt($lastAnswer->getCreatedAt());
    }

    /**
     * @return Answer
     */
    public function getLastAnswer()
    {
        return $this->lastAnswer;
    }

    /**
     * @param \DateTime $resolvedAt
     */
    public function setResolvedAt(\DateTime $resolvedAt)
    {
        $this->resolvedAt = $resolvedAt;
    }

    /**
     * @return \DateTime
     */
    public function getResolvedAt()
    {
        return $this->resolvedAt;
    }

    public function setResolved($resolvedAt = true)
    {
        $this->resolvedAt = $resolvedAt ? new \DateTime() : null;
    }

    public function isResolved()
    {
        return $this->resolvedAt !== null;
    }

    public function getResolvedAtTimestamp()
    {
        return $this->resolvedAt ? $this->resolvedAt->getTimestamp() : null;
    }

    /**
     * @param User $resolvedBy
     */
    public function setResolvedBy(User $resolvedBy = null)
    {
        $this->resolvedBy = $resolvedBy;
    }

    public function getTopicStatus()
    {
        if ($this->resolvedBy && $this->resolvedAt) {
            return self::STATUS_RESOLVED;
        }

        if ($this->resolvedBy) {
            return self::STATUS_REOPENED;
        }

        return self::STATUS_NEW;
    }

    /**
     * @return User
     */
    public function getResolvedBy()
    {
        return $this->resolvedBy;
    }

    public static function getTopicStatusAliases()
    {
        return array(
            self::STATUS_NEW => 'Новая',
            self::STATUS_REOPENED => 'Переоткрыта',
            self::STATUS_RESOLVED => 'Решена',
        );
    }

    public static function getSentFromAliases()
    {
        return array(
            self::SOURCE_CORPSITE => 'Корпоротивный сайт',
            self::SOURCE_PRIVATE_OFFICE => 'Личный кабинет',
            self::SOURCE_ADMIN => 'Обращение модератора',
        );
    }
}
