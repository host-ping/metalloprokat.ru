<?php

namespace Metal\UsersBundle\Telegram\Service;

use Metal\UsersBundle\Telegram\Exception\ExpiredException;
use Metal\UsersBundle\Telegram\Exception\InvalidHashException;
use Metal\UsersBundle\Telegram\Model\ConnectTelegram;

class TelegramConnector
{
    private $apiToken;

    public function __construct(string $apiToken)
    {
        $this->apiToken = $apiToken;
    }

    function checkTelegramAuthorization(array $authData): ConnectTelegram
    {
        $checkHash = $authData['hash'];
        unset($authData['hash']);

        $dataParts = [];
        foreach ($authData as $key => $value) {
            $dataParts[] = $key.'='.$value;
        }
        sort($dataParts);

        $dataPartsAsString = implode("\n", $dataParts);
        $secretKey = hash('sha256', $this->apiToken, true);
        $hash = hash_hmac('sha256', $dataPartsAsString, $secretKey);

        if (strcmp($hash, $checkHash) !== 0) {
            throw new InvalidHashException();
        }

        if ((time() - $authData['auth_date']) > 86400) {
            throw new ExpiredException();
        }

        return new ConnectTelegram(
            $authData['id'],
            $authData['username'] ?? null,
            $authData['first_name'] ?? null,
            $authData['last_name'] ?? null
        );
    }
}
