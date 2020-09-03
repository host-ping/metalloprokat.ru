<?php

namespace Metal\ProductsBundle\Controller;

use Brouzie\Components\Indexer\Indexer;
use Doctrine\ORM\EntityManager;
use Metal\CategoriesBundle\Entity\Category;
use Metal\CategoriesBundle\Entity\CategoryTestItem;
use Metal\CompaniesBundle\Entity\Company;
use Metal\ProductsBundle\ChangeSet\ProductsBatchEditChangeSet;
use Metal\ProductsBundle\DataFetching\Spec\ProductsFilteringSpec;
use Metal\ProductsBundle\Entity\Product;
use Metal\ProductsBundle\Form\ProductsBatchEditType;
use Metal\ProductsBundle\Form\ProductsImportType;
use Metal\ProductsBundle\Form\ProductsInlineEditType;
use Metal\ProductsBundle\Indexer\Operation\ProductChangeSet;
use Metal\ProductsBundle\Indexer\Operation\ProductsCriteria;
use Metal\ProjectBundle\Exception\FormValidationException;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;
use Sonata\AdminBundle\Controller\CRUDController;
use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class ProductAdminController extends CRUDController
{
    const MAX_PRODUCTS_BATCH_ACTION = 5000;

    public function createAction($id = null)
    {
        if (!$id) {
            $this->addFlash('sonata_flash_error', 'Сначала нужно выбрать компанию из списка компаний');

            return new RedirectResponse(
                $this->container->get('router')->generate('admin_metal_companies_company_list')
            );
        }

        return parent::createAction();
    }

    public function exportAction(Request $request)
    {
        if (false === $this->admin->isGranted('EXPORT')) {
            throw new AccessDeniedException();
        }

        $format = $request->get('format');

        $allowedExportFormats = (array)$this->admin->getExportFormats();

        if (!in_array($format, $allowedExportFormats)) {
            throw new \RuntimeException(
                sprintf(
                    'Export in format `%s` is not allowed for class: `%s`. Allowed formats are: `%s`',
                    $format,
                    $this->admin->getClass(),
                    implode(', ', $allowedExportFormats)
                )
            );
        }

        $dir = $this->container->getParameter('upload_dir');

        $fileName = $this->get('metal.products.product_export_service')->exportProducts($format, null, array(), $this->admin);
        $response = new BinaryFileResponse($dir.'/products-export/'.$fileName);
        $response->headers->set('Content-Type', 'application/vnd.ms-excel');

        $response->setContentDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            $fileName
        );

        return $response;
    }

    public function batchActionDisable(ProxyQueryInterface $query)
    {
        $alias = $query->getRootAliases();
        $alias = reset($alias);
        $query
            ->join(sprintf('%s.productLog', $alias), 'pl')
            ->addSelect('pl');

        $products = $query->execute();
        /* @var $products Product[] */

        if (!$products) {
            $this->addFlash('sonata_flash_error', 'Товары не выбраны.');

            return $this->redirectToProductsList();
        }

        $em = $this->getDoctrine()->getManager();
        /* @var $em EntityManager */

        $companyCounterRepository = $em->getRepository('MetalCompaniesBundle:CompanyCounter');

        $companiesIds = array();
        $productIds = array();
        $user = $this->getUser();

        foreach ($products as $product) {
            $productIds[] = $product->getId();
            $companiesIds[$product->getCompany()->getId()] = true;
            $product->getProductLog()->setUpdatedBy($user);
            $product->setChecked(Product::STATUS_DELETED);
            $product->setUpdated();
        }

        $companyCounterRepository->generalUpdateProductsCount(array_keys($companiesIds));

        $em->flush();

        $productsChangeSet = new ProductsBatchEditChangeSet();
        $productsChangeSet->productsToDisable = $productIds;

        $this->container->get('sonata.notification.backend')->createAndPublish('admin_products', array('changeset' => $productsChangeSet));

        $this->addFlash('sonata_flash_info', sprintf('Отключено %d товаров.', count($productIds)));

        return $this->redirectToProductsList();
    }

    public function deleteAllProductsAction(Request $request)
    {
        $parentAdmin = $this->admin->getParent();
        if (!$parentAdmin) {
            $this->addFlash('sonata_flash_error', 'Сначала нужно выбрать компанию из списка компаний');

            return $this->redirectToRoute('admin_metal_companies_company_list');
        }

        if (!$request->isMethod('POST')) {
            return $this->render(
                '@MetalProducts/ProductAdmin/delete_all_products.html.twig',
                array(
                    'object' => null,
                    'action' => null
                )
            );
        }

        if (!$this->isCsrfTokenValid('delete_all_products', $request->request->get('_token'))) {
            $this->addFlash('sonata_flash_error', 'Некорректное значение csrf, попробуйте еще раз.');

            return $this->redirectToProductsList();
        }

        /** @var Company $company */
        $company = $parentAdmin->getSubject();

        $productsRepo = $this->getDoctrine()->getRepository('MetalProductsBundle:Product');
        /** @var Indexer $indexer */
        $indexer = $this->get('metal_products.indexer.products');

        foreach ($productsRepo->getAllCompanyProductsPerBatches($company) as $productsIds) {
            $productsRepo->disableProducts($productsIds, $this->getUser());
            $indexer->delete($productsIds);
        }

        $cache = $this->container->get('cache.app');
        $cache->invalidateTags(array(sprintf(ProductsFilteringSpec::COMPANY_TAG_PATTERN, $company->getId())));

        $this->addFlash('sonata_flash_success', 'Все товары были удалены.');

        return $this->redirectToProductsList();
    }

    public function batchActionEdit(ProxyQueryInterface $query)
    {
        $request = $this->admin->getRequest();

        $products = $query->execute();
        /* @var $products Product[] */

        $productsLockedForEdit = array_values(array_filter($products, function(Product $product) {
                return $product->isLockedForEditing();
            }));

        $allowActions = array(
            'checked' => array(
                Product::STATUS_CHECKED,
                Product::STATUS_DELETED,
                Product::STATUS_NOT_CHECKED,
                Product::STATUS_PROCESSING,
                Product::STATUS_PENDING_CATEGORY_DETECTION,
                Product::STATUS_LIMIT_EXCEEDING,
            ),
            'isSpecialOffer' => array(
                Product::STATUS_CHECKED,
                Product::STATUS_DELETED,
                Product::STATUS_NOT_CHECKED,
                Product::STATUS_PENDING_CATEGORY_DETECTION,
            ),
            'isHotOffer' => array(
                Product::STATUS_CHECKED,
                Product::STATUS_DELETED,
                Product::STATUS_NOT_CHECKED,
                Product::STATUS_PENDING_CATEGORY_DETECTION,
            ),
            'isPriceFrom' => array(
                Product::STATUS_CHECKED,
                Product::STATUS_DELETED,
                Product::STATUS_NOT_CHECKED,
                Product::STATUS_PENDING_CATEGORY_DETECTION,
            ),
            'category' => array(
                Product::STATUS_CHECKED,
                Product::STATUS_DELETED,
                Product::STATUS_NOT_CHECKED,
                Product::STATUS_PENDING_CATEGORY_DETECTION,
            ),
            'measureId' => array(
                Product::STATUS_CHECKED,
                Product::STATUS_DELETED,
                Product::STATUS_NOT_CHECKED,
            ),
            'branchOffice' => array(
                Product::STATUS_CHECKED,
                Product::STATUS_DELETED,
                Product::STATUS_NOT_CHECKED,
                Product::STATUS_PENDING_CATEGORY_DETECTION,
            ),
            'image' => array(
                Product::STATUS_CHECKED,
                Product::STATUS_DELETED,
                Product::STATUS_NOT_CHECKED,
                Product::STATUS_PENDING_CATEGORY_DETECTION,
            )
        );

        $companyId = $this->get('request_stack')->getMasterRequest()->attributes->get('id');
        if (!$products) {
            $this->addFlash('sonata_flash_error', 'Все выбранные товары заблокированы для редактирования');

            return $this->redirectToProductsList();
        }

        if (count($products) > self::MAX_PRODUCTS_BATCH_ACTION) {
            $this->addFlash('sonata_flash_error', sprintf(
                'Максимальное число продуктов для редактирования %d.',
                self::MAX_PRODUCTS_BATCH_ACTION)
            );

            return $this->redirectToProductsList();
        }

        $usedCompanies = array();
        foreach ($products as $selectedProduct) {
            $usedCompanies[$selectedProduct->getCompany()->getId()] = true;
        }

        $form = $this->createForm(
            new ProductsBatchEditType(),
            null,
            array(
                'company_id' => count($usedCompanies) == 1 ? $products[0]->getCompany()->getId() : null,
                'show_images' => (bool)$companyId
            )
        );

        if (!$request->request->get('submitted')) {
            return $this->render(
                'MetalProductsBundle:ProductAdmin:batch_action_edit.html.twig',
                array(
                    'form' => $form->createView(),
                    'object' => null,
                    'action' => null,
                    'productsNotProcessed' => count($productsLockedForEdit)
                )
            );
        }

        $form->handleRequest($request);


        if ($form->has('category') && $form->get('categoryEditable')->getData()) {
            $category = $form->get('category')->getData();
            /* @var $category Category */
            if (!$category || !$category->getAllowProducts()) {
                $this->addFlash('sonata_flash_error', 'Выбрана категория к которой нельзя присоеденять товары');
                return $this->render(
                    'MetalProductsBundle:ProductAdmin:batch_action_edit.html.twig',
                    array(
                        'form' => $form->createView(),
                        'object' => null,
                        'action' => null,
                        'productsNotProcessed' => count($productsLockedForEdit)
                    )
                );
            }
        }

        $isFieldEditable = function ($field) use ($form) {
            return $form->has($field) && $form->get($field . 'Editable')->getData();
        };

        $productsToEnable = array();
        $productsToDisable = array();
        $productsToChangeCategory = array();
        $productsIdsWithErrors = array();

        if ($isFieldEditable('checked')) {
            if ($form->get('checked')->getData() == Product::STATUS_CHECKED) {
                $newCategory = null;
                if ($form->has('category')) {
                    $newCategory = $form->get('category')->getData();
                }

                $newBranchOffice = null;
                if ($form->has('branchOffice')) {
                    $newBranchOffice = $form->get('branchOffice')->getData();
                }

                $newMeasureId = null;
                if ($form->has('measureId')) {
                    $newMeasureId = $form->get('measureId')->getData();
                }

                foreach ($products as $product) {
                    $errorFields = array();
                    if (!$product->getCategory() && !$newCategory) {
                        $errorFields[] = 'category';
                    }

                    if (!$product->getBranchOffice() && !$newBranchOffice) {
                        $errorFields[] = 'branchOffice';
                    }

                    if (null === $product->getMeasureId() && null === $newMeasureId) {
                        $errorFields[] = 'measureId';
                    }

                    if ($errorFields) {
                        $productsIdsWithErrors[$product->getId()] = $errorFields;
                        continue;
                    }


                    if ($product->isModerated()) {
                        continue; // product already enabled, nothing to do with it
                    }

                    $productsToEnable[$product->getId()] = true;
                }
            } else {
                foreach ($products as $product) {
                    if (!$product->isModerated()) {
                        continue; // product already disabled, nothing to do with it
                    }

                    $productsToDisable[$product->getId()] = true;
                }
            }
        }

        if ($isFieldEditable('category')) {
            $newCategory = $form->get('category')->getData();
            $newCategoryId = $newCategory ? $newCategory->getId() : null;

            foreach ($products as $product) {
                if (isset($productsToEnable[$product->getId()]) || isset($productsToDisable[$product->getId()]) || !$product->isModerated()) {
                    continue; // already scheduled for changes, so skip them
                }

                if ($product->getCategoryId() == $newCategoryId) {
                    continue; // category wasn't changed
                }

                $productsToChangeCategory[$product->getId()] = array('old' => $product->getCategoryId(), 'new' => $newCategoryId);
            }
        }

        $changeSet = new ProductChangeSet();
        $hasChanges = false;
        if ($isFieldEditable('isSpecialOffer')) {
            $hasChanges = true;
            $changeSet->setIsSpecialOffer($form->get('isSpecialOffer')->getData());
        }

        if ($isFieldEditable('isHotOffer')) {
            $hasChanges = true;
            $changeSet->setIsHotOffer($form->get('isHotOffer')->getData());
        }

        $criteria = new ProductsCriteria();
        foreach ($products as $product) {
            $criteria->addId($product->getId());
        }

        $em = $this->getDoctrine()->getManager();
        /* @var $em EntityManager */
        $fields = array(
            'checked' => 'setChecked',
            'isSpecialOffer' => 'setIsSpecialOffer',
            'isHotOffer' => 'setIsHotOffer',
            'isPriceFrom' => 'setIsPriceFrom',
            'category' => 'setCategory',
            'measureId' => 'setMeasureId',
            'branchOffice' => 'setBranchOffice',
            'image' => 'setImage',
        );

        // batch loading of product log
        if ($products) {
            $em->getRepository('MetalProductsBundle:ProductLog')->findBy(array('product' => $products));
        }

        $flushRequired = false;
        $productsNotAllowedMethod = array();
        $updatedProductsIds = array();
        foreach ($fields as $field => $setter) {
            if ($isFieldEditable($field)) {
                $flushRequired = true;
                foreach ($products as $product) {
                    if (!in_array($product->getChecked(), $allowActions[$field])) {
                        if (!isset($productsNotAllowedMethod[$field])) {
                            $productsNotAllowedMethod[$field] = array();
                        }
                        $productsNotAllowedMethod[$field][] = $product->getId();
                        continue;
                    }

                    if (isset($productsIdsWithErrors[$product->getId()])) {
                        continue;
                    }

                    $product->$setter($form->get($field)->getData());
                    $product->getProductLog()->setUpdatedBy($this->getUser());
                    $product->setUpdated();
                    $updatedProductsIds[] = $product->getId();
                }
            }
        }

        $errorProductsIds = array_keys($productsIdsWithErrors);
        if ($productsNotAllowedMethod) {
            foreach ($productsNotAllowedMethod as $key => $productsIds) {
                $this->addFlash('sonata_flash_error', sprintf('Смена %s для продуктов: %s запрещена.', $key, implode(',', $productsIds)));
                $errorProductsIds = array_unique(array_merge($errorProductsIds, $productsIds));
            }
        }

        if (!$flushRequired) {
            $this->addFlash('sonata_flash_error', 'Ни один пункт не был выбран');

            return $this->redirectToProductsList();
        }

        foreach ($productsIdsWithErrors as $productId => $fields) {
            $this->addFlash('sonata_flash_error', sprintf('При обновлении продукта %d произошли ошибки: %s',  $productId, implode(',', array_values($fields))));
        }

        $em->flush();

        if ($isFieldEditable('checked') || $isFieldEditable('branchOffice')) {
            $productsHashes = array();
            foreach ($products AS $product) {
                $productsHashes[$product->getItemHash()] = true;
            }

            $productsForDisabling = $em->getRepository('MetalProductsBundle:Product')->disableDuplicatedProductsByProductHashes(array_keys($productsHashes));

            if ($productsForDisabling) {
                $this->container->get('metal_products.indexer.products')->delete($productsForDisabling);
                $this->addFlash(
                    'sonata_flash_success',
                    sprintf('Отключено дубликатов продуктов: %d', count($productsForDisabling))
                );
            }
            unset($productsForDisabling);
        }

        //TODO перенести в консамеры
        $companyCityRepository = $em->getRepository('MetalCompaniesBundle:CompanyCity');

        $companiesIds = array();
        foreach ($products as $product) {
            $companiesIds[$product->getCompany()->getId()] = true;
        }

        $companyCityRepository->refreshBranchOfficeHasProducts(array_keys($companiesIds));

        /** @var \Brouzie\Components\Indexer\Indexer $indexer */
        $indexer = $this->get('metal_products.indexer.products');
        //TODO: move this checks to Indexer class + isEmpty methods for criteria
        if ($hasChanges && count($criteria->getIds())) {
            $indexer->update($changeSet, $criteria);
        }

        //TODO: Подумать как лучше это оптимизировать
        /*
        $productsIdsToReindex = $companyCityRepository->getProductsIdsForReindex(array_keys($companiesIds));

        if ($productsIdsToReindex) {
            $this->container->get('sphinxy.index_manager')->reindexItems('products', $productsIdsToReindex);
        }*/

        $this->addFlash('sonata_flash_success', sprintf('Обновлено продуктов: %d', count(array_diff(array_unique($updatedProductsIds), $errorProductsIds))));

        $productsChangeSet = new ProductsBatchEditChangeSet();
        $productsChangeSet->productsToEnable = array_keys($productsToEnable);
        $productsChangeSet->productsToDisable = array_keys($productsToDisable);
        $productsChangeSet->productsToChangeCategory = $productsToChangeCategory;

        $this->container->get('sonata.notification.backend')->createAndPublish('admin_products', array('changeset' => $productsChangeSet));

        return $this->redirectToProductsList();
    }

    public function editInlineAction()
    {
        $request = $this->admin->getRequest();

        $em = $this->getDoctrine()->getManager();
        /* @var $em EntityManager */

        $product = $this->admin->getSubject();
        /* @var $product Product */

        if (!$product) {
            return JsonResponse::create(array('errors' => 'Продукт не найден.'));
        }

        $productValueTitle = $this->container->getParameter('tokens.product_volume_title');
        $form = $this->container->get('form.factory')->createNamed(
            ProductsInlineEditType::getNameForProduct($product),
            new ProductsInlineEditType(),
            $product,
            array('product_volume_title' => $productValueTitle)
        );

        $form->handleRequest($request);

        if (!$form->isValid()) {
            $errors = $this->get('metal.project.form_helper')->getFormErrorMessages($form);

            return JsonResponse::create(
                array(
                    'errors' => $errors
                )
            );
        }

        $product->getProductLog()->setUpdatedBy($this->getUser());

        $em->flush();

        $productsChangeSet = new ProductsBatchEditChangeSet();

        if (in_array($form->get('checked')->getData(), array(Product::STATUS_DELETED, Product::STATUS_NOT_CHECKED))) {
            $productsChangeSet->productsToDisable = array($product->getId());
        } else {
            $productsChangeSet->productsToEnable = array($product->getId());
        }

        $this->container->get('sonata.notification.backend')->createAndPublish('admin_products', array('changeset' => $productsChangeSet));

        return JsonResponse::create(
            array(
                'status' => 'success',
            )
        );
    }

    public function importProductsAction(Request $request, $id = null)
    {
        if (!$id) {
            $this->addFlash('sonata_flash_error', 'Сначала нужно выбрать компанию из списка компаний');

            return $this->redirectToRoute('admin_metal_companies_company_list');
        }

        $form = $this->createForm(new ProductsImportType(), null, array('company_id' => $id, 'xls' => true));

        if (!$request->isMethod('POST')) {
            return $this->render(
                'MetalProductsBundle:ProductAdmin:import_products.html.twig',
                array(
                    'form' => $form->createView(),
                    'object' => null,
                    'action' => null
                )
            );
        }

        $form->handleRequest($request);

        $company = $this->admin->getParent()->getSubject();

        try {
            $resultsAfterImport = $this->get('metal.products.product_import_service')->importProductsFromExcel(
                $request,
                $company,
                $this->getUser(),
                false,
                $form->get('template')->getData()
            );
            $this->addFlash(
                'sonata_flash_success',
                'Добавлено продуктов: ' . $resultsAfterImport['countNewProducts']
            );
            $this->addFlash(
                'sonata_flash_success',
                'Обновлено продуктов: ' . $resultsAfterImport['countUpdatedProducts']
            );

            foreach ($resultsAfterImport['resultErrors'] as $rowNum => $resultErrors) {
                foreach ($resultErrors as $error) {
                    $this->addFlash('sonata_flash_error', 'Ошибка в строке ' . $rowNum . ': ' . $error);
                }
            }

            foreach ($resultsAfterImport['resultWarnings'] as $rowNum => $resultWarnings) {
                foreach ($resultWarnings as $warning) {
                    $this->addFlash('sonata_flash_info', 'Предупреждение в строке ' . $rowNum . ': ' . $warning);
                }
            }

        } catch (FormValidationException $e) {
            $resultErrors = $this->get('metal.project.form_helper')->getFormErrorMessages($e->getForm());
            foreach ($resultErrors as $errors) {
                foreach ($errors as $error) {
                    $this->addFlash('sonata_flash_error', $error);
                }
            }
        }

        return $this->redirectToProductsList();
    }

    private function redirectToProductsList()
    {
        $parentAdmin = $this->admin->getParent();

        if ($parentAdmin) {
            return $this->redirect(
                $this->admin->generateUrl(
                    'list',
                    [
                        $parentAdmin->getIdParameter() => $parentAdmin->getSubject()->getId(),
                        'filters' => ['_sort_order' => 'DESC', '_sort_by' => 'updatedAt']
                    ]
                )
            );
        }

        return $this->redirect(
            $this->admin->generateUrl(
                'list',
                [
                    'filters' => ['_sort_order' => 'DESC', '_sort_by' => 'updatedAt']
                ]
            )
        );
    }

    public function batchActionAddToTests(ProxyQueryInterface $query)
    {
        $em = $this->getDoctrine()->getManager();
        /* @var $em EntityManager */

        $products = $query->execute();
        /* @var $products Product[] */

        foreach ($products as $product) {
            $testCase = new CategoryTestItem();
            $testCase->setTitle($product->getTitle());
            $testCase->setSizeString($product->getSize());
            $testCase->setCategory($product->getCategory());

            $productParams = $em->getRepository('MetalProductsBundle:ProductParameterValue')->findBy(array('product' => $product));
            foreach ($productParams as $productParam) {
                $testCase->setParam($productParam->getParameterOption());
            }
            $em->persist($testCase);
        }
        $em->flush();
        $this->addFlash('sonata_flash_success', count($products).' позиций добавлено в тест-кейсы');

        return $this->redirectToProductsList();
    }

    public function batchActionSetParameter(ProxyQueryInterface $query)
    {
        return $this->batchActionSetCategoryParameter($query, false);
    }

    public function batchActionSetCategoryParameter(ProxyQueryInterface $query, $detectCategory = true)
    {
        $em = $this->getDoctrine()->getManager();
        /* @var $em EntityManager */

        $products = $query->execute();
        /* @var $products Product[] */

        $productsToChangeCategory = array();
        $categoryService = $this->get('metal.categories.category_matcher');

        $count = 0;
        foreach ($products as $product) {
            if ($product->isModerated() || $product->isPending() || $product->isWaitingForModeration()) {

                if ($detectCategory) {
                    $category = $categoryService->getCategoryByTitle($product->getTitle());
                    $productsToChangeCategory[$product->getId()] = array('old' => $product->getCategoryId(), 'new' => $category->getId());
                    $product->setCategory($category);
                } else {
                    $productsToChangeCategory[$product->getId()] = array('old' => $product->getCategoryId(), 'new' => $product->getCategoryId());
                }

                //Не сбрасываем модерацию, при смени категории происходит реиндекс а не removeItems и из сфинкса они не пропадут а из mysql не выберутся
                //$product->setChecked(Product::STATUS_NOT_CHECKED);
                $count ++;
            }
        }
        $em->flush();

        $productsChangeSet = new ProductsBatchEditChangeSet();
        $productsChangeSet->productsToChangeCategory = $productsToChangeCategory;

        $this->container->get('sonata.notification.backend')->createAndPublish('admin_products', array('changeset' => $productsChangeSet));

        if ($detectCategory) {
            $this->addFlash('sonata_flash_success', sprintf('Обновлены категории и параметры для %d продуктов, статус модерации продуктов остался прежний.', $count));
        } else {
            $this->addFlash('sonata_flash_success', sprintf('Обновлены параметры для %d продуктов, статус модерации продуктов остался прежний.', $count));
        }

        return $this->redirectToProductsList();
    }

    public function getProductsImagesAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        /* @var $em EntityManager */

        $name = $request->query->get('name', '');
        $page = $request->query->get('page', 1);
        $onlyCompany = $request->query->get('only_company') === 'true';
        $companyId = $request->attributes->get('id');

        $qb = $em->getRepository('MetalProductsBundle:ProductImage')->getProductsImages($companyId, $name, $onlyCompany);

        $adapter = new DoctrineORMAdapter($qb, false);
        $pagerfanta = new Pagerfanta($adapter);
        $pagerfanta->setMaxPerPage(Product::MAX_RESULT_IMAGES_IN_ADMINPANEL);
        $pagerfanta->setCurrentPage($page);

        return $this->render(
            '@MetalProducts/ProductAdmin/products_images.html.twig',
            array('pagerfanta' => $pagerfanta, 'imageName' => 'metal_productadmin[image]')
        );
    }
}
