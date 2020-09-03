<?php

namespace Metal\DemandsBundle\Bot\Telegram;

use BotMan\BotMan\Messages\Conversations\Conversation;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\Drivers\Telegram\TelegramDriver;
use Metal\DemandsBundle\Controller\TelegramController;

/**
 * @link https://groosha.gitbooks.io/telegram-bot-lessons/content/chapter9.html
 * @link https://rinkovec.com/using-botman-symfony-service-container/
 * @link https://github.com/botman/botman/issues/771
 * @link https://github.com/botman/botman/issues/256
 */
class SubscribeConversation extends Conversation
{
    public function run()
    {
        $this
            ->ask(
                "1. Укажите свой номер телефона на странице https://my.metalloprokat.ru/management/account в формате +7 123 456-78-90.\n".
                '2. Нажмите "Связать учетные записи" и передайте свой номер телефона.',
                function (Answer $answer) {
                    $rawPayload = json_decode(json_encode($answer->getMessage()->getPayload()), true);

                    $removeKeyboardResponse = [
                        'reply_markup' => json_encode(
                            [
                                'remove_keyboard' => true,
                                'inline_keyboard' => [],
                            ]
                        ),
                    ];

//                    $this->say(json_encode($payload));
//                    return;

                    $payload = $this->denormalizePayload($rawPayload);

                    if (!$payload) {
                        $this->say('Нажмите кнопку "Связать учетные записи".', $removeKeyboardResponse);

                        return;
                    }

                    if (!$this->assertThatContactIsOwn($payload)) {
                        $this->say('Вы передали чужой контакт.', $removeKeyboardResponse);

                        return;
                    }

                    try {
                        TelegramController::$connector->connectUser($payload);
                    } catch (\InvalidArgumentException $e) {
                        $this->say($e->getMessage(), $removeKeyboardResponse);

                        return;
                    } catch (\Throwable $e) {
                        $this->say('Произошла неизвестная ошибка.', $removeKeyboardResponse);
                        $this->say($e->getMessage());

                        return;
                    }

                    $this->say(
                        'Ваш контакт успешно связан и теперь вы будете получать уведомления о заявках.',
                        $removeKeyboardResponse
                    );

                    /** @var TelegramDriver $driver */
//                    $driver = $this->getBot()->getDriver();
//                    $this->say('');
//                    $driver->messagesHandled();
                },
                [
                    'reply_markup' => json_encode(
                        [
                            'keyboard' => [
                                [

                                    [
                                        'text' => 'Связать учетные записи',
                                        'request_contact' => true,
                                    ],
                                ],
                            ],
//                            'one_time_keyboard' => true,
//                            'resize_keyboard' => true,
                        ]
                    ),
                ]
            );
    }

    private function denormalizePayload(array $payload): ?ContactPayload
    {
        if (empty($payload['contact'])) {
            return null;
        }

        return new ContactPayload(
            $payload['from']['id'],
            $payload['contact']['user_id'],
            $payload['contact']['phone_number'],
            $payload['contact']['first_name'] ?? null,
            $payload['contact']['last_name'] ?? null
        );
    }

    private function assertThatContactIsOwn(ContactPayload $payload): bool
    {
        return $payload->contactUserId === $payload->authorId;
    }
}
