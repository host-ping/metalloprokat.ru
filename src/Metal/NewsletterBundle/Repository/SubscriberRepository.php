<?php

namespace Metal\NewsletterBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Metal\NewsletterBundle\Entity\Subscriber;

class SubscriberRepository extends EntityRepository
{
    public function getTaskHash()
    {
        return substr(sha1(microtime(true).mt_rand(0, 99999)), 0, 20);
    }

    public function resetOutdatedSubscribers($hours = 3)
    {
        $this->createQueryBuilder('s')
            ->update()
            ->set('s.taskHash', 'NULL')
            ->set('s.taskHashCreatedAt', 'NULL')
            ->where('s.taskHash IS NOT NULL')
            ->andWhere('s.taskHashCreatedAt < :task_date')
            ->setParameter('task_date', new \DateTime('-'.$hours.' hours'), 'datetime')
            ->getQuery()
            ->execute();
    }

    /**
     * @param $taskHash
     *
     * @return Subscriber[]
     */
    public function getSubscribersByTaskHash($taskHash)
    {
        return $this->createQueryBuilder('s')
            ->leftJoin('s.user', 'u')
            ->addSelect('u')
            ->andWhere('s.taskHash = :task_hash')
            ->setParameter('task_hash', $taskHash)
            ->getQuery()
            ->getResult();
    }

    public function releaseSubscriber(Subscriber $subscriber, array $dataToUpdate)
    {
        $qb = $this->_em->createQueryBuilder()
            ->update('MetalNewsletterBundle:Subscriber', 's')
            ->set('s.taskHash', 'NULL')
            ->set('s.taskHashCreatedAt', 'NULL')
            ->andWhere('s.id = :subscriber')
            ->setParameter('subscriber', $subscriber->getId());

        foreach ($dataToUpdate as $field => $value) {
            $qb
                ->set("s.$field", ":$field")
                ->setParameter($field, $value);
        }

        $qb
            ->getQuery()
            ->execute();
    }
}
