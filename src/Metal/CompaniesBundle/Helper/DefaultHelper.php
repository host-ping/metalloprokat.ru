<?php

namespace Metal\CompaniesBundle\Helper;

use Brouzie\Bundle\HelpersBundle\Helper\HelperAbstract;
use Doctrine\ORM\EntityManager;
use Metal\CompaniesBundle\Entity\Company;
use Metal\CompaniesBundle\Entity\Promocode;
use Metal\ProjectBundle\Helper\FormattingHelper;
use Metal\ProjectBundle\Helper\TextHelper;
use Metal\UsersBundle\Entity\User;

class DefaultHelper extends HelperAbstract
{
    public function isCompanyOnline(Company $company)
    {
        $tokenStorage = $this->container->get('security.token_storage');
        $authorizationChecker = $this->container->get('security.authorization_checker');

        if ($authorizationChecker->isGranted('ROLE_USER')) {
            /** @var User $user */
            $user = $tokenStorage->getToken()->getUser();

            // current authenticated user are always online
            if ($user->getCompany() && $company->getId() == $user->getCompany()->getId()) {
                return true;
            }
        }

        $timeout = $this->container->getParameter('company_display_online_timeout');

        return $company->getLastVisitAt() > new \DateTime($timeout);
    }

    public function getViolationTextForCompanyCity(Company $company)
    {
        $translator = $this->container->get('translator');

        $maxPossibleCompanyCitiesCount = $company->getMaxPossibleCompanyCitiesCount();

        $errorTextPattern = 'add_company_city_error_limit_exceeded';
        $parameters['%count%'] = $maxPossibleCompanyCitiesCount;

        if ($company->getMaxPossibleCompanyCitiesCount() === 0) {
            $errorTextPattern = 'add_company_city_error_no_package';
            $parameters = array();
        }

        return $translator->transChoice($errorTextPattern, $maxPossibleCompanyCitiesCount, $parameters, 'validators');
    }

    public function getReservedSlugs()
    {
        static $slugs;

        if (null !== $slugs) {
            return $slugs;
        }

        $slugs = array(
            'company',
            'corp',
            'spros',
            'demand'
        );

        $slugs = array_unique(array_merge($slugs, $this->container->getParameter('minisite_reserved_slugs')));

        return $slugs;
    }

    public function isSlugAvailable($slug, $companyId = null, $checkCompany = true)
    {
        $length = mb_strlen($slug);

        if ($length < 2 || $length > 35) {
            return false;
        }

        if (in_array($slug, $this->getReservedSlugs())) {
            return false;
        }

        $em = $this->container->get('doctrine')->getManager();
        /* @var $em EntityManager */

        $categoryRepository = $em->getRepository('MetalCategoriesBundle:Category');
        $category = $categoryRepository->createQueryBuilder('cat')
            ->select('cat.id')
            ->andWhere('cat.slug = :slug')
            ->setParameter('slug', $slug)
            ->getQuery()
            ->getResult();

        if ($category) {
            return false;
        }

        if (is_numeric($slug)) {
            // для областей мы используем в качестве слага айди области
            $region = $em->getRepository('MetalTerritorialBundle:Region')->find($slug);

            if ($region) {
                return false;
            }
        }

        if ($checkCompany) {
            $companyRepository = $em->getRepository('MetalCompaniesBundle:Company');
            $company = $companyRepository->createQueryBuilder('comp')
                ->select('comp.id')
                ->andWhere('comp.slug = :slug')
                ->andWhere('comp.deletedAtTS = 0')
                ->setParameter('slug', $slug)
                ->getQuery()
                ->getOneOrNullResult();

            if ($companyId && $company) {
                return $company['id'] == $companyId;
            }

            if ($company) {
                return false;
            }
        }

        return true;
    }

    public function isCompanyHasDocuments(Company $company)
    {
        $em = $this->container->get('doctrine')->getManager();
        /* @var $em EntityManager */

        $companyDocuments = $em->getRepository('MetalCompaniesBundle:CompanyFile')->findBy(array('company' => $company));

        return count($companyDocuments) > 0;
    }

    public function generateCompanySlug($companyId, $companyTitle, $citySlug)
    {
        $textHelper = $this->container->get('brouzie.helper_factory')->get('MetalProjectBundle:Text');
        /* @var $textHelper TextHelper */

        $companyTitleParts = explode(' ', $companyTitle);
        do {
            $slug = $textHelper->slugifyCompanyTitle(implode(' ', $companyTitleParts));

            if (!$slug) {
                break;
            }

            if ($this->isSlugAvailable($slug)) {
                return $slug;
            }

            if ($citySlug) {
                $slugByCity = $slug.'-'.$citySlug;
                if ($this->isSlugAvailable($slugByCity)) {
                    return $slugByCity;
                }
            }

            $slugByCompany = $slug.'-'.$companyId;
            if ($this->isSlugAvailable($slugByCompany)) {
                return $slugByCompany;
            }

            array_pop($companyTitleParts);
        } while ($companyTitleParts);

        return sprintf('company-%d', $companyId);
    }

    public function isPromocodeExist($code)
    {
        $em = $this->container->get('doctrine')->getManager();
        /* @var $em EntityManager */

        return $em->getRepository('MetalCompaniesBundle:Promocode')
            ->findBy(array('code' => $code));
    }

    public function generatePromocode($length = Promocode::CODE_LENGTH)
    {
        do {
            $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
            $size = strlen($chars);
            $code = '';
            for ($i = 0; $i < $length; $i++) {
                $code .= $chars[mt_rand(0, $size - 1)];
            }

        } while ($this->isPromocodeExist($code));

        return $code;
    }

    public function validatePromocode(Company $company, $code)
    {
        $em = $this->container->get('doctrine')->getManager();
        /* @var $em EntityManager */
        $promocodeRepository = $em->getRepository('MetalCompaniesBundle:Promocode');
        $companyPackageRepository = $em->getRepository('MetalCompaniesBundle:CompanyPackage');

        if ($company->getPromocode()) {
            return array('status' => 'error', 'error_message' => 'Компания уже использует промокод.');
        }

        if ($companyPackageRepository->findOneBy(array('company' => $company))) {
            return array('status' => 'error', 'error_message' => 'Компания уже использовала платные пакеты.');
        }

        $promocode = $promocodeRepository->findOneBy(array('code' => $code));

        if (!$promocode) {
            return array('status' => 'error', 'error_message' => 'Промокод не существует.');
        }

        if ($promocode->getCompany()) {
            return array('status' => 'error', 'error_message' => 'Промокод уже используется.');
        }

        $currentDate = new \DateTime('midnight');
        $formattingHelper = $this->getHelper('MetalProjectBundle:Formatting');
        /* @var $formattingHelper FormattingHelper */

        if ($currentDate < $promocode->getStartsAt() || $currentDate > $promocode->getEndsAt()) {
            return array('status' => 'error', 'error_message' => sprintf(
                'Промокод действителен с %s до %s',
                $formattingHelper->formatDate($promocode->getStartsAt()),
                $formattingHelper->formatDate($promocode->getEndsAt())
            ));
        }

        return array('status' => 'success');
    }
}
