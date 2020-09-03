<?php

namespace Metal\CompaniesBundle\Command;

use Doctrine\ORM\QueryBuilder;
use Metal\NewsletterBundle\Command\NewsletterCommandAbstract;
use Metal\NewsletterBundle\Entity\Subscriber;
use Symfony\Component\Console\Input\InputOption;

class RecallActualizeCompanyNameCommand extends NewsletterCommandAbstract
{
    protected function configure()
    {
        parent::configure();

        $this
            ->setName('metal:companies:recall-actualize-name')
            ->addOption('from-subscriber', InputOption::VALUE_OPTIONAL)
        ;
    }

    protected function filterSubscribersQb(QueryBuilder $qb)
    {
        $qb
            ->andWhere('s.user IS NOT NULL')
            ->join('user.company', 'c')
            ->andWhere("c.title = ''")
            ->orderBy('s.id', 'DESC');

        if ($subscriberId = $this->input->getOption('from-subscriber')) {
            $qb
                ->andWhere('s.id >= :subscriber')
                ->setParameter('subscriber', $subscriberId);
        }
    }

    /**
     * @param Subscriber[] $subscribers
     */
    protected function processSubscribers(array $subscribers)
    {
        foreach ($subscribers as $subscriber) {
            $this->sendEmail(
                'MetalCompaniesBundle::emails/recall_actualize_name.html.twig',
                $subscriber,
                array(
                    'user' => $subscriber->getUser(),
                    'country' => $subscriber->getUser() ? $subscriber->getUser()->getCountry() : null,
                )
            );
        }
    }
}
