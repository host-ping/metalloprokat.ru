<?php

namespace Metal\DemandsBundle\Command;

use Doctrine\ORM\QueryBuilder;
use Metal\DemandsBundle\Entity\Demand;
use Metal\NewsletterBundle\Command\NewsletterCommandAbstract;
use Metal\NewsletterBundle\Entity\Subscriber;
use Symfony\Component\Console\Input\InputOption;

class UserDemandRecallCommand extends NewsletterCommandAbstract
{
    protected function configure()
    {
        parent::configure();

        $this
            ->setName('metal:demands:send-recall-emails')
            ->addOption('days', null, InputOption::VALUE_OPTIONAL, null, 7);
    }

    protected function filterSubscribersQb(QueryBuilder $qb)
    {
        $qb
            ->join('MetalDemandsBundle:Demand', 'd', 'WITH', 's.user = d.user')
            ->andWhere('s.user IS NOT NULL')
            ->andWhere('user.isEnabled = true')
            ->andWhere('d.moderatedAt IS NOT NULL')
            ->andWhere('d.deletedAt IS NULL')
            ->andWhere('s.subscribedOnDemandRecallEmails = true')
            ->andWhere('(s.demandRecallEmailSentAt IS NULL OR s.demandRecallEmailSentAt < :from_day)')
            ->addGroupBy('d.user')
            ->andHaving('MAX(d.createdAt) < :from_day')
            ->setParameter('from_day', new \DateTime(sprintf('-%d days', $this->input->getOption('days'))));
    }

    /**
     * @param Subscriber[] $subscribers
     */
    protected function processSubscribers(array $subscribers)
    {
        $daysCount = $this->input->getOption('days');
        $userIds = array();
        foreach ($subscribers as $subscriber) {
            $userIds[] = $subscriber->getUser()->getId();
        }

        $demands = $this->em->getRepository('MetalDemandsBundle:Demand')->createQueryBuilder('d')
            ->andWhere('d.user IN (:user_ids)')
            ->andWhere('d.moderatedAt IS NOT NULL')
            ->andWhere('d.deletedAt IS NULL')
            ->addGroupBy('d.user')
            ->andHaving('MAX(d.createdAt) < :from_day')
            ->setParameter('user_ids', $userIds)
            ->setParameter('from_day', new \DateTime(sprintf('-%d days', $daysCount)))
            ->getQuery()
            ->getResult();
        /* @var $demands Demand[] */

        $demandWithUserId = array();
        foreach ($demands as $demand) {
            $demandWithUserId[$demand->getUser()->getId()] = $demand;
        }

        foreach ($subscribers as $subscriber) {
            $this->sendEmail(
                'MetalDemandsBundle::emails/user_demand_recall_email.html.twig',
                $subscriber,
                array(
                    'user' => $subscriber->getUser(),
                    'daysCount' => $daysCount,
                    'demand' => $demandWithUserId[$subscriber->getUser()->getId()],
                    'country' => $subscriber->getUser() ? $subscriber->getUser()->getCountry() : null,
                    'newsletterType' => 'demand-recall'
                ),
                array(
                    'demandRecallEmailSentAt' => new \DateTime()
                )
            );
        }
    }

    protected function configureMessage(\Swift_Message $message)
    {
        $message->setReplyTo($this->getContainer()->getParameter('mailer_from_account'));
    }
}
