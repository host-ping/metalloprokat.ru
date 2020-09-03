<?php

namespace Metal\UsersBundle\Telegram\Model;

class ConnectTelegram
{
    private $id;

    private $username;

    private $firstName;

    private $lastName;

    public function __construct(string $id, ?string $username, ?string $firstName, ?string $lastName)
    {
        $this->id = $id;
        $this->username = $username;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }
}
