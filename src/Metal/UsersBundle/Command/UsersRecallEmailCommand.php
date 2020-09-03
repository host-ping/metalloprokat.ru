<?php

namespace Metal\UsersBundle\Command;

use Doctrine\ORM\QueryBuilder;
use Metal\NewsletterBundle\Command\NewsletterCommandAbstract;
use Metal\NewsletterBundle\Entity\Subscriber;
use Symfony\Component\Console\Input\InputOption;

class UsersRecallEmailCommand extends NewsletterCommandAbstract
{
    protected function configure()
    {
        parent::configure();

        $this
            ->setName('metal:users:send-recall-emails')
            ->addOption('days', null, InputOption::VALUE_OPTIONAL, null, 7);
    }

    protected function filterSubscribersQb(QueryBuilder $qb)
    {
        $qb
            ->addSelect('COALESCE(MAX(uv.date), 0) AS date_filter')
            ->andWhere('s.user IS NOT NULL')
            ->leftJoin('MetalStatisticBundle:UserVisiting', 'uv', 'WITH', 'user.company = uv.company')
            ->join('MetalCompaniesBundle:CompanyCounter', 'cc', 'WITH', 'user.company = cc.company')
            ->andWhere('cc.allProductsCount > 0')
            ->andWhere('s.subscribedOnRecallEmails = true')
            ->andWhere('(s.recallEmailSentAt IS NULL OR s.recallEmailSentAt < :from_day)')
            ->addGroupBy('s.user')
            ->andHaving('date_filter < :from_day')
            ->setParameter('from_day', new \DateTime(sprintf('-%d days', $this->input->getOption('days'))));
    }

    /**
     * @param Subscriber[] $subscribers
     */
    protected function processSubscribers(array $subscribers)
    {
        $daysCount = $this->input->getOption('days');
        foreach ($subscribers as $subscriber) {
            $this->sendEmail(
                'MetalUsersBundle::emails/users_recall_email.html.twig',
                $subscriber,
                array(
                    'user' => $subscriber->getUser(),
                    'daysCount' => $daysCount,
                    'country' => $subscriber->getUser() ? $subscriber->getUser()->getCountry() : null,
                    'newsletterType'    => 'recall'
                ),
                array(
                    'recallEmailSentAt' => new \DateTime()
                )
            );
        }
    }
}
