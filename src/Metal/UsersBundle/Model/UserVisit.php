<?php

namespace Metal\UsersBundle\Model;

class UserVisit implements \Serializable
{
    private $userId;

    private $companyId;

    private $visitedAt;

    private $ip;

    public function __construct(int $userId, int $companyId, \DateTime $visitedAt, string $ip)
    {
        $this->userId = $userId;
        $this->companyId = $companyId;
        $this->visitedAt = $visitedAt;
        $this->ip = $ip;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getCompanyId(): int
    {
        return $this->companyId;
    }

    public function getVisitedAt(): \DateTime
    {
        return $this->visitedAt;
    }

    public function getIp(): string
    {
        return $this->ip;
    }

    public function serialize()
    {
        return serialize([$this->userId, $this->companyId, $this->visitedAt, $this->ip]);
    }

    public function unserialize($serialized)
    {
        list($this->userId, $this->companyId, $this->visitedAt, $this->ip) = unserialize(
            $serialized,
            ['allowed_classes' => \DateTime::class]
        );
    }
}
