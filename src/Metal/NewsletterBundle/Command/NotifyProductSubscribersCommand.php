<?php

namespace Metal\NewsletterBundle\Command;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityNotFoundException;
use Metal\NewsletterBundle\Entity\Subscriber;
use Metal\NewsletterBundle\Service\Mailer;
use Metal\TerritorialBundle\Entity\Country;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class NotifyProductSubscribersCommand extends ContainerAwareCommand
{
    /**
     * @var \Swift_Mailer
     */
    private $mailer;

    /**
     * @var Mailer
     */
    private $mailerHelper;

    protected function configure()
    {
        $this
            ->setName('metal:notify:product-send')
            ->addOption('user-id', null, InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->mailerHelper = $this->getContainer()->get('metal.newsletter.mailer');
        $this->mailer = $this->getContainer()->get('swiftmailer.mailer.delayed_mailer');

        $output->writeln(sprintf('Sending newsletter'));

        $status = $this->processNewsletter($input->getOption('user-id'));

        $output->writeln(
            sprintf(
                'Processed newsletter : success=<comment>%d</comment>, error=<error>%d</error>',
                $status['success'],
                $status['error']
            )
        );
    }

    protected function processNewsletter(array $usersIds = null)
    {
        $em = $this->getContainer()->get('doctrine')->getManager();
        /* @var $em EntityManager */
        $subscriberRepository = $em->getRepository('MetalNewsletterBundle:Subscriber');

        $skip = 0;
        $limit = 300;
        $status = array(
            'success' => 0,
            'error' => 0
        );

        if ($usersIds) {
            $subscribers = array($em->getRepository('MetalNewsletterBundle:Subscriber')->findOneBy(array('user' => $usersIds)));
        } else {
            $subscribers = $subscriberRepository->createQueryBuilder('s')
                ->join('s.user', 'u')
                ->join('u.company', 'c')
                ->andWhere("u.password = ''")
                ->andWhere('s.isInvalid = 0')
                ->addOrderBy('c.codeAccess', 'DESC')
                ->addOrderBy('u.id', 'DESC')
                ->setMaxResults($limit)
                ->setFirstResult($skip)
                ->getQuery()
                ->getResult();
        }

        foreach ($subscribers as $subscriber) {
            /* @var $subscriber Subscriber */
            $email = $subscriber->getEmail();
            $email = trim(trim($email), "'");
            $user = $subscriber->getUser();
            $company = $user->getCompany();

            $country = null;
            try {
                $country = $user->getCountry();
                $country->getTitle();
            } catch (EntityNotFoundException $e) {
                $country = null;
            }

            if (!$country) {
                try {
                    $country = $company->getCountry();
                    $country->getTitle();
                } catch (EntityNotFoundException $e) {
                    $country = null;
                }
            }

            if (!$country) {
                $country = $em->getRepository('MetalTerritorialBundle:Country')->find(Country::COUNTRY_ID_RUSSIA);
            }

            $this->getContainer()->get('metal.users.user_service')->updatePassword($user);
            try {
                $message = $this->mailerHelper->prepareMessage('MetalNewsletterBundle::product-reloaded.html.twig', $email,
                    array(
                        'userEmail' => $email,
                        'user' => $user,
                        'country' => $country
                    )
                );
                // шлем почту через отложенный мейлер
                $this->mailer->send($message);

                $status['success']++;
            } catch (\Swift_RfcComplianceException $e) {
                $subscriber->setIsInvalid(true);
                $status['error']++;
            }

            $em->flush($subscriber);
            $em->flush($user);
        }

        return $status;
    }
}
