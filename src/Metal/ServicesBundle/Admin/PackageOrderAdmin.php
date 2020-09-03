<?php

namespace Metal\ServicesBundle\Admin;

use Metal\ServicesBundle\Entity\Package;
use Metal\ServicesBundle\Entity\PackageOrder;
use Metal\ServicesBundle\Entity\ValueObject\ServicePeriodTypesProvider;
use Metal\ServicesBundle\Repository\PackageRepository;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class PackageOrderAdmin extends AbstractAdmin
{
    protected $datagridValues = array(
        '_sort_order' => 'DESC',
        'processedBy' => array(
            'value' => 'n',
        )
    );

    protected $formOptions = array(
        'validation_groups' => array('admin_panel'),
    );

    protected $parentAssociationMapping = 'company';

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    public function __construct(
        $code,
        $class,
        $baseControllerName,
        TokenStorageInterface $tokenStorage
    ) {
        parent::__construct($code, $class, $baseControllerName);

        $this->tokenStorage = $tokenStorage;
    }

    public function configureRoutes(RouteCollection $collection)
    {
        $collection->remove('delete');
    }

    public function toString($object)
    {
        return $object instanceof PackageOrder ? sprintf('Заявка на пакет %d', $object->getId()) : '';
    }

    public function preUpdate($object)
    {
        /* @var $object PackageOrder */
        if ($object->isProcessed()) {
            if (!$object->getProcessedBy()) {
                $object->setProcessedBy($this->tokenStorage->getToken()->getUser());
            }
        } else {
            $object->setProcessed(false);
        }
    }

    public function prePersist($object)
    {
        /* @var $object PackageOrder */
        if ($object->isProcessed()) {
            $object->setProcessedBy($this->tokenStorage->getToken()->getUser());
        }
    }

    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add(
                'package',
                'entity',
                array(
                    'label' => 'Тип услуги',
                    'class' => 'MetalServicesBundle:Package',
                    'property' => 'title',
                    'query_builder' => function (PackageRepository $r) {
                        return $r->createQueryBuilder('p')
                            ->where('p.id <> :base_package')
                            ->setParameter('base_package', Package::BASE_PACKAGE)
                            ->orderBy('p.priority', 'ASC');
                    },
                )
            )
            ->add(
                'packagePeriod',
                'choice',
                array('label' => 'Длительность', 'choices' => ServicePeriodTypesProvider::getAllTypesAsSimpleArray())
            )
            ->add('comment', 'textarea', array('label' => 'Комментарий', 'required' => false))
            ->add('fullName', 'text', array('label' => 'Имя'));

        if (!$this->isChild()) {
            $suggestUrl = $this->getRouteGenerator()->generate('MetalTerritorialBundle:Suggest:getCities');
            $formMapper
                ->add(
                    'company',
                    'entity_id',
                    array(
                        'label' => 'ID Компании',
                        'class' => 'MetalCompaniesBundle:Company',
                        'required' => false,
                        'hidden' => false,
                    )
                )
                ->add(
                    'cityTitle',
                    'text',
                    array(
                        'label' => 'Город',
                        'required' => true,
                        'attr' => array(
                            'typeahead' => '',
                            'typeahead-prefetch-url' => $suggestUrl,
                            'typeahead-suggestion-template-url' => "'typeahead-suggestion-with-parent'",
                            'typeahead-model' => 'city',

                        ),
                    )
                )
                ->add(
                    'city',
                    'entity_id',
                    array(
                        'class' => 'MetalTerritorialBundle:City',
                        'label' => 'ID города',
                        'hidden' => false,
                        'read_only' => true,
                        'required' => false,
                        'attr' => array(
                            'ng-model' => 'city.id',
                            'initial-value' => '',
                        ),
                    )
                )
                ->add('phone', 'text', array('label' => 'Телефон'))
                ->add('email', 'text', array('label' => 'Электронная почта'));
        }

        $formMapper
            ->add('startAt', 'sonata_type_date_picker', array('label' => 'Дата начала', 'format' => 'dd.MM.yyyy'))
            ->add('finishAt', 'sonata_type_date_picker', array('label' => 'Дата окончания', 'format' => 'dd.MM.yyyy'));

        if ($this->isChild() || $this->getSubject()->getCompany()) {
            $formMapper->add('isPayed', 'checkbox', array('label' => 'Оплачено', 'required' => false));
        }
        $formMapper->add('processed', 'checkbox', array('label' => 'Обработана', 'required' => false));
    }

    public function getSubject()
    {
        parent::getSubject();

        if ($this->subject && $this->subject->getCity()) {
            $this->subject->cityTitle = $this->subject->getCity()->getTitle();
        }

        return $this->subject;
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id')
            ->add('packageName', null, array('label' => 'Тип услуги'))
            ->add('packagePeriodName', null, array('label' => 'Длительность'))
            ->add('comment', null, array('label' => 'Комментарий'))
            ->add(
                'user',
                null,
                array('label' => 'Пользователь', 'template' => 'MetalServicesBundle:Admin:user_with_id.html.twig')
            )
            ->add(
                'companyTitle',
                null,
                array('label' => 'Компания', 'template' => 'MetalProjectBundle:Admin:company_with_id_list.html.twig')
            )
            ->add('city', null, array('label' => 'Город', 'associated_property' => 'title'))
            ->add('fullName', null, array('label' => 'Имя'))
            ->add('phone', null, array('label' => 'Телефон'))
            ->add('email', null, array('label' => 'Электронная почта'))
            ->add('createdAt', null, array('label' => 'Дата создания'))
            ->add('startAt', null, array('label' => 'Дата начала'))
            ->add('finishAt', null, array('label' => 'Дата окончания'))
            ->add('isPayed', null, array('label' => 'Оплачено'))
            ->add('processedAt', null, array('label' => 'Дата обработки'))
            ->add(
                'processedBy',
                null,
                array('label' => 'Кем обработана', 'template' => 'MetalServicesBundle:Admin:userInfo.html.twig')
            );
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add(
                'is_active',
                'doctrine_orm_callback',
                array(
                    'label' => 'Время действия',
                    'callback' => function ($queryBuilder, $alias, $field, $value) {
                        if (!isset($value['value'])) {
                            return;
                        }

                        switch ($value['value']) {
                            case 'past': {
                                $queryBuilder->andWhere(sprintf("%s.finishAt < :now", $alias));
                                break;
                            }
                            case 'active': {
                                $queryBuilder->andWhere(
                                    sprintf(":now BETWEEN %s.startAt AND %s.finishAt", $alias, $alias)
                                );
                                break;
                            }
                            case 'future': {
                                $queryBuilder->andWhere(sprintf("%s.startAt > :now", $alias));

                                break;
                            }
                        }
                        $queryBuilder->setParameter('now', new \DateTime());

                        return true;
                    },
                ),
                'choice',
                array(
                    'choices' => array(
                        'past' => 'Отработавшие',
                        'active' => 'Действующие',
                        'future' => 'Будущие',
                    ),
                )
            )
            ->add(
                'isPayed',
                'doctrine_orm_callback',
                array(
                    'label' => 'Оплачено',
                    'callback' => function ($queryBuilder, $alias, $field, $value) {
                        if (!isset($value['value'])) {
                            return;
                        }
                        if ($value['value'] == 'y') {
                            $queryBuilder->andWhere(
                                sprintf("(%s.isPayed IS NOT NULL AND %s.isPayed <> '')", $alias, $alias)
                            );
                        } else {
                            $queryBuilder->andWhere(sprintf("(%s.isPayed IS NULL OR %s.isPayed = '')", $alias, $alias));
                        }

                        return true;
                    },
                ),
                'choice',
                array(
                    'choices' => array(
                        'y' => 'Да',
                        'n' => 'Нет',
                    ),
                )
            )
            ->add(
                'processedBy',
                'doctrine_orm_callback',
                array(
                    'label' => 'Обработанные',
                    'callback' => function ($queryBuilder, $alias, $field, $value) {
                        if (!isset($value['value'])) {
                            return null;
                        }

                        if ($value['value'] === 'y') {
                            $queryBuilder->andWhere(sprintf('%s.processedBy IS NOT NULL', $alias));
                        } else {
                            $queryBuilder->andWhere(sprintf('%s.processedBy IS NULL', $alias));
                        }

                        return true;
                    }
                ),
                'choice',
                array(
                    'choices' => array(
                        'y' => 'Обработанные',
                        'n' => 'Не обработанные'
                    )
                )
            )
        ;
    }
}
