<?php

namespace Metal\CategoriesBundle\Admin;

use Doctrine\ORM\EntityManager;
use Metal\CategoriesBundle\Entity\MenuItem;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Route\RouteCollection;

class MenuItemAdmin extends AbstractAdmin
{
    /**
     * @var EntityManager
     */
    private $em;

    public function __construct($code, $class, $baseControllerName, EntityManager $em)
    {
        parent::__construct($code, $class, $baseControllerName);

        $this->em = $em;
    }

    public function getNewInstance()
    {
        $object = parent::getNewInstance();
        /* @var $object MenuItem */

        if ($parentId = $this->getRequest()->query->get('parent')) {
            $parent = $this->getModelManager()->find($this->getClass(), $parentId);
            $object->setParent($parent);
        }

        return $object;
    }

    protected function configureRoutes(RouteCollection $collection)
    {
        $collection
            ->add('show_tree', 'showTree', array('_controller' => 'MetalCategoriesBundle:MenuItemAdmin:showTree'))
            ->add('validate', 'validate', array('_controller' => 'MetalCategoriesBundle:MenuItemAdmin:validation'))
            ->add(
                'change_position',
                $this->getRouterIdParameter().'/{action}',
                array('_controller' => 'MetalCategoriesBundle:MenuItemAdmin:changePosition'),
                array('action' => 'up|down')
            );

        parent::configureRoutes($collection);
    }

    public function getDashboardActions()
    {
        $actions = parent::getDashboardActions();

        $actions['show_tree'] = array(
            'label' => 'Просмотр в виде дерева',
            'url' => $this->generateUrl('show_tree'),
            'icon' => 'tree'
        );

        return $actions;
    }

    public function configure()
    {
        parent::configure();
        $this->setTemplate('list', 'MetalCategoriesBundle:MenuItemAdmin:list.html.twig');
    }

    public function getBatchActions()
    {
        return array();
    }

    protected function configureFormFields(FormMapper $formMapper)
    {
        $subject = $this->getSubject();
        /* @var $subject MenuItem */
        $id = $subject->getId();

        $formMapper
            ->add('title', null, array('label' => 'Название'))
            ->add(
                'parent',
                'entity',
                array(
                    'label' => 'Родитель',
                    'required' => false,
                    'class' => 'MetalCategoriesBundle:MenuItem',
                    'property' => 'nestedTitle',
                    'placeholder' => 'Без родителя',
                    'choices' => $this->em->getRepository('MetalCategoriesBundle:MenuItem')->buildCategoriesByLevels($id),
                )
            )

            ->add(
                'mode',
                'choice',
                array('label' => 'Режим', 'choices' => MenuItem::getCategoryModesAsSimpleArray())
            )
            ->add(
                'category',
                'entity',
                array(
                    'label' => 'Категория',
                    'required' => false,
                    'class' => 'MetalCategoriesBundle:Category',
                    'property' => 'nestedTitle',
                    'choices' => $this->em->getRepository('MetalCategoriesBundle:Category')->buildCategoriesByLevels(),
                )
            )
            ->add('virtualChildrenIds', 'textarea',
                array(
                    'label' => 'Виртуальные дети',
                    'required' => false,
                    'help' => 'Идентификаторы виртуальных дочерних елементов меню через запятую, только для "Виртуальная ссылка"'
                ))
            ->add('logo', null, array('label' => 'Логотип'))
            ->add(
                'dependsFromMenuItems',
                'textarea',
                array('required' => false, 'label' => 'Зависит от'),
                array('help' => 'Список id элементов, от которых зависит показ этого узла')
            )
            ->add('hideIfNotActive', null, array('required' => false, 'label' => 'Скрыть если не активен'))
            ->add('position', null, array('label' => 'Позиция'))
            ->end();
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id')
            ->add('title', null, array('label' => 'Название'))
            ->add(
                'mode',
                'choice',
                array(
                    'label' => 'Режим',
                    'choices' => MenuItem::getCategoryModesAsSimpleArray()
                )
            )
            ->add('parent', null, array('label' => 'Родитель', 'associated_property' => 'titleWithParent'))
            ->add('category', null, array('label' => 'Категория', 'associated_property' => 'titleWithParent'))
            ->add('position', null, array('label' => 'Позиция'));
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('title', null, array('label' => 'Название'))
            ->add(
                'mode',
                'doctrine_orm_choice',
                array(
                    'label' => 'Режим'
                ),
                'choice',
                array(
                    'choices' => MenuItem::getCategoryModesAsSimpleArray()
                )
            )
            ->add(
                'parent',
                null,
                array(
                    'label' => 'Родитель',
                    'required' => false,
                ),
                'entity',
                array(
                    'class' => 'MetalCategoriesBundle:MenuItem',
                    'property' => 'nestedTitle',
                    'choices' => $this->em->getRepository('MetalCategoriesBundle:MenuItem')->buildCategoriesByLevels()
                )
            )
            ->add(
                'category',
                null,
                array(
                    'label' => 'Категория',
                    'required' => false,
                ),
                'entity',
                array(
                    'class' => 'MetalCategoriesBundle:Category',
                    'property' => 'nestedTitle',
                    'choices' => $this->em->getRepository('MetalCategoriesBundle:Category')->buildCategoriesByLevels()
                )
            )
            ->add('position', null, array('label' => 'Позиция'));
    }

    public function toString($object)
    {
        return $object instanceof MenuItem ? $object->getTitle() : '';
    }
}
