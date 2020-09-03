<?php

namespace Metal\DemandsBundle\Notifications\Telegram;

use BotMan\BotMan\BotMan;
use BotMan\BotMan\BotManFactory;
use BotMan\BotMan\Drivers\DriverManager;
use BotMan\BotMan\Interfaces\CacheInterface;
use BotMan\BotMan\Interfaces\StorageInterface;
use BotMan\Drivers\Telegram\TelegramDriver;

class TelegramFactory
{
    private $telegramBotApiToken;

    private $cache;

    private $storage;

    public function __construct(string $telegramBotApiToken, CacheInterface $cache, StorageInterface $storage)
    {
        $this->telegramBotApiToken = $telegramBotApiToken;
        $this->cache = $cache;
        $this->storage = $storage;
    }

    public function getBotMan(): BotMan
    {
        DriverManager::loadDriver(TelegramDriver::class);

        $config = [
            'telegram' => [
                'token' => $this->telegramBotApiToken,
            ],
        ];

        $botMan = BotManFactory::create($config, $this->cache, null, $this->storage);
        $botMan->loadDriver(TelegramDriver::class);

        return $botMan;
    }
}
