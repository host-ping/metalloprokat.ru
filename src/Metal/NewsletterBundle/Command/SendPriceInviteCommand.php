<?php

namespace Metal\NewsletterBundle\Command;

use Doctrine\ORM\QueryBuilder;
use Metal\NewsletterBundle\Entity\Subscriber;
use Metal\UsersBundle\Entity\UserAutoLogin;
use Symfony\Component\Console\Input\InputOption;

class SendPriceInviteCommand extends NewsletterCommandAbstract
{
    protected function configure()
    {
        parent::configure();

        $this
            ->setName('metal:newsletter:send-invite-price')
            ->addOption('days', null, InputOption::VALUE_OPTIONAL, null, 30)
            ->addOption('limit-per-day', null, InputOption::VALUE_OPTIONAL, null, 10000);
    }

    protected function filterSubscribersQb(QueryBuilder $qb)
    {
        $qb
            ->addSelect('COALESCE(MAX(uv.date), 0) AS date_filter')
            ->leftJoin('MetalStatisticBundle:UserVisiting', 'uv', 'WITH', 'user.company = uv.company')
            ->andWhere('s.user IS NOT NULL')
            ->andWhere('user.company IS NOT NULL')
            ->andWhere('s.subscribedOnPriceInviteEmails = true')
            ->andWhere('(s.priceInviteEmailSentAt IS NULL OR s.priceInviteEmailSentAt < :from_day)')
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
        $subscribersLimit = $this->input->getOption('limit-per-day');

        $userAutoLoginRepo = $this->em->getRepository('MetalUsersBundle:UserAutoLogin');

        $sentSubscribersCount  = $this->em->createQueryBuilder()
            ->select('COUNT(s)')
            ->from('MetalNewsletterBundle:Subscriber', 's')
            ->where('s.priceInviteEmailSentAt IS NOT NULL AND s.priceInviteEmailSentAt BETWEEN :date_start AND :date_end')
            ->setParameter('date_start', new \DateTime('today'))
            ->setParameter('date_end', new \DateTime())
            ->getQuery()
            ->getSingleScalarResult();

        if ($sentSubscribersCount > $subscribersLimit) {
            return;
        }

        foreach ($subscribers as $subscriber) {
            $userAutoLogin = $userAutoLoginRepo->createUserAutoLogin($subscriber->getUser(), UserAutoLogin::TARGET_EMAIL);

            $this->sendEmail(
                'MetalNewsletterBundle::product-appeal-to-price.html.twig',
                $subscriber,
                array(
                    'user' => $subscriber->getUser(),
                    'daysCount' => $daysCount,
                    'country' => $subscriber->getUser() ? $subscriber->getUser()->getCountry() : null,
                    'newsletterType' => 'price-invite-recall',
                    'userAutoLogin' => $userAutoLogin,
                ),
                array(
                    'priceInviteEmailSentAt' => new \DateTime()
                )
            );
        }
    }
}
