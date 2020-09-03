<?php

namespace Metal\DemandsBundle\Bot\Telegram;

use Metal\UsersBundle\Entity\User;
use Metal\UsersBundle\Entity\UserTelegram;
use Metal\UsersBundle\Repository\UserTelegramRepository;
use Metal\UsersBundle\Telegram\Model\ConnectTelegram;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Doctrine\ManagerRegistry;

class Connector
{
    private $doctrine;

    private $logger;

    public function __construct(ManagerRegistry $doctrine, LoggerInterface $logger)
    {
        $this->doctrine = $doctrine;
        $this->logger = $logger;
    }

    public function connectUser(ContactPayload $payload): void
    {
        $this->logger->info(
            'Trying to connect user to telegram.',
            [
                'payload.phone' => $payload->contactPhoneNumber,
                'payload.first_name' => $payload->contactFirstName,
                'payload.last_name' => $payload->contactLastName,
            ]
        );

        $user = $this->doctrine->getRepository(User::class)
            ->findOneBy(['phoneCanonical' => $payload->contactPhoneNumber]);

        if (!$user) {
            throw new \InvalidArgumentException('Пользователь с заданным телефоном не зарегистрирован на сайте.');
        }

        $userTelegram = $this->getUserTelegramRepository()->findOneBy(['user' => $user]);
        $connectTelegram = new ConnectTelegram(
            $payload->contactUserId,
            '',
            $payload->contactFirstName,
            $payload->contactLastName
        );


        $em = $this->doctrine->getManagerForClass(UserTelegram::class);

        if ($userTelegram) {
            $userTelegram->updateContact($connectTelegram);
        } else {
            $userTelegram = new UserTelegram($user, $connectTelegram);
            $em->persist($userTelegram);
        }

        $em->flush();
    }

    public function disconnectUser(string $telegramUserId): void
    {
        $userTelegram = $this->findConnectionByTelegramUserId($telegramUserId);

        if (!$userTelegram) {
            throw new \InvalidArgumentException('Вы не были подключены.');
        }

        $em = $this->doctrine->getManagerForClass(UserTelegram::class);

        $em->remove($userTelegram);
        $em->flush();
    }

    public function isConnected(string $telegramUserId): bool
    {
        $userTelegram = $this->findConnectionByTelegramUserId($telegramUserId);

        return null !== $userTelegram;
    }

    private function getUserTelegramRepository(): UserTelegramRepository
    {
        return $this->doctrine->getRepository(UserTelegram::class);
    }

    private function findConnectionByTelegramUserId(string $telegramUserId): ?UserTelegram
    {
        return $this->getUserTelegramRepository()->findOneBy(['telegramUserId' => $telegramUserId]);
    }

}
