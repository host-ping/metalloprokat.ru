<?php

namespace Metal\CompaniesBundle\Admin;

use Brouzie\Sphinxy\IndexManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;
use Geocoder\Exception\NoResultException;
use Geocoder\Geocoder;
use Knp\Menu\ItemInterface as MenuItemInterface;

use Metal\CategoriesBundle\Repository\CategoryRepository;
use Metal\CompaniesBundle\Entity\Company;
use Metal\CompaniesBundle\Entity\CompanyCategory;
use Metal\CompaniesBundle\Entity\CompanyCity;
use Metal\CompaniesBundle\Entity\CompanyOldSlug;
use Metal\CompaniesBundle\Entity\ValueObject\ActionTypeProvider;
use Metal\CompaniesBundle\Entity\ValueObject\CompanyPackageTypeProvider;
use Metal\CompaniesBundle\Entity\ValueObject\CompanyServiceProvider;
use Metal\CompaniesBundle\Entity\ValueObject\CompanyTypeProvider;
use Metal\CompaniesBundle\Repository\CompanyRepository;
use Metal\CompaniesBundle\Service\CompanyService;
use Metal\ProductsBundle\Entity\Product;
use Metal\ProjectBundle\Service\CloudflareService;
use Metal\UsersBundle\Admin\UserAdmin;
use Metal\UsersBundle\Repository\UserRepository;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\CoreBundle\Validator\ErrorElement;
use Sonata\NotificationBundle\Backend\BackendInterface;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBag;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Validator\Constraints as Assert;

class CompanyAdmin extends AbstractAdmin
{
    const FILTER_ACTIVE = 0;
    const FILTER_DELETED = 1;
    const FILTER_ALL = -1;

    protected $datagridValues = array(
        '_sort_order' => 'DESC',
        '_sort_by' => 'createdAt',
        'deleted' => array(
            'value' => self::FILTER_ACTIVE,
        ),
        '_page' => 1,
        '_per_page' => 25,
    );

    /**
     * The number of result to display in the list.
     *
     * @var int
     */
    protected $maxPerPage = 25;

    /**
     * Predefined per page options.
     *
     * @var array
     */
    protected $perPageOptions = array(15, 25, 50, 100, 150, 200);

    protected $formOptions = array(
        'validation_groups' => array(
            'Default',
            'company_edit',
            'company_edit_admin',
            'company_address',
            'company_city',
            'branch_office',
        ),
        'cascade_validation' => true
    );

    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var CompanyRepository
     */
    private $companyRepository;

    /**
     * @param ProductRepository
     */
    private $productRepository;

    /**
     * @var CategoryRepository
     */
    private $categoryRepository;

    /**
     * @var BackendInterface
     */
    private $notificationBackend;

    /**
     * @var Geocoder
     */
    private $geocoder;
    /**
     * @var IndexManager
     */

    /**
     * CloudflareService
     */
    protected $cloudflareService;

    /**
     * FlashBag
     */
    protected $flashBag;

    private $sphinxyIndexManager;

    private $original;

    private $subjectInitialized = false;

    /**
     * @var CompanyService
     */
    private $companyService;

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    public function __construct($code, $class, $baseControllerName, EntityManager $em, BackendInterface $notificationBackend,
        IndexManager $sphinxyIndexManager, Geocoder $geocoder, CloudflareService $cloudflareService, FlashBag $flashBag,
                                CompanyService $companyService, TokenStorageInterface $tokenStorage)
    {
        parent::__construct($code, $class, $baseControllerName);

        $this->em = $em;
        $this->companyRepository = $em->getRepository('MetalCompaniesBundle:Company');
        $this->productRepository = $em->getRepository('MetalProductsBundle:Product');
        $this->categoryRepository = $em->getRepository('MetalCategoriesBundle:Category');
        $this->notificationBackend = $notificationBackend;
        $this->sphinxyIndexManager = $sphinxyIndexManager;
        $this->geocoder = $geocoder;
        $this->cloudflareService = $cloudflareService;
        $this->flashBag = $flashBag;

        $this->tokenStorage = $tokenStorage;
        $this->companyService = $companyService;
    }

    public function getCategories()
    {
        $categories = $this->categoryRepository->buildCategoriesByLevels();

        $result = array();
        foreach ($categories as $category) {
            $result[] = array(
                'id' => $category->getId(),
                'title' => $category->getNestedTitle(),
                'allowProducts' => $category->getAllowProducts()
            );
        }

        return $result;
    }

    protected function configureTabMenu(MenuItemInterface $menu, $action, AdminInterface $childAdmin = null)
    {
        if (null !== $childAdmin) {
            if ($childAdmin instanceof UserAdmin) {
                $menu->addChild('Редактировать компанию', array('uri' => $this->getConfigurationPool()->getAdminByAdminCode('metal.companies.admin.company')->generateUrl('edit', array('id' => $this->getSubject()->getId()))));
            }

            return;
        }

        if (!in_array($action, array('show', 'edit'))) {
            return;
        }

        $company = $this->getSubject();
        /* @var $company Company */

        $menu->addChild('Обращения к модератору', array('uri' => $this->getChild('metal.support.admin.topic')->generateUrl('list')));
        $menu->addChild('Баннеры', array('uri' => $this->getChild('metal.announcements.admin.announcement')->generateUrl('list')));
        $menu->addChild('Шапки', array('uri' => $this->getChild('metal.mini_site.admin.mini_site_cover')->generateUrl('list')));
        $menu->addChild('Товары', array('uri' => $this->getChild('metal.products.admin.product')->generateUrl('list')));
        $menu->addChild('Заказанные услуги', array('uri' => $this->getChild('metal.services.admin.package_order')->generateUrl('list')));
        $menu->addChild('Фото товаров', array('uri' => $this->getChild('metal.products.admin.product_image')->generateUrl('list')));
        $menu->addChild('Связанные пользователи', array('uri' => $this->getChild('metal.users.admin.user')->generateUrl('list')));
        if ($company->getInCrm()) {
            $menu->addChild('Компания в CRM', array('uri' => sprintf($this->getConfigurationPool()->getContainer()->getParameter('company_crm_url'), $company->getId())));
        }
    }

    protected function configureFormFields(FormMapper $formMapper)
    {
        $options = array(
            'required' => false,
            'data_class' => null,
            'mapped' => false,
            'attr' => array(
                'disabled' => 'disabled'
            ),
        );

        $subject = $this->getSubject();
        /* @var $subject Company */

        $options['label'] = 'Документ';
        if ($subject && $subject->getPaymentDetails()->getFile()->getName()) {
            $options['help'] = '<a href="'.$this->generateObjectUrl('download_file', $subject).'">'.htmlspecialchars($subject->getPaymentDetails()->getFile()->getOriginalName(), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8').'</a>';
        }

        $slugHelp = 'Рекомендуется менять один раз.';
        if ($subject->isSlugChanged()) {
            $slugHelp .= sprintf(' <span style="color: red">Последний раз слаг был изменен %s.</span>', $subject->getSlugChangedAt()->format('Y-m-d H:i:s'));
        }

        $changeCityUrl = '<a href="'.$this->generateObjectUrl('change_main_city', $subject).'">Изменить главный город компании</a>';

        $formMapper
            ->tab('Основное')
                ->add('title', null, array('label' => 'Название'))
                ->add(
                    'companyTypeId',
                    'choice',
                    array('label' => 'Тип собственности компании', 'choices' => CompanyTypeProvider::getAllTypesAsSimpleArray())
                )
                ->add('slug', 'text', array(
                        'label' => 'Слаг',
                        'required'  => true,
                        'help' => $slugHelp
                    ))
                ->add('domain', null, array('label' => 'Домен', 'disabled' => true))
                ->add('cityTitle', 'text', array(
                    'label' => 'Город',
                    'read_only' => true,
                    'help' => $changeCityUrl,
                ))
                ->add('address', null, array('label' => 'Адрес', 'required' => false))
                ->add('latitude', null, array('label' => 'Широта', 'precision' => 6))
                ->add('longitude', null, array('label' => 'Долгота', 'precision' => 6))
                ->add('companyLog.createdBy', 'entity', array(
                        'label' => 'Главный пользователь',
                        'class' => 'MetalUsersBundle:User',
                        'placeholder' => '',
                        'property' => 'fullName',
                        'query_builder' => function (UserRepository $repository) {
                            return $repository->createQueryBuilder('u')
                                ->andWhere('u.company = :c')
                                ->setParameter('c', $this->getSubject()->getId());
                        },
                        'required' => true,
                        'constraints' => array(new Assert\NotBlank())
                    ))
                ->add('phones', 'sonata_type_collection',
                    array(
                        'label' => 'Телефон',
                        'by_reference' => false,
                        'required' => false
                    ),
                    array(
                        'edit' => 'inline',
                        'inline' => 'table',
                    )
                )
                ->add(
                    'sites',
                    'collection',
                    array(
                        'label' => 'Сайты',
                        'type' => 'url',
                        'required' => false,
                        'allow_add' => true,
                        'allow_delete' => true,
                        'delete_empty' => true
                    ),
                    array(
                        'edit' => 'inline',
                        'inline' => 'table',
                    )
                )
                ->add(
                    'companyDescription.description',
                    'textarea',
                    array(
                        'label' => 'Описание',
                        'required' => false,
                        'attr' => array(
                            'cols' => 250,
                            'rows' => 3,
                            'style' => 'width: 100%; height: 200px;',
                        )
                    )
                )
                ->add('slogan', null, array('label' => 'Слоган'))
                ->add('deliveryDescription', null, array(
                    'label' => 'Текст доставки',
                    'disabled' => !$subject->getPackageChecker()->isAllowedSetDeliveryDescription(),
                    'help' => 'Этот текст будет выводиться на минисайте на странице "О компании" в секции "Осуществляем доставку в города"(Доступно в пакетах плюс)'
                ))
                ->add('hasTerritorialRules', 'checkbox',
                    array(
                        'label' => 'Компания содержит особые правила отображения сотрудников',
                        'required' => false
                    )
                )
                ->add('mainUserAllSees', 'checkbox',
                    array(
                        'label' => 'Дублировать все заявки и обратные звонки директору',
                        'required' => false
                    )
                )
                ->add('enabledAutoAssociationWithPhotos', 'checkbox', array(
                    'label' => 'Включить автоассоциацию с фото',
                    'required' => false
                ))
                ->add('isAllowedExtraCities', 'checkbox', array(
                    'label' => 'Позволять добавлять больше городов, чем доступно в пакете.',
                    'required' => false
                ))
                ->add('minisiteEnabled', 'checkbox', array(
                    'label' => 'Включить минисайт',
                    'required' => false,
                    'help' => 'Отключает/включает минисайт компании и вывод ссылок на него.'
                ))
                ->add('indexMinisite', 'checkbox', array(
                    'label' => 'Индексирование минисайта в поисковых системах',
                    'required' => false,
                ))
                ->add('companyAttributesTypesIds', 'choice', array(
                        'choices' => CompanyServiceProvider::getAllServicesAsSimpleArray(),
                        'label' => 'Услуги',
                        'expanded' => true,
                        'multiple' => true,
                        'required' => false,
                    ))
                ->add('optimizeLogo', 'checkbox',
                    array(
                        'label' => 'Оптимизировать логотип',
                        'required' => false,
                        'help' => 'При отключении оптимизации логотип будет на белом фоне.'
                    )
                )
                ->add('inCrm', 'checkbox',
                    array(
                        'label' => 'Включить в CRM',
                        'required' => false
                    )
                )
            ->end()
            ->end()

            ->tab('Реквизиты')
                ->add('paymentDetails.nameOfLegalEntity', null, array('label' => 'Наименование юридического лица', 'required' => false))
                ->add('paymentDetails.inn', null, array('label' => 'Идентификационный код', 'required' => false))
                ->add('paymentDetails.kpp', null, array('label' => 'КПП', 'required' => false))
                ->add('paymentDetails.orgn', null, array('label' => 'ОГРН', 'required' => false))
                ->add('paymentDetails.directorFullName', null, array('label' => 'ФИО руководителя', 'required' => false))
                ->add('paymentDetails.mailAddress', null, array('label' => 'Почтовый адрес', 'required' => false))
                ->add('paymentDetails.legalAddress', null, array('label' => 'Юридический адрес', 'required' => false))
                ->add('paymentDetails.bankAccount', null, array('label' => 'Номер р/счета в рублях', 'required' => false))
                ->add('paymentDetails.bankCorrespondentAccount', null, array('label' => 'Номер корп. счета', 'required' => false))
                ->add('paymentDetails.bankBik', null, array('label' => 'БИК банка', 'required' => false))
                ->add('paymentDetails.bankTitle', null, array('label' => 'Наименование банка', 'required' => false))
                ->add('paymentDetails.uploadedFile', 'file', $options)
                ->add(
                    'paymentDetails.approved',
                    'checkbox',
                    array(
                        'label' => 'Документ проверен',
                        'required' => false
                    )
                )
                ->add(
                    'paymentDetails.displayOnMiniSite',
                    'checkbox',
                    array(
                        'label' => 'Отображать на мини сайте',
                        'required' => false
                    )
                )
            ->end()
            ->end()
            ->tab('Города')
                ->add('companyCities', 'sonata_type_collection',
                    array(
                        'label' => 'Города',
                        'by_reference' => false,
                        'required' => false
                    ),
                    array(
                        'edit' => 'inline',
                        'inline' => 'table',
                    ))
                ->end()
            ->end()
            ->tab('Разделы')
                ->add('companyCategories', 'sonata_type_collection',
                    array(
                        'label' => 'Разделы',
                        'by_reference' => false,
                        'required' => false,
//                        'delete_empty' => true,
                    ),
                    array(
                        'edit' => 'inline',
                        'inline' => 'table',
                    ))
            ->end()
            ->end()
        ;
    }

    protected function configureRoutes(RouteCollection $collection)
    {
        $collection
            ->add(
                'download_file',
                $this->getRouterIdParameter().'/download_file',
                array('_controller' => 'MetalCompaniesBundle:CompanyAdmin:downloadFile')
            )
            ->add(
                'toggle_status',
                $this->getRouterIdParameter().'/toggle_status',
                array('_controller' => 'MetalCompaniesBundle:CompanyAdmin:toggleStatus')
            )
            ->add(
                'invalidate_cache',
                $this->getRouterIdParameter().'/invalidate_cache',
                array('_controller' => 'MetalCompaniesBundle:CompanyAdmin:invalidateCache')
            )
            ->add(
                'change_main_city',
                $this->getRouterIdParameter().'/change_main_city',
                array('_controller' => 'MetalCompaniesBundle:CompanyAdmin:changeMainCity')
            )
            ->add('merge', 'merge', array('_controller' => 'MetalCompaniesBundle:CompanyAdmin:mergeCompanies'))
            ->add('view_demand_history', $this->getRouterIdParameter().'/view_demand_history', array('_controller' => 'MetalCompaniesBundle:CompanyAdmin:viewDemandHistory'))
            ->remove('delete')
            ->add(
                'search_by_site',
                'search_by_site',
                array('_controller' => 'MetalCompaniesBundle:CompanyAdmin:searchBySite')
            );
        ;
    }

    public function getDashboardActions()
    {
        $actions = parent::getDashboardActions();

        $actions['search_by_site'] = array(
            'label' => 'Поиск по адресу сайта',
            'url' => $this->generateUrl('search_by_site'),
            'icon' => 'search'
        );

        return $actions;
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id')
            ->add(
                'title',
                null,
                array(
                    'label' => 'Название',
                    'template' => 'MetalCompaniesBundle:AdminCompany:companyName.html.twig'
                )
            )
            ->add('city', null, array('label' => 'Город', 'associated_property' => 'title'))
            ->add('address', null, array('label' => 'Адрес'))
            ->add('slug', null, array('label' => 'Слаг'))
            ->add('createdAt', null, array('label' => 'Дата создания'))
            ->add('updatedAt', null, array('label' => 'Дата редактирования'))
            ->add(
                'user',
                null,
                array(
                    'label' => 'Закрепленные пользователи',
                    'template' => 'MetalCompaniesBundle:AdminCompany:companyUser.html.twig'
                )
            )
            ->add(
                'paymentDetails.uploadedAt',
                null,
                array(
                    'label' => 'Дата загрузки документа'
                )
            )
            ->add('site', null, array('label' => 'Сайт'))
            ->add(
                'topic',
                null,
                array(
                    'label' => 'Действия',
                    'template' => 'MetalCompaniesBundle:AdminCompany:companyActions.html.twig'
                )
            );
    }

    public function createQuery($context = 'list')
    {
        $query = parent::createQuery($context);
        $alias = $query->getRootAliases();
        $alias = reset($alias);
        $query->leftJoin(sprintf('%s.paymentDetails', $alias), 'pd')->addSelect('pd');

        return $query;
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id', null, array('label' => 'ID'))
            ->add('title', null, array('label' => 'Название'))
            ->add('slug', null, array('label' => 'Слаг'))
            ->add(
                'city',
                'doctrine_orm_choice',
                array(
                    'label' => 'Город компании',
                    'property' => 'title',
                ),
                'choice',
                array(
                    'choices' => $this->em->getRepository('MetalTerritorialBundle:City')->getSimpleCitiesArray()
                )
            )
            ->add(
                'companiesCities',
                'doctrine_orm_callback',
                array(
                    'label' => 'Филиал',
                    'callback' => function ($queryBuilder, $alias, $field, $value) {
                        if (!isset($value['value'])) {
                            return null;
                        }

                        /* @var $queryBuilder QueryBuilder */
                        $queryBuilder->join(sprintf('%s.companyCities', $alias), 'companyCities')
                            ->join('companyCities.city', 'city')
                            ->andWhere('city.id = :cityId')
                            ->setParameter('cityId', $value['value']);

                        return true;
                    }
                ),
                'choice',
                array(
                    'choices' => $this->em->getRepository('MetalTerritorialBundle:City')->getSimpleCitiesArray()
                )
            )
            ->add(
                '_slugLength',
                'doctrine_orm_callback',
                array(
                    'label' => 'Длина слага',
                    'callback' => function ($queryBuilder, $alias, $field, $value) {
                        if (!isset($value['value'])) {
                            return;
                        }

                        if ($value['value'] === 'y') {
                            $queryBuilder->andWhere(sprintf('LENGTH(%s.slug) > 35', $alias));
                        } else {
                            $queryBuilder->andWhere(sprintf('LENGTH(%s.slug) <= 35', $alias));
                        }

                        return true;
                    }
                ),
                'choice',
                array(
                    'choices' => array(
                        'y' => 'Больше 35',
                        'n' => 'Меньше или равно 35'
                    )
                )
            )
            ->add(
                '_companySites',
                'doctrine_orm_callback',
                array(
                    'label' => 'Адрес сайта',
                    'callback' => function ($queryBuilder, $alias, $field, $value) {
                        if (!isset($value['value'])) {
                            return;
                        }

                        $queryBuilder
                            ->join('MetalCompaniesBundle:NormalizedCompanyUrl', 'normalizedCompanyUrl', 'WITH', sprintf('%s.id = normalizedCompanyUrl.company', $alias))
                            ->andWhere('normalizedCompanyUrl.urlAsString LIKE :searchSite')
                            ->setParameter('searchSite', '%'.$value['value'].'%');

                        return true;
                    }
                )
            )
            ->add(
                'codeAccess',
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
                'visibilityStatus',
                'doctrine_orm_choice',
                array(
                    'label' => 'Видимость компании',
                ),
                'choice',
                array(
                    'choices' => Company::getVisibilityStatusesAsArray()
                )
            )
            ->add(
                'documents',
                'doctrine_orm_callback',
                array(
                    'label' => 'Непромодерированые документы',
                    'callback' => function ($queryBuilder, $alias, $field, $value) {
                        if (!isset($value['value'])) {
                            return;
                        }
                        if ($value['value'] === 'y') {
                            $queryBuilder
                                ->andWhere('(pd.uploadedAt > pd.approvedAt OR pd.approvedAt IS NULL)')
                                ->andWhere('pd.uploadedAt IS NOT NULL')
                            ;
                        }

                        return true;
                    }
                ),
                'choice',
                array(
                    'choices' => array(
                        'y' => 'Показать'
                    )
                )
            )
            ->add(
                'usedPromocode',
                'doctrine_orm_callback',
                array(
                    'label' => 'Используют промокод',
                    'callback' => function ($queryBuilder, $alias, $field, $value) {
                        if (!isset($value['value'])) {
                            return;
                        }

                        if ($value['value'] === 'y') {
                            $queryBuilder->andWhere(sprintf('%s.promocode IS NOT NULL', $alias));
                        }

                        return true;
                    }
                ),
                'choice',
                array(
                    'choices' => array(
                        'y' => 'Показать'
                    )
                )
            )
            ->add(
                'company',
                'doctrine_orm_callback',
                array(
                    'label' => 'Содержат непромодерированые товары',
                    'callback' => function ($queryBuilder, $alias, $field, $value) {
                        if (!isset($value['value'])) {
                            return;
                        }

                        if ($value['value'] === 'y') {
                            $queryBuilder->andWhere(
                                $queryBuilder->expr()->exists(
                                    $this->em->createQueryBuilder()
                                        ->select('p')
                                        ->from('MetalProductsBundle:Product', 'p')
                                        ->where('p.company = '.sprintf('%s.id', $alias))
                                        ->andWhere('p.checked = :status')
                                        ->setMaxResults(1)
                                        ->getDQL()
                                )
                            )->setParameter('status', Product::STATUS_NOT_CHECKED);
                        }

                        return true;
                    }
                ),
                'choice',
                array(
                    'choices' => array(
                        'y' => 'Показать'
                    )
                )
            )
            ->add(
                'deleted',
                'doctrine_orm_callback',
                array(
                    'label' => 'Удаленность',
                    'callback' => function ($queryBuilder, $alias, $field, $value) {
                        if (!isset($value['value'])) {
                            return;
                        }

                        if ($value['value'] == self::FILTER_ACTIVE) {
                            $queryBuilder->andWhere(sprintf("%s.deletedAtTS = 0", $alias));
                        } else if ($value['value'] == self::FILTER_DELETED) {
                            $queryBuilder->andWhere(sprintf("%s.deletedAtTS > 0", $alias));
                        }

                        return true;
                    }
                ),
                'choice',
                array(
                    'choices' => array(
                        self::FILTER_ACTIVE => 'Активные',
                        self::FILTER_DELETED => 'Удаленные',
                        self::FILTER_ALL => 'Все подряд',
                    )
                )
            )
            ->add(
                'enabledAutoAssociationWithPhotos',
                'doctrine_orm_boolean',
                array(
                    'label' => 'Включена автоассоциация с фото'
                ),
                'sonata_type_boolean'
            )
        ;
    }

    public function getSubject()
    {
        $company = parent::getSubject();
        /* @var $company Company */

        if ($company && !$this->subjectInitialized) {
            $this->companyRepository->attachCompanyHistoryToCompany($company);
            $this->em->getRepository('MetalCompaniesBundle:CompanyCategory')->loadCompanyCategoriesCollectionForCompany($company);
            $this->em->getRepository('MetalCompaniesBundle:CompanyCity')->loadCompanyCitiesCollectionForCompany($company);
            $this->subjectInitialized = true;
        }

        return $company;
    }

    public function getDatagrid()
    {
        if ($this->datagrid) {
            return $this->datagrid;
        }

        $datagrid = parent::getDatagrid();
        $companies = $datagrid->getResults();
        /* @var $companies Company[] */
        $this->companyRepository->attachUsersToCompanies($companies);
        $this->companyRepository->attachIsParsedCompanyFlag($companies);
        $this->productRepository->attachProductsCountToCompanies($companies);
        $companiesIds = array();
        foreach ($companies as $company) {
            $companiesIds[$company->getId()] = true;
        }

        $this->em->getRepository('MetalCompaniesBundle:PaymentDetails')->synchronizePaymentDetails(array_keys($companiesIds));

        return $this->datagrid;
    }

    public function configure()
    {
        $this->setTemplate('base_list_field','SonataAdminBundle:CRUD:base_list_flat_field.html.twig');
        $this->setTemplate('inner_list_row','MetalCompaniesBundle:AdminCompany:list_inner_row.html.twig');
        $this->setTemplate('list', 'MetalCompaniesBundle:AdminCompany:list.html.twig');
        $this->setTemplate('edit', 'MetalCompaniesBundle:AdminCompany:edit.html.twig');
    }

    private function isNeedUpdateCoordinates(Company $object, array $original)
    {
        $cityChanged = true;
        if ($original['city']) {
            $cityChanged = $object->getCity()->getId() !== $original['city']->getId();
        }

        return $object->getAddress() && (
            $cityChanged ||
            $original['address'] != $object->getAddress() ||
            !$object->hasCoordinate());
    }

    private function updateCoordinates(Company $object)
    {
        try {
            $coords = $this->geocoder
                ->using('yandex')
                ->geocode(sprintf('%s + %s', $object->getCity()->getTitle(), $object->getAddress()));
            $object->setLatitude($coords->getLatitude());
            $object->setLongitude($coords->getLongitude());
        } catch (NoResultException $e) {
            $this->request->getSession()->getFlashBag()->set('sonata_flash_error', sprintf('Проверьте адрес. Ошибка (%s)', $e->getMessage()));
        }
    }

    public function preUpdate($object)
    {
        /* @var $object Company */

        //TODO: создать тикет в sonata admin, что delete_empty не работает на коллекциях
        foreach ($object->getCompanyCategories() as $companyCategory) {
            if (!$companyCategory->getCategory()) {
                $object->removeCompanyCategory($companyCategory);
            }
        }

        $object->scheduleSynchronization();

        $author = $this->tokenStorage->getToken()->getUser();

        $this->original = $this->em->getUnitOfWork()->getOriginalEntityData($object);

        if ($object->getInCrm() != $this->original['inCrm']) {
            $companyHistory = $this->companyService->addCompanyHistory($object, $author, ActionTypeProvider::CHANGE_CRM_STATUS);
            $companyHistory->setComment($object->getInCrm() ? 'Включили' : 'Отключили');
            $this->em->flush($companyHistory);
        }

        if (!$this->original['slug'] || ($object->getSlug() != $this->original['slug'])) {
            $object->setSlugChangedAt(new \DateTime());
        }

        $countryChanged = true;
        $cityChanged = false;
        if ($this->original['city']) {
            $countryChanged = $this->original['city']->getCountry()->getId() !== $object->getCity()->getCountry()->getId();
            $cityChanged = $this->original['city']->getId() !== $object->getCity()->getId();
        }

        if ($countryChanged || $cityChanged) {
            $qb = $this->em
                ->createQueryBuilder()
                ->update('MetalUsersBundle:User', 'user')
                ->set('user.city', $object->getCity()->getId())
                ->set('user.country', $object->getCity()->getCountry()->getId())
                ->where('user.company = :company_id')
                ->setParameter('company_id', $object->getId());

            if ($cityChanged && !$countryChanged) {
                $qb
                    ->andWhere('user.city = :cityId')
                    ->setParameter('cityId', $this->original['city']->getId());
            }

            $qb->getQuery()->execute();
        }

        if ($this->isNeedUpdateCoordinates($object, $this->original)) {
            $this->updateCoordinates($object);
        }

        if ($object->getSlug() != $this->original['slug']) {
            $this->em->getRepository('MetalCompaniesBundle:CompanyOldSlug')->changeCompanySlug($object, $this->original['slug']);
        }

        if ($object->getTitle() != $this->original['title']) {
            $companyHistory = $this->companyService->addCompanyHistory($object, $author, ActionTypeProvider::CHANGE_COMPANY_TITLE);
            $companyHistory->setComment($this->original['title']);
            $this->em->flush($companyHistory);
        }

        $uniqid = $this->request->query->get('uniqid');
        if ($uniqid) {
            $companyCategoryRepository = $this->em->getRepository('MetalCompaniesBundle:CompanyCategory');
            $currentRequest = $this->request->request->get($uniqid);
            $deletedCategories = array();
            if (!empty($currentRequest['companyCategories'])) {
                foreach ($currentRequest['companyCategories'] as $companyCategory) {
                    if (isset($companyCategory['_delete'])) {
                        $deletedCategories[$companyCategory['category']] = true;
                    }
                }
            }

            if ($deletedCategories) {
                $companyCategoryRepository->onDeleteCompanyCategory($object->getId(), array_keys($deletedCategories));
            }
        }

        return;
        $companyAttributesTypesIds = $object->getCompanyAttributesTypesIds();
        $companyProductsCategoriesIds = $object->getCategoriesIds();
        $oldCompanyAsArray = $object->getCompanyToArray();

        $companyCities = array();
        foreach ($object->getCompanyCities() as $companyCity) {
            $companyCities[$companyCity->getCity()->getId()] = $companyCity->getId();
        }

        $queueBackendAttributesData = array(
            'company_id'                       => $object->getId(),
            'company_attributes_types_ids'     => $companyAttributesTypesIds,
            'old_company_attributes_types_ids' => array(),
        );

        $this->notificationBackend->createAndPublish('company_attributes_change', $queueBackendAttributesData);

        $queueBackendCategoriesData = array(
            'company_id'                 => $object->getId(),
            'company_categories_ids'     => $companyProductsCategoriesIds,
            'old_company_categories_ids' => array(),
            'old_company_as_array'       => $oldCompanyAsArray
        );

        $this->notificationBackend->createAndPublish('company_categories_change', $queueBackendCategoriesData);

        $queueBackendCitiesData = array(
            'company_cities_ids'     => $companyCities,
            'old_company_cities_ids' =>  array(),
            'old_company_as_array'   => $oldCompanyAsArray
        );

        $this->notificationBackend->createAndPublish('city_delivery_change', $queueBackendCitiesData);
    }

    public function postUpdate($object)
    {
        /* @var $object Company */

        $user = $object->getCompanyLog()->getCreatedBy();
        $user->setEditPermission(true);
        $user->setCanUseService(true);
        $user->setApproved(true);

        $object->setUpdated();

        $this->em->flush();

        if ($this->original['city']->getId() != $object->getCity()->getId()) {
//            $this->em->getRepository('MetalCompaniesBundle:CompanyCity')->updateMainOfficeStatus($object->getId());
        }

        if ($object->getSlug() != $this->original['slug'] && $object->getPackageChecker()->isHttpsAllowed()) {
            $logger = function ($record, $success, $errorMsg) {
                if ($success) {
                    $this->flashBag->add('sonata_flash_info', sprintf('Компания "%s" обновлена в cloudflare', $record));
                } else {
                    $this->flashBag->add('sonata_flash_error', sprintf('Record "%s" failed ("%s").', $record, $errorMsg));
                }
            };

            $this->cloudflareService->renameRecord($this->original['slug'], $object->getSlug(), $logger);
        }

        $this->em->getRepository('MetalProductsBundle:Product')->disableProductsNotBranchOffice($object);
        $this->em->getRepository('MetalCompaniesBundle:CompanyCategory')->onInsertCompanyCategory(array($object->getId()));
    }

    public function toString($object)
    {
        return $object instanceof Company ? $object->getTitle() : '';
    }

    public function validate(ErrorElement $errorElement, $object)
    {
        /* @var $object Company */
        $hasMainOffice = false;
        $companyCities = $object->getCompanyCities();
        foreach ($companyCities as $companyCity) {
            if ($companyCity->getIsMainOffice() && $companyCity->getEnabled()) {
                $hasMainOffice = true;
            }
        }

        $this->original = $this->em->getUnitOfWork()->getOriginalEntityData($object);
        if (!$hasMainOffice && $this->original['city']) {
            $errorElement
                ->with('companyCities')
                ->addViolation('Нельзя удалять главный город компании. Сначала поменяйте главный город и сохраните изменения.')
                ->end();
        }
    }

    public function getFormTheme()
    {
        return array_merge(
            parent::getFormTheme(),
            array('MetalCompaniesBundle:AdminCompany:edit_city_item.html.twig')
        );
    }
}
