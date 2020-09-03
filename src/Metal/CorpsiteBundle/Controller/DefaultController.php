<?php

namespace Metal\CorpsiteBundle\Controller;

use Doctrine\ORM\EntityManager;

use Metal\AnnouncementsBundle\Entity\OrderAnnouncement;
use Metal\AnnouncementsBundle\Entity\ValueObject\SectionProvider;
use Metal\AnnouncementsBundle\Entity\Zone;
use Metal\AnnouncementsBundle\Form\OrderAnnouncementType;
use Metal\CorpsiteBundle\Form\PackageOrderType;
use Metal\ProjectBundle\Helper\ImageHelper;
use Metal\ServicesBundle\Entity\AgreementTemplate;
use Metal\ServicesBundle\Entity\Package;
use Metal\ServicesBundle\Entity\PackageOrder;
use Metal\ServicesBundle\Entity\Payment;
use Metal\ServicesBundle\Entity\ValueObject\ServicePeriodTypesProvider;
use Metal\UsersBundle\Entity\User;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    public function indexAction()
    {
        $template = $this->getDoctrine()->getManager()
            ->find('MetalServicesBundle:AgreementTemplate', AgreementTemplate::CORP_ABOUT_COMPANY);
        $content = $this->renderContent($template);

        return $this->render('@MetalCorpsite/Default/index.html.twig', compact('content'));
    }

    public function announcementOrderAction()
    {
        $em = $this->getDoctrine()->getManager();
        /* @var $em EntityManager */

        $zones = $em->getRepository('MetalAnnouncementsBundle:Zone')->createQueryBuilder('z')
            ->andWhere('z.isHidden = :isHidden')
            ->setParameter('isHidden', false)
            ->orderBy('z.priority', 'ASC')
            ->getQuery()
            ->getResult();

        $zonesBySections = array();

        $sections =  SectionProvider::getAllTypes();
        foreach ($sections as $section) {
            $zonesBySections[$section->getId()] = array(
                'section' => $section
            );
        }

        $em->getRepository('MetalAnnouncementsBundle:ZoneStatus')->attachZoneStatusesToZones($zones);

        foreach ($zones as $zone) {
            /* @var $zone Zone */
            $zonesBySections[$zone->getSectionId()]['zones'][] = $zone;
        }

        $user = null;
        if ($this->isGranted('ROLE_USER')) {
            $user = $this->getUser();
            /* @var $user User */
        }
        $announcementOrder = new OrderAnnouncement();
        $options = array(
            'is_authenticated' => $user !== null,
        );
        $form = $this->createForm(new OrderAnnouncementType(), $announcementOrder, $options);

        $announcementsSchemasImages = $this->container->getParameter('project.announcement_schema_images');

        return $this->render(
            '@MetalCorpsite/Default/announcement.html.twig',
            array(
                'zonesBySections' => $zonesBySections,
                'form' => $form->createView(),
                'announcementsSchemasImages' => $announcementsSchemasImages
            )
        );
    }

    public function saveOrderAnnouncementAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        /* @var $em EntityManager */

        $user = null;
        if ($this->isGranted('ROLE_USER')) {
            $user = $this->getUser();
            /* @var $user User */
        }

        $announcementOrder = new OrderAnnouncement();
        $options = array(
            'is_authenticated' => $user !== null,
        );
        $form = $this->createForm(new OrderAnnouncementType(), $announcementOrder, $options);

        $form->handleRequest($request);

        if (!$form->isValid()) {
            $errors = $this->get('metal.project.form_helper')->getFormErrorMessages($form);

            return JsonResponse::create(
                array(
                    'errors' => $errors,
                )
            );
        }

        if ($user) {
            $announcementOrder->setUser($user);
        }

        $em->persist($announcementOrder);
        $em->flush();

        return JsonResponse::create(
            array(
                'status' => 'success'
            )
        );
    }

    public function servicesAction()
    {
        $em = $this->getDoctrine()->getManager();
        /* @var $em EntityManager */
        $packageRepository = $em->getRepository('MetalServicesBundle:Package');

        $promotionRepository = $em->getRepository('MetalCorpsiteBundle:Promotion');

        $isPromoExist = $promotionRepository->getActivePromotions();

        $packages = $packageRepository->getPackages(false,true);
        $serviceItemsTree = $packageRepository->getServiceItemsTree();
        $periods = array_reverse(ServicePeriodTypesProvider::getAllTypesAsSimpleArray(), true);

        return $this->render('@MetalCorpsite/Default/services.html.twig', compact('packages', 'serviceItemsTree', 'periods', 'isPromoExist'));
    }

    public function clientsAction()
    {
        $em = $this->getDoctrine()->getManager();
        /* @var $em EntityManager */

        $companyRepository = $em->getRepository('MetalCompaniesBundle:Company');

        $companiesIds = $companyRepository->getCompaniesIdsForClientsReviews();
        $companiesIdsWithReviews = $companyRepository->getCompaniesIdsForClientsReviews(true);

        $resultCompaniesIds = $companiesIdsWithReviews;
        if (count($resultCompaniesIds) < 50) {
            $resultCompaniesIds = array_merge($resultCompaniesIds, array_slice($companiesIds, 0, 50 - count($resultCompaniesIds)));
        }

        $companiesData = $companyRepository->createQueryBuilder('c')
            ->andWhere('c.id IN (:companies_ids)')
            ->leftJoin('MetalCorpsiteBundle:ClientReview', 'cr', 'WITH', 'cr.company = c.id')
            ->setParameter('companies_ids', $resultCompaniesIds)
            ->orderBy('cr.id', 'DESC')
            ->getQuery()
            ->getResult();

        $imageHelper = $this->container->get('brouzie.helper_factory')->get('MetalProjectBundle:Image');
        /* @var $imageHelper ImageHelper */

        $companies = array();
        foreach ($companiesData as $company) {
            if ($imageHelper->getCompanyLogoUrl($company, 'sq168', 'corp')) {
                $companies[] = $company;
            }
        }
        $companies = array_slice($companies, 0, 25);

        $companyRepository->attachCompanyReviewToCompanies($companies);

        return $this->render('@MetalCorpsite/Default/clients.html.twig', compact('companies'));
    }

    public function contactsAction()
    {
        $template = $this->getDoctrine()->getManager()->find('MetalServicesBundle:AgreementTemplate', AgreementTemplate::CORP_CONTACTS);
        $content = $this->renderContent($template);

        return $this->render('@MetalCorpsite/Default/contacts.html.twig', compact('content'));
    }

    public function promotionsAction()
    {
        $em = $this->getDoctrine()->getManager();
        /* @var $em EntityManager */

        $promotions = $em->getRepository('MetalCorpsiteBundle:Promotion')->getActivePromotions();

        return $this->render('@MetalCorpsite/Default/promotion.html.twig', compact('promotions'));
    }

    public function licenseAgreementAction()
    {
        $em = $this->getDoctrine()->getManager();
        /* @var $em EntityManager */

        $agreement = $em->getRepository('MetalServicesBundle:AgreementTemplate')->find(AgreementTemplate::AGREEMENT);
        $agreementHtml = $this->renderContent($agreement);

        return $this->render('@MetalCorpsite/Default/licenseAgreement.html.twig', array(
            'agreementHtml' => $agreementHtml,
            'agreement' => $agreement,
        ));
    }

    public function orderPackageAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $packageOrder = new PackageOrder();
        $form = $this->createForm(
            new PackageOrderType(),
            $packageOrder,
            array('city_repository' => $em->getRepository('MetalTerritorialBundle:City'))
        );

        $form->handleRequest($request);

        if (!$form->isValid()) {
            $errors = $this->get('metal.project.form_helper')->getFormErrorMessages($form);

            return JsonResponse::create(
                array(
                    'errors' => $errors,
                )
            );
        }

        $user = null;
        $additionalText = '';
        if ($this->isGranted('ROLE_USER')) {
            $user = $this->getUser();
            /* @var $user User */
            $packageOrder->setUser($user);
            if ($company = $user->getCompany()) {
                $additionalText = ', id - ' . $company->getId();
            }
        }

        $em->persist($packageOrder);

        $payment = Payment::createFromPackageOrder($packageOrder);
        $payment->setServName(sprintf(
            'Оплата информационных услуг на сайте %s, срок подключения - %s%s',
            $this->container->getParameter('project.title'),
            $packageOrder->getPackagePeriodName(),
            $additionalText
        ));

        $em->persist($payment);

        $em->flush();

        $redirectTo = $this->generateUrl('MetalCorpsiteBundle:Default:orderPayment', array('id' => $payment->getId()));
        if ($payment->getCompany()) {
            $redirectTo = $this->generateUrl('MetalPrivateOfficeBundle:Details:payment');
        }

        return JsonResponse::create(array('status' => 'success', 'redirect_to' => $redirectTo));
    }

    /**
     * @ParamConverter("payment", class="Metal\ServicesBundle\Entity\Payment")
     */
    public function orderPaymentAction(Payment $payment)
    {
        return $this->render('@MetalCorpsite/Default/order_payment.html.twig', array('payment' => $payment));
    }

    protected function renderContent(AgreementTemplate $agreementTemplate)
    {
        $twigTemplate = $this->get('twig')->createTemplate($agreementTemplate->getContent());

        return $twigTemplate->render(array());
    }
}
