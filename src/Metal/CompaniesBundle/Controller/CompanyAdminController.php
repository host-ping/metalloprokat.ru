<?php

namespace Metal\CompaniesBundle\Controller;

use Doctrine\ORM\EntityManager;
use Metal\CompaniesBundle\Entity\Company;
use Metal\CompaniesBundle\Entity\CompanyCity;
use Metal\CompaniesBundle\Entity\CompanyPhone;
use Metal\CompaniesBundle\Entity\ValueObject\ActionTypeProvider;
use Metal\CompaniesBundle\Form\CompanyChangeCityType;
use Metal\CompaniesBundle\Form\CompanyCreationType;
use Metal\CompaniesBundle\Form\CompanyMergerType;
use Metal\CompaniesBundle\Form\CompanyToggleStatusType;
use Metal\ProductsBundle\DataFetching\Spec\ProductsFilteringSpec;
use Metal\ProductsBundle\Entity\Product;
use Metal\TerritorialBundle\Entity\City;
use Sonata\AdminBundle\Controller\CRUDController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;

class CompanyAdminController extends CRUDController
{
    public function editAction($id = null)
    {
        //TODO: use preEdit after update sonata
        $id = $this->get('request_stack')->getMasterRequest()->get($this->admin->getIdParameter());
        $object = $this->admin->getObject($id);
        /* @var $object Company */
        if ($object && !$object->getCity()) {
            $this->addFlash('sonata_flash_info', 'Нужно ассоциировать компанию с городом.');

            return $this->redirect($this->admin->generateObjectUrl('change_main_city', $object));
        }

        return parent::editAction($id);
    }

    public function viewDemandHistoryAction(Request $request)
    {
        $id = $request->attributes->get($this->admin->getIdParameter());

        $company = $this->admin->getObject($id);
        /* @var $object Company */

        $em = $this->getDoctrine()->getManager();
        /* @var $em EntityManager */

        $demandViews = $em->getRepository('MetalDemandsBundle:DemandView')->createQueryBuilder('dv')
            ->join('dv.user', 'u')
            ->addSelect('u')
            ->leftJoin('u.company', 'c')
            ->addSelect('c')
            ->andWhere('u.company = :company_id')
            ->setParameter('company_id', $company->getId())
            ->orderBy('dv.viewedAt', 'DESC')
            ->getQuery()
            ->getResult();

        return $this->render(
            'MetalCompaniesBundle:AdminCompany:companyViewsDemand.html.twig',
            array(
                'demandViews' => $demandViews,
                'action' => null,
                'object' => null
            )
        );
    }

    public function downloadFileAction(Request $request)
    {
        $id = $request->attributes->get($this->admin->getIdParameter());

        $object = $this->admin->getObject($id);
        /* @var $object Company */

        $paymentDetail = $object->getPaymentDetails();

        return $this->get('vich_uploader.download_handler')
            ->downloadObject($paymentDetail, 'uploadedFile', null, true);
    }

    public function mergeCompaniesAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        /* @var $em EntityManager */

        $form = $this->createForm(new CompanyMergerType());

        if (!$request->isMethod('POST')) {
            return $this->render(
                'MetalCompaniesBundle:AdminCompany:company_merger.html.twig',
                array(
                    'form' => $form->createView(),
                    'action' => null,
                    'object' => null
                )
            );
        }

        $form->handleRequest($request);

        if (!$form->isValid()) {
            return $this->render(
                'MetalCompaniesBundle:AdminCompany:company_merger.html.twig',
                array(
                    'form' => $form->createView(),
                    'action' => null,
                    'object' => null
                )
            );
        }

        $mainCompany = $form->get('main_company')->getData();
        /* @var $mainCompany Company */
        $additionalCompany = $form->get('additional_company')->getData();
        /* @var $additionalCompany Company */
        $comment = $form->get('comment')->getData();

        if (!$mainCompany->getPackageChecker()->isPaidAccount() && $additionalCompany->getPackageChecker()->isPaidAccount()) {
            $this->addFlash(
                'sonata_flash_error',
                sprintf(
                    'Данные не перенесены. Основная компания %d бесплатник, а компания %d платник. Поменяйте местами.',
                    $mainCompany->getId(),
                    $additionalCompany->getId()
                )
            );

            return $this->redirect($this->admin->generateUrl('merge'));
        }

        if (!$mainCompany->getMainOffice() || !$mainCompany->getCity()) {
            $this->addFlash('sonata_flash_error', 'У основной компании должен быть головной офис с привязкой к городу.');

            return $this->redirect($this->admin->generateUrl('edit', array('id' => $mainCompany->getId())));
        }

        $mainCompanyCategoriesIds = $mainCompany->getCategoriesToCompanyCategoriesIds();

        $mainCompanyCitiesToIds = array();

        foreach ($mainCompany->getCompanyCities() as $mainCompanyCity) {
            $mainCompanyCitiesToIds[$mainCompanyCity->getCity()->getId()] = $mainCompanyCity->getId();
        }

        $additionalCompanyBranchOfficesToIds = array();
        foreach ($additionalCompany->getBranchOffices() as $additionalCompanyBranchOffice) {
            $additionalCompanyBranchOfficesToIds[$additionalCompanyBranchOffice->getCity()->getId()] = $additionalCompanyBranchOffice->getId();
        }

        $em->beginTransaction();

        $categoriesToDelete = array();
        $companyCategoryRepository = $this->getDoctrine()->getRepository('MetalCompaniesBundle:CompanyCategory');
        foreach ($additionalCompany->getCompanyCategories() as $additionalCategory) {
            $categoryId = $additionalCategory->getCategory()->getId();
            if (isset($mainCompanyCategoriesIds[$categoryId])) {
                $categoriesToDelete[] = $categoryId;

                $em->remove($additionalCategory);
                continue;
            }

            $mainCompany->addCompanyCategory($additionalCategory);
            $companyCategoryRepository->onInsertCompanyCategory(array($mainCompany->getId()));
        }

        $companyCategoryRepository->onDeleteCompanyCategory($additionalCompany->getId(), $categoriesToDelete);

        $em->flush();

        if ($mainCompany->getPackageChecker()->isPaidAccount()) {
            foreach ($additionalCompany->getCompanyCities() as $additionalCompanyCity) {
                if (isset($mainCompanyCitiesToIds[$additionalCompanyCity->getCity()->getId()])) {
                    continue;
                }

                if ($additionalCompanyCity->getIsMainOffice()) {
                    $originalCompanyCity = $additionalCompanyCity;
                    // если офис - главный, то мы его не обновляем, а копируем, что бы если вдруг дополнительную компанию
                    // когда-то придется включить - она могла бы нормально функционировать
                    $additionalCompanyCity = new CompanyCity();
                    $additionalCompanyCity->setCompany($mainCompany);
                    $additionalCompanyCity->setCity($originalCompanyCity->getCity());
                    $additionalCompanyCity->setCreatedAt($originalCompanyCity->getCreatedAt());
                    $additionalCompanyCity->setUpdatedAt($originalCompanyCity->getUpdatedAt());
                    $additionalCompanyCity->setPhonesAsString($originalCompanyCity->getPhonesAsString());
                    $additionalCompanyCity->setEmail($originalCompanyCity->getEmail());
                    $additionalCompanyCity->setSite($originalCompanyCity->getSite());
                    $additionalCompanyCity->setAddressOld($originalCompanyCity->getAddressOld());
                    $additionalCompanyCity->setDescription($originalCompanyCity->getDescription());
                    $additionalCompanyCity->setLatitude($originalCompanyCity->getLatitude());
                    $additionalCompanyCity->setLongitude($originalCompanyCity->getLongitude());
                    $additionalCompanyCity->setAddress($originalCompanyCity->getAddress());
                    $additionalCompanyCity->setKind(CompanyCity::KIND_BRANCH_OFFICE);
                    $additionalCompanyCity->setCoordinatesUpdatedAt($originalCompanyCity->getCoordinatesUpdatedAt());
                    $mainCompany->addCompanyCity($additionalCompanyCity);
                } else {
                    $additionalCompanyCity->setCompany($mainCompany);
                    $mainCompany->addCompanyCity($additionalCompanyCity);
                }
            }
        } else {
            $this->addFlash(
                'sonata_flash_success',
                sprintf(
                    'Компания %d бесплатник, филиалы не переносим.',
                    $mainCompany->getId()
                )
            );
        }

        $em->flush();

        //Перезаполняем новыми айдишниками филиалов
        foreach ($mainCompany->getCompanyCities() as $mainCompanyCity) {
            if (!isset($mainCompanyCitiesToIds[$mainCompanyCity->getCity()->getId()])) {
                continue;
            }

            $mainCompanyCitiesToIds[$mainCompanyCity->getCity()->getId()] = $mainCompanyCity->getId();
        }

        $userRepository = $this->getDoctrine()->getRepository('MetalUsersBundle:User');
        $userRepository->createQueryBuilder('u')
            ->update('MetalUsersBundle:User', 'u')
            ->set('u.company', ':main_company')
            ->andWhere('u.company = :additional_company')
            ->setParameter('main_company', $mainCompany->getId())
            ->setParameter('additional_company', $additionalCompany->getId())
            ->getQuery()
            ->execute();

        $additionalCompany->setDeleted();

        $mainCompany->setInCrm(true);
        $additionalCompany->setInCrm(true);
        $mainCompany->scheduleSynchronization();
        $additionalCompany->scheduleSynchronization();

        $mainCompanyPhones = array();
        foreach ($mainCompany->getCompanyPhones() as $mainCompanyPhone) {
            $mainCompanyPhones[$mainCompanyPhone->getPhone()] = true;
        }

        $companyCityRepository = $em->getRepository('MetalCompaniesBundle:CompanyCity');
        foreach ($additionalCompany->getCompanyPhones() as $additionalCompanyPhone) {
            if (array_key_exists($additionalCompanyPhone->getPhone(), $mainCompanyPhones)) {
                continue;
            }

            $companyPhone = new CompanyPhone();
            $companyPhone->setCompany($mainCompany);

            $phoneBranchOffice = null;
            if ($mainCompany->getPackageChecker()->isPaidAccount()) {
                if ($additionalCompanyPhone->getBranchOffice()) {
                    $phoneBranchOffice = $companyCityRepository->findOneBy(array('company' => $mainCompany, 'city' => $additionalCompanyPhone->getBranchOffice()->getCity()));
                } elseif ($additionalCompany->getCity() instanceof City) { //TODO: у компаний может и не быть головного офиса
                    $phoneBranchOffice = $companyCityRepository->findOneBy(array('company' => $mainCompany, 'city' => $additionalCompany->getCity()));
                }
            }

            if ($phoneBranchOffice && !$phoneBranchOffice->getIsMainOffice()) {
                $companyPhone->setBranchOffice($phoneBranchOffice);
            }

            $companyPhone->setPhone($additionalCompanyPhone->getPhone());
            $companyPhone->setAdditionalCode($additionalCompanyPhone->getAdditionalCode());

            $mainCompany->addCompanyPhone($companyPhone);
        }

        if ($additionalCompany->getSites()) {
            foreach ($additionalCompany->getSites() as $site) {
                $mainCompany->addSite($site);
            }
        }

        $mainCompanyPaymentDetails = $mainCompany->getPaymentDetails();
        $additionalCompanyPaymentDetails = $additionalCompany->getPaymentDetails();

        $paymentDetailsFields = array(
            'NameOfLegalEntity',
            'Inn',
            'Kpp',
            'Orgn',
            'DirectorFullName',
            'MailAddress',
            'LegalAddress',
            'BankAccount',
            'BankCorrespondentAccount',
            'BankBik',
            'BankTitle',
        );

        $isUpdated = false;
        foreach ($paymentDetailsFields as $field) {
            $getter = 'get'.$field;
            if (!$mainCompanyPaymentDetails->$getter()) {
                $setter = 'set'.$field;
                $mainCompanyPaymentDetails->$setter($additionalCompanyPaymentDetails->$getter());
                $isUpdated = true;
            }
        }

        if (!$mainCompanyPaymentDetails->getFile()->getName() && $additionalCompanyPaymentDetails->getFile()->getName()) {
            $mainCompanyPaymentDetails->setFile($additionalCompanyPaymentDetails->getFile());
            $mainCompanyPaymentDetails->setUploadedAt($additionalCompanyPaymentDetails->getUploadedAt());
            $isUpdated = true;
        }

        if (!$mainCompanyPaymentDetails->getApprovedAt()) {
            $mainCompanyPaymentDetails->setApproved($additionalCompanyPaymentDetails->getApprovedAt() ? true : false);
        }

        if ($isUpdated) {
            $mainCompanyPaymentDetails->setUpdatedAt(new \DateTime());
        }

        if (!$mainCompany->getAddress()) {
            $mainCompany->setAddress($additionalCompany->getAddress());
        }

        $idsToAdd = array_diff($additionalCompany->getCompanyAttributesTypesIds(), $mainCompany->getCompanyAttributesTypesIds());
        foreach ($idsToAdd as $id) {
            $mainCompany->addCompanyAttributesTypesId($id);
        }

        $em->flush();

        $productRepository = $this->getDoctrine()->getRepository('MetalProductsBundle:Product');
        foreach ($additionalCompanyBranchOfficesToIds as $cityId => $additionalBranchOfficeId) {
            $qb = $productRepository->createQueryBuilder('p')
                ->update('MetalProductsBundle:Product', 'p')
                ->set('p.company', ':main_company')
                ->set('p.branchOffice', ':main_branch_office')
                ->andWhere('p.company = :additional_company')
                ->andWhere('p.isVirtual = false')
            ;

            if ($mainCompany->getPackageChecker()->isPaidAccount()) {
                //Компания платник, филиалы примержили,
                // делаем выборку по филиалу $additionalBranchOfficeId и выставляем айди нового companyCity
                // из $mainCompanyCitiesToIds по айди города
                // в $mainCompanyCitiesToIds хранятся ключь city.id значение companyCity.id новых созданых филиалов
                $qb
                    ->andWhere('p.branchOffice = :additional_branch_office')
                    ->setParameter('additional_branch_office', $additionalBranchOfficeId)
                    ->setParameter('main_branch_office', $mainCompanyCitiesToIds[$cityId]);

            } else {
                //Если компания бесплатник то мы филиалы не примерживали,
                // выставляем головной город главной компании для всех продуктов без выборки
                $qb->setParameter('main_branch_office', $mainCompany->getMainOffice());
            }

            $qb->setParameter('main_company', $mainCompany->getId())
                ->setParameter('additional_company', $additionalCompany->getId())
                ->getQuery()
                ->execute();

            //Если компания беспланик, то всем продуктам был выставлен главный офис.
            if (!$mainCompany->getPackageChecker()->isPaidAccount()) {
                break;
            }
        }

        $companyHistory = $this->container->get('metal.companies.company_service')->addCompanyHistory($mainCompany, $this->getUser(), ActionTypeProvider::UNION_COMPANY);
        $companyHistory->setRelatedCompany($additionalCompany);
        $companyHistory->setComment($comment);

        $em->flush();

        $em->commit();

        $this->addFlash(
            'sonata_flash_success',
            'Данные успешно перенесены. Компания с id '.$additionalCompany->getId().' отключена.'
        );

        return $this->redirect($this->admin->generateUrl('list', array('filter' => $this->admin->getFilterParameters())));
    }

    public function invalidateCacheAction()
    {
        $companyId = $this->admin->getSubject()->getId();

        $cache = $this->container->get('cache.app');

        $cache->invalidateTags(array(sprintf(ProductsFilteringSpec::COMPANY_TAG_PATTERN, $companyId)));

        return $this->redirect($this->admin->generateUrl('list', array('filter' => $this->admin->getFilterParameters())));
    }

    public function toggleStatusAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        /* @var $em EntityManager */
        $company = $this->admin->getSubject();
        /* @var $company Company */
        $form = $this->createForm(new CompanyToggleStatusType());

        if (!$request->isMethod('POST')) {
            return $this->render(
                'MetalCompaniesBundle:AdminCompany:company_toggle_status.html.twig',
                array(
                    'form' => $form->createView(),
                    'action' => null,
                    'object' => $company
                )
            );
        }

        $form->handleRequest($request);

        if (!$form->isValid()) {
            return $this->render(
                'MetalCompaniesBundle:AdminCompany:company_toggle_status.html.twig',
                array(
                    'form' => $form->createView(),
                    'action' => null,
                    'object' => $company
                )
            );
        }

        $comment = $form->get('comment')->getData();
        $company->setDeleted(!$company->isDeleted());

        $em->getConnection()->executeUpdate('
            UPDATE UserSend AS us
            JOIN User AS u ON us.user_id = u.User_ID
            SET us.deleted = :status
            WHERE u.ConnectCompany = :company_id',
            array('status' => $company->isDeleted(), 'company_id' => $company->getId())
        );

        $em->flush();

        $companyHistory = $this->container->get('metal.companies.company_service')->addCompanyHistory($company, $this->getUser(), ActionTypeProvider::TOGGLE_STATUS_COMPANY);
        $companyHistory->setComment($comment);

        if (!$company->isDeleted() && !$company->getVirtualProduct()) {
            $virtualProduct = Product::createVirtualProduct($company);
            $em->persist($virtualProduct);
            $em->flush();

            $company->setVirtualProduct($virtualProduct);
        }

        $em->flush();

        return $this->redirect($this->admin->generateUrl('list'));
    }

    public function changeMainCityAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        /* @var $em EntityManager */
        $company = $this->admin->getSubject();
        /* @var $company Company */

        $originalCity = $company->getCity();
        $companyCityRepository = $em->getRepository('MetalCompaniesBundle:CompanyCity');
        $oldMainCompanyCity = null;
        if ($originalCity) {
            $oldMainCompanyCity = $companyCityRepository->findOneBy(
                array('enabled' => true, 'isMainOffice' => true, 'company' => $company)
            );
        }

        $form = $this->createForm(new CompanyChangeCityType(), $company, array('city_repository' => $em->getRepository('MetalTerritorialBundle:City')));

        if (!$request->isMethod('POST')) {
            return $this->render(
                '@MetalCompanies/AdminCompany/company_change_main_city.html.twig',
                array(
                    'form' => $form->createView(),
                    'action' => null,
                    'object' => $company,
                    'hasCount' => false
                )
            );
        }

        $form->handleRequest($request);

        if (!$form->isValid()) {
            return $this->render(
                '@MetalCompanies/AdminCompany/company_change_main_city.html.twig',
                array(
                    'form' => $form->createView(),
                    'action' => null,
                    'object' => $company,
                    'hasCount' => false
                )
            );
        }

        $city = $form->get('city')->getData();
        /* @var $city City */
        $companyId = $company->getId();

        $companyService = $this->container->get('metal.companies.company_service');

        if (!$originalCity) { // компания не была привязана к городу
            $mainOffice = new CompanyCity();
            $mainOffice->setCity($city);
            $mainOffice->setIsMainOffice(true);
            $mainOffice->setKind(CompanyCity::KIND_BRANCH_OFFICE);

            $company->addCompanyCity($mainOffice);
            $company->setCity($city);

            $em->flush();

            $companyHistory = $companyService
                ->addCompanyHistory($company, $this->getUser(), ActionTypeProvider::CHANGE_COMPANY_MAIN_CITY);
            $companyHistory->setComment(sprintf('"%s" на "%s"', '-', $city->getTitle()));

        } elseif ($city->getId() != $originalCity->getId()) {
            if ($request->get('btn_update_and_edit')) {
                $productsCountFromOldBranch = $companyCityRepository->getProductsCountByCompanyAndCity($company, $originalCity);
                $productsCountFromNewBranch = $companyCityRepository->getProductsCountByCompanyAndCity($company, $city);

                return $this->render(
                    '@MetalCompanies/AdminCompany/company_change_main_city.html.twig',
                    array(
                        'form' => $form->createView(),
                        'action' => null,
                        'object' => $company,
                        'productsCountFromOldBranch' => $productsCountFromOldBranch,
                        'productsCountFromNewBranch' => $productsCountFromNewBranch,
                        'hasCount' => true
                    )
                );
            }

            $moveProducts = $request->get('btn_move_products');
            $companyCityRepository->updateMainOfficeStatus($companyId, $city->getId(), $moveProducts);

            $companyHistory = $companyService
                ->addCompanyHistory($company, $this->getUser(), ActionTypeProvider::CHANGE_COMPANY_MAIN_CITY);
            $companyHistory->setComment(sprintf('"%s" на "%s"', $originalCity->getTitle(), $city->getTitle()));
        }

        if ($oldMainCompanyCity && !$oldMainCompanyCity->getAddress()) {
            if ($oldMainCompanyCity->getCity()->getId() != $city->getId()) {
                $oldMainCompanyCity->setKind(CompanyCity::KIND_DELIVERY);
            }
        }

        $em->flush();

        $newMainCompanyCity = $companyCityRepository->findOneBy(
            array('enabled' => true, 'isMainOffice' => true, 'company' => $company)
        );

        if ($newMainCompanyCity) {
            $newMainCompanyCity->setKind(CompanyCity::KIND_BRANCH_OFFICE);
            $em->flush($newMainCompanyCity);
        }

        $em->getRepository('MetalProductsBundle:Product')->createOrUpdateVirtualProduct($company);

        if ($originalCity) {
            $this->addFlash('sonata_flash_success', 'Изменен главный город для компании.');
        } else {
            $this->addFlash('sonata_flash_success', 'Установлен главный город для компании.');
        }

        return $this->redirect($this->admin->generateUrl('edit', array('id' => $companyId)));
    }

    public function createAction()
    {
        $em = $this->getDoctrine()->getManager();
        /* @var $em EntityManager */

        $request = $this->admin->getRequest();
        $company = new Company();

        $form = $this->createForm(new CompanyCreationType(), $company, array(
                'city_repository' => $em->getRepository('MetalTerritorialBundle:City'),
                'user_repository' => $em->getRepository('MetalUsersBundle:User'),
                'is_admin_panel' => true
            ));

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);

            if (!$form->isValid()) {
                return $this->render(
                    'MetalCompaniesBundle:AdminCompany:createCompany.html.twig',
                    array(
                        'form' => $form->createView(),
                        'action' => null,
                        'object' => null
                    )
                );
            }

            $user = $form->get('user')->getData();
            if ($user->getCompany()) {
                $form->get('user')->addError(new FormError('У выбранного пользователя уже есть компания'));
            } else {
                $companyName = $form->get('companyTitle')->getData();
                $companyType = $form->get('companyType')->getData();
                $city = $form->get('city')->getData();

                $registrationService = $this->container->get('metal.users.registration_services');
                $registrationService->createCompany(
                    $company,
                    $user,
                    $user->getPhone(),
                    $companyName,
                    $companyType,
                    $city
                );

                $subscriberService = $this->container->get('metal.newsletter.subscriber_service');
                $subscriberService->createOrUpdateSubscriberForUser($user);

                $em->flush();

                $companyHistory = $this->container->get('metal.companies.company_service')->addCompanyHistory(
                    $company,
                    $this->getUser(),
                    ActionTypeProvider::COMPANY_CREATION
                );
                $companyHistory->setUser($user);

                $em->flush();

                $this->addFlash('sonata_flash_success', 'Компания успешно создана.');

                return $this->redirect($this->admin->generateUrl('edit', array('id' => $company->getId())));
            }
        }

        return $this->render(
            'MetalCompaniesBundle:AdminCompany:createCompany.html.twig',
            array(
                'form' => $form->createView(),
                'action' => null,
                'object' => null
            )
        );
    }

    public function searchBySiteAction(Request $request)
    {
        if (!$request->isMethod('post')) {
            return $this->render(
                '@MetalCompanies/AdminCompany/search_by_site.html.twig',
                array(
                    'action' => null,
                    'object' => null
                )
            );
        }

        $site = $request->request->get('search');

        if (!$site) {
            $this->addFlash('sonata_flash_error', 'Введите адрес сайта.');

            return $this->redirect($this->generateUrl('admin_metal_companies_company_search_by_site'));
        }

        return $this->redirect(
            $this->generateUrl(
                'admin_metal_companies_company_list',
                array('filter' => array('_companySites' => array('value' => $site), 'deleted' => array()))
            )
        );
    }
}
