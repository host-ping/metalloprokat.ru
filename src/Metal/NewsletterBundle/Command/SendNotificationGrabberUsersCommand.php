<?php

namespace Metal\NewsletterBundle\Command;

use Doctrine\ORM\EntityManager;
use Metal\UsersBundle\Entity\User;
use Metal\UsersBundle\Entity\UserAutoLogin;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Security\Core\Encoder\EncoderFactory;

class SendNotificationGrabberUsersCommand extends ContainerAwareCommand
{
    /**
     * @var OutputInterface
     */
    protected $output;

    protected function configure()
    {
        parent::configure();

        $this
            ->setName('metal:newsletter:send-notification-grabber-users')
            ->addOption('limit', null, InputOption::VALUE_OPTIONAL, null, 500);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln(sprintf('%s: Start command "%s"', date('d.m.Y H:i:s'), $this->getName()));
        $this->output = $output;
        $em = $this->getContainer()->get('doctrine')->getManager();
        /* @var $em EntityManager */

        $users = $em->createQueryBuilder()
            ->select('u')
            ->from('MetalUsersBundle:User', 'u')
            ->join('MetalContentBundle:UserRegistrationWithParser', 'urwp', 'WITH', 'u.id = urwp.user')
            ->andWhere('urwp.notified = 0')
            ->setMaxResults((int)$input->getOption('limit'))
            ->getQuery()
            ->getResult();

        $this->processUsers($users);

        $output->writeln(sprintf('%s: Completed', date('d.m.Y H:i:s')));
    }

    /**
     * @param User[] $users
     */
    protected function processUsers(array $users)
    {
        $em = $this->getContainer()->get('doctrine')->getManager();
        /* @var $em EntityManager */

        $encoderFactory = $this->getContainer()->get('security.encoder_factory');
        /* @var $encoderFactory EncoderFactory */

        $userAutoLoginRepo = $em->getRepository('MetalUsersBundle:UserAutoLogin');

        foreach ($users as $user) {
            $user->newPassword = User::randomPassword();
            $encoder = $encoderFactory->getEncoder($user);
            $password = $encoder->encodePassword($user->newPassword, $user->getSalt());
            $user->setPassword($password);

            $userAutoLogin = $userAutoLoginRepo->createUserAutoLogin($user, UserAutoLogin::TARGET_EMAIL);

            $em->flush();

            $this->sendEmail(
                '@MetalNewsletter/product-appeal-to-price.html.twig',
                $user,
                array(
                    'user' => $user,
                    'country' => $user->getCountry(),
                    'userAutoLogin' => $userAutoLogin,
                    'newsletterType' => 'notify-old-stroy-user',
                )
            );

            $em->createQueryBuilder()
                ->update('MetalContentBundle:UserRegistrationWithParser', 'urwp')
                ->set('urwp.notified', 1)
                ->where('urwp.user = :user_id')
                ->setParameter('user_id', $user->getId())
                ->getQuery()
                ->execute();
        }
    }

    protected function sendEmail($template, User $user, array $context = array())
    {
        $container = $this->getContainer();
        $mailerHelper = $container->get('metal.newsletter.mailer');
        $this->output->writeln(
            sprintf(
                'Send email for user = %d. Memory usage: %sMb.',
                $user->getId(),
                round(memory_get_usage() / 1024 / 1024)
            )
        );

        try {
            $message = $mailerHelper->prepareMessage($template, array($user->getEmail() => $user->getFullName()), $context);

            $container->get('swiftmailer.mailer.delayed_mailer')->send($message);
        } catch (\Swift_RfcComplianceException $e) {
            $this->output->writeln(sprintf('%s: Bad email address for user id %s.', date('d.m.Y H:i:s'), $user->getId()));
        }
    }
}
