<?php

namespace Metal\DemandsBundle\Command;

use Doctrine\ORM\QueryBuilder;
use Metal\DemandsBundle\DataFetching\Spec\DemandFilteringSpec;
use Metal\NewsletterBundle\Command\NewsletterCommandAbstract;
use Metal\NewsletterBundle\Entity\Subscriber;
use Metal\DemandsBundle\Entity\Demand;

use Metal\ServicesBundle\Entity\Package;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\LockHandler;

class DemandsNewsletterCommand extends NewsletterCommandAbstract
{
    protected function configure()
    {
        parent::configure();

        $this
            ->setName('metal:demands:send-newsletter')
            ->addOption('days', null, InputOption::VALUE_OPTIONAL, null, 1)
            ->addOption('clients-only', null, InputOption::VALUE_NONE)
            ->addOption('hourly-subscribers', null, InputOption::VALUE_NONE)
            ->addOption('weekly-subscribers', null, InputOption::VALUE_NONE);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $lock = new LockHandler('sphinxy:populate-index:demands');
        if (!$lock->lock()) {
            $output->writeln(
                sprintf('%s: The command "sphinxy:populate-index" is running for index "demands".', date('d.m.Y H:i:s'))
            );

            return 0;
        }

        return parent::execute($input, $output);
    }


    protected function filterSubscribersQb(QueryBuilder $qb)
    {
        if ($this->input->getOption('hourly-subscribers')) {
            $qb
                ->andWhere('(s.demandsDigestSentAt IS NULL OR s.demandsDigestSentAt < :thisHour)')
                ->andWhere('s.subscribedForDemands = :hourly')
                ->join('user.company', 'c')
                ->andWhere('(s.user IS NOT NULL)')
                ->setParameter('thisHour', new \DateTime(date('d-m-Y H:00:00')), 'datetime')
                ->setParameter('hourly', Subscriber::DEMAND_SUBSCRIPTION_PERIODICITY_HOURLY)
                ->orderBy('c.sprosEndsAt', 'DESC');

            if ($this->input->getOption('clients-only')) {
                $qb
                    ->andWhere('(c.sprosEndsAt IS NOT NULL OR c.codeAccess <> :code_access)')
                    ->setParameter('code_access', Package::BASE_PACKAGE);
            }
        } elseif ($this->input->getOption('weekly-subscribers')) {
            $qb
                ->andWhere('(s.demandsDigestSentAt IS NULL OR s.demandsDigestSentAt < :thisHour)')
                ->andWhere('s.subscribedForDemands = :weekly')
                ->join('user.company', 'c')
                ->andWhere('(s.user IS NOT NULL)')
                ->setParameter('thisHour', new \DateTime(date('d-m-Y H:00:00')), 'datetime')
                ->setParameter('weekly', Subscriber::DEMAND_SUBSCRIPTION_PERIODICITY_WEEKLY)
                ->orderBy('c.sprosEndsAt', 'DESC');
            if ($this->input->getOption('clients-only')) {
                $qb
                    ->andWhere('(c.sprosEndsAt IS NOT NULL OR c.codeAccess <> :code_access)')
                    ->setParameter('code_access', Package::BASE_PACKAGE);
            }
        } else {
            $qb
                ->andWhere('(s.demandsDigestSentAt IS NULL OR s.demandsDigestSentAt < :now)')
                ->andWhere('s.subscribedForDemands = :daily')
                ->setParameter('now', new \DateTime(), 'date')
                ->setParameter('daily', Subscriber::DEMAND_SUBSCRIPTION_PERIODICITY_DAILY)
                ->andWhere('(s.user IS NULL OR user.isEnabled = true)');
            if ($this->input->getOption('clients-only')) {
                $qb
                    ->andWhere('s.user IS NOT NULL')
                    ->join('user.company', 'c')
                    ->andWhere('(c.sprosEndsAt IS NOT NULL OR c.codeAccess <> :code_access)')
                    ->setParameter('code_access', Package::BASE_PACKAGE);
            }
        }
    }

    /**
     * @param Subscriber[] $subscribers
     */
    protected function processSubscribers(array $subscribers)
    {
        $daysCount = $this->input->getOption('days');

        $dateFinish = new \DateTime();
        $dateStart = new \DateTime(sprintf('-%d days', $this->input->getOption('days')));

        $this->output->writeln(sprintf('%s: Get demands.', date('d.m.Y H:i:s')));
        $demands = $this->getDemands($dateStart, $dateFinish);

        if (!count($demands)) {
            $this->output->writeln(sprintf('%s: Completed, no demands.', date('d.m.Y H:i:s')));
            return;
        }

        $demandCollection = $this->normalizeDemandsCollection($demands);

        $usersIds = array();
        foreach ($subscribers as $subscriber) {
            if ($subscriber->getUser()) {
                $usersIds[] = $subscriber->getUser()->getId();
            }
        }
        $categoriesIds = $this->em->getRepository(
            'MetalDemandsBundle:DemandSubscriptionCategory'
        )->getCategoryIdsPerUser($usersIds);
        $territorialIds = $this->em->getRepository(
            'MetalDemandsBundle:DemandSubscriptionTerritorial'
        )->getSubscribedTerritorialIdsPerUser($usersIds);
        $demandsDataFetcher = $this->getContainer()->get('metal.demands.data_fetcher');

        foreach ($subscribers as $subscriber) {
            $user = $subscriber->getUser();

            $demandCollectionToSend = $demandCollection;
            if ($user) {
                $criteria = new DemandFilteringSpec();
                $criteria->country($user->getCountry());
                if (!empty($categoriesIds[$user->getId()])) {
                    $criteria->categoriesIds($categoriesIds[$user->getId()]);
                }

                if (!empty($territorialIds[$user->getId()])) {
                    $criteria->territorialStructureIds($territorialIds[$user->getId()]);
                }

                $criteria->dateFrom($subscriber->getDemandsDigestSentAt());

                $criteria->ids(array_keys($demands));

                $companiesForLogging = array(
                    2041670,
                    2048721,
                    7732,
                );
                if ($user->getCompany()) {
                    $companyId = $user->getCompany()->getId();
                    if (in_array($companyId, $companiesForLogging)) {
                        file_put_contents(
                            $this->getContainer()->getParameter('kernel.logs_dir').'/'.$companyId.'-'.time().'.txt',
                            var_export($criteria, true)
                        );
                    }
                }

                $resultSet = $demandsDataFetcher->getResultSetByCriteria($criteria, null, 9999999);
                $demandsIds = array_column(iterator_to_array($resultSet), 'id', 'id');

                $demandsFromSend = array_intersect_key($demands, $demandsIds);
                $demandCollectionToSend = $this->normalizeDemandsCollection($demandsFromSend);
                if (!$demandCollectionToSend) {
                    //TODO: добавить колонку с пометками, что заявок нет
                    $this->subscribersRepository->releaseSubscriber(
                        $subscriber,
                        array('demandsDigestSentAt' => new \DateTime())
                    );
                    $this->output->writeln(
                        sprintf('%s: No demands for subscriber id %s.', date('d.m.Y H:i:s'), $subscriber->getId())
                    );

                    continue;
                }
            }

            if ($subscriber->getSubscribedForDemands() == Subscriber::DEMAND_SUBSCRIPTION_PERIODICITY_HOURLY) {
                $forHours = 1;
            } elseif ($subscriber->getSubscribedForDemands() == Subscriber::DEMAND_SUBSCRIPTION_PERIODICITY_WEEKLY) {
                $forHours = $daysCount * 24 * 7;
            } else {
                $forHours = $daysCount * 24;
            }

            $this->sendEmail(
                'MetalDemandsBundle::emails/daily_newsletter.html.twig',
                $subscriber,
                array(
                    'demandCollection' => $demandCollectionToSend,
                    'forHours' => $forHours,
                    'dateStart' => $dateStart,
                    'dateFinish' => $dateFinish,
                    'country' => $subscriber->getUser() ? $subscriber->getUser()->getCountry() : null,
                    'newsletterType' => 'demands',
                ),
                array(
                    'demandsDigestSentAt' => new \DateTime(),
                )
            );
        }
    }

    /**
     * @param \DateTime $dateStart
     * @param \DateTime $dateFinish
     *
     * @return Demand[]
     */
    private function getDemands(\DateTime $dateStart, \DateTime $dateFinish)
    {
        $demands = $this->em->createQueryBuilder()
            ->select('d')
            ->from('MetalDemandsBundle:Demand', 'd', 'd.id')
            ->join('d.category', 'c')
            ->addSelect('c')
            ->andWhere('d.moderatedAt >= :date_start')
            ->andWhere('d.moderatedAt < :current_date')
            ->andWhere('d.moderatedAt IS NOT NULL')
            ->andWhere('d.deletedAt IS NULL')
            ->setParameter('date_start', $dateStart)
            ->setParameter('current_date', $dateFinish)
            ->getQuery()
            ->getResult();

        $this->output->writeln(sprintf('%s: Attach cities.', date('d.m.Y H:i:s')));
        $this->em->getRepository('MetalDemandsBundle:Demand')->attachCitiesToDemands($demands);
        $this->output->writeln(sprintf('%s: Attach demand items.', date('d.m.Y H:i:s')));
        $this->em->getRepository('MetalDemandsBundle:DemandItem')->attachDemandItems($demands);

        return $demands;
    }

    /**
     * @param Demand[] $demands
     *
     * @return Demand[]
     */
    private function normalizeDemandsCollection(array $demands)
    {
        $demandCollection = array();
        foreach ($demands as $demandId => $demand) {
            $demandCollection[$demand->getCategory()->getRootCategory()->getId()][] = $demand;
        }

        return $demandCollection;
    }
}
