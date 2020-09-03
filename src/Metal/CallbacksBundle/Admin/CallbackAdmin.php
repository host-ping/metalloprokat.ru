<?php

namespace Metal\CallbacksBundle\Admin;

use Doctrine\ORM\EntityManager;
use Knp\Menu\ItemInterface as MenuItemInterface;
use Metal\CallbacksBundle\Entity\Callback;
use Metal\CallbacksBundle\Repository\CallbackRepository;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class CallbackAdmin extends AbstractAdmin
{
    protected $datagridValues = array(
        '_sort_order' => 'DESC',
        '_sort_by' => 'createdAt',
        'processedBy' => array(
            'value' => 'n',
        ),
        'kind' => array(
            'value' => Callback::CALLBACK_TO_MODERATOR,
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

    /**
     * @var CallbackRepository
     */
    private $callbackRepository;

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    public function __construct($code, $class, $baseControllerName, EntityManager $em, TokenStorageInterface $tokenStorage)
    {
        parent::__construct($code, $class, $baseControllerName);

        $this->callbackRepository = $em->getRepository('MetalCallbacksBundle:Callback');

        $this->tokenStorage = $tokenStorage;
    }

    protected function configureRoutes(RouteCollection $collection)
    {
        $collection
            ->remove('create')
            ->remove('delete');
    }

    public function configure()
    {
        $this->setTemplate('edit', 'MetalCallbacksBundle:CallbackAdmin:callback_info_edit.html.twig');
    }

    protected function configureTabMenu(MenuItemInterface $menu, $action, AdminInterface $childAdmin = null)
    {
        if (null !== $childAdmin) {
            return;
        }

        if (!$this->getSubject()) {
            return;
        }

        if (!in_array($action, array('show', 'edit'))) {
            return;
        }

        if ($this->getSubject()->isPublic()) {
            $menu->addChild(
                'Создать заявку на основании этого звонка',
                array(
                    'uri' => $this->routeGenerator->generate(
                        'admin_metal_demands_abstractdemand_create',
                        array(
                            'callback_id' => $this->getSubject()->getId(),
                            'subclass' => 'demand',
                        )
                    ),
                )
            );
        }
    }


    public function isGranted($name, $object = null)
    {
        /* @var $object Callback */
        if ($name === 'EDIT' && $object && !$object->isPublic()) {
            return false;
        }

        return parent::isGranted($name, $object);
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $filterParameters = $this->getFilterParameters();
        $listMapper
            ->addIdentifier('id')
            ->add('phone', null, array('label' => 'Телефон'))
            ->add('createdAt', null, array('label' => 'Дата создания'))
            ->add('processedAt', null, array('label' => 'Дата обработки'))
            ->add(
                'processedBy',
                null,
                array('label' => 'Кем обработан', 'template' => 'MetalCallbacksBundle:CallbackAdmin:userInfo.html.twig')
            )
            ->add('volume', null, array('label' => 'Объем'))
            ->add('measureName', null, array('label' => 'Ед. измерения'));

        if ($filterParameters['kind']['value'] == Callback::CALLBACK_TO_SUPPLIER) {
            // специально используем несуществующее поле
            $listMapper
                ->add('_companyTitle', null, array('label' => 'Получатель', 'template' => 'MetalProjectBundle:Admin:company_with_id_list.html.twig'));
        }

        $listMapper
            ->add('about', null, array('label' => ' ', 'template' => 'MetalCallbacksBundle:CallbackAdmin:callback_info_list.html.twig'));
    }

    protected function configureFormFields(FormMapper $formMapper)
    {
        $subject = $this->getSubject();
        /* @var $subject Callback */

        $categoryTitle = '';
        if ($subject->getCategory()) {
            $categoryTitle = $subject->getCategory()->getTitle();
        }

        $formMapper
            ->add('phone', null, array('label' => 'Телефон', 'required' => false, 'attr' => array('readonly' => true)))
            ->add(
                '_categoryTitle',
                'text',
                array(
                    'label' => 'Категория',
                    'mapped' => false,
                    'required' => false,
                    'attr' => array('readonly' => true, 'placeholder' => $categoryTitle),
                )
            )
            ->add('volume', null, array('label' => 'Объем', 'attr' => array('readonly' => true, 'required' => false)))
            ->add(
                'measureName',
                'text',
                array(
                    'label' => 'Ед. измерения',
                    'read_only' => true,
                    'required' => false,
                    'attr' => array('style' => 'width: 225px', 'readonly' => true),
                )
            )
            ->add('notation', 'textarea', array('label' => 'Примечание модератора', 'required' => false))
            ->add('processed', 'checkbox', array('label' => 'Обработан', 'required' => false));
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add(
                'processedBy',
                'doctrine_orm_callback',
                array(
                    'label' => 'Обработанные',
                    'callback' => function ($queryBuilder, $alias, $field, $value) {
                        if (!isset($value['value'])) {
                            return;
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
            ->add(
                'kind',
                'doctrine_orm_choice',
                array(
                    'label' => 'Для кого',
                ),
                'choice',
                array(
                    'choices' => array(
                        Callback::CALLBACK_TO_MODERATOR => 'Для модератора',
                        Callback::CALLBACK_TO_SUPPLIER => 'Для компании',
                    )
                )
            );
    }

    public function getDatagrid()
    {
        if ($this->datagrid) {
            return $this->datagrid;
        }

        $datagrid = parent::getDatagrid();
        $callbacks = $datagrid->getResults();

        $this->callbackRepository->attachCompaniesToCallbacks($callbacks);
        $this->callbackRepository->attachCitiesToCallbacks($callbacks);
        $this->callbackRepository->attachCategoriesToCallbacks($callbacks);

        return $this->datagrid;
    }

    public function preUpdate($object)
    {
        /* @var $object Callback */

        if ($object->isProcessed()) {
            if (!$object->getProcessedBy()) {
                $object->setProcessedBy($this->tokenStorage->getToken()->getUser());
            }
        } else {
            $object->setProcessed(false);
        }

    }

    public function toString($object)
    {
        return $object instanceof Callback ? $object->getPhone() : '';
    }
}
