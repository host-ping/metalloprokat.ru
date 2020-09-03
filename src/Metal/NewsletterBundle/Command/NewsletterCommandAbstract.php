<?php

namespace Metal\NewsletterBundle\Command;

use Doctrine\DBAL\LockMode;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Metal\NewsletterBundle\Entity\Subscriber;
use Metal\NewsletterBundle\Helper\DefaultHelper;
use Metal\NewsletterBundle\Repository\SubscriberRepository;
use Metal\NewsletterBundle\Service\Mailer;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

abstract class NewsletterCommandAbstract extends ContainerAwareCommand
{
    /**
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     * @var SubscriberRepository
     */
    protected $subscribersRepository;

    /**
     * @var Mailer
     */
    protected $mailerHelper;

    /**
     * @var DefaultHelper
     */
    protected $newsletterHelper;

    /**
     * @var \Swift_Mailer
     */
    protected $mailer;

    /**
     * @var InputInterface
     */
    protected $input;

    /**
     * @var OutputInterface
     */
    protected $output;

    protected function configure()
    {
        $this
            ->addOption('limit', null, InputOption::VALUE_OPTIONAL, null, 400)
            ->addOption('subscriber-email', null, InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY)
            ->addOption('email', null, InputOption::VALUE_OPTIONAL, 'Allow test emails in services like https://www.mail-tester.com/ . Requires pass subscriber-email option also.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln(sprintf('%s: Start command "%s"', date('d.m.Y H:i:s'), $this->getName()));

        $container = $this->getContainer();
        $context = $container->get('router')->getContext();
        $context->setHost($container->getParameter('base_host'));

        $this->em = $container->get('doctrine')->getManager();
        $this->em->getRepository('MetalProjectBundle:Site')->disableLogging();
        $this->subscribersRepository = $this->em->getRepository('MetalNewsletterBundle:Subscriber');
        $this->mailerHelper = $container->get('metal.newsletter.mailer');
        $this->newsletterHelper = $this->getContainer()->get('brouzie.helper_factory')->get('MetalNewsletterBundle');
        $this->mailer = $container->get('swiftmailer.mailer.delayed_mailer');
        $this->input = $input;
        $this->output = $output;

        $subscribers = $this->getSubscribers((int)$input->getOption('limit'), (array)$input->getOption('subscriber-email'));

        if (!count($subscribers)) {
            $output->writeln(sprintf('%s: No subscribers to process.', date('d.m.Y H:i:s')));

            return;
        }

        $this->processSubscribers($subscribers);

        $output->writeln(sprintf('%s: Completed', date('d.m.Y H:i:s')));
    }

    protected function sendEmail($template, Subscriber $subscriber, array $context = array(), array $dataToUpdate = array())
    {
        $email = $subscriber->getEmail();
        if ($this->input->getOption('subscriber-email') && $this->input->getOption('email')) {
            $email = $this->input->getOption('email');
        }

        if ('@ukr.net' === substr($email, -strlen('@ukr.net'))) {
            $this->subscribersRepository->releaseSubscriber($subscriber, $dataToUpdate);

            return;
        }

        $this->output->writeln(
            sprintf(
                'Send email for subscriber = %d. Memory usage: %sMb.',
                $subscriber->getId(),
                round(memory_get_usage() / 1024 / 1024)
            )
        );

        $context['subscriber'] = $subscriber;

        try {
            $message = $this->mailerHelper->prepareMessage($template, $email, $context);
            if (!empty($context['newsletterType'])) {
                $unsubscribeUrl = $this->newsletterHelper->generateUnsubscribeUrl(
                    $email,
                    $context['newsletterType']
                );
                $message->getHeaders()->addTextHeader('List-Unsubscribe', $unsubscribeUrl);
            }

            $this->configureMessage($message);

            $companiesForLogging = array(
                2041670,
                2048721,
                7732,
            );

            if ($subscriber->getUser() && $subscriber->getUser()->getCompany()) {
                $companyId = $subscriber->getUser()->getCompany()->getId();
                if (in_array($companyId, $companiesForLogging)) {
                    file_put_contents(
                        $this->getContainer()->getParameter('kernel.logs_dir').'/'.$companyId.'-'.time().'.eml',
                        $message->toString()
                    );
                }
            }

            $this->mailer->send($message);
        } catch (\Swift_RfcComplianceException $e) {
            $this->output->writeln(sprintf('%s: Bad email address for subscriber id %s.', date('d.m.Y H:i:s'), $subscriber->getId()));
            $subscriber->setIsInvalid(true);
            $this->em->flush($subscriber);
        }

        $this->subscribersRepository->releaseSubscriber($subscriber, $dataToUpdate);
    }

    /**
     * @param int $limit
     * @param array $subscribersEmails
     *
     * @return Subscriber[]
     */
    protected function getSubscribers($limit, array $subscribersEmails = array())
    {
        if ($subscribersEmails) {
            return $this->subscribersRepository->findBy(array('email' => $subscribersEmails));
        }

        $this->output->writeln(sprintf('%s: Reset outdated task hash.', date('d.m.Y H:i:s')));
        $this->subscribersRepository->resetOutdatedSubscribers();

        $taskHash = $this->subscribersRepository->getTaskHash();
        $this->output->writeln(sprintf('%s: Update task hash subscribers.', date('d.m.Y H:i:s')));

        $this->em->transactional(function(EntityManager $em) use ($limit, $taskHash) {
            $subscribersQb = $em->createQueryBuilder()
                ->select('s.id')
                ->from('MetalNewsletterBundle:Subscriber', 's', 's.id')
                ->leftJoin('s.user', 'user')
                ->andWhere('(user.isEnabled = true OR s.user IS NULL)')
                ->andWhere('s.deleted = false')
                ->andWhere('s.taskHash IS NULL')
                ->andWhere('s.isInvalid = false')
                ->andWhere('s.bouncedAt IS NULL')
                ->orderBy('s.createdAt', 'DESC')
                ->setMaxResults($limit);

            $this->filterSubscribersQb($subscribersQb);

            $subscribersIds = $subscribersQb
                ->getQuery()
                ->setLockMode(LockMode::PESSIMISTIC_WRITE)
                ->getResult();

            $em->getRepository('MetalNewsletterBundle:Subscriber')->createQueryBuilder('s')
                ->update('MetalNewsletterBundle:Subscriber', 's')
                ->set('s.taskHash', ':taskHash')
                ->set('s.taskHashCreatedAt', ':now')
                ->andWhere('s.id IN (:subscribers_ids)')
                ->setParameter('taskHash', $taskHash)
                ->setParameter('now', new \DateTime())
                ->setParameter('subscribers_ids', array_keys($subscribersIds))
                ->getQuery()
                ->execute();
        });

        $this->output->writeln(sprintf('%s: Done.', date('d.m.Y H:i:s')));

        return $this->subscribersRepository->getSubscribersByTaskHash($taskHash);
    }

    protected function configureMessage(\Swift_Message $message)
    {
        // override this method for your needs in subclasses
    }

    abstract protected function filterSubscribersQb(QueryBuilder $qb);

    /**
     * @param Subscriber[] $subscribers
     */
    abstract protected function processSubscribers(array $subscribers);
}
