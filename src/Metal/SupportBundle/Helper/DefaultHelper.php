<?php

namespace Metal\SupportBundle\Helper;

use Brouzie\Bundle\HelpersBundle\Helper\HelperAbstract;
use Metal\SupportBundle\Entity\Topic;

class DefaultHelper extends HelperAbstract
{
    /**
     * @param Topic $topic
     * @return array
     */
    public function getAnswersForTopic(Topic $topic)
    {
        $em = $this->container->get('doctrine')->getManager();
        /* @var $em \Doctrine\ORM\EntityManager */

        return $em->getRepository('MetalSupportBundle:Answer')->createQueryBuilder('a')
            ->select('a')
            ->addSelect('u')
            ->leftJoin('a.author', 'u')
            ->andWhere('a.topic = :topic_id')
            ->setParameter('topic_id', $topic->getId())
            ->orderBy('a.createdAt')
            ->getQuery()
            ->getResult();
    }
}
