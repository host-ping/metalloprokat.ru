<?php

namespace Metal\ProductsBundle\Admin;

use Brouzie\Components\Indexer\Indexer;
use Doctrine\ORM\EntityManager;

use Metal\CategoriesBundle\Service\CategoryDetectorInterface;
use Metal\CompaniesBundle\Entity\CompanyCity;
use Metal\CompaniesBundle\Entity\ValueObject\CompanyPackageTypeProvider;
use Metal\CompaniesBundle\Repository\CompanyCityRepository;
use Metal\ProductsBundle\ChangeSet\ProductsBatchEditChangeSet;
use Metal\ProductsBundle\Entity\Product;
use Metal\ProductsBundle\Entity\ValueObject\CurrencyProvider;
use Metal\ProductsBundle\Entity\ValueObject\ProductMeasureProvider;
use Metal\ProductsBundle\Form\ProductsInlineEditType;
use Metal\ProductsBundle\Indexer\Operation\ProductChangeSet;
use Metal\ProductsBundle\Indexer\Operation\ProductsCriteria;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\CoreBundle\Validator\ErrorElement;
use Sonata\DoctrineORMAdminBundle\Datagrid\ProxyQuery;
use Sonata\NotificationBundle\Backend\BackendInterface;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class ProductAdmin extends AbstractAdmin
{
    protected $datagridValues = array(
        '_sort_order' => 'DESC',
        '_sort_by' => 'id',
//        '_editingMode' => array(
//            'value' => 'batch',
//        ),
    );

    protected $maxPerPage = 100;
    protected $perPageOptions = array(100, 200, 500, 1500, 2500, 5000);
    protected $parentAssociationMapping = 'company';

    private $iterator = -1;
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var BackendInterface
     */
    private $notificationBackend;

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    private $indexer;

    /**
     * @var CategoryDetectorInterface
     */
    private $categoryDetector;

    private $productIdToReindex;

    public function __construct(
        $code,
        $class,
        $baseControllerName,
        EntityManager $em,
        BackendInterface $notificationBackend,
        TokenStorageInterface $tokenStorage,
        Indexer $indexer,
        CategoryDetectorInterface $categoryDetector
    ) {
        parent::__construct($code, $class, $baseControllerName);

        $this->notificationBackend = $notificationBackend;
        $this->tokenStorage = $tokenStorage;

        $this->em = $em;
        $this->indexer = $indexer;
        $this->categoryDetector = $categoryDetector;
    }

    public function isGranted($name, $object = null)
    {
        /* @var $object Product */
        if ($name === 'EDIT' && $object && $object->isLockedForEditing()) {
            return false;
        }

        return parent::isGranted($name, $object);
    }

    protected function configureRoutes(RouteCollection $collection)
    {
        $collection
            ->remove('delete')
            ->add('edit_inline', $this->getRouterIdParameter().'/edit_inline', array('_controller' => 'MetalProductsBundle:ProductAdmin:editInline'))
            ->add('import', 'import', array('_controller' => 'MetalProductsBundle:ProductAdmin:importProducts'))
            ->add('delete_all', 'delete_all', array('_controller' => 'MetalProductsBundle:ProductAdmin:deleteAllProducts'))
            ->add('get_products_images', 'get-products-images', array('_controller' => 'MetalProductsBundle:ProductAdmin:getProductsImages'));

        if (!$this->isChild()) {
            $collection->remove('export');
        }
    }

    public function hasRoute($name)
    {
        //TODO: remove this method override when https://github.com/sonata-project/SonataAdminBundle/pull/3024 will be merged
        $filterParameters = $this->getFilterParameters();

        if ($name === 'batch' && isset($filterParameters['_editingMode']) && $filterParameters['_editingMode']['value'] === 'inline') {
            return false;
        }

        return parent::hasRoute($name);
    }

    public function configure()
    {
        $this->setTemplate('list', 'MetalProductsBundle:ProductAdmin:list.html.twig');
    }

    public function getBatchActions()
    {
        $filterParameters = $this->getFilterParameters();

        if (isset($filterParameters['_editingMode']) && $filterParameters['_editingMode']['value'] === 'inline') {
            return array();
        }

        return array(
            'edit' => array(
                'label' => 'Редактировать',
                'ask_confirmation' => false,
            ),
            'set_parameter' => array(
                'label' => 'Определить параметры',
                'ask_confirmation' => false
            ),
            'set_category_parameter' => array(
                'label' => 'Определить категорию и параметры',
                'ask_confirmation' => false
            ),
            'add_to_tests' => array(
                'label' => 'Добавить в тесты',
                'ask_confirmation' => false
            ),
            'disable' => array(
                'label' => 'Отключить',
                'ask_confirmation' => true,
            )
        );
    }

    public function setRequest(Request $request)
    {
        parent::setRequest($request);

        $filterParameters = $this->getFilterParameters();

        if (isset($filterParameters['_editingMode']) && $filterParameters['_editingMode']['value'] === 'inline') {
            $this->setTemplate('inner_list_row', 'MetalProductsBundle:ProductAdmin:list_inner_row.html.twig');
        }
    }

    public function createQuery($context = 'list')
    {
        $query = parent::createQuery($context);
        $alias = $query->getRootAliases();
        $alias = reset($alias);
        $query->andWhere(sprintf('%s.isVirtual = false', $alias));
        //$query->join(sprintf('%s.branchOffice', $alias), 'bo')->addSelect('bo');
        //$query->join(sprintf('%s.company', $alias), 'company')->addSelect('company');
        //$query->leftJoin(sprintf('%s.image', $alias), 'i')->addSelect('i');

        return $query;
    }

    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $id = $this->getRequest()->attributes->get('id');
        $filterParameters = $this->getFilterParameters();

        $categories = $this->em->getRepository('MetalCategoriesBundle:Category')->getCategoriesAsSimpleArray(
            true,
            false,
            false,
            $this->isChild() ? $id : null,
            true,
            isset($filterParameters['checked']['value']) && $filterParameters['checked']['value'] !== '' ? $filterParameters['checked']['value'] : null,
            isset($filterParameters['image']['value']) && $filterParameters['image']['value'] !== '' ? $filterParameters['image']['value'] : null
        );

        $datagridMapper
            ->add('id', null, array('label' => 'ID'))
            ->add(
                'title',
                null,
                array(
                    'label' => 'Название товара'
                )
            )
            ->add(
                'checked',
                'doctrine_orm_choice',
                array(
                    'label' => 'Статус модерации товара'
                ),
                'choice',
                array(
                    'choices' => Product::getProductStatusesAsSimpleArray()
                )
            )
            ->add(
                'category_ancestor',
                'doctrine_orm_callback',
                array(
                    'label' => 'Категория с дочерними',
                    'callback' => function (ProxyQuery $queryBuilder, $alias, $field, $value) {
                        if (!isset($value['value'])) {
                            return;
                        }
                        $queryBuilder
                            ->join('MetalCategoriesBundle:CategoryClosure', 'closure', 'WITH',  sprintf('%s.category = closure.descendant', $alias))
                            ->andWhere('closure.ancestor = :closure_ancestor')
                            ->setParameter('closure_ancestor', $value['value'])
                        ;

                        return true;
                    }
                ),
                'choice',
                array(
                    'choices' => $categories
                )
            )
            ->add(
                'category',
                'doctrine_orm_choice',
                array(
                    'label' => 'Категория',
                ),
                'choice',
                array(
                    'choices' => $categories
                )
            )
            ->add(
                '_not_other_category',
                'doctrine_orm_callback',
                array(
                    'label' => 'Без товаров из прочего',
                    'mapped' => false,
                    'callback' => function (ProxyQuery $queryBuilder, $alias, $field, $value) {
                        if (!isset($value['value']) || 'yes' !== $value['value']) {
                            return;
                        }
                        $queryBuilder->andWhere(sprintf('%s.category <> :default_category_id', $alias))
                            ->setParameter('default_category_id', $this->categoryDetector->getDefaultCategoryId())
                        ;
                    }
                ),
                'choice',
                array('choices' => array('yes' => 'Да'))
            )
            ->add(
                '_editingMode',
                'doctrine_orm_callback',
                array(
                    'label' => 'Режим редактирования',
                    'mapped' => false,
                    'callback' => function () {
                    }
                ),
                'choice',
                array('choices' => array('batch' => 'Пакетный', 'inline' => 'Построчный'))
            )
            ->add(
                'hasImage',
                'doctrine_orm_callback',
                array(
                    'label' => 'Наличие фото',
                    'callback' => function (ProxyQuery $queryBuilder, $alias, $field, $value) {
                        if (!isset($value['value'])) {
                            return;
                        }

                        if ($value['value'] === 'y') {
                            $queryBuilder->andWhere(sprintf('%s.image IS NOT NULL', $alias));
                        } else {
                            $queryBuilder->andWhere(sprintf('%s.image IS NULL', $alias));
                        }

                        return true;
                    }
                ),
                'choice',
                array(
                    'choices' => array(
                        'y' => 'С фото',
                        'n' => 'Без фото'
                    )
                )
            )
            ->add('image', 'doctrine_orm_number', array('label' => 'ID фото'))
            ->add(
                'isSpecialOffer',
                'doctrine_orm_boolean',
                array(
                    'label' => 'Спецпредложение'
                ),
                'sonata_type_boolean'
            )
            ->add(
                'isHotOffer',
                'doctrine_orm_boolean',
                array(
                    'label' => 'Горячее предложение'
                ),
                'sonata_type_boolean'
            )
            ->add(
                'company.codeAccess',
                'doctrine_orm_choice',
                array(
                    'label' => 'Флаг клиента',
                ),
                'choice',
                array(
                    'choices' => CompanyPackageTypeProvider::getAllCompanyPackagesAsSimpleArray()
                )
            )
            ->add(
                '_titleLength',
                'doctrine_orm_callback',
                array(
                    'label' => 'Длина названия',
                    'callback' => function ($queryBuilder, $alias, $field, $value) {
                        if (!isset($value['value'])) {
                            return;
                        }

                        if ($value['value'] === 'y') {
                            $queryBuilder->andWhere(sprintf('LENGTH(%s.title) > 120', $alias));
                        } else {
                            $queryBuilder->andWhere(sprintf('LENGTH(%s.title) <= 120', $alias));
                        }

                        return true;
                    }
                ),
                'choice',
                array(
                    'choices' => array(
                        'y' => 'Больше 120',
                        'n' => 'Меньше или равно 120'
                    )
                )
            );

        if ($this->isChild()) {
            $datagridMapper
                ->add(
                    'branchOffice',
                    'doctrine_orm_choice',
                    array(
                        'label' => 'Филиал',
                    ),
                    'entity',
                    array(
                        'class' => 'MetalCompaniesBundle:CompanyCity',
                        'query_builder' => function (CompanyCityRepository $rep) use ($id) {
                            return $rep->createQueryBuilder('cc')
                                ->join('cc.city', 'c')
                                ->addSelect('c')
                                ->andWhere('cc.company = :company_id')
                                ->andWhere('cc.kind = :kind')
                                ->setParameter('company_id', $id)
                                ->setParameter('kind', CompanyCity :: KIND_BRANCH_OFFICE)
                                ->orderBy('cc.isMainOffice', 'DESC')
                                ->addOrderBy('c.title');
                            },
                        'property' => 'city.title'
                    )
                )
            ;
        }

        $datagridMapper->add('category.allowProducts',
            'doctrine_orm_callback',
            array(
                'label' => 'Разрешены продукты',
                'callback' => function (ProxyQuery $queryBuilder, $alias, $field, $value) {
                    if (!isset($value['value'])) {
                        return null;
                    }

                    #$alias тут равен s_category и мы пытаемся приджоинить от категории категорию
                    #see  https://github.com/sonata-project/SonataDoctrineORMAdminBundle/pull/482

                    $aliases = $queryBuilder->getRootAliases();
                    $queryBuilder->join(sprintf('%s.category', reset($aliases)), 'c');

                        if ($value['value'] === 'y') {
                            $queryBuilder->andWhere('c.allowProducts = true');
                        } else {
                            $queryBuilder->andWhere('c.allowProducts = false');
                        }

                        return true;
                    }
                ),
                'choice',
                array('choices' => array('y' => 'Да', 'n' => 'Нет'))
            )
            ->add('_haveAttributes',
                'doctrine_orm_callback',
                array(
                    'label' => 'Есть присоединенные атрибуты',
                    'callback' => function (ProxyQuery $queryBuilder, $alias, $field, $value) {
                        if (!isset($value['value'])) {
                            return;
                        }

                        $dql = sprintf(
                            'SELECT ppv.id FROM MetalProductsBundle:ProductParameterValue AS ppv WHERE ppv.product = %s.id',
                            $alias
                        );

                        if ($value['value'] === 'y') {
                            $queryBuilder->andWhere(sprintf('EXISTS(%s)', $dql));
                        } else {
                            $queryBuilder->andWhere(sprintf('NOT EXISTS(%s)', $dql));
                        }

                        return true;
                    }
                ),
                'choice',
                array('choices' => array('y' => 'Да', 'n' => 'Нет'))
            )
            ->add(
                'currencyId',
                'doctrine_orm_choice',
                array(
                    'label' => 'Валюта',
                ),
                'choice',
                array(
                    'choices' => CurrencyProvider::getAllTypesEnAsSimpleArray()
                )
            )
        ;
    }

    public function getExportFormats()
    {
        return array('xls', 'xls-admin');
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $container = $this->getConfigurationPool()->getContainer();
        $listMapper
            ->addIdentifier('id')
            ->add('title', null, array('label' => 'Название', 'template' => 'MetalProductsBundle:ProductAdmin:colorModeratedText.html.twig'))
            ->add('size', null, array('label' => mb_convert_case($container->getParameter('tokens.product_volume_title'), MB_CASE_TITLE)))
            ->add('price', null, array('label' => 'Цена', 'template' => 'MetalProductsBundle:ProductAdmin:price.html.twig'))
            ->add('measureId', 'choice', array('label' => 'Ед. измерения', 'choices' => ProductMeasureProvider::getAllTypesAsSimpleArray()))
            ->add('branchOffice', null, array('label' => 'Филиал', 'associated_property' => 'city.title'))
            ->add('company', null, array('label' => 'Компания', 'template' => 'MetalProjectBundle:Admin:company_with_id_list_for_product.html.twig'))
            ->add('position', null, array('label' => 'Порядок вывода на витрине'))
            ->add('createdAt', null, array('label' => 'Создан в'))
            ->add('updatedAt', null, array('label' => 'Обновлен в'))
            ->add('image', null, array(
                    'label' => 'Фото',
                    'template' => 'MetalProductsBundle:ProductAdmin:product_image.html.twig'
                ))
            ->add(
                'topic',
                null,
                array(
                    'label' => ' ',
                    'template' => 'MetalProductsBundle:ProductAdmin:productOnSite.html.twig'
                )
            )
        ;
    }

    public function validate(ErrorElement $errorElement, $object)
    {
        /* @var $object Product */
        if ($object->isModerated() && !$object->getCategory()) {
            $errorElement
                ->with('category')
                ->addViolation('Промодерированный товар требует наличие выбранной категории')
                ->end();
        }

        if ($object->getCompany()->getPromocode()) {
            $original = $this->em->getUnitOfWork()->getOriginalEntityData($object);

            if ($original['checked'] != Product::STATUS_CHECKED && $object->isModerated()) {
                $productRepository = $this->em->getRepository('MetalProductsBundle:Product');
                $maxAvailableProductsCount = $productRepository->getAvailableAddProductsCountToCompany($object->getCompany());

                if (null !== $maxAvailableProductsCount && $maxAvailableProductsCount <= 0) {
                    $errorElement
                        ->with('title')
                        ->addViolation(sprintf('Полный и расширенный пакеты позволяют добавлять более %d товаров.', $object->getCompany()->getPackageChecker()->getMaxAvailableProductsCount()))
                        ->end();
                }
            }
        }
    }

    public function getDatagrid()
    {
        if ($this->datagrid) {
            return $this->datagrid;
        }

        $datagrid = parent::getDatagrid();
        $products = $datagrid->getResults();
        /* @var $products Product[] */

        $companiesIds = array();
        $companyCitiesIds = array();
        $productsImagesIds = array();
        foreach ($products as $product) {
            if ($product->getCompany()) {
                $companiesIds[$product->getCompany()->getId()] = true;
            }

            if ($product->getBranchOffice()) {
                $companyCitiesIds[$product->getBranchOffice()->getId()] = true;
            }

            if ($product->getImage()) {
                $productsImagesIds[$product->getImage()->getId()] = true;
            }
        }

        if ($productsImagesIds) {
            $this->em->getRepository('MetalProductsBundle:ProductImage')
                ->findBy(array('id' => array_keys($productsImagesIds)));
        }

        if ($companiesIds) {
            $this->em->getRepository('MetalCompaniesBundle:Company')
                ->findBy(array('id' => array_keys($companiesIds)));
        }

        if ($companyCitiesIds) {
            $this->em->getRepository('MetalCompaniesBundle:CompanyCity')
                ->findBy(array('id' => array_keys($companyCitiesIds)));
        }

        $productAttributeValueRepository = $this->em->getRepository('MetalProductsBundle:ProductAttributeValue');
        $productRepository = $this->em->getRepository('MetalProductsBundle:Product');

        $productRepository->attachUsersToProducts($products);

        $productRepository->attachCategoriesToProducts($products);
        $productAttributeValueRepository->attachAttributesCollectionToProducts($products);

        return $this->datagrid;
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $product = $this->getSubject();
        /** @var $product Product */
        $companyId = $product->getCompany()->getId();
        $price = null;
        $container = $this->getConfigurationPool()->getContainer();

        if ($product->getPrice() != Product::PRICE_CONTRACT) {
            $price = $product->getPrice();
        }

        $formMapper
            ->add('title', null, array('label' => 'Название'))
            ->add('size', null, array('label' => mb_convert_case($container->getParameter('tokens.product_volume_title'), MB_CASE_TITLE), 'required' => false))
            ->add(
                'measureId',
                'choice',
                array(
                    'label' => 'Ед. измерения',
                    'placeholder' => '',
                    'required' => true,
                    'choices' => ProductMeasureProvider::getAllTypesAsSimpleArray())
            )
            ->add('price', null, array('label' => 'Цена', 'required' => false, 'data' => $price))
            ->add(
                'currencyId',
                'choice',
                array(
                    'label' => 'Валюта',
                    'choices' => CurrencyProvider::getAllTypesEnAsSimpleArray(),
                    'required' => false
                )
            )
            ->add('isPriceFrom', null, array(
                    'label' => 'Цена от',
                    'required' => false,
                ))
            ->add('position', null, array('label' => 'Порядок вывода на витрине', 'required' => true))
            ->add('isSpecialOffer', null, array(
                    'label' => 'Спецпредложение',
                    'required' => false,
                ))
            ->add('isHotOffer', null, array(
                'label' => 'Горячее предложение',
                'required' => false,
            ))
            ->add('productDescription.description', 'textarea', array('label' => 'Описание', 'required' => false))
            ->add('externalUrl', 'text', array('label' => 'Товар на внешнем сайте', 'required' => false))
            ->add(
                'checked',
                'choice',
                array(
                    'label' => 'Статус','required' => true,
                    'choices' =>  Product::getProductStatusesAsSimpleArray($product->isLimitExceeding())
                )
            )
            ->add(
                'category',
                'entity',
                array(
                    'label' => 'Категория',
                    'required' => false,
                    'placeholder' => '',
                    'class' => 'MetalCategoriesBundle:Category',
                    'property' => 'nestedTitle',
                    'choices' => $this->em->getRepository('MetalCategoriesBundle:Category')->buildCategoriesByLevels()
                )
            )
            ->add(
                'branchOffice',
                null,
                array(
                    'label' => 'Филиал',
                    'required' => true,
                    'query_builder' => function (CompanyCityRepository $rep) use ($companyId) {
                            return $rep->createQueryBuilder('cc')
                                ->join('cc.city', 'c')
                                ->addSelect('c')
                                ->andWhere('cc.company = :company_id')
                                ->andWhere('cc.kind = :kind')
                                ->setParameter('company_id', $companyId)
                                ->setParameter('kind', CompanyCity :: KIND_BRANCH_OFFICE)
                                ->orderBy('cc.isMainOffice', 'DESC')
                                ->addOrderBy('c.title');
                        },
                    'property' => 'city.title'
                )
            )
        ;
    }

    public function preUpdate($object)
    {
        /* @var $object Product */
        $object->setUpdated();

        $object->getProductLog()->setUpdatedBy($this->tokenStorage->getToken()->getUser());

        $unitOfWork = $this->em->getUnitOfWork();
        $original = $unitOfWork->getOriginalEntityData($object);
        $unitOfWork->computeChangeSet($this->em->getClassMetadata(get_class($object)), $object);
        $diff = $unitOfWork->getEntityChangeSet($object);
        $unitOfWork->setOriginalEntityData($object, $original);

        $productsBatchEditChangeSet = new ProductsBatchEditChangeSet();
        $markToProcess = false;

        if (isset($diff['isSpecialOffer'])) {
            $object->setAttribute('_change_special_offer', true);
        }

        if (isset($diff['checked'])) {
            $object->setAttribute('_changeBranchOffice', true);
            if ($diff['checked'][0] == Product::STATUS_CHECKED) { // продукт выключился
                $productsBatchEditChangeSet->productsToDisable[] = $object->getId();
                $markToProcess = true;
            } elseif ($diff['checked'][1] == Product::STATUS_CHECKED) { // продукт включился
                $productsBatchEditChangeSet->productsToEnable[] = $object->getId();
                $markToProcess = true;
            }
        }

        if (isset($diff['branchOffice'])) { //Поменялся branchOffice
            $object->setAttribute('_changeBranchOffice', true);
        }

        if (!$markToProcess && $object->isModerated()) {
            if (isset($diff['category'])) { // продукт включен и поменялась категория
                $productsBatchEditChangeSet->productsToChangeCategory[$object->getId()] = array(
                    'old' => $diff['category'][0] ? $diff['category'][0]->getId() : null,
                    'new' => $diff['category'][1]->getId()
                );
            }
        }

        $fieldsToResetStatus = array('title', 'size', 'category');
        $hasChangesForResetModeration = (bool)array_intersect(array_keys($diff), $fieldsToResetStatus);

        if ($hasChangesForResetModeration) {
            if (isset($diff['title'])) {
                $possibleCategory = $this->categoryDetector->getCategoryByTitle($object->getTitle());
                $object->setCategory($possibleCategory);
            }

//            $object->setChecked(Product::STATUS_NOT_CHECKED);
//            $this->indexManager->removeItems('products', array($object->getId()));
        } elseif (!empty($diff)) {
            $this->productIdToReindex = $object->getId();
        }

        $object->setAttribute('_changeset_to_process', $productsBatchEditChangeSet);
    }

    /**
     * @param Product $object
     */
    public function postUpdate($object)
    {
        if ($object->getAttribute('_changeBranchOffice')) {
            $this->processRefreshCitiesIdsProducts(array($object->getCompany()->getId()));
        }

        if ($object->getAttribute('_changeset_to_process')) {
            $this->notificationBackend->createAndPublish('admin_products', array('changeset' => $object->getAttribute('_changeset_to_process')));
        }

        if ($this->productIdToReindex) {
            $this->indexer->reindexIds([$this->productIdToReindex]);
        }

        if ($object->getAttribute('_change_special_offer')) {
            $changeSet = new ProductChangeSet();
            //TODO: Добавить price?
            $changeSet->setIsSpecialOffer($object->getIsSpecialOffer());

            $criteria = new ProductsCriteria();
            $criteria->addId($object->getId());

            $this->indexer->update($changeSet, $criteria);
        }

        $this->em->getRepository('MetalProductsBundle:Product')->disableDuplicatedProductsByProductHashes(array($object->getItemHash()));
    }

    public function prePersist($object)
    {
        /* @var $object Product */
        $object->getProductLog()->setCreatedBy($this->tokenStorage->getToken()->getUser());
        $object->setCurrency($object->getCompany()->getCountry()->getCurrency());
    }

    public function postPersist($object)
    {
        /* @var $object Product */
        if ($object->isModerated()) {
            $this->processRefreshCitiesIdsProducts(array($object->getCompany()->getId()));
        }

        $this->em->getRepository('MetalProductsBundle:Product')->disableDuplicatedProductsByProductHashes(array($object->getItemHash()));
    }

    private function processRefreshCitiesIdsProducts($companies_ids)
    {
        $companyCityRepository = $this->em->getRepository('MetalCompaniesBundle:CompanyCity');
        $companyCityRepository->refreshBranchOfficeHasProducts($companies_ids);

       /* $productsIdsToReindex = $companyCityRepository->getProductsIdsForReindex($companies_ids);

        if ($productsIdsToReindex) {
            $this->sphinxIndexManager->reindexItems('products', $productsIdsToReindex);
        }*/
    }

    //FIXME: handle postPersist. Если продукт создается и сразу включается - то нужно обработать changeset products to enabling

    public function getFormForObject(Product $product)
    {
        $this->iterator++;
        $productValueTitle = $this->getConfigurationPool()->getContainer()->getParameter('tokens.product_volume_title');
        $form = $this->getFormContractor()->getFormBuilder('form')->getFormFactory()->createNamed(
            ProductsInlineEditType::getNameForProduct($product),
            new ProductsInlineEditType(),
            $product,
            array(
                'product_volume_title' => $productValueTitle,
                'iterator' => $this->iterator,
                'suggest_url' => $this->routeGenerator->generate(
                    'MetalProductsBundle:Api:getProductImages',
                    array(
                        'id' => $product->getCompany()->getId(),
                        'q' => '__QUERY__'
                    )
                )
            )
        );

        return $form->createView();
    }

    public function toString($object)
    {
        return $object instanceof Product ? $object->getTitle() : '';
    }
}
