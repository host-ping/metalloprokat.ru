<?php

namespace Metal\NewsletterBundle\Command;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Metal\NewsletterBundle\Entity\EmailDeferred;
use Metal\NewsletterBundle\Entity\Subscriber;
use Metal\NewsletterBundle\Repository\SubscriberRepository;
use Metal\ProjectBundle\Repository\SiteRepository;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ProcessSendMailLogCommand extends ContainerAwareCommand
{
    const DEFERRED_COUNT = 5;

    /**
     * @var $em EntityManager
     */
    private $em;

    /**
     * @var SubscriberRepository
     */
    private $subscriberRepository;

    /**
     * @var EntityRepository
     */
    private $emailDeferredRepository;

    private $mailerFrom;

    protected function configure()
    {
        $this
            ->setName('metal:newsletter:process-sendmail-log')
            ->addArgument('file', InputArgument::REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln(sprintf('%s: Start command "%s"', date('d.m.Y H:i:s'), $this->getName()));

        $file = $input->getArgument('file');

        $this->mailerFrom = $this->getContainer()->getParameter('mailer_from');

        $this->em = $this->getContainer()->get('doctrine.orm.default_entity_manager');

        $siteRepository =  $this->em->getRepository('MetalProjectBundle:Site');
        /* @var $siteRepository SiteRepository */
        $siteRepository->disableLogging();

        $this->subscriberRepository = $this->em->getRepository('MetalNewsletterBundle:Subscriber');
        $this->emailDeferredRepository = $this->em->getRepository('MetalNewsletterBundle:EmailDeferred');

        $fh = fopen($file, 'r');
        $i = 0;
        while (!feof($fh)) {
            $line = fgets($fh);

            $email = $this->getEmail($line);
            if (!$email) {
                continue;
            }

            $subscriber = $this->subscriberRepository->findOneBy(array('email' => $email, 'bouncedAt' => null));
            if (!$subscriber) {
                $output->writeln(sprintf('%s: Not found subscriber for email "%s" or already bounced.', date('d.m.Y H:i:s'), $email));

                continue;
            }

            if (preg_match('/status=bounced/ui', $line)) {
                $this->handleBounced($subscriber, $line, $output);
                $i++;
            }

            if (preg_match('/status=deferred/ui', $line) && self::isAllowAdditionDeferred($line)) {
                $this->handleDeferred($subscriber, $line, $output);
                $i++;
            }

            if (($i % 100) === 0) {
                $this->em->flush();
                $this->em->clear();
            }
        }

        $this->em->flush();
        $this->em->clear();

        fclose($fh);
        $output->writeln(sprintf('%s Unsubscribing users where deferred emails count >= %d.', date('Y-m-d H:i'), self::DEFERRED_COUNT));

        $this->unsubscribeByDeferredEmails($output);

        $output->writeln(sprintf('End command. %s at %s', $this->getName(), date('Y-m-d H:i')));

        $siteRepository->restoreLogging();
    }

    private function unsubscribeByDeferredEmails(OutputInterface $output)
    {
        $emailDeferred = $this->em->createQueryBuilder()
            ->select('emailDeferred.email')
            ->from('MetalNewsletterBundle:EmailDeferred', 'emailDeferred', 'emailDeferred.email')
            ->where('emailDeferred.unsubscribed = false')
            ->groupBy('emailDeferred.email')
            ->having('COUNT(emailDeferred) >= :allowed_count')
            ->setParameter('allowed_count', self::DEFERRED_COUNT)
            ->getQuery()
            ->getArrayResult()
        ;

        $emails = array_keys($emailDeferred);
        unset($emailDeferred);
        $conn = $this->em->getConnection();

        $i = 0;
        foreach ($emails as $email) {
            $subscriber = $this->subscriberRepository->findOneBy(array('email' => $email));
            /* @var $subscriber Subscriber */

            if (!$subscriber) {
                $output->writeln(sprintf('%s: Not found subscriber for email: %s.', date('d.m.Y H:i:s'), $email));

                continue;
            }

            $log = $conn->fetchColumn('SELECT GROUP_CONCAT(ed.log SEPARATOR "\n") AS log FROM email_deferred AS ed WHERE ed.email = :email', array('email' => $email));

            $subscriber->setBouncedAt(new \DateTime());
            $subscriber->setBounceLog($log);

            $conn->executeUpdate('UPDATE email_deferred SET unsubscribed = true WHERE email = :email', array('email' => $email));

            if (($i % 50) === 0) {
                $this->em->flush();
                $this->em->clear();
            }

            $i++;
        }

        $this->em->flush();
        $this->em->clear();
    }

    private function handleBounced(Subscriber $subscriber, $line, OutputInterface $output)
    {
        $output->writeln(sprintf('%s: Found bad subscriber id: %d', date('d.m.Y H:i:s'), $subscriber->getId()));

        $subscriber->setBouncedAt(new \DateTime());
        $subscriber->setBounceLog($line);
    }

    private function handleDeferred(Subscriber $subscriber, $line, OutputInterface $output)
    {
        $date = $this->getDate($line);
        if (!$date) {
            $output->writeln(sprintf('%s: Not date.', date('d.m.Y H:i:s')));

            return;
        }

        $email = $subscriber->getEmail();
        if ($this->emailDeferredRepository->findOneBy(array('email' => $email, 'dateSend' => $date))) {
            $output->writeln(sprintf('%s: Already there email: %s.', date('d.m.Y H:i:s'), $email));

            return;
        }

        $output->writeln(sprintf('%s: Add deferred email: %s', date('d.m.Y H:i:s'), $email));

        $emailDeferred = new EmailDeferred();
        $emailDeferred->setDateSend($date);
        $emailDeferred->setEmail($email);
        $emailDeferred->setLog($line);

        $this->em->persist($emailDeferred);
        // флашим прямо сейчас, что бы findOneBy находил бы только добавленные строки
        $this->em->flush($emailDeferred);
    }

    private function getEmail($line)
    {
        preg_match('/to\=\<(.*?)\>\,/ui', $line, $matches);
        if ($matches && !isset($this->mailerFrom[$matches[1]])) {
            return $matches[1];
        }

        return null;
    }

    private function getDate($line)
    {
        //TODO: почему два шаблона? Попробовать использовать \DateTime::createFromFormat();
        preg_match('/^(\w{2,8}\s\d{2}\s\d{2}:\d{2}:\d{2})/ui', $line, $matchesDate);
        if ($matchesDate) {
            return new \DateTime($matchesDate[1]);
        }

        preg_match('/^(\w{3,10}\s{1,2}\d\s\d{2}:\d{2}:\d{2})/ui', $line, $matchesDate);
        if ($matchesDate) {
            return new \DateTime($matchesDate[1]);
        }

        return null;
    }

    private function isAllowAdditionDeferred($subject)
    {
        $list = array(
            'user unknown or mailbox full',
            'user unknown or mailbox full ',
            'Recipient address rejected\: undeliverable address: unknown user\:',
            'Recipient address rejected\: unverified address\: unknown user\: \"antons\"',
            'Recipient address rejected\: unverified address\: host',
            'Recipient address rejected\: User unknown in local recipient table',
            'Server configuration error',
            'Host or domain name not found',
            'Domain \(.*?\) is suspended',
            'No route to host',
            'mail receiving disabled',
            'Mailbox has exceeded the limit',
            '451 Domain .*? is over quota, try again later',
            'Recipient address rejected\: END Diskspace Quota for user',
            'Recipient address rejected\: Mailbox is full',
            '451 user quota exceeded',
            'Mailbox has exceeded the limit',
            '452 Message for <.*?> would exceed mailbox quota'
        );

        foreach ($list as $row) {
            if (preg_match(sprintf('/%s/ui', $row), $subject)) {
                return true;
            }
        }

        return false;
    }

}
