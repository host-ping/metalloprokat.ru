<?php

namespace Metal\UsersBundle\Service;

use Brouzie\Bundle\HelpersBundle\Helper\HelperFactory;
use Doctrine\ORM\EntityManager;

use Metal\CompaniesBundle\Entity\Company;
use Metal\CompaniesBundle\Entity\CompanyCity;
use Metal\CompaniesBundle\Entity\CompanyCounter;
use Metal\CompaniesBundle\Entity\CompanyDescription;
use Metal\CompaniesBundle\Entity\CompanyLog;
use Metal\CompaniesBundle\Entity\CompanyPhone;
use Metal\CompaniesBundle\Entity\CompanyPackage;
use Metal\CompaniesBundle\Entity\PaymentDetails;
use Metal\CompaniesBundle\Helper\DefaultHelper;
use Metal\MiniSiteBundle\Entity\MiniSiteConfig;
use Metal\ServicesBundle\Entity\Package;
use Metal\TerritorialBundle\Entity\City;
use Metal\UsersBundle\Entity\User;

class RegistrationService
{
    /**
     * @var EntityManager
     */
    private $em;
    /**
     * @var HelperFactory
     */
    private $helperFactory;

    /**
     * @var UserMailer
     */
    private $userMailer;

    public function __construct(EntityManager $em, HelperFactory $helperFactory, UserMailer $userMailer)
    {
        $this->em = $em;
        $this->helperFactory = $helperFactory;
        $this->userMailer = $userMailer;
    }

    public function connectCompany(Company $company, User $user)
    {
        $this->userMailer->notifyMainUserOnRegistration($user);
        $company->scheduleSynchronization();

        $user->setCompany($company);

        $this->em->flush();
    }

    public function createCompany(Company $company, User $user, $phone, $companyName, $companyType, City $city = null)
    {
        $user->setCompany($company);

        $company->setTitle($companyName);
        $company->setCompanyTypeId($companyType);
        $company->setCity($city);
        $user->setHasEditPermission(true);
        $user->setCanUseService(true);
        $user->setApproved(true);

        $this->em->persist($company);

        $this->em->flush();

        $slugHelper = $this->helperFactory->get('MetalCompaniesBundle');
        /* @var $slugHelper DefaultHelper */

        $slug = $slugHelper->generateCompanySlug($company->getId(), $company->getTitle(), $company->getCity()->getSlug());
        $company->setSlug($slug);

        $companyPhone = new CompanyPhone();
        $companyPhone->setPhone($phone);
        $company->addPhone($companyPhone);

        $companyLog = new CompanyLog();
        $companyLog->setCreatedBy($user);
        $company->setCompanyLog($companyLog);
        $this->em->persist($companyLog);

        $companyCounter = new CompanyCounter();
        $company->setCounter($companyCounter);
        $this->em->persist($companyCounter);

        $mainBranchOffice = new CompanyCity();
        $mainBranchOffice->setKind(CompanyCity::KIND_BRANCH_OFFICE);
        $mainBranchOffice->setIsMainOffice(true);
        $mainBranchOffice->setCity($company->getCity());
        $company->addCompanyCity($mainBranchOffice);

        $paymentDetail = new PaymentDetails();
        $company->setPaymentDetails($paymentDetail);
        $this->em->persist($paymentDetail);

        $companyDescription = new CompanyDescription();
        $company->setCompanyDescription($companyDescription);
        $this->em->persist($companyDescription);

        $miniSiteConfig = new MiniSiteConfig();
        $company->setMinisiteConfig($miniSiteConfig);
        $this->em->persist($miniSiteConfig);


        $this->em->flush();
    }

    public function applyPromocodeToCompany(Company $company, $code)
    {
        $promocodeRepository = $this->em->getRepository('MetalCompaniesBundle:Promocode');
        $productRepository = $this->em->getRepository('MetalProductsBundle:Product');
        $promocode = $promocodeRepository->findOneBy(array('code' => $code));

        $promocode->setActivatedAt(new \DateTime());
        $promocode->setCompany($company);

        $companyPackage = new CompanyPackage();
        $companyPackage->setCompany($company);
        $companyPackage->setStartAt(new \DateTime());
        $companyPackage->setEndAt(new \DateTime('+1 month'));
        $companyPackage->setPackageId(Package::FULL_PACKAGE);
        $company->setCodeAccess(Package::FULL_PACKAGE);
        $this->em->persist($companyPackage);

        $company->setPromocode($promocode);
        $company->setInCrm(true);
        $company->scheduleSynchronization();

        $this->em->flush();
        $productRepository->updatePermissionShowProducts($company);
    }
}
