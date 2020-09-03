<?php

namespace Metal\DemandsBundle\Controller;

use BotMan\BotMan\BotMan;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use BotMan\BotMan\Messages\Outgoing\Question;
use Metal\DemandsBundle\Bot\Telegram\Connector;
use Metal\DemandsBundle\Bot\Telegram\SubscribeConversation;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class TelegramController extends Controller
{
    /**
     * @var Connector
     */
    public static $connector;

    /**
     * @link https://api.telegram.org/bot<TOKEN>/setWebhook?url=https://www.metalloprokat.ru/tg/callback
     */
    public function callbackAction()
    {
        $bot = $this->get('metal_notifications.telegram.botman');

        self::$connector = $this->get('metal_demands.bot_telegram.connector');;

        $bot->hears(
            'connect',
            function (BotMan $bot) {
                $bot->startConversation(new SubscribeConversation());
            }
        );

        $bot->hears(
            'disconnect',
            function (BotMan $bot) {
                $telegramUserId = $bot->getMessage()->getSender();

                try {
                    self::$connector->disconnectUser($telegramUserId);
                } catch (\InvalidArgumentException $e) {
                    $bot->reply($e->getMessage());

                    return;
                } catch (\Throwable $e) {
                    $bot->reply('Произошла неизвестная ошибка.');
                    $bot->reply($e->getMessage());

                    return;
                }

                $bot->reply('Вы успешно отключились от нашей рассылки. Нам будет вас не хватать ;(.');
            }
        );

        $bot->fallback(
            function (BotMan $bot) {
                $rawPayload = json_decode(json_encode($bot->getMessage()->getPayload()), true);

//                $bot->reply(json_encode($rawPayload));

//                $telegramUserId = $bot->getMessage()->getSender();
                $telegramUserId = $rawPayload['from']['id'];

                $connected = self::$connector->isConnected($telegramUserId);

                if (!$connected) {
                    $question = Question::create('Хотите регулярно получать свежие заявки?');
                    $question->addAction((new Button('Хочу!'))->value('connect'));

                    $bot->reply($question);

                    return;
                }

                $question = Question::create('Вы подключены к нашей рассылке. Хотите отключиться?');
                $question->addAction((new Button('Отключиться'))->value('disconnect'));

                $bot->reply($question);
            }
        );

        $bot->listen();

        return new Response();
    }
}
