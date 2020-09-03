<?php

namespace Metal\UsersBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Metal\UsersBundle\Telegram\Model\ConnectTelegram;

/**
 * @ORM\Entity(repositoryClass="Metal\UsersBundle\Repository\UserTelegramRepository")
 * @ORM\Table(name="user_telegram")
 */
class UserTelegram
{
    /**
     * @ORM\Id
     * @ORM\OneToOne(targetEntity="User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="User_ID", nullable=false)
     *
     * @var User
     */
    private $user;

    /**
     * @ORM\Column(type="string", name="telegram_user_id", unique=true)
     *
     * @var string
     */
    private $telegramUserId;

    /**
     * @ORM\Column(type="string", name="telegram_username", nullable=true)
     *
     * @var string|null
     */
    private $telegramUsername;

    /**
     * @ORM\Column(type="string", name="telegram_first_name", nullable=true)
     *
     * @var string|null
     */
    private $telegramFirstName;

    /**
     * @ORM\Column(type="string", name="telegram_last_name", nullable=true)
     *
     * @var string|null
     */
    private $telegramLastName;

    /**
     * @ORM\Column(type="datetime", name="created_at")
     *
     * @var \DateTime
     */
    private $createdAt;

    public function __construct(User $user, ConnectTelegram $connectTelegram)
    {
        $this->user = $user;
        $this->updateContact($connectTelegram);
    }

    public function updateContact(ConnectTelegram $connectTelegram): void
    {
        $this->telegramUserId = $connectTelegram->getId();
        $this->telegramUsername = $connectTelegram->getUsername();
        $this->telegramFirstName = $connectTelegram->getFirstName();
        $this->telegramLastName = $connectTelegram->getLastName();
        $this->createdAt = new \DateTime();
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getTelegramUserId(): string
    {
        return $this->telegramUserId;
    }

    public function getTelegramUsername(): ?string
    {
        return $this->telegramUsername;
    }

    public function getTelegramFirstName(): ?string
    {
        return $this->telegramFirstName;
    }

    public function getTelegramLastName(): ?string
    {
        return $this->telegramLastName;
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    public function getDisplayName(): string
    {
        if ($this->telegramUsername) {
            return $this->telegramUsername;
        }

        return implode(' ', array_filter([$this->telegramFirstName, $this->telegramLastName]));
    }
}
