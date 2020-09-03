<?php

namespace Metal\DemandsBundle\Admin;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;
use Enqueue\Client\ProducerInterface;
use Metal\CallbacksBundle\Entity\Callback as CallbackEntity;
use Metal\DemandsBundle\Entity\AbstractDemand;
use Metal\DemandsBundle\Entity\Demand;
use Metal\DemandsBundle\Entity\DemandItem;
use Metal\DemandsBundle\Entity\PrivateDemand;
use Metal\DemandsBundle\Entity\ValueObject\ConsumerTypeProvider;
use Metal\DemandsBundle\Entity\ValueObject\DemandPeriodicityProvider;
use Metal\DemandsBundle\Form\Admin\VolumeFormType;
use Metal\DemandsBundle\Async\Message\DemandModerated;
use Metal\DemandsBundle\Async\Events;
use Metal\DemandsBundle\Repository\DemandItemAttributeValueRepository;
use Metal\DemandsBundle\Repository\DemandItemRepository;
use Metal\DemandsBundle\Repository\DemandRepository;
use Metal\ProductsBundle\Entity\ValueObject\ProductMeasureProvider;
use Metal\ProjectBundle\Entity\ValueObject\AdminSourceTypeProvider;
use Metal\ProjectBundle\Entity\ValueObject\SiteSourceTypeProvider;
use Metal\TerritorialBundle\Entity\City;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\CoreBundle\Validator\ErrorElement;
use Sonata\CoreBundle\Form\Type\BooleanType;
use Sonata\DoctrineORMAdminBundle\Datagrid\ProxyQuery;
use Sonata\NotificationBundle\Backend\BackendInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class DemandAdmin extends AbstractAdmin
{
    protected $datagridValues = array(
        '_sort_order' => 'DESC',
        '_sort_by' => 'createdAt',
        'private' => array('value' => AbstractDemand::TYPE_PUBLIC),
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

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * @var DemandItemRepository
     */
    private $demandItemsRepository;

    /**
     * @var DemandItemAttributeValueRepository
     */
    private $demandItemAttributeValueRepository;

    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var DemandRepository
     */
    private $demandRepository;

    /**
     * @deprecated
     *
     * @var BackendInterface
     */
    private $notificationBackend;

    private $subjectInitialized = false;

    private $project;

    private $filterParameters = array();

    private $isExtended = false;

    private $messageBus;

    public function __construct(
        $code,
        $class,
        $baseControllerName,
        TokenStorageInterface $tokenStorage,
        EntityManager $em,
        BackendInterface $notificationBackend,
        ProducerInterface $messageBus,
        $project
    ) {
        parent::__construct($code, $class, $baseControllerName);

        $this->em = $em;
        $this->demandItemsRepository = $em->getRepository('MetalDemandsBundle:DemandItem');
        $this->demandRepository = $em->getRepository('MetalDemandsBundle:Demand');
        $this->demandItemAttributeValueRepository = $em->getRepository('MetalDemandsBundle:DemandItemAttributeValue');
        $this->notificationBackend = $notificationBackend;
        $this->tokenStorage = $tokenStorage;
        $this->project = $project;
        $this->messageBus = $messageBus;
    }

    public function getBatchActions()
    {
        $actions = array(
            'deleteDemands' => array(
                'label'            => 'Пометить как удалённые',
                'ask_confirmation' => true
            ),
            'reDetectionCategory'  => array(
                'label'            => 'Переопределить категории и атрибуты',
                'ask_confirmation' => true
            )
        );

        $actions = array_merge($actions, parent::getBatchActions());

        return $actions;
    }

    public function prePersist($object)
    {
        /* @var $object Demand */
        $object->setSourceTypeId(SiteSourceTypeProvider::SOURCE_ADMIN);

        $user = $this->tokenStorage->getToken()->getUser();
        $object->setUser($user);

        if ($object->getModeratedAt()) {
            $object->setModeratedAt(new \DateTime());
        }

        $object->populateDataFromRequest($this->getRequest(), false);

        $object->setUpdatedAt(new \DateTime());
        $object->setUpdatedBy($user);
    }

    public function getSubject()
    {
        $demand = parent::getSubject();
        /* @var $demand Demand */

        if ($demand && !$this->subjectInitialized) {
            $demand->cityTitle = $demand->getCity() ? $demand->getCity()->getTitle() : '';
            $this->em->getRepository('MetalDemandsBundle:DemandItem')->loadDemandItemsCollectionForCompany($demand);
            $this->subjectInitialized = true;
        }

        return $this->subject;
    }

    public function preUpdate($object)
    {
        /* @var $object Demand */
        $hasCityTitle = $this->getForm()->has('cityTitle');
        if ($hasCityTitle) {
            $cityTitle = $this->getForm()->get('cityTitle')->getData();
            if ($cityTitle) {
                /* @var $city City */
                $city = $this->em->getRepository('MetalTerritorialBundle:City')->findOneBy(array('title' => $cityTitle));

                if ($city) {
                    $object->setCity($city);
                }
            }
        }

        $object->updateCategories();

        if ($object->getModeratedAt()) {
            $object->setModeratedAt(new \DateTime());
        }

        $object->populateDataFromRequest($this->getRequest(), false, true);

        $object->setUpdatedAt(new \DateTime());
        $object->setUpdatedBy($this->tokenStorage->getToken()->getUser());
    }

    public function postUpdate($object)
    {
        /* @var $object AbstractDemand */
        $demandData = array();
        if ($object->isModerated() && !$object->isDeleted()) {
            $demandData['reindex'] = array($object->getId());
            $this->messageBus->sendEvent(Events::EVENT_DEMAND_MODERATED, new DemandModerated($object->getId()));
        } else {
            $demandData['removeIndex'] = array($object->getId());
        }
        $this->notificationBackend->createAndPublish('admin_demand', $demandData);

        if ($object instanceof Demand && ($callback = $object->getFromCallback()) && !$callback->isProcessed() && $callback->isPublic()) {
            $callback->setProcessed();
            $callback->setProcessedBy($this->tokenStorage->getToken()->getUser());
            $this->em->flush($callback);
        }

        if ($object instanceof PrivateDemand) {
            $this->em->getRepository('MetalCompaniesBundle:CompanyCounter')->updateViewDemandCounter(array($object->getCompany()));
        }
    }

    public function postPersist($object)
    {
        /* @var $object Demand */
        if ($object->isModerated() && !$object->isDeleted()) {
            $demandData['reindex'] = [$object->getId()];
            $this->messageBus->sendEvent(Events::EVENT_DEMAND_MODERATED, new DemandModerated($object->getId()));
        } else {
            $demandData['removeIndex'] = [$object->getId()];
        }

        $this->notificationBackend->createAndPublish('admin_demand', $demandData);

        $callback = $object->getFromCallback();
        if ($callback && !$callback->isProcessed() && $callback->isPublic()) {
            $callback->setProcessed();
            $callback->setProcessedBy($this->tokenStorage->getToken()->getUser());
            $this->em->flush($callback);
        }
    }

    public function validate(ErrorElement $errorElement, $object)
    {
        /* @var $object AbstractDemand */
        if ($object->isModerated()) {
            if (!count($object->getDemandItems())) {
                $errorElement
                    ->with('demandItems')
                    ->addViolation('Промодерированная заявка требует наличие как минимум одной позиции')
                    ->end()
                ;
            }

            if (!$object->getPerson()) {
                $errorElement
                    ->with('person')
                    ->addViolation('Промодерированная заявка требует заполненого поля с ФИО')
                    ->end()
                ;
            }

            if (!$object->getCreatedAt()) {
                $errorElement
                    ->with('createdAt')
                    ->addViolation('Заявка должна иметь дату создания')
                    ->end()
                ;
            }
        }
    }

    public function getDatagrid()
    {
        if ($this->datagrid) {
            return $this->datagrid;
        }

        $datagrid = parent::getDatagrid();
        $demands = $datagrid->getResults();
        /* @var $demands AbstractDemand[] */
        $this->demandItemsRepository->attachDemandItems($demands);

        $filterPrivate = $datagrid->getFilter('private')->getValue();
        if ($filterPrivate['value'] == AbstractDemand::TYPE_PRIVATE) {
            $this->demandRepository->attachCompaniesToDemands($demands);
        }

        $this->demandRepository->attachCategoriesToDemands($demands);
        $this->demandItemAttributeValueRepository->attachAttributesCollectionToDemands($demands);

        $usersIds = array();
        $citiesIds = array();
        foreach ($demands as $demand) {
            if ($updatedBy = $demand->getUpdatedBy()) {
                $usersIds[$updatedBy->getId()] = true;
            }

            if ($createdBy = $demand->getUser()) {
                $usersIds[$createdBy->getId()] = true;
            }

            if ($city = $demand->getCity()) {
                $citiesIds[$city->getId()] = true;
            }
        }

        if ($usersIds) {
            $this->em->getRepository('MetalUsersBundle:User')
                ->findBy(array('id' => array_keys($usersIds)));
        }

        if ($citiesIds) {
            $this->em->getRepository('MetalTerritorialBundle:City')
                ->findBy(array('id' => array_keys($citiesIds)));
        }

        return $this->datagrid;
    }

    protected function configureRoutes(RouteCollection $collection)
    {
        $collection
            ->add('view_history', $this->getRouterIdParameter().'/view_history', array('_controller' => 'MetalDemandsBundle:DemandAdmin:viewHistory'))
            ->add('show_all_statistic', 'show_all_statistic', array('_controller' => 'MetalDemandsBundle:DemandAdmin:viewAllDemandsStatistics'))
            ->add('simple_list', 'simple_list', array('_controller' => 'MetalDemandsBundle:DemandAdmin:simpleList'))
            ->add('copy_demand', $this->getRouterIdParameter().'/copy_demand', array('_controller' => 'MetalDemandsBundle:DemandAdmin:copyDemand'))
            ->remove('delete');
    }

    public function getDashboardActions()
    {
        $actions = parent::getDashboardActions();

        $actions['simple_list'] = array(
            'label' => 'Список заявок на модерацию',
            'url' => $this->generateUrl('simple_list'),
            'icon' => 'list'
        );

        $actions['create'] = array(
            'label' => 'link_add',
            'url' => $this->generateUrl('create', array('subclass' => 'demand')),
            'icon' => 'plus-circle'
        );

        return $actions;
    }

    public function configure()
    {
        $this->setTemplate('edit', 'MetalDemandsBundle:DemandAdmin:edit.html.twig');
        $this->setTemplate('show', 'MetalDemandsBundle:DemandAdmin:show_demand.html.twig');
    }

    public function getFormTheme()
    {
        return array_merge(
            parent::getFormTheme(),
            array('MetalDemandsBundle:DemandAdmin:edit_item.html.twig')
        );
    }

    public function setRequest(Request $request)
    {
        parent::setRequest($request);

        $filterParameters = $this->getFilterParameters();

        if (isset($filterParameters['_editingMode']) && $filterParameters['_editingMode']['value'] === 'extended') {
            $this->isExtended = true;
            $this->setTemplate('inner_list_row','MetalDemandsBundle:DemandAdmin:list_inner_row.html.twig');
        }
    }

    public function getNewInstance()
    {
        $object = new Demand();
        $object->setModerated();
        $demandItem = new DemandItem();
        $object->addDemandItem($demandItem);

        $callbackId = $this->getRequest()->query->get('callback_id');
        if ($callbackId) {
            $callback = $this->em->getRepository('MetalCallbacksBundle:Callback')->find($callbackId);
            /* @var $callback CallbackEntity */

            if ($callback->getCategory()) {
                $demandItem->setTitle($callback->getCategory()->getTitle());
                $demandItem->setCategory($callback->getCategory());
            }

            $demandItem->setVolumeTypeId($callback->getVolumeTypeId());
            $demandItem->setVolume($callback->getVolume());
            $object->setPhone($callback->getPhone());
            $object->setCity($callback->getCity());
            $object->setFromCallback($callback);
        } else {
            $demandItem->setVolumeTypeId(ProductMeasureProvider::WITHOUT_VOLUME);
        }

        foreach ($this->getExtensions() as $extension) {
            $extension->alterNewInstance($this, $object);
        }

        if ($this->project === 'product') {
            $object->setDemandPeriodicityId(DemandPeriodicityProvider::PERMANENT);
            $object->setAdminSourceTypeId(AdminSourceTypeProvider::ADMIN_SOURCE_PHONE);
        } elseif ($this->project === 'metalloprokat') {
            $object->setDemandPeriodicityId(DemandPeriodicityProvider::ONCE);
            $object->setAdminSourceTypeId(AdminSourceTypeProvider::ADMIN_SOURCE_PHONE);
        }
        $object->setConsumerTypeId(ConsumerTypeProvider::CONSUMER);

        return $object;
    }

    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('id', null, array('label' => 'ID заявки', 'read_only' => true))
            ->add('demandFiles', null, array(
                'label' => 'Файл',
                'template' => 'MetalDemandsBundle:DemandAdmin:show_file.html.twig'

            ))
            ->add(
                'createdAt',
                null,
                array(
                    'widget' => 'single_text',
                    'label' => 'Дата создания',
                    'format' => 'dd.MM.yyyy HH:mm:ss',
                    'required' => true,
                    'read_only' => true
                )
            )
            ->add('body', null, array('label' => 'Примечание для модератора', 'read_only' => true))
            ->add('info', null, array('label' => 'Информация', 'read_only' => true, 'attr' => array('rows' => 9)))
            ->add('demandItems', null, array('label' => 'Позиции', 'template' => 'MetalDemandsBundle:DemandAdmin:show_items.html.twig'))
        ;
    }

    protected function configureFormFields(FormMapper $formMapper)
    {
        $subject = $this->getSubject();
        /* @var $subject Demand */

        if (!$subject->isPublic()) {
            $formMapper->add('deleted', 'checkbox', array('label' => 'Удалена', 'required' => false));

            return;
        }

        if ($subject->getSourceTypeId() == SiteSourceTypeProvider::SOURCE_MIRROR_FROM_METALLOPROKAT) {
            $this->demandRepository->attachIdDemandFromMetalloprokat($subject);
        }

        $phoneHelps = <<<EOF
    <p style="color: red" class="old-phone-number"></p>
    <button type='button' id='normalize-phone-btn'>Нормализовать номер</button>
    <script type="text/javascript">
          $('#normalize-phone-btn').bind("click", function () {
              var phoneStr = $('.row-phone-number').val();
              var phoneNumbers = phoneStr.match(/(\+?\d[\-\s]?)?(\(?\d{3,4}\)?[\-\s]?)?[\d\-\s]{5,9}(\s|,|$)/gi);
              var result = '';
              var i = 0;
              
              if (phoneNumbers === null) {
                return;
              }
            
              phoneNumbers.forEach(function (number) {
                var separator = i == 0 ? '' : ', ';
                i++;
                result += separator + number.replace(/[^0-9\+]/gi, '');
              });
            
              $('.row-phone-number').val(result);
              $('.old-phone-number').text('Оригинальный номер: ' + phoneStr);
          });
    </script>
EOF;
        $emailOptions['label'] = 'Email';
        $emailOptions['required'] = false;
        $emailOptions['help'] = '';
        $emailOptions['attr'] = array('style' => 'width: 580px;');
        if ($subject->getEmailFilePath()) {
            $webPath = $this->getRequest()->getUriForPath($subject->getEmailWebPath());
            $emailOptions['help'] = '<img src="' . $webPath . '" />';
        }

        $formMapper
            ->with('center', array('label' => 'Позиции'))
                ->add(
                    'demandItems',
                    'sonata_type_collection',
                    array(
                        'label' => false,
                        'by_reference' => false,
                    ),
                    array(
                        'edit' => 'inline',
                        'inline' => 'table',
                    )
                )
                ->add('info', null, ['label' => 'Информация', 'attr' => ['rows' => 9, 'style' => 'width: 100%;']])
            ->end()

            ->with('left', array('label' => 'Доп. информация', 'class' => 'col-md-6'))
                ->add(
                    'cityTitle',
                    'text',
                    array(
                        'label' => 'Город',
                        'required' => true,
                        'attr' => array(
                            'typeahead' => '',
                            'typeahead-prefetch-url' => $this->routeGenerator->generate(
                                'MetalTerritorialBundle:Suggest:getAllCities'
                            ),
                            'typeahead-suggestion-template-url' => "'typeahead-suggestion-with-parent'",
                            'typeahead-model' => 'city',
                            'style' => 'width: 580px;',
                        ),
                    )
                )
                ->add('city', 'entity_id', array(
                    'class' => 'MetalTerritorialBundle:City',
                    'label' => 'ID города',
                    'hidden' => false,
                    'read_only' => true,
                    'required' => false,
                    'attr' => array(
                        'ng-model' => 'city.id',
                        'initial-value' => '',
                        'style' => 'width: 580px;',
                    ),
                ))
                ->add(
                    'companyTitle',
                    null,
                    array(
                        'label' => 'Компания',
                        'attr' => array('style' => 'width: 580px;')
                    )
                )
                ->add('address', null, array('label' => 'Адрес', 'attr' => array('style' => 'width: 580px;')))
                ->add('person', null, array('label' => 'ФИО', 'required' => false, 'attr' => array('style' => 'width: 580px;')))
                ->add(
                    'phone',
                    null,
                    array(
                        'label' => 'Телефон',
                        'help' => $phoneHelps,
                        'attr' => array(
                            'class' => 'row-phone-number',
                            'style' => 'width: 580px;'
                        )
                    )
                )
                ->add('email', null, $emailOptions)
                ->add(
                    'demandFiles',
                    'sonata_type_collection',
                    array(
                        'label' => 'Файлы',
                        'by_reference' => false,
                        'required' => false
                    ),
                    array(
                        'edit' => 'inline',
                        'inline' => 'table',
                    )
                )
                ->add('displayFileOnSite', 'checkbox', array('label' => 'Отображать файл на сайте', 'required' => false))
                ->add('wholesale', null, array('label' => 'Оптовик', 'required' => false))
                ->add(
                    'demandPeriodicityId',
                    'choice',
                    array(
                        'label' => 'Периодичность',
                        'choices' => DemandPeriodicityProvider::getAllTypesAsSimpleArray(),
                        'attr' => array('style' => 'width: 580px;')
                    )
                )
                ->add(
                    'consumerTypeId',
                    'choice',
                    array(
                        'label' => 'Тип потребителя',
                        'choices' => ConsumerTypeProvider::getAllTypesAsSimpleArray(),
                        'attr' => array('style' => 'width: 580px;')
                    )
                )
                ->add('from', 'hidden', array('mapped' => false, 'required' => false, 'data' => $this->getRequest()->query->get('from')))
            ->end()

            ->with('right', array('label' => 'Статус заявки', 'class' => 'col-md-6'))
                ->add('moderated', 'checkbox', array('label' => 'Промодерирована', 'required' => false))
                ->add('deleted', 'checkbox', array('label' => 'Удалена', 'required' => false));
                if ($this->project === 'metalloprokat') {
                    $formMapper
                        ->add('mirrorToStroy', 'checkbox', array('label' => 'Отобразить на Строй.ру', 'required' => false));
                }
            $formMapper
                ->add(
                    'adminSourceTypeId',
                    'choice',
                    array(
                        'label' => 'Источник заявки',
                        'choices' => AdminSourceTypeProvider::getAllTypesAsSimpleArray(),
                        'attr' => array('style' => 'width: 580px;'),
                    )
                )
                ->add(
                    'body',
                    null,
                    array(
                        'label' => 'Примечание для модератора',
                        'attr' => array('style' => 'width: 580px;'),
                    )
                )
                ->add(
                    'createdAt',
                    'sonata_type_datetime_picker',
                    array(
                        'label' => 'Дата создания',
                        'format' => 'dd.MM.yyyy HH:mm:ss',
                        'required' => true,
                    )
                )
                ->add(
                    'publicUntil',
                    'sonata_type_date_picker',
                    array(
                        'label' => 'Отображать в свободном доступе до',
                        'format' => 'dd.MM.yyyy',
                        'required' => false,
                        'help' => 'При установке даты, контакты заявки будут видны всем, в том числе незарегистрированным пользователям',
                    )
                )
            ->end();

        $formMapper
            ->with('hidden', array('class' => 'g-hidden'))
                ->add('fromCallback', 'entity_id', array(
                    'class' => 'MetalCallbacksBundle:Callback',
                    'label' => 'ID обратного звонка',
                    'hidden' => false,
                    'read_only' => true,
                    'required' => false,
                    'attr' => array(
                        'initial-value' => '',
                    ),
                ))
                ->add('deadline', null, array('label' => 'Актуальность'))
                ->add('timeToShow', null, array('label' => 'Время показа'))
            ->end()
        ;
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $filterParameters = $this->getFilterParameters();
        if ($this->filterParameters) {
            $listMapper
                ->addIdentifier('id', null, array(
                    'template' => 'MetalDemandsBundle:DemandAdmin:colorId.html.twig',
                    'route' => array('parameters' => $this->filterParameters ? array('from' => 'simple') : array())
                ));
        } else {
            $listMapper
                ->add('id', null, array(
                    'template' => 'MetalDemandsBundle:DemandAdmin:colorIdWithLinks.html.twig'
                ));
        }
        $listMapper
            ->add(
                'person',
                null,
                array('label' => 'ФИО', 'template' => 'MetalDemandsBundle:DemandAdmin:colorModeratedText.html.twig')
            )
            ->add('phone', null, array('label' => 'Телефон'))
        ;

        if (!$this->isExtended) {
            $listMapper->add(
                'demandItems',
                null,
                array(
                    'label' => 'Позиции',
                    'template' => 'MetalDemandsBundle:DemandAdmin:demandItems.html.twig'
                )
            );
        }

        $listMapper->add('city', null, array('label' => 'Город', 'associated_property' => 'title'))
//            ->add('deadline', null, array('label' => 'Актуальность'))
            ->add('demandPeriodicityTitle', null, array('label' => 'Периодичность'))
            ->add('consumerTypeTitle', null, array('label' => 'Тип потребителя'))
            ->add('adminSourceTypeTitle', null, array('label' => 'Источник заявки'))
//            ->add('timeToShow', null, array('label' => 'Время показа'))
            ->add('wholesale', null, array('label' => 'Оптовик'))
            ->add('createdAt', null, array('label' => 'Дата создания'));

        if ($this->isExtended) {
            $listMapper->add('updatedAt', null, array('label' => 'Дата изменения'));
        }

        $listMapper
            ->add('user', null, array('label' => 'Автор', 'associated_property' => 'fullName'))
            ->add(
                'category',
                null,
                array(
                    'label' => 'Основная категория заявки',
                    'template' => 'MetalDemandsBundle:DemandAdmin:title.html.twig'
                )
            );

        if (!$this->filterParameters) {
            $listMapper
                ->add(
                    '_action',
                    'actions',
                    array(
                        'label' => 'Просмотры заявки',
                        'actions' => array(
                            'viewDemandHistory' => array(
                                'template' => 'MetalDemandsBundle:DemandAdmin:list__action_view.html.twig'
                            )
                        )
                    )
                );
        }

        if ($filterParameters['private']['value'] == AbstractDemand::TYPE_PRIVATE) {
            // специально используем несуществующее поле
            $listMapper
                ->add(
                    '_companyTitle',
                    null,
                    array(
                        'label' => 'Получатель',
                        'template' => 'MetalProjectBundle:Admin:company_with_id_list.html.twig'
                    )
                );
        };
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id', null, array('label' => 'ID заявки'))
            ->add(
                '_editingMode',
                'doctrine_orm_callback',
                array(
                    'label' => 'Режим просмотра',
                    'mapped' => false,
                    'callback' => function () {
                    }
                ),
                'choice',
                array('choices' => array('batch' => 'Стартовый', 'extended' => 'Расширенный'))
            )
            ->add(
                'category',
                'doctrine_orm_callback',
                array(
                    'label' => 'Категория',
                    'callback' => function (ProxyQuery $queryBuilder, $alias, $field, $value) {
                        if (!isset($value['value'])) {
                            return null;
                        }

                        /* @var $queryBuilder QueryBuilder */

                        $queryBuilder
                            ->join('MetalCategoriesBundle:CategoryClosure', 'closure', 'WITH', sprintf('%s.category = closure.descendant', $alias))
                            ->andWhere('closure.ancestor = :closure_ancestor')
                            ->setParameter('closure_ancestor', $value['value']);

                        return true;
                    }
                ),
                'choice',
                array(
                    'choices' => $this->em->getRepository('MetalCategoriesBundle:Category')->getCategoriesAsSimpleArray(true)
                )
            )
            ->add('companyTitle', null, array('label' => 'Компания'))
            ->add('person', null, array('label' => 'ФИО'))
            ->add('phone', null, array('label' => 'Телефон'))
            ->add('email', null, array('label' => 'Email'))
            ->add(
                'consumerTypeId',
                'doctrine_orm_choice',
                array(
                    'label' => 'Тип потребителя'
                ),
                'choice',
                array(
                    'required' => false,
                    'choices' => ConsumerTypeProvider::getAllTypesAsSimpleArray()
                )
            )
            ->add(
                'demandPeriodicityId',
                'doctrine_orm_choice',
                array(
                    'label' => 'Периодичность'
                ),
                'choice',
                array(
                    'required' => false,
                    'choices' => DemandPeriodicityProvider::getAllTypesAsSimpleArray()
                )
            )
            ->add(
                'sourceTypeId',
                'doctrine_orm_choice',
                array(
                    'label' => 'Источник'
                ),
                'choice',
                array(
                    'required' => false,
                    'choices' => $this->project !== 'stroy' ? SiteSourceTypeProvider::getAllTypesAsSimpleArrayWithExclude(SiteSourceTypeProvider::SOURCE_MIRROR_FROM_METALLOPROKAT) : SiteSourceTypeProvider::getAllTypesAsSimpleArray()
                )
            )
            ->add(
                'adminSourceTypeId',
                'doctrine_orm_choice',
                array(
                    'label' => 'Источник заявки'
                ),
                'choice',
                array(
                    'required' => false,
                    'choices' => AdminSourceTypeProvider::getAllTypesAsSimpleArray()
                )
            )
            ->add(
                'city',
                'doctrine_orm_choice',
                array(
                    'label' => 'Город',
                    'property' => 'title',
                ),
                'choice',
                array(
                    'choices' => $this->em->getRepository('MetalTerritorialBundle:City')->getSimpleCitiesArray()
                )
            )
            ->add(
                'demandFiles',
                'doctrine_orm_callback',
                array(
                    'label' => 'Наличие файла',
                    'callback' => function (ProxyQuery $queryBuilder, $alias, $field, $value) {
                        if (!isset($value['value'])) {
                            return null;
                        }

                        /* @var $queryBuilder QueryBuilder */
                        $queryBuilder->leftJoin(sprintf('%s.demandFiles', $alias), 'demandFiles');

                        if ($value['value'] === 'y') {
                            $queryBuilder->andWhere('demandFiles.file.name IS NOT NULL');
                        } else {
                            $queryBuilder->andWhere('demandFiles.file.name IS NULL');
                        }

                        return true;
                    }
                ),
                'choice',
                array(
                    'choices' => array(
                        'y' => 'С файлом',
                        'n' => 'Без файла'
                    )
                )
            )

            ->add(
                'moderatedAt',
                'doctrine_orm_callback',
                array(
                    'label' => 'Модерация',
                    'callback' => function (ProxyQuery $queryBuilder, $alias, $field, $value) {
                        if (!isset($value['value'])) {
                            return null;
                        }
                        /* @var $queryBuilder QueryBuilder */

                        if ($value['value'] === 'y') {
                            $queryBuilder->andWhere(sprintf('%s.moderatedAt IS NOT NULL', $alias));
                        } else {
                            $queryBuilder->andWhere(sprintf('%s.moderatedAt IS NULL', $alias));
                        }

                        return true;
                    }
                ),
                'choice',
                array(
                    'choices' => array(
                        'y' => 'Промодерирована',
                        'n' => 'Не промодерирована'
                    )
                )
            )
            ->add(
                'deletedAt',
                'doctrine_orm_callback',
                array(
                    'label' => 'Удаленность',
                    'callback' => function (ProxyQuery $queryBuilder, $alias, $field, $value) {
                        if (!isset($value['value'])) {
                            return null;
                        }
                        /* @var $queryBuilder QueryBuilder */

                        if ($value['value'] === 'y') {
                            $queryBuilder->andWhere(sprintf('%s.deletedAt IS NOT NULL', $alias));
                        } else {
                            $queryBuilder->andWhere(sprintf('%s.deletedAt IS NULL', $alias));
                        }

                        return true;
                    }
                ),
                'choice',
                array(
                    'choices' => array(
                        'y' => 'Удалена',
                        'n' => 'Не удалена'
                    )
                )
            )
            ->add(
                'private',
                'doctrine_orm_callback',
                array(
                    'label' => 'Тип',
                    'callback' => function (ProxyQuery $queryBuilder, $alias, $field, $value) {
                        if (!isset($value['value'])) {
                            return null;
                        }
                        /* @var $queryBuilder QueryBuilder */

                        if ($value['value'] == AbstractDemand::TYPE_PRIVATE) {
                            $queryBuilder->andWhere(sprintf('%s INSTANCE OF Metal\DemandsBundle\Entity\PrivateDemand', $alias));
                        } else {
                            $queryBuilder->andWhere(sprintf('%s NOT INSTANCE OF Metal\DemandsBundle\Entity\PrivateDemand', $alias));
                        }

                        return true;
                    }
                ),
                'choice',
                array(
                    'choices' => array(
                        AbstractDemand::TYPE_PRIVATE => 'Приватные',
                        AbstractDemand::TYPE_PUBLIC => 'Публичные'
                    )
                )
            )
            ->add(
                'user',
                'doctrine_orm_number',
                array(
                    'label' => 'Айди автора заявки',
                )
            )
        ;

        $filterParameters = $this->getFilterParameters();
        if (!empty($filterParameters['private']['value']) && $filterParameters['private']['value'] == AbstractDemand::TYPE_PUBLIC) {
            $datagridMapper->add(
                'fakeUser',
                'doctrine_orm_choice',
                array(
                    'label' => 'Автор заявки',
                    'field_name' => 'user',
                ),
                'choice',
                array('choices' => $this->em->getRepository('MetalUsersBundle:User')->getSimpleModerators())
            );
        }

        $datagridMapper
            ->add(
                'createdAt',
                'doctrine_orm_date_range',
                array('label' => 'Дата создания'),
                'sonata_type_date_range_picker',
                array(
                    'field_options_start' => array(
                        'format' => 'dd.MM.yyyy',
                        'label' => 'Дата от',
                    ),
                    'field_options_end' => array(
                        'format' => 'dd.MM.yyyy',
                        'label' => 'Дата до',
                    ),
                    'attr' => array(
                        'class' => 'js-sonata-datepicker',
                    ),
                )
            )
            ->add(
                'publicUntil',
                'doctrine_orm_callback',
                array(
                    'label' => 'В открытом доступе',
                    'callback' => function (ProxyQuery $queryBuilder, $alias, $field, $value) {
                        if (!isset($value['value'])) {
                            return null;
                        }

                        /* @var $queryBuilder QueryBuilder */

                        if ($value['value'] === BooleanType::TYPE_YES) {
                            $queryBuilder->andWhere(sprintf('%s.publicUntil IS NOT NULL', $alias));
                        } else {
                            $queryBuilder->andWhere(sprintf('%s.publicUntil IS NULL', $alias));
                        }

                        return true;
                    },
                ),
                'sonata_type_boolean'
            )
            ->add(
                '_volumeTypeId',
                'doctrine_orm_callback',
                array(
                    'label' => 'Объем потребности от',
                    'callback' => function (ProxyQuery $queryBuilder, $alias, $field, $value) {
                        if (empty($value['value']['volumeTypeId']) && empty($value['value']['volume'])) {
                            return null;
                        }

                        /* @var $queryBuilder QueryBuilder */
                        $queryBuilder
                            ->join("$alias.demandItems", 'demandItems')
                            ->andWhere('demandItems.volumeTypeId = :volume_type')
                            ->setParameter('volume_type', $value['value']['volumeTypeId'])
                            ->andWhere('demandItems.volume >= :volume')
                            ->setParameter('volume', $value['value']['volume'])
                        ;

                        return true;
                    }
                ),
                VolumeFormType::class
            );
    }

    public function toString($object)
    {
        if ($object instanceof Demand || $object instanceof PrivateDemand) {
            return sprintf('Заявка %d', $object->getId());
        }

        return '';
    }

    public function getFormBuilder()
    {
        $this->formOptions['validation_groups'] = function(FormInterface $form) {
            $deleted = $form->get('deleted')->getData();

            // для удаленной заявки устанавливаем несуществующую группу что бы валидация не выполнялась
            return $deleted ? array('dummy') : array('Default', 'admin_panel');
        };

        return parent::getFormBuilder();
    }

    public function setFilterParameters(array $filterParameters)
    {
        $this->filterParameters = $filterParameters;
    }

    public function getFilterParameters()
    {
        $this->datagridValues = array_merge($this->filterParameters, $this->datagridValues);

        return parent::getFilterParameters();
    }

    public function generateUrl($name, array $parameters = array(), $absolute = false)
    {
        if ($name === 'list' && $this->filterParameters) {
            $name = 'simple_list';
        }

        return parent::generateUrl($name, $parameters, $absolute);
    }
}
