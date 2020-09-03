<?php

namespace Metal\CategoriesBundle\Admin;

use Metal\CategoriesBundle\Repository\CategoryRepository;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;

use Sonata\AdminBundle\Admin\AbstractAdmin;

class CategoryTestItemAdmin extends AbstractAdmin
{
    public function getBatchActions()
    {
        $actions = parent::getBatchActions();
        $actions['test_category'] = array('label' => 'Определить категорию', 'ask_confirmation' => false);
        $actions['test_parameters'] = array('label' => 'Определить параметры', 'ask_confirmation' => false);

        return $actions;
    }

    protected function configureFormFields(FormMapper $formMapper)
    {
        $container = $this->getConfigurationPool()->getContainer();

        $formMapper
            ->add('title', 'textarea', array(
                'label' => 'Заголовок',
                'attr' => array(
                    'cols' => 250,
                    'rows' => 3,
                    'style' => 'width: 100%; height: 200px;',
                ),
            ))
            ->add('sizeString', null, array('label' => mb_convert_case($container->getParameter('tokens.product_volume_title'), MB_CASE_TITLE),'required' => false))
            ->add(
                'category',
                null,
                array(
                    'label' => 'Категория',
                    'required' => false,
                    'placeholder' => '',
                    'query_builder' => function (CategoryRepository $repo) {
                            return $repo
                                ->createQueryBuilder('c')
                                ->select('PARTIAL c.{id, title, branchIds}')
                                ->where('c.allowProducts = true')
                                ->andWhere('c.virtual = false')
                                ->orderBy('c.title');
                    },
                )
            )
        ;
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $container = $this->getConfigurationPool()->getContainer();

        $listMapper
            ->addIdentifier('id')
            ->add('title', null, array('label' => 'Заголовок'))
            ->add('sizeString', null, array('label' => mb_convert_case($container->getParameter('tokens.product_volume_title'), MB_CASE_TITLE)))
            ->add(
                'category',
                null,
                array(
                    'label' => 'Категория', 'associated_property' => 'title'
                )
            )
            ->add('mark', null, array('label' => 'Марка', 'associated_property' => 'title'))
            ->add('gost', null, array('label' => 'Гост', 'associated_property' => 'title'))
            ->add('size', null, array('label' => 'р-р', 'associated_property' => 'title'))
            ->add('class', null, array('label' => 'Класс', 'associated_property' => 'title'))
            ->add('type', null, array('label' => 'Вид', 'associated_property' => 'title'))
            ->add('vid', null, array('label' => 'Тип', 'associated_property' => 'title'))

        ;
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('title', null, array('label' => 'Название'))
            ->add('category', null, array('label' => 'Категория'), null, array(
                'query_builder' => function (CategoryRepository $repository) {
                        return $repository->createQueryBuilder('c')
                               ->orderBy('c.title');
                    },
            ))
        ;
    }
}
