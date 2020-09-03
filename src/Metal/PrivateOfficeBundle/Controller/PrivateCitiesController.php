<?php

namespace Metal\PrivateOfficeBundle\Controller;

use Doctrine\ORM\EntityManager;
use Geocoder\Exception\NoResultException;

use Metal\CompaniesBundle\Entity\Company;
use Metal\CompaniesBundle\Entity\CompanyCity;
use Metal\CompaniesBundle\Entity\CompanyPhone;
use Metal\CompaniesBundle\Form\BranchOfficeSimpleType;
use Metal\CompaniesBundle\Form\BranchOfficeType;
use Metal\CompaniesBundle\Helper\DefaultHelper;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class PrivateCitiesController extends Controller
{
    /**
     * @ParamConverter("branchOffice", class="MetalCompaniesBundle:CompanyCity", isOptional=true)
     * @Security("has_role('ROLE_SUPPLIER') and user.getHasEditPermission() and (not branchOffice or branchOffice.getCompany().getId() == user.getCompany().getId())")
     */
    public function viewAction(Request $request, CompanyCity $branchOffice = null)
    {
        $em = $this->getDoctrine()->getManager();
        /* @var $em EntityManager */

        $company = $this->getUser()->getCompany();
        /* @var $company Company */

        if (!$branchOffice) {
            $branchOffice = new CompanyCity();
            $branchOffice->setCompany($company);
        }

        if ($branchOffice->getPhones()->isEmpty()) {
            $branchOffice->addPhone(new CompanyPhone());
        }

        $branches = array();
        if ($company->getCity()) {
            $branches = $em->getRepository('MetalCompaniesBundle:CompanyCity')
                ->createQueryBuilder('companyCity')
                ->addSelect('city')
                ->andWhere('companyCity.company = :company')
                ->andWhere('companyCity.enabled = true')
                ->andWhere('companyCity.city <> :mainCity')
                ->setParameter('mainCity', $company->getCity())
                ->setParameter('company', $company)
                ->leftJoin('companyCity.city', 'city')
                ->addOrderBy('city.title', 'ASC')
                ->getQuery()
                ->getResult();
        }

        $form = $this->createForm(
            new BranchOfficeType(),
            $branchOffice,
            array(
                'city_repository' => $em->getRepository('MetalTerritorialBundle:City')
            )
        );

        $simpleForm = $this->createForm(new BranchOfficeSimpleType(), $company);

        if ($request->isXmlHttpRequest()) {
            return JsonResponse::create(array(
                'html' => $this->renderView('@MetalPrivateOffice/partials/filial_form.html.twig', array(
                    'form' => $form->createView(),
                    'simpleForm' => $simpleForm->createView(),
                    'branch' => $branchOffice,
                    'branches' => $branches,
                )),
            ));
        }

        return $this->render('@MetalPrivateOffice/PrivateCities/view.html.twig', array(
            'form' => $form->createView(),
            'simpleForm' => $simpleForm->createView(),
            'branch' => $branchOffice,
            'branches' => $branches,
        ));
    }

    /**
     * @ParamConverter("branchOffice", class="MetalCompaniesBundle:CompanyCity", isOptional=true)
     * @Security("has_role('ROLE_SUPPLIER') and user.getHasEditPermission() and has_role('ROLE_CONFIRMED_EMAIL') and (not branchOffice or branchOffice.getCompany().getId() == user.getCompany().getId())")
     */
    public function saveAction(Request $request, CompanyCity $branchOffice = null)
    {
        $em = $this->getDoctrine()->getManager();
        /* @var $em EntityManager */
        $company = $this->getUser()->getCompany();
        /* @var $company Company */
        $companyCityRepository = $em->getRepository('MetalCompaniesBundle:CompanyCity');

        $oldBranchOfficeAsArray = array();
        $isExists = $branchOffice && $branchOffice->getId();
        if ($isExists) {
            $oldBranchOfficeAsArray = $em->getUnitOfWork()->getOriginalEntityData($branchOffice);
        } else {
            $branchOffice = new CompanyCity();
            $company->addCompanyCity($branchOffice);
        }

        if (!$company->canCreateCompanyCity()) {
            $companiesHelper = $this->container->get('brouzie.helper_factory')->get('MetalCompaniesBundle');
            /* @var $companiesHelper DefaultHelper */

            $widgetManager = $this->container->get('brouzie_widgets.widget_manager');
            $widget = $widgetManager
                ->createWidget(
                    'MetalPrivateOfficeBundle:CompletePackage',
                    array(
                        'text' => $companiesHelper->getViolationTextForCompanyCity($company),
                        'visible_close_button' => $company->getMaxPossibleCompanyCitiesCount() !== 0,
                        'visible_save_button' => $company->getMaxPossibleCompanyCitiesCount() === 0
                    )
                );

            $html = $widgetManager->renderWidget($widget);

            return JsonResponse::create(array(
                'errors' => array(),
                'html' => $html
            ));
        }

        $form = $this->createForm(new BranchOfficeType(), $branchOffice, array('city_repository' => $em->getRepository('MetalTerritorialBundle:City')));

        $form->handleRequest($request);

        // для добавления уже существующего, но отключенного города
        $companyCity = null;
        if (!$isExists) {
            //enabled = false - что-бы не перезаписывать включенный город.
            $companyCity = $companyCityRepository->findOneBy(array('company' => $company, 'city' => $branchOffice->getCity(), 'enabled' => false));

            if ($companyCity) {
                $company->removeCompanyCity($branchOffice);
                $branchOffice = $companyCity;
                $oldBranchOfficeAsArray = $em->getUnitOfWork()->getOriginalEntityData($branchOffice);
                $branchOffice->setEnabled(true);

                $form = $this->createForm(new BranchOfficeType(), $branchOffice, array('city_repository' => $em->getRepository('MetalTerritorialBundle:City')));
                $form->handleRequest($request);
            }
        }

        if (!$form->isValid()) {
            return JsonResponse::create(array(
                'errors' => $this->get('metal.project.form_helper')->getFormErrorMessages($form)
            ));
        }

        if ($branchOffice->getAddress() && (!$oldBranchOfficeAsArray || $oldBranchOfficeAsArray['address'] !== $branchOffice->getAddress() || $oldBranchOfficeAsArray['city'] !== $branchOffice->getCity())) {
            try {
                $cityCoords = $this->get('bazinga_geocoder.geocoder')
                    ->using('yandex')
                    ->geocode($branchOffice->getCity()->getTitle().'+'.$branchOffice->getAddress());
                $branchOffice->setLatitude($cityCoords->getLatitude());
                $branchOffice->setLongitude($cityCoords->getLongitude());
            } catch (NoResultException $e) {
                $errors = array($form->get('address')->createView()->vars['full_name'] => array('Проверьте адрес'));

                return JsonResponse::create(array('errors' => $errors));
            }
        }

        $branchOffice->setUpdated();
        $branchOffice->normalizePhonesAsString();

        $oldCompanyAsArray = $company->getCompanyToArray();

        $em->flush();

        $queueBackend = $this->get('sonata.notification.backend');
        $queueBackendData = array(
            'branch_office_id'     => $branchOffice->getId(),
            'old_company_as_array' => $oldCompanyAsArray
        );

        $queueBackend->createAndPublish('branch_office_creation', $queueBackendData);

        $companyCityRepository->refreshBranchOfficeHasProducts(array($company->getId()));

       /* $productsIdsToReindex = $companyCityRepository->getProductsIdsForReindex(array($company->getId()));
        if ($productsIdsToReindex) {
            $this->container->get('sphinxy.index_manager')->reindexItems('products', $productsIdsToReindex);
        }*/

        return JsonResponse::create(array(
                'status' => 'success'
            ));
    }

    /**
     * @Security("has_role('ROLE_SUPPLIER') and user.getHasEditPermission() and has_role('ROLE_CONFIRMED_EMAIL')")
     */
    public function simpleSaveAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        /* @var $em EntityManager */

        $company = $this->getUser()->getCompany();
        /* @var $company Company */

        if (!$company->canCreateCompanyCity()) {
            $companiesHelper = $this->container->get('brouzie.helper_factory')->get('MetalCompaniesBundle');
            /* @var $companiesHelper DefaultHelper */

            $widgetManager = $this->container->get('brouzie_widgets.widget_manager');
            $widget = $widgetManager
                ->createWidget(
                    'MetalPrivateOfficeBundle:CompletePackage',
                    array(
                        'text' => $companiesHelper->getViolationTextForCompanyCity($company),
                        'visible_close_button' => $company->getMaxPossibleCompanyCitiesCount() !== 0,
                        'visible_save_button' => $company->getMaxPossibleCompanyCitiesCount() === 0
                    )
                );

            $html = $widgetManager->renderWidget($widget);

            return JsonResponse::create(array(
                'errors' => array(),
                'html' => $html
            ));
        }

        $form = $this->createForm(new BranchOfficeSimpleType(), $company);
        $form->handleRequest($request);

        if (!$form->isValid()) {
            $errors = $this->get('metal.project.form_helper')->getFormErrorMessages($form);

            return JsonResponse::create(array(
                'errors' => $errors
            ));
        }

        $em->flush();

        return JsonResponse::create(array(
            'status' => 'success'
        ));
    }

    /**
     * @ParamConverter("branchOffice", class="MetalCompaniesBundle:CompanyCity")
     * @Security("has_role('ROLE_SUPPLIER') and user.getHasEditPermission() and has_role('ROLE_CONFIRMED_EMAIL') and branchOffice.getCompany().getId() == user.getCompany().getId()")
     */
    public function deleteAction(CompanyCity $branchOffice)
    {
        $em = $this->getDoctrine()->getManager();
        /* @var $em EntityManager */

        $company = $branchOffice->getCompany();
        $oldCompanyAsArray = $company->getCompanyToArray();

        $queueBackendData = array(
            'branch_office_id'     => array($branchOffice->getCity()->getId() => $branchOffice->getId()),
            'old_company_as_array' => $oldCompanyAsArray
        );

        $company->removeCompanyCity($branchOffice);
        $em->remove($branchOffice);
        $em->flush();

        $companyCityRepository = $em->getRepository('MetalCompaniesBundle:CompanyCity');
        $em->getRepository('MetalProductsBundle:Product')->disableProductsNotBranchOffice($company);
        $companyCityRepository->refreshBranchOfficeHasProducts(array($company->getId()));

        /*$productsIdsToReindex = $companyCityRepository->getProductsIdsForReindex(array($company->getId()));
        if ($productsIdsToReindex) {
            $this->container->get('sphinxy.index_manager')->reindexItems('products', $productsIdsToReindex);
        }*/

        $queueBackend = $this->get('sonata.notification.backend');
        $queueBackend->createAndPublish('branch_office_removal', $queueBackendData);

        return JsonResponse::create( array(
            'status' => 'success',
        ));
    }
}
