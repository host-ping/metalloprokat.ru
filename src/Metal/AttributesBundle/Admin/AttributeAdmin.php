<?php

namespace Metal\AttributesBundle\Admin;

use Knp\Menu\ItemInterface as MenuItemInterface;
use Metal\AttributesBundle\Entity\Attribute;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;

class AttributeAdmin extends AbstractAdmin
{
    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->remove('delete');
        parent::configureRoutes($collection);
    }

    protected function configureTabMenu(MenuItemInterface $menu, $action, AdminInterface $childAdmin = null)
    {
        if (null !== $childAdmin) {
            return;
        }

        if (!in_array($action, array('show', 'edit'))) {
            return;
        }

        $menu->addChild(
            'Значения атрибутов',
            array('uri' => $this->getChild('metal.attributes.admin.attribute_value')->generateUrl('list'))
        );
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id')
            ->add('title', null, array('label' => 'Название'))
            ->add('code', null, array('label' => 'Код'))
            ->add('suffix', null, array('label' => 'Суффикс'))
            ->add('urlPriority', null, array('label' => 'Приоритет в URL'))
            ->add('outputPriority', null, array('label' => 'Приоритет вывода'))
            ->add('maxMatches', null, array('label' => 'Кол-во повторов в наим.'))
            ->add('indexableForSeo', null, array('label' => 'Индексация для SEO'));
    }

    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('title', null, array('label' => 'Название', 'required' => true))
            ->add('code', null, array('label' => 'Код', 'required' => true))
            ->add('suffix', null, array('label' => 'Суффикс', 'required' => false, 'trim' => false))
            ->add('columnsCount', null, array('label' => 'Количество столбцов', 'required' => true))
            ->add('urlPriority', null, array('label' => 'Приоритет в URL', 'required' => true))
            ->add('outputPriority', null, array('label' => 'Приоритет вывода', 'required' => true))
            ->add('maxMatches', null, array('label' => 'Кол-во повторов в наим.', 'required' => false))
            ->add('indexableForSeo', null, array('label' => 'Индексация для SEO', 'required' => false))
        ;
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('title', null, array('label' => 'Название'))
            ->add('code', null, array('label' => 'Код'))
            ->add(
                'indexableForSeo',
                'doctrine_orm_boolean',
                array('label' => 'Индексация для SEO'),
                'sonata_type_boolean'
            );
    }

    public function toString($object)
    {
        return $object instanceof Attribute ? $object->getTitle() : '';
    }
}
