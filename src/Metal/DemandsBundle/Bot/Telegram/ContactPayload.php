<?php

namespace Metal\DemandsBundle\Bot\Telegram;

class ContactPayload
{
    /**
     * @var int
     */
    public $authorId;

    /**
     * @var int
     */
    public $contactUserId;

    /**
     * @var string
     */
    public $contactPhoneNumber;

    /**
     * @var ?string
     */
    public $contactFirstName;

    /**
     * @var ?string
     */
    public $contactLastName;

    public function __construct(
        int $authorId,
        int $contactUserId,
        ?string $contactPhoneNumber,
        ?string $contactFirstName,
        ?string $contactLastName
    ) {
        $this->authorId = $authorId;
        $this->contactUserId = $contactUserId;
        $this->contactPhoneNumber = $contactPhoneNumber;
        $this->contactFirstName = $contactFirstName;
        $this->contactLastName = $contactLastName;
    }
}
