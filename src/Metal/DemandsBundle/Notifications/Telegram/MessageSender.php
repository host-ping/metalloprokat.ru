<?php

namespace Metal\DemandsBundle\Notifications\Telegram;

use BotMan\BotMan\BotMan;

class MessageSender
{
    private $botMan;

    public function __construct(BotMan $botMan)
    {
        $this->botMan = $botMan;
    }

    public function sendMessage(string $chatId, string $message): void
    {
        $this->botMan->say($message, $chatId, null, ['parse_mode' => 'Markdown']);
    }
}
