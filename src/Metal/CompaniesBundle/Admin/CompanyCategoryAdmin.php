<?php

namespace Metal\CompaniesBundle\Admin;

use Metal\CategoriesBundle\Repository\CategoryRepository;
use Metal\CompaniesBundle\Entity\CompanyCategory;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Form\FormMapper;

class CompanyCategoryAdmin extends AbstractAdmin
{
    protected $datagridValues = array(
        '_sort_order' => 'ASC',
        '_sort_by' => 'category.title'
    );

    protected function configureFormFields(FormMapper $formMapper)
    {
        static $i = -1;
        $i++;

        $formMapper
            ->add('categoryTitle', 'text', array(
                'label' => 'Категория',
                'attr' => array(
                    'typeahead' => '',
                    'typeahead-prefetch-url' => $this->routeGenerator->generate('MetalCategoriesBundle:Suggest:getCategories'),
                    'typeahead-model' => "category$i",
                    'typeahead-suggestion-template-url' => "'typeahead-suggestion-with-parent'",
                )
            ))
            ->add(
                'category',
                'entity_id',
                array(
                    'label' => 'ID Категории',
                    'required' => false,
                    'hidden' => false,
                    'read_only' => true,
                    'class' => 'MetalCategoriesBundle:Category',
                    'query_builder' => function (CategoryRepository $repo, $id) {
                        return $repo
                            ->createQueryBuilder('c')
                            ->select('PARTIAL c.{id, title, branchIds}')
                            ->andWhere('c.id = :id')
                            ->setParameter('id', $id);
                    },
                    'attr' => array(
                        'ng-model' => "category$i.id",
                        'initial-value' => ''
                    ),
                )
            )
            ->add('isAutomaticallyAdded', null, array(
                'label' => 'Добавлена автоматически',
                'read_only' => true,
                'required' => false,
                'disabled' => true
            ))
            ->add(
                'displayPosition',
                null,
                array(
                    'label' => 'Порядок вывода на витрине',
                )
            )
            ->add('enabled', null, array(
                'label' => 'Включена',
                'required' => false,
            ))
        ;
    }

    public function toString($object)
    {
        return $object instanceof CompanyCategory ? $object->getCategoryTitle() : '';
    }
}
