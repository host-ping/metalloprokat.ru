<?php

namespace Metal\DemandsBundle\Notifications;

use Doctrine\ORM\EntityManagerInterface;
use Metal\DemandsBundle\Entity\AbstractDemand;
use Metal\DemandsBundle\Entity\DemandNotification;

class OneTimeNotifier implements Notifier
{
    private $decorated;

    private $service;

    private $em;

    public function __construct(Notifier $decorated, int $service, EntityManagerInterface $em)
    {
        $this->decorated = $decorated;
        $this->service = $service;
        $this->em = $em;
    }

    public function notifyOnNewDemand(int $demandId): void
    {
        if ($this->isAlreadyNotified($demandId)) {
            return;
        }

        $this->decorated->notifyOnNewDemand($demandId);
        $this->markNotified($demandId);
    }

    private function isAlreadyNotified(int $demandId): bool
    {
        $notification = $this
            ->em
            ->getRepository(DemandNotification::class)
            ->findOneBy(
                [
                    'demand' => $demandId,
                    'service' => $this->service,
                ]
            );

        return $notification !== null;
    }

    private function markNotified(int $demandId): void
    {
        $demand = $this->em->find(AbstractDemand::class, $demandId);
        $notification = new DemandNotification($demand, $this->service);

        $this->em->persist($notification);
        $this->em->flush();
    }
}
