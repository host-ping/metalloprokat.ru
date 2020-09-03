<?php

namespace Metal\CompaniesBundle\Service;

use Brouzie\Bundle\HelpersBundle\Helper\HelperAbstract;

use Doctrine\ORM\EntityManagerInterface;
use Metal\CompaniesBundle\Entity\CompanyReview;
use Metal\NewsletterBundle\Service\Mailer;
use Metal\UsersBundle\Repository\UserRepository;

class CompanyReviewMailer extends HelperAbstract
{
    /**
     * @var Mailer
     */
    protected $mailer;

    /**
     * @var UserRepository
     */
    private $userRepository;

    public function __construct($mailer, EntityManagerInterface $em)
    {
        $this->mailer = $mailer;
        $this->userRepository = $em->getRepository('MetalUsersBundle:User');
    }

    /**
     * @param CompanyReview $review
     */
    public function notifyOfCompanyReview(CompanyReview $review)
    {
        $employees = $this->userRepository->getApprovedUsers($review->getCompany());

        foreach ($employees as $employee) {
            try {
                $this->mailer->sendMessage('MetalUsersBundle::emails/added_review.html.twig', $employee->getEmail(),
                    array(
                        'review' => $review,
                        'employee' => $employee,
                        'country' => $employee->getCountry()
                    ));
            } catch (\Swift_RfcComplianceException $e) {

            }
        }
    }

    /**
     * @param CompanyReview $companyReview
     */
    public function notifyOfCompanyReviewAnswer(CompanyReview $companyReview)
    {
        try {
            $this->mailer->sendMessage('MetalUsersBundle::emails/reply_to_review.html.twig', $companyReview->getFixedEmail(),
                array(
                    'review' => $companyReview,
                    'employee' => $companyReview->getCompany()->getCompanyLog()->getCreatedBy(),
                    'country' => $companyReview->getCompany()->getCompanyLog()->getCreatedBy()->getCountry()
                ));
        } catch (\Swift_RfcComplianceException $e) {

        }
    }
}
