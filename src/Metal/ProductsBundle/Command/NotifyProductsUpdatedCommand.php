<?php

namespace Metal\ProductsBundle\Command;

use Doctrine\ORM\EntityManager;
use Metal\CompaniesBundle\Entity\Company;
use Metal\NewsletterBundle\Entity\Subscriber;
use Metal\NewsletterBundle\Service\Mailer;
use Metal\ProductsBundle\Entity\Product;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class NotifyProductsUpdatedCommand extends ContainerAwareCommand
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
        $this->setName('metal:products:notify-updated');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->mailerHelper = $this->getContainer()->get('metal.newsletter.mailer');
        $this->mailer = $this->getContainer()->get('swiftmailer.mailer.delayed_mailer');
        $newsletterHelper = $this->getContainer()->get('brouzie.helper_factory')->get('MetalNewsletterBundle');

        $output->writeln(sprintf('Sending newsletter'));

        $em = $this->getContainer()->get('doctrine')->getManager();
        /* @var $em EntityManager */

        $productsCountToCompanies = $em->getRepository('MetalCompaniesBundle:Company')->createQueryBuilder('c')
            ->select('c AS _company')
            ->addSelect('COUNT(p.id) AS _count')
            ->addSelect('m.id AS productAuthorId')
            ->join('MetalProductsBundle:Product', 'p', 'WITH', 'p.company = c.id')
            ->join('p.productLog', 'pl')
            ->join('pl.updatedBy', 'u')
            ->join('pl.createdBy', 'm')
            ->andWhere('u.additionalRoleId <> 0')
            ->andWhere('p.checked = :status')
            ->andWhere('p.moderatedAt > :date')
            ->addOrderBy('c.codeAccess', 'DESC')
            ->addOrderBy('m.additionalRoleId', 'ASC')
            ->setParameter('status', Product::STATUS_CHECKED)
            ->setParameter('date', new \DateTime('today'))
            ->groupBy('c.id')
            ->getQuery()
            ->getResult();

        $currentDate = $this->getContainer()->get('brouzie.helper_factory')->get('MetalProjectBundle:Formatting')->formatDate(new \DateTime(), 'full');
        $userRepository = $em->getRepository('MetalUsersBundle:User');
        $subscriberRepository = $em->getRepository('MetalNewsletterBundle:Subscriber');
        foreach ($productsCountToCompanies as $productsCountToCompany) {
            $company = $productsCountToCompany['_company'];
            /* @var $company Company */

            $output->writeln(sprintf('Process company_id=%d', $company->getId()));

            $companyUsers = $userRepository->getApprovedUsers($company);
            $users = array();
            foreach ($companyUsers as $user) {
                if ($user->isMainUserForCompany()) {
                    $users[] = $user;
                } elseif ($user->getId() == $productsCountToCompany['productAuthorId']) {
                    $users[] = $user;
                }
            }

            $subscribers = $subscriberRepository->findBy(array('user' => $users));

            foreach ($subscribers as $subscriber) {
                if (!$subscriber->getSubscribedOnProductsUpdate()) {
                    continue;
                }

                try {
                    $message = $this->mailerHelper->prepareMessage('@MetalProducts/emails/notify_product_updated.html.twig',
                        $subscriber->getEmail(),
                        array(
                            'user' => $subscriber->getUser(),
                            'country' => $company->getCountry(),
                            'company' => $company,
                            'productsCount' => $productsCountToCompany['_count'],
                            'currentDate' => $currentDate,
                            'newsletterType' => 'products-updated'
                        )
                    );

                    $unsubscribeUrl = $newsletterHelper->generateUnsubscribeUrl(
                        $subscriber->getEmail(),
                        'products-updated'
                    );
                    $message->getHeaders()->addTextHeader('List-Unsubscribe', $unsubscribeUrl);

                    // шлем почту через отложенный мейлер
                    $this->mailer->send($message);
                } catch (\Swift_RfcComplianceException $e) {

                }
            }
        }

        $output->writeln(sprintf('End command'));
    }
}
