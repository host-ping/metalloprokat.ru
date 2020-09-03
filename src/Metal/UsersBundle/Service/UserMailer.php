<?php

namespace Metal\UsersBundle\Service;

use Doctrine\ORM\EntityManager;
use Metal\CompaniesBundle\Entity\Company;
use Metal\NewsletterBundle\Service\Mailer;
use Metal\ServicesBundle\Entity\Package;
use Metal\ServicesBundle\Entity\ValueObject\ServicePeriodTypesProvider;
use Metal\UsersBundle\Entity\User;

class UserMailer
{
    /**
     * @var Mailer
     */
    private $mailer;

    /**
     * @var EntityManager
     */
    private $em;

    private $emailFrom;

    private $registrationEmailTemplate;

    public function __construct(Mailer $mailer, EntityManager $em, $emailFrom, $registrationEmailTemplate)
    {
        $this->mailer = $mailer;
        $this->em = $em;
        $this->emailFrom = $emailFrom;
        $this->registrationEmailTemplate = $registrationEmailTemplate;
    }

    public function notifyOnRegistration(User $user, $email = null)
    {
        $packageRepository = $this->em->getRepository('MetalServicesBundle:Package');
        $packages = $packageRepository->getPackages(true);
        $serviceItemsTree = $packageRepository->getServiceItemsTree();
        $periods = ServicePeriodTypesProvider::getAllPeriodsNameByIds();

        try {
            $this->mailer->sendMessage(
                $this->registrationEmailTemplate,
                $email ?: $user->getEmail(),
                array(
                    'user' => $user,
                    'country' => $user->getCountry(),
                    'packages' => $packages,
                    'serviceItemsTree' => $serviceItemsTree,
                    'periods' => $periods,
                ),
                $this->emailFrom
            );
        } catch (\Swift_RfcComplianceException $e) {
        }
    }

    public function notifyMainUserOnRegistration(User $user)
    {
        try {
            if ($user->getCompany() && !$user->isApproved()
                && $user->getId() != $user->getCompany()->getCompanyLog()->getCreatedBy()->getId()) {
                $this->mailer->sendMessage(
                    'MetalUsersBundle::emails/to_main_user.html.twig',
                    $user->getCompany()->getCompanyLog()->getCreatedBy()->getEmail(),
                    array(
                        'user' => $user,
                        'mainUser' => $user->getCompany()->getCompanyLog()->getCreatedBy(),
                        'country' => $user->getCompany()->getCompanyLog()->getCreatedBy()->getCountry()
                    )
                );
            }
        } catch (\Swift_RfcComplianceException $e) {
        }
    }

    public function notifyOfAccessionToCompany(
        User $user,
        $approved,
        $isAdminpanel = false,
        Company $originalCompany = null
    ) {
        try {
            if ($approved) {
                $this->mailer->sendMessage(
                    'MetalUsersBundle::emails/accession_employee.html.twig',
                    $user->getEmail(),
                    array(
                        'user' => $user,
                        'isAdminpanel' => $isAdminpanel,
                        'country' => $user->getCountry()
                    )
                );
            } else {
                if ($originalCompany) {
                    $company = $originalCompany;
                } else {
                    $company = $user->getCompany();
                }

                $this->mailer->sendMessage(
                    'MetalUsersBundle::emails/disconnecting_employee.html.twig',
                    $user->getEmail(),
                    array(
                        'user' => $user,
                        'company' => $company,
                        'isAdminpanel' => $isAdminpanel,
                        'country' => $user->getCountry()
                    )
                );
            }
        } catch (\Swift_RfcComplianceException $e) {
        }
    }

    public function notifyOfConnectingToCompany(User $user, Company $oldCompany = null)
    {
        try {
            if ($oldCompany) {
                $this->mailer->sendMessage(
                    'MetalUsersBundle::emails/connecting_employee.html.twig',
                    $user->getEmail(),
                    array('user' => $user, 'company' => $oldCompany, 'country' => $user->getCountry())
                );
            } else {
                $this->mailer->sendMessage(
                    'MetalUsersBundle::emails/connecting_new_employee.html.twig',
                    $user->getEmail(),
                    array('user' => $user, 'country' => $user->getCountry())
                );
            }
        } catch (\Swift_RfcComplianceException $e) {
        }
    }

    public function notifyOfChangePassword(User $user)
    {
        try {
            $this->mailer->sendMessage(
                'MetalPrivateOfficeBundle:emails:change_password.html.twig',
                $user->getEmail(),
                array(
                    'user' => $user,
                    'country' => $user->getCountry()
                )
            );
        } catch (\Swift_RfcComplianceException $e) {
        }
    }

    public function sendRecoveryPasswordEmail(User $user)
    {
        try {
            $this->mailer->sendMessage(
                '@MetalUsers/emails/recover_mail.html.twig',
                $user->getEmail(),
                array('user' => $user, 'country' => $user->getCountry()),
                $this->emailFrom
            );
        } catch (\Swift_RfcComplianceException $e) {

        }
    }
}
