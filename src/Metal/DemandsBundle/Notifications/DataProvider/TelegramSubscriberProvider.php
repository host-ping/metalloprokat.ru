<?php

namespace Metal\DemandsBundle\Notifications\DataProvider;

use Doctrine\ORM\EntityManagerInterface;
use Metal\DemandsBundle\Entity\DemandSubscriptionCategory;
use Metal\DemandsBundle\Entity\DemandSubscriptionTerritorial;
use Metal\DemandsBundle\Notifications\Model\TelegramSubscriber;
use Metal\UsersBundle\Entity\UserTelegram;

class TelegramSubscriberProvider
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @return TelegramSubscriber[]
     */
    public function getSubscribers(): iterable
    {
        //TODO: сделать пакетное ращбиение а не грузить всех подписчиков за раз
        $telegramUserIdsPerUserId = $this->em
            ->getRepository(UserTelegram::class)
            ->getTelegramUserIdsPerUserId();

        $usersIds = array_keys($telegramUserIdsPerUserId);

        $categoryIdsPerUser = $this->em
            ->getRepository(DemandSubscriptionCategory::class)
            ->getCategoryIdsPerUser($usersIds);

        $territorialIdsPerUser = $this->em
            ->getRepository(DemandSubscriptionTerritorial::class)
            ->getSubscribedTerritorialIdsPerUser($usersIds);

        foreach ($telegramUserIdsPerUserId as $userId => $tgUserId) {
            $categoryIds = $categoryIdsPerUser[$userId] ?? [];
            $territorialIds = $territorialIdsPerUser[$userId] ?? [];

            yield new TelegramSubscriber($tgUserId, $categoryIds, $territorialIds);
        }
    }
}
