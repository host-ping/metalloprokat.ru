<?php

namespace Metal\PrivateOfficeBundle\Controller;

use Doctrine\ORM\EntityManager;
use Geocoder\Exception\NoResultException;
use Metal\CompaniesBundle\Entity\Company;
use Metal\CompaniesBundle\Entity\CompanyCity;
use Metal\CompaniesBundle\Entity\CompanyPhone;
use Metal\CompaniesBundle\Form\CompanySaveLogoType;
use Metal\CompaniesBundle\Form\CompanyType;
use Metal\CompaniesBundle\Helper\DefaultHelper;
use Metal\PrivateOfficeBundle\Form\PromocodeType;
use Metal\UsersBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class PrivateCompanyController extends Controller
{
    /**
     * @Security("has_role('ROLE_SUPPLIER') and user.getHasEditPermission() and (not request.isMethod('POST') or has_role('ROLE_CONFIRMED_EMAIL'))")
     */
    public function editAction(Request $request)
    {
        $companiesHelper = $this->container->get('brouzie.helper_factory')->get('MetalCompaniesBundle');
        /* @var $companiesHelper DefaultHelper */

        $em = $this->getDoctrine()->getManager();
        /* @var $em EntityManager */

        $company = $this->getUser()->getCompany();
        /* @var $company Company */

        if ($request->isMethod('GET') && $company->getPhones()->isEmpty()) {
            $phones = new CompanyPhone();
            $company->addPhone($phones);
        }

        if (!$company->getSites()) {
            $company->addSite('');
        }

        if (!$company->getSlug()) {
            $cityTitle = $company->getCity() ? $company->getCity()->getTitle() : '';
            $slug = $companiesHelper->generateCompanySlug($company->getId(), $company->getTitle(), $cityTitle);
            $company->setSlug($slug);
        }

        $em->getRepository('MetalCompaniesBundle:CompanyCategory')->loadCompanyCategoriesCollectionForCompany($company);

        $em->getRepository('MetalCompaniesBundle:CompanyCity')->loadCompanyCitiesCollectionForCompany($company);

        $form = $this->createForm(new CompanyType(), $company, array(
                'city_repository' => $em->getRepository('MetalTerritorialBundle:City'),
            )
        );

        if ($request->isMethod('POST')) {
            $oldCompanyAsArray = $company->getCompanyToArray();
            $oldCompanyAttributesTypesIds = $company->getCompanyAttributesTypesIds();
            $oldCompanyCategoriesIds = $company->getCategoriesIds();

            $oldCompanyCities = array();
            foreach ($company->getCompanyCities() as $companyCity) {
                $oldCompanyCities[$companyCity->getCity()->getId()] = $companyCity->getId();
            }

            $form->handleRequest($request);
            if (!$form->isValid()) {
                $errors = $this->get('metal.project.form_helper')->getFormErrorMessages($form);

                return JsonResponse::create(array(
                    'errors' => $errors
                ));
            }

            $company->setUpdated();

            $oldMainCity = $oldCompanyAsArray['cityId'];
            if ($company->getCity()->getId() != $oldMainCity && !in_array($company->getCity()->getId(), $company->getCitiesIds())) {
                $newMainCity = new CompanyCity();
                $newMainCity->setKind(CompanyCity::KIND_BRANCH_OFFICE);
                $newMainCity->setCity($company->getCity());
                $newMainCity->setIsMainOffice(true);
                $company->addCompanyCity($newMainCity);
                $em->persist($newMainCity);
            } else {
                foreach ($company->getCompanyCities() as $companyCity) {
                    if ($companyCity->getCity()->getId() == $company->getCity()->getId()) {
                        $companyCity->setKind(CompanyCity::KIND_BRANCH_OFFICE);
                    }
                }
            }

            if ($company->getCity()->getId() != $oldMainCity || $oldCompanyAsArray['address'] != $company->getAddress()) {
                try {
                    $cityCoords = $this->get('bazinga_geocoder.geocoder')
                        ->using('yandex')
                        ->geocode($company->getCity()->getTitle() . '+' . $company->getAddress());
                    $company->setLatitude($cityCoords->getLatitude());
                    $company->setLongitude($cityCoords->getLongitude());
                } catch (NoResultException $e) {
                    $errors = array($form->get('address')->createView()->vars['full_name'] => array('Проверьте адрес'));

                    return JsonResponse::create(array('errors' => $errors));
                }
            }

            foreach ($company->getSites() as $site) {
                if (!$site) {
                    $company->removeSite($site);
                }
            }

            $em->flush();

            $companyCityRepository = $em->getRepository('MetalCompaniesBundle:CompanyCity');
            $companyCityRepository->refreshBranchOfficeHasProducts(array($company->getId()));

            /*$productsIdsToReindex = $companyCityRepository->getProductsIdsForReindex(array($company->getId()));
            if ($productsIdsToReindex) {
                $this->container->get('sphinxy.index_manager')->reindexItems('products', $productsIdsToReindex);
            }*/

            $companyAttributesTypesIds = $company->getCompanyAttributesTypesIds();
            $companyProductsCategoriesIds = $company->getCategoriesIds();

            $companyCities = array();
            foreach ($company->getCompanyCities() as $companyCity) {
                $companyCities[$companyCity->getCity()->getId()] = $companyCity->getId();
            }

            $queueBackend = $this->get('sonata.notification.backend');

            $queueBackendAttributesData = array(
                'company_id'                       => $company->getId(),
                'company_attributes_types_ids'     => $companyAttributesTypesIds,
                'old_company_attributes_types_ids' => $oldCompanyAttributesTypesIds
            );

            $queueBackend->createAndPublish('company_attributes_change', $queueBackendAttributesData);

            $queueBackendCategoriesData = array(
                'company_id'                 => $company->getId(),
                'company_categories_ids'     => $companyProductsCategoriesIds,
                'old_company_categories_ids' => $oldCompanyCategoriesIds,
                'old_company_as_array'       => $oldCompanyAsArray
            );

            $queueBackend->createAndPublish('company_categories_change', $queueBackendCategoriesData);

            $queueBackendCitiesData = array(
                'company_cities_ids'     => $companyCities,
                'old_company_cities_ids' => $oldCompanyCities,
                'old_company_as_array'   => $oldCompanyAsArray
            );

            $queueBackend->createAndPublish('city_delivery_change', $queueBackendCitiesData);

            return JsonResponse::create(array(
                'status' => 'success',
                'data' => array(
                    'date' => $this->get('brouzie.helper_factory')
                        ->get('MetalProjectBundle:Formatting')
                        ->formatDateTime($company->getUpdatedAt()),
                ),
            ));
        }

        $companyPackageRepo = $em->getRepository('MetalCompaniesBundle:CompanyPackage');
        $companyPackage = $companyPackageRepo->findOneBy(array('company' => $company));
        $company->setAttribute('already_used_package', false);
        if ($companyPackage) {
            $company->setAttribute('already_used_package', true);
        }

        $formLogo = $this->createForm(new CompanySaveLogoType(), $company);

        return $this->render('@MetalPrivateOffice/PrivateCompany/view.html.twig', array(
            'form' => $form->createView(),
            'formLogo' => $formLogo->createView(),
            'company' => $company,
        ));
    }

    /**
     * @Security("has_role('ROLE_SUPPLIER') and user.getHasEditPermission() and has_role('ROLE_CONFIRMED_EMAIL')")
     */
    public function saveCompanyLogoAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        /* @var $em EntityManager */

        $user = $this->getUser();
        /* @var $user User */
        $company = $user->getCompany();

        $form = $this->createForm(new CompanySaveLogoType(), $company);

        $form->handleRequest($request);

        if (!$form->isValid()) {
            $errors = $this->get('metal.project.form_helper')->getFormErrorMessages($form);

            return JsonResponse::create(array(
                'errors' => $errors
            ));
        }

        $em->flush();

        return JsonResponse::create(
            array(
                'status' => 'success',
                'logo' => array(
                    'url' => $this->container->get('brouzie.helper_factory')
                        ->get('MetalProjectBundle:Image')
                        ->getCompanyLogoUrl($company, 'sq168', 'private'),
                    'optimizeLogo' => $company->getOptimizeLogo(),
                ),
                // set Content-Type header for IE
            ),
            200,
            array('Content-Type' => 'text/plain')
        );
    }

    /**
     * @Security("has_role('ROLE_SUPPLIER') and user.getHasEditPermission() and has_role('ROLE_CONFIRMED_EMAIL')")
     */
    public function deleteCompanyLogoAction()
    {
        //TODO: handle csrf
        $em = $this->getDoctrine()->getManager();
        /* @var $em EntityManager */

        $user = $this->getUser();
        /* @var $user User */
        $company = $user->getCompany();

        $this->get('vich_uploader.upload_handler')->remove($company, 'uploadedLogo');

        $company->setUpdated();
        $em->flush();

        return JsonResponse::create(
            array(
                'status' => 'success',
            )
        );
    }

    /**
     * @Security("has_role('ROLE_SUPPLIER') and user.getHasEditPermission() and has_role('ROLE_CONFIRMED_EMAIL')")
     */
    public function usePromocodeAction(Request $request)
    {
        if (true !== $this->container->getParameter('project.promocode_enabled')) {
            throw new AccessDeniedException('Promocodes disabled.');
        }

        $form = $this->createForm(new PromocodeType());
        $form->handleRequest($request);

        if (!$form->isValid()) {
            $errors = $this->get('metal.project.form_helper')->getFormErrorMessages($form);

            return JsonResponse::create(array(
                'errors' => $errors
            ));
        }

        $company = $this->getUser()->getCompany();
        /* @var $company Company */
        $companyHelper = $this->container->get('brouzie.helper_factory')->get('MetalCompaniesBundle');
        /* @var $companyHelper DefaultHelper */

        if ($promocode = $form->get('promocode')->getData()) {
            $promocodeStatus = $companyHelper->validatePromocode($company, $promocode);
            if (isset($promocodeStatus['error_message'])) {
                $errors = array($form->get('promocode')->createView()->vars['full_name'] => array($promocodeStatus['error_message']));

                return JsonResponse::create(array('errors' => $errors));
            }
        }

        $registrationService = $this->container->get('metal.users.registration_services');

        $registrationService->applyPromocodeToCompany($company, $promocode);

        return JsonResponse::create(array('status' => 'success'));
    }
}
