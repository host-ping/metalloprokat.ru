<?php

namespace Spros\ProjectBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Metal\CategoriesBundle\Repository\CategoryRepository;
use Spros\ProjectBundle\Entity\SourcePoint;

class SourcePointAdmin extends AbstractAdmin
{
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('query', null, array('label' => 'Параметры'))
            ->add(
                'category',
                null,
                array(
                    'label' => 'Слова перехода',
                    'required' => false,
                    'property' => 'title',
                    'query_builder' => function (CategoryRepository $er) {
                        $qb = $er->createQueryBuilder('c')
                            ->addOrderBy('c.title', 'ASC');

                        return $qb;
                    },
                    'attr' => array(
                        'style' => 'width: 468px;'
                    ),
                )
            )
        ;
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id')
            ->add('category', null, array('label' => 'Категория'))
            ->add('query', null, array('label' => 'Слова перехода'))

        ;
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {

    }

    public function toString($object)
    {
        return $object instanceof SourcePoint ? $object->getCategory()->getTitle() : '';
    }

}
