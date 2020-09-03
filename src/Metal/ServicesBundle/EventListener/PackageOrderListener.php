<?php

namespace Metal\ServicesBundle\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Metal\NewsletterBundle\Service\Mailer;
use Metal\ServicesBundle\Entity\PackageOrder;
use Symfony\Component\DependencyInjection\ContainerInterface;

class PackageOrderListener
{
    /**
     * @var array
     */
    protected $emails;

    /** @var ContainerInterface */
    protected $container;

    public function __construct(ContainerInterface $container, array $emails)
    {
        // Передаем container чтоб не было "Circular reference"
        $this->container = $container;
        $this->emails = $emails;
    }

    public function postPersist(PackageOrder $packageOrder, LifecycleEventArgs $args)
    {
        $mailer = $this->container->get('metal.newsletter.mailer');

        $country = null;
        if ($packageOrder->getCompany() && $packageOrder->getCompany()->getCity()) {
            $country = $packageOrder->getCompany()->getCity()->getCountry();
        } elseif ($packageOrder->getCity()) {
            $country = $packageOrder->getCity()->getCountry();
        }

        $companyTitle = '';
        if ($packageOrder->getCompany()) {
            $companyTitle = $packageOrder->getCompany()->getTitle();
        } elseif ($packageOrder->getCompanyTitle()) {
            $companyTitle = $packageOrder->getCompanyTitle();
        }

        try {
            foreach ($this->emails as $email) {
                $mailer->sendMessage(
                    '@MetalServices/emails/creation_demand_package.html.twig',
                    $email,
                    array(
                        'package' => $packageOrder,
                        'country' => $country,
                        'companyTitle' => $companyTitle,
                    )
                );
            }
        } catch (\Swift_RfcComplianceException $e) {
        }
    }
}
