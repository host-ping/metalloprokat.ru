<?php

namespace Metal\PrivateOfficeBundle\Controller;

use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManager;

use Metal\CategoriesBundle\Entity\Category;
use Metal\CategoriesBundle\Entity\CategoryAbstract;
use Metal\CategoriesBundle\Repository\CategoryRepository;
use Metal\CompaniesBundle\Entity\Company;
use Metal\CompaniesBundle\Entity\CompanyCity;
use Metal\CompaniesBundle\Entity\CustomCompanyCategory;
use Metal\CompaniesBundle\Repository\CustomCompanyCategoryRepository;
use Metal\PrivateOfficeBundle\Form\UpdateTimeType;
use Metal\PrivateOfficeBundle\Helper\SerializerHelper;
use Metal\ProductsBundle\ChangeSet\ProductsBatchEditChangeSet;
use Metal\ProductsBundle\Entity\Product;
use Metal\ProductsBundle\Entity\ProductImage;
use Metal\ProductsBundle\Repository\ProductImageRepository;
use Metal\ProductsBundle\Repository\ProductRepository;
use Metal\ProjectBundle\Exception\FormValidationException;
use Metal\ProjectBundle\Helper\FormattingHelper;
use Metal\ProjectBundle\Util\InsertUtil;
use Metal\UsersBundle\Entity\User;

use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpKernel\Exception\HttpException;

class PrivateProductsController extends Controller
{
    /**
     * @Security("has_role('ROLE_USER')")
     */
    public function listAction()
    {
        if (!$this->isGranted('ROLE_SUPPLIER')) {
            return $this->redirect($this->generateUrl('MetalPrivateOfficeBundle:CompanyCreation:createCompany'));
        }

        $user = $this->getUser();
        /* @var $user User */

        if (!$user->getHasEditPermission()) {
            throw $this->createAccessDeniedException('User has not edit permission');
        }

        $company = $user->getCompany();
        $em = $this->getDoctrine()->getManager();
        /* @var $em EntityManager */
        //$maxCountProductsForCompany = $em->getRepository('MetalProductsBundle:Product')->getAvailableAddProductsCountToCompany($company);
        /*vdc($maxCountProductsForCompany);*/
       // $productsImageRepository = $em->getRepository('MetalProductsBundle:ProductImage');
        ///* @var  $productsImageRepository ProductImageRepository */

//        $commonProductPhotos = $em->getRepository('MetalProductsBundle:ProductImage')->findBy(
//            array(
//                'company' => null,
//                'category' =>  $company->getCategoriesIds()
//            )
//        );
        $commonProductPhotos = array();

        $categoryRepository = $em->getRepository('MetalCategoriesBundle:Category');

        $categories = $categoryRepository->findBy(array('isEnabled' => true, 'virtual' => false));

        $categories = $categoryRepository->serializeAndFlattenCategories($categories);

        $serializerHelper = $this->get('brouzie.helper_factory')->get('MetalPrivateOfficeBundle:Serializer');
        /* @var $serializerHelper SerializerHelper */

        $commonPhotosArray = array();
        foreach ($commonProductPhotos as $commonProductPhoto) {
            $commonPhotosArray[] = $serializerHelper->serializeProductPhoto($commonProductPhoto);
        }

        $form = $this->createForm(new UpdateTimeType(), $company->getCounter());

        return $this->render('MetalPrivateOfficeBundle:PrivateProducts:list.html.twig', array(
            'commonPhotos' => $commonPhotosArray,
            'categories' => $categories,
            'company' => $company,
            'form' => $form->createView(),
            'branches' => $em->getRepository('MetalCompaniesBundle:CompanyCity')->getCompanyCitiesDataForCompany($company),
        ));
    }

    /**
     * @Security("has_role('ROLE_SUPPLIER') and user.getHasEditPermission()")
     */
    public function saveScheduledActualizationAction(Request $request)
    {
        $company = $this->getUser()->getCompany();
        /* @var $company Company */

        $em = $this->getDoctrine()->getManager();

        $form = $this->createForm(new UpdateTimeType(), $company->getCounter());
        $form->handleRequest($request);

        if (!$form->isValid()) {
            $errors = $this->get('metal.project.form_helper')->getFormErrorMessages($form);

            return JsonResponse::create(array(
                    'errors' => $errors,
                ));
        }

        $em->flush();

        return JsonResponse::create(array('status' => 'success'));
    }

    /**
     * @Security("has_role('ROLE_SUPPLIER') and user.getHasEditPermission()")
     */
    public function getProductsIdsByFilterAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $productsRepository = $em->getRepository('MetalProductsBundle:Product');
        /* @var  $productsRepository ProductRepository*/

        $company = $this->getUser()->getCompany();
        /* @var $company Company */

        $queryBag = $request->query;

        $filters = (array)json_decode($request->query->get('filters', json_encode(array())), true);

        $productsQb = $productsRepository
            ->createQueryBuilder('p')
            ->andWhere('p.company = :companyId')
            ->leftJoin('p.category', 'c')
            ->select('p.id')
            ->andWhere('p.checked <> :statuses')
            ->andWhere('p.isVirtual = :false')
            ->setParameter('false', false)
            ->setParameter('statuses', Product::STATUS_DELETED)
            ->setParameter('companyId', $company->getId())
            ->andWhere('p.branchOffice = :branchOffice')
            ->setParameter('branchOffice', $filters['filial_id']);

        // filter by status moderated/unmoderated
        switch ($filters['status_filter']) {
            case 'moderated':
                $productsQb
                    ->andWhere('p.checked = :status_ch')
                    ->setParameter('status_ch', Product::STATUS_CHECKED);
                break;
            case 'unmoderated':
                $productsQb
                    ->andWhere('p.checked = :status_ch')
                    ->setParameter('status_ch', Product::STATUS_NOT_CHECKED);
                break;
            case 'exceeding':
                $productsQb
                    ->andWhere('p.checked = :status_ch')
                    ->setParameter('status_ch', Product::STATUS_LIMIT_EXCEEDING);
                break;

        }

        // filter by photo/no photo
        switch ($filters['photo_filter']) {
            case 'with':
                $productsQb->andWhere('p.image IS NOT NULL');
                break;
            case 'without':
                $productsQb->andWhere('p.image IS NULL');
                break;
        }

        if ($filters['uncategorized']) {
            if ($queryBag->get('checkCustomCategories')) {
                $productsQb->andWhere('p.customCategory IS NULL');
            } else {
                $productsQb->andWhere('p.category = :defaultCategory')
                    ->setParameter('defaultCategory', $this->get('metal.categories.category_matcher')->getDefaultCategoryId());
            }
        } elseif (!empty($filters['category_id'])) {
            $productsQb->andWhere('p.category = :categoryId')
                ->setParameter('categoryId', $filters['category_id']);
        } elseif (!empty($filters['custom_category_id'])) {
            $productsQb->andWhere('p.customCategory = :categoryId')
                ->setParameter('categoryId', $filters['custom_category_id']);
        }

        if ($filters['title']) {
            if ($queryBag->get('checkCustomCategories')) {
                $productsQb
                    ->leftJoin('p.customCategory', 'customCategory')
                    ->andWhere('(customCategory.title LIKE :product_title OR p.title LIKE :product_title OR p.id = :product_id OR c.title LIKE :product_title OR p.size LIKE :product_title)')
                    ->setParameter('product_id', $filters['title'])
                    ->setParameter('product_title', '%'.$filters['title'].'%');
            } else {
                $productsQb
                    ->andWhere('(p.title LIKE :product_title OR p.id = :product_id OR c.title LIKE :product_title OR p.size LIKE :product_title)')
                    ->setParameter('product_id', $filters['title'])
                    ->setParameter('product_title', '%'.$filters['title'].'%');
            }
        }

        if ($filters['special_offer']) {
            $productsQb->andWhere('p.isSpecialOffer = true');
        }

        if ($filters['hot_offer']) {
            $productsQb->andWhere('p.isHotOffer = true');
        }

        // sorts by date(default)/price/title
        $sort = $queryBag->get('sort');
        if ($sort === 'price') {
            $productsQb->orderBy('p.price', 'ASC');
        } elseif ($sort === 'abc') {
            $productsQb->orderBy('p.title', 'ASC');
        } elseif ($sort === 'position') {
            $productsQb->orderBy('p.isSpecialOffer', 'DESC')
                ->addOrderBy('p.position', 'ASC');
        } else {
            $productsQb->orderBy('p.createdAt', 'DESC');
        }

        $productsIds = $productsQb->getQuery()->getResult();
        $productsIds = array_column($productsIds, 'id');

        $pendingProductsIds = $productsQb
            ->andWhere('p.checked IN (:checked)')
            ->setParameter('checked', array(Product::STATUS_PENDING_CATEGORY_DETECTION, Product::STATUS_PROCESSING))
            ->getQuery()
            ->getResult();
        $pendingProductsIds = array_column($pendingProductsIds, 'id');

        return JsonResponse::create(array('productsIds' => $productsIds, 'pendingProductsIds' => $pendingProductsIds));
    }

    /**
     * @Security("has_role('ROLE_SUPPLIER') and user.getHasEditPermission()")
     */
    public function loadCompanyPhotosAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $company = $this->getUser()->getCompany();
        /* @var $company Company */

        $perPage = 48;
        $photosSearch = $request->query->get('photosSearch');

        $productsImageRepository = $em->getRepository('MetalProductsBundle:ProductImage');
        /* @var  $productsImageRepository ProductImageRepository */

        $companyPhotosQb = $productsImageRepository->createQueryBuilder('pi')
            ->andWhere('pi.company = :companyId')
            ->andWhere('(pi.downloaded = true or pi.url IS NULL)')
            ->setParameter('companyId', $company->getId())
        ;

        if ($photosSearch) {
            $companyPhotosQb
                ->andWhere('pi.description LIKE :title')
                ->setParameter('title', '%'.$photosSearch.'%');
        }

        $adapter = new DoctrineORMAdapter($companyPhotosQb, false);
        $pagerfanta = new Pagerfanta($adapter);
        $pagerfanta->setMaxPerPage($perPage ?: 9999999);
        $pagerfanta->setCurrentPage($request->query->get('page', 1));

        $companyPhotos = iterator_to_array($pagerfanta->getIterator());

        $serializerHelper = $this->get('brouzie.helper_factory')->get('MetalPrivateOfficeBundle:Serializer');
        /* @var $serializerHelper SerializerHelper */

        $photosArray = array();
        foreach ($companyPhotos as $companyPhoto) {
            $photosArray[] = $serializerHelper->serializeProductPhoto($companyPhoto);
        }

        return JsonResponse::create(
            array(
                'companyPhotos' => $photosArray,
                'hasMore' => $pagerfanta->hasNextPage()
            )
        );
    }

    /**
     * @Security("has_role('ROLE_SUPPLIER') and user.getHasEditPermission()")
     */
    public function loadProductsByIdAction(Request $request, $for_custom_category)
    {
        $em = $this->getDoctrine()->getManager();
        $company = $this->getUser()->getCompany();
        /* @var $company Company */

        $serializerHelper = $this->get('brouzie.helper_factory')->get('MetalPrivateOfficeBundle:Serializer');
        /* @var $serializerHelper SerializerHelper */

        $productsIds = (array)$request->query->get('productsIds');

        if (!$productsIds) {
            return JsonResponse::create(array('products' => array()));
        }

        $products = $em->createQueryBuilder()
            ->select('p')
            ->addSelect('c')
            ->from('MetalProductsBundle:Product', 'p', 'p.id')
            ->leftJoin('p.category', 'c')
            ->andWhere('p.company = :companyId')
            ->setParameter('companyId', $company->getId())
            ->andWhere('p.id IN ( :productsId )')
            ->setParameter('productsId', $productsIds)
            ->andWhere('p.checked <> :statuses')
            ->setParameter('statuses', Product::STATUS_DELETED)
            ->andWhere('p.isVirtual = 0')
            ->getQuery()
            ->getResult()
        ;

        $productsArray = array();
        foreach ($productsIds as $productId) {
            if (isset($products[$productId])) {
                $productsArray[] = $serializerHelper->serializeProduct($products[$productId], $for_custom_category);
            }
        }

        return JsonResponse::create(array('products' => $productsArray));
    }

    public function loadCategoriesByFilialIdAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $company = $this->getUser()->getCompany();
        /* @var $company Company */

        $queryBag = $request->query;

        $categoryRepository = $em->getRepository('MetalCategoriesBundle:Category');
        /* @var $categoryRepository CategoryRepository */
        $customCompanyCategoryRepository = $em->getRepository('MetalCompaniesBundle:CustomCompanyCategory');
        /* @var $customCompanyCategoryRepository CustomCompanyCategoryRepository */

        $filialCategories = $categoryRepository
            ->createQueryBuilder('fc')
            ->andWhere('fc.isEnabled = true')
            ->andWhere('fc.virtual = false')
            ->join('MetalProductsBundle:Product', 'p', 'WITH', 'fc.id = p.category')
            ->where('p.company = :company_id')
            ->andWhere('p.checked <> :deleted_status')
            ->andWhere('p.branchOffice = :filial')
            ->setParameter('company_id', $company->getId())
            ->setParameter('deleted_status', Product::STATUS_DELETED)
            ->setParameter('filial', $queryBag->get('filialId'))
            ->groupBy('fc.id')
            ->getQuery()
            ->getResult();

        $customCompanyCategories = array();
        if ($queryBag->get('addCustomCategories')) {
            $customCompanyCategories = $customCompanyCategoryRepository
                ->createQueryBuilder('customCompanyCategory')
                ->andWhere('customCompanyCategory.company = :company')
                ->setParameter('company', $company)
                ->join('MetalProductsBundle:Product', 'p', 'WITH', 'customCompanyCategory.id = p.customCategory')
                ->andWhere('p.checked <> :deleted_status')
                ->setParameter('deleted_status', Product::STATUS_DELETED)
                ->andWhere('p.branchOffice = :filial')
                ->setParameter('filial', $queryBag->get('filialId'))
                ->groupBy('customCompanyCategory.id')
                ->getQuery()
                ->getResult();
        }

        $categories = array_merge($filialCategories, $customCompanyCategories);
        usort($categories, function (CategoryAbstract $a, CategoryAbstract $b) {
            return strnatcmp($a->getTitle(), $b->getTitle());
        });
        /* @var $categories CategoryAbstract[] */

        $categoriesJson = array();
        foreach ($categories as $category) {
            $categoriesJson[] = array(
                'id' => $category->getId(),
                'title' => $category->getTitle(),
                'isCustomCategory' => $category instanceof CustomCompanyCategory
            );
        }

        return JsonResponse::create(array('categories' => $categoriesJson));
    }

    /**
     * @Security("has_role('ROLE_SUPPLIER') and has_role('ROLE_CONFIRMED_EMAIL') and user.getHasEditPermission()")
     */
    public function moveToCategoryAction(Request $request)
    {
        $company = $this->getUser()->getCompany();
        /* @var $company Company */

        $categoryId = $request->request->get('category_id');
        $productsIds = $request->request->get('products');

        $em = $this->getDoctrine()->getManager();
        /* @var $em EntityManager */
        $category = $em->find('MetalCategoriesBundle:Category', $categoryId);
        $productRepository = $em->getRepository('MetalProductsBundle:Product');

        $products = $productRepository->moveToCategory($company, $category, $productsIds);

        $em->getRepository('MetalCompaniesBundle:CompanyCategory')->updateCompaniesCategories(array($company->getId() => $productsIds));

        $company->getCounter()->setProductsUpdatedAt(new \DateTime());

        $em->flush();

        $productsBatchEditChangeSet = new ProductsBatchEditChangeSet();
        $productsBatchEditChangeSet->productsToDisable = array_keys($products);

        $this->container->get('sonata.notification.backend')->createAndPublish('admin_products', array('changeset' => $productsBatchEditChangeSet));

        $formattingHelper = $this->get('brouzie.helper_factory')->get('MetalProjectBundle:Formatting');
        /* @var $formattingHelper FormattingHelper */

        return JsonResponse::create(array(
            'status' => 'success',
            'category' => array(
                'id' => $category->getId(),
                'title' => $category->getTitle(),
            ),
            'productsUpdatedAt' => $formattingHelper->formatDateTime($company->getCounter()->getProductsUpdatedAt()),
        ));
    }

    /**
     * @Security("has_role('ROLE_SUPPLIER') and has_role('ROLE_CONFIRMED_EMAIL') and user.getHasEditPermission()")
     */
    public function changeOfferStatusAction(Request $request)
    {
        $company = $this->getUser()->getCompany();
        /* @var $company Company */

        $status = $request->request->get('status');
        $productsIds = $request->request->get('products');
        $field = $request->request->get('fieldName');

        $em = $this->getDoctrine()->getManager();
        /* @var $em EntityManager */
        $productRepository = $em->getRepository('MetalProductsBundle:Product');

        $productRepository->changeOfferStatus($company, $field, $status, $productsIds);

        $company->getCounter()->setProductsUpdatedAt(new \DateTime());

        $em->flush();

        $productsToReindex = $productRepository->getProductsToEnable($company, $productsIds);
        $productsBatchEditChangeSet = new ProductsBatchEditChangeSet();
        $productsBatchEditChangeSet->productsToEnable = array_keys($productsToReindex);
        $this->container->get('sonata.notification.backend')->createAndPublish('admin_products', array('changeset' => $productsBatchEditChangeSet));

        $formattingHelper = $this->get('brouzie.helper_factory')->get('MetalProjectBundle:Formatting');
        /* @var $formattingHelper FormattingHelper */

        return JsonResponse::create(array(
            'status' => 'success',
            'productsUpdatedAt' => $formattingHelper->formatDateTime($company->getCounter()->getProductsUpdatedAt()),
        ));
    }

    /**
     * @Security("has_role('ROLE_SUPPLIER') and has_role('ROLE_CONFIRMED_EMAIL') and user.getHasEditPermission()")
     */
    public function deleteAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $company = $this->getUser()->getCompany();
        /* @var $company Company */

        $productsIds = $request->request->get('products');

        $productRepository = $this->getDoctrine()->getRepository('MetalProductsBundle:Product');
        /* @var $productRepository ProductRepository */

        $products = $em->createQueryBuilder()
            ->select('p.id')
            ->from('MetalProductsBundle:Product', 'p', 'p.id')
            ->andWhere('p.id IN (:products_id)')
            ->andWhere('p.company = :company_id')
            ->andWhere('p.checked IN (:statuses)')
            ->setParameter('products_id', $productsIds, Connection::PARAM_INT_ARRAY)
            ->setParameter('company_id', $company->getId())
            ->setParameter('statuses', array(Product::STATUS_CHECKED, Product::STATUS_LIMIT_EXCEEDING))
            ->getQuery()
            ->getResult();

        $callable = function ($productsIds) use ($productRepository, $company) {
            $productRepository->createQueryBuilder('p')
                ->andWhere('p.company = :company_id')
                ->setParameter('company_id', $company->getId())
                ->update('MetalProductsBundle:Product', 'p')
                ->andWhere('p.id IN (:products_id)')
                ->set('p.checked', ':status_delete')
                ->setParameter('products_id', $productsIds, Connection::PARAM_INT_ARRAY)
                ->setParameter('status_delete', Product::STATUS_DELETED)
                ->getQuery()
                ->execute();
        };

        InsertUtil::processBatch($productsIds, $callable, 5000);

        $em->getRepository('MetalCompaniesBundle:CompanyCounter')->generalUpdateProductsCount(array($company->getId()));
        $productRepository->updatePermissionShowProducts($company);

        $company->getCounter()->setProductsUpdatedAt(new \DateTime());

        $em->flush();

        $productsBatchEditChangeSet = new ProductsBatchEditChangeSet();
        $productsBatchEditChangeSet->productsToDisable = array_keys($products);

        $this->container->get('sonata.notification.backend')
            ->createAndPublish('admin_products', array('changeset' => $productsBatchEditChangeSet));

        $formattingHelper = $this->get('brouzie.helper_factory')->get('MetalProjectBundle:Formatting');
        /* @var $formattingHelper FormattingHelper */

        return JsonResponse::create(array(
            'status' => 'success',
            'productsUpdatedAt' => $formattingHelper->formatDateTime($company->getCounter()->getProductsUpdatedAt()),
        ));
    }

    /**
     * @ParamConverter("productImage", class="MetalProductsBundle:ProductImage", options={"id" = "photo_id"})
     * @Security("is_granted('CAN_CONNECT_PRODUCTS_WITH_PHOTO', productImage)")
     */
    public function connectProductsWithPhotoAction(Request $request, ProductImage $productImage)
    {
        if (!$productsIds = $request->request->get('products')) {
            return JsonResponse::create(array(
                'status' => 'error',
                'message' => 'Нужно выбрать товары.',
            ));
        }

        $em = $em = $this->getDoctrine()->getManager();
        /* @var $em EntityManager */
        $company = $this->getUser()->getCompany();
        /* @var $company Company */

        $productRepository = $this->getDoctrine()->getRepository('MetalProductsBundle:Product');
        /* @var $productRepository ProductRepository */
        $callable = function ($productsIds) use ($productRepository, $company, $productImage) {
            $productRepository->createQueryBuilder('p')
                ->update('MetalProductsBundle:Product', 'p')
                ->set('p.image', ':productImage')
                ->setParameter('productImage', $productImage)
                ->andWhere('p.company = :company')
                ->setParameter('company', $company)
                ->andWhere('p.id IN (:productsIds)')
                ->setParameter('productsIds', $productsIds)
                ->andWhere('p.checked = :checked')
                ->setParameter('checked', Product::STATUS_CHECKED)
                ->getQuery()
                ->execute();
        };

        InsertUtil::processBatch($productsIds, $callable, 5000);

        $company->getCounter()->setProductsUpdatedAt(new \DateTime());
        $em->flush();

        $formattingHelper = $this->get('brouzie.helper_factory')->get('MetalProjectBundle:Formatting');
        /* @var $formattingHelper FormattingHelper */

        return JsonResponse::create(array(
            'status' => 'success',
            'productsUpdatedAt' => $formattingHelper->formatDateTime($company->getCounter()->getProductsUpdatedAt()),
        ));
    }

    /**
     * @Security("has_role('ROLE_SUPPLIER') and user.getHasEditPermission() and has_role('ROLE_CONFIRMED_EMAIL')")
     */
    public function disconnectPhotoFromProductAction(Request $request)
    {
        //TODO: переписать на paramconverter+security
        //TODO: handle csrf
        $em = $this->getDoctrine()->getManager();
        /* @var $em EntityManager */
        $user = $this->getUser();
        /* @var $user User */
        $company = $user->getCompany();
        $productId = $request->request->get('product');

        $product = $em->getRepository('MetalProductsBundle:Product')
            ->findOneBy(array(
                    'id' => $productId,
                    'company' => $company,
                )
            );

        /* @var $product Product */
        if (!$product) {
            return JsonResponse::create(array(
                'status' => 'error',
                'message' => 'Photo not found.'
            ));
        }

        $product->setImage(null);
        $company->getCounter()->setProductsUpdatedAt(new \DateTime());

        $em->flush();

        $formattingHelper = $this->get('brouzie.helper_factory')->get('MetalProjectBundle:Formatting');
        /* @var $formattingHelper FormattingHelper */

        return JsonResponse::create(array(
            'status' => 'success',
            'productsUpdatedAt' => $formattingHelper->formatDateTime($company->getCounter()->getProductsUpdatedAt()),
        ));
    }

    /**
     * @Security("has_role('ROLE_SUPPLIER') and user.getHasEditPermission() and has_role('ROLE_CONFIRMED_EMAIL') and user.getCompany().getPackageChecker().isScheduledActualizationAvailable()")
     */
    public function actualizeAction()
    {
        $user = $this->getUser();
        /* @var $user User */
        $company = $user->getCompany();

        $this->get('metal.products.product_actualization_service')->actualizeProducts($company);

        $formattingHelper = $this->get('brouzie.helper_factory')->get('MetalProjectBundle:Formatting');
        /* @var $formattingHelper FormattingHelper */

        return JsonResponse::create(array(
            'status' => 'success',
            'productsUpdatedAt' => $formattingHelper->formatDateTime($company->getCounter()->getProductsUpdatedAt()),
        ));
    }

    /**
     * @Security("has_role('ROLE_SUPPLIER') and has_role('ROLE_CONFIRMED_EMAIL') and user.getHasEditPermission()")
     */
    public function importAction(Request $request)
    {
        $user = $this->getUser();
        /* @var $user User */
        $company = $user->getCompany();
        $productImportService = $this->get('metal.products.product_import_service');

        try {
            if ($request->request->get('metal_productsbundle_product[ymlUrl]', null, true)) {
                $resultsAfterImport = $productImportService->importProductsFromYml($request, $company, $user);
            } else {
                $resultsAfterImport = $productImportService->importProductsFromExcel($request, $company, $user);
            }
        } catch (FormValidationException $e) {
            $errors = $this->get('metal.project.form_helper')->getFormErrorMessages($e->getForm());

            return JsonResponse::create(array('errors' => $errors));
        }

        return JsonResponse::create(array(
            'status' => 'success',
            'html' => $this->renderView('MetalPrivateOfficeBundle:PrivateProducts:validate.html.twig', array(
                    'errorsArray' => $resultsAfterImport['resultErrors'],
                    'warningsArray' => $resultsAfterImport['resultWarnings'],
                    'countNewProducts' => $resultsAfterImport['countNewProducts'],
                    'countUpdatedProducts' => $resultsAfterImport['countUpdatedProducts']
                ))
        ));
    }

    /**
     * @Security("has_role('ROLE_SUPPLIER') and has_role('ROLE_CONFIRMED_EMAIL') and user.getHasEditPermission()")
     */
    public function exportAction(Request $request, $format)
    {
        $company = $this->getUser()->getCompany();
        $productsIds = $request->request->get('products');

        if (!$productsIds) {
            throw new HttpException(400, 'Expected products ids.');
        }

        return JsonResponse::create(
            array('url' => $this->generateUrl( 'MetalPrivateOfficeBundle:Products:downloadExport',
                array(
                    'filename' => $this->get('metal.products.product_export_service')->exportProducts($format, $company, $productsIds)
                )
            ))
        );
    }

    /**
     * @Security("has_role('ROLE_SUPPLIER') and user.getHasEditPermission()")
     */
    public function findFileUrlAction($filename)
    {
        $dir = $this->container->getParameter('upload_dir');

        $response = new BinaryFileResponse($dir.'/products-export/'.$filename);
        $response->headers->set('Content-Type', 'application/vnd.ms-excel');
        $response->setContentDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            $filename
        );

        return $response;
    }
}
