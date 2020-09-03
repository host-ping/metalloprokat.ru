<?php

namespace Metal\NewsletterBundle\Service;

use Doctrine\ORM\EntityManager;
use Metal\NewsletterBundle\Entity\Subscriber;
use Metal\UsersBundle\Entity\User;

class SubscriberService
{
    protected $em;

    /**
     *  @param EntityManager $em
    */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function createOrUpdateSubscriberForUser(User $user)
    {
        $subscriber = $this->em->getRepository('MetalNewsletterBundle:Subscriber')->findOneBy(array('user' => $user));
        if (!$subscriber) {
            $subscriber = new Subscriber();
            $this->em->persist($subscriber);
        }

        $subscriber->setUser($user);
        $subscriber->setEmail($user->getEmail());
        $subscriber->setSubscribedForDemands($user->getCompany() !== null);
        $subscriber->setUpdatedAt(new \DateTime());

        return $subscriber;
    }

    public function removeUnnecessarySubscriberForUser(User $user, $email = null)
    {
        if (null === $email) {
            $email = $user->getEmail();
        }
        $subscriberWithNewEmail = $this->em->getRepository('MetalNewsletterBundle:Subscriber')->findOneBy(array('email' => $email));
        if ($subscriberWithNewEmail && (null === $subscriberWithNewEmail->getUser() || $subscriberWithNewEmail->getUser()->getId() != $user->getId())) {
            $this->em->remove($subscriberWithNewEmail);
            $this->em->flush($subscriberWithNewEmail);
        }
    }
}
