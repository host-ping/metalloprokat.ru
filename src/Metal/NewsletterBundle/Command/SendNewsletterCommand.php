<?php

namespace Metal\NewsletterBundle\Command;

use Doctrine\ORM\QueryBuilder;
use Metal\NewsletterBundle\Entity\Subscriber;
use Symfony\Component\Console\Input\InputArgument;

class SendNewsletterCommand extends NewsletterCommandAbstract
{
    protected function configure()
    {
        parent::configure();

        $this
            ->setName('metal:newsletter:send')
            ->addArgument('newsletter-id', InputArgument::REQUIRED, 'Идентификатор рассылки.');
    }

    protected function filterSubscribersQb(QueryBuilder $qb)
    {
        //!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
        // При импорте в базу подписчиков на новость в "Recipient" учитывать чтобы пользователь был подписан на новости.
        //!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
        $qb
            ->join('MetalNewsletterBundle:Recipient', 'r', 'WITH', 's.id = r.subscriber AND r.newsletter = :newsletter')
            ->setParameter('newsletter', $this->input->getArgument('newsletter-id'))
            ->andWhere('r.processedAt IS NULL');
    }

    /**
     * @param Subscriber[] $subscribers
     */
    protected function processSubscribers(array $subscribers)
    {
        $newsletterId = $this->input->getArgument('newsletter-id');
        $newsletter = $this->em->find('MetalNewsletterBundle:Newsletter', $newsletterId);

        if (!$newsletter) {
            $this->output->writeln(
                sprintf(
                    '%s: <error>Newsletter "%s" not exists.</error>',
                    date('d.m.Y H:i:s'),
                    $newsletterId
                )
            );

            return null;
        }

        $templating = $this->getContainer()->get('templating');
        if (!$templating->exists($newsletter->getTemplate())) {
            $this->output->writeln(
                sprintf(
                    '%s: <error>Template "%s" not exists.</error> <info>Newsletter ID "%d"</info>',
                    date('d.m.Y H:i:s'),
                    $newsletter->getTemplate(),
                    $newsletter->getId()
                )
            );

            return null;
        }

        foreach ($subscribers as $subscriber) {

            $hashKey = sha1(implode('-', array(time(), $subscriber->getId(), $newsletter->getId())));

            $this->sendEmail(
                $newsletter->getTemplate(),
                $subscriber,
                array(
                    'user' => $subscriber->getUser(),
                    'country' => $subscriber->getUser() ? $subscriber->getUser()->getCountry() : null,
                    'newsletterType' => 'news',
                    'hashKey' => $hashKey
                )
            );

            $this->em->createQueryBuilder()
                ->update('MetalNewsletterBundle:Recipient', 'r')
                ->set('r.processedAt', ':now')
                ->set('r.sentAt', ':now')
                ->set('r.hashKey', ':hashKey')
                ->setParameter('hashKey', $hashKey)
                ->setParameter('now', new \DateTime())
                ->andWhere('r.newsletter = :newsletter')
                ->setParameter('newsletter', $newsletterId)
                ->andWhere('r.subscriber = :subscriber')
                ->setParameter('subscriber', $subscriber->getId())
                ->getQuery()
                ->execute();
        }
    }

    protected function configureMessage(\Swift_Message $message)
    {
        $message->setFrom($this->getContainer()->getParameter('mailer_from_news'));
    }
}
