<?php

namespace Metal\CategoriesBundle\Admin;

use Doctrine\ORM\EntityManager;
use Metal\CategoriesBundle\Entity\ParameterGroup;
use Metal\CategoriesBundle\Entity\ParameterOption;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;

class ParameterOptionAdmin extends AbstractAdmin
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

    public function configureRoutes(RouteCollection $collection)
    {
        $collection->remove('delete');
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id')
            ->add('title', null, array('label' => 'Название'))
            ->add('titleAccusative', null, array('label' => 'Винительный падеж'))
            ->add('keyword', null, array('label' => 'Кейворд'))
            ->add('pattern', null, array('label' => 'Паттерн'))
            ->add('slug', null, array('label' => 'Слаг'))
            ->add(
                'typeId',
                'choice',
                array(
                    'label' => 'Тип группы',
                    'choices' => array(
                        ParameterGroup::PARAMETER_MARKA => 'Марка',
                        ParameterGroup::PARAMETER_GOST => 'Гост',
                        ParameterGroup::PARAMETER_RAZMER => 'Размер',
                        ParameterGroup::PARAMETER_KLASS => 'Класс',
                        ParameterGroup::PARAMETER_TIP => 'Тип',
                        ParameterGroup::PARAMETER_VID => 'Вид',
                        ParameterGroup::PARAMETER_CONDITION => 'Состояние',
                        ParameterGroup::PARAMETER_PURPOSE => 'Назначение',
                        ParameterGroup::PARAMETER_SIDETYPE => 'Тип стенки',
                        ParameterGroup::PARAMETER_SHINETYPE => 'Блеск',
                        ParameterGroup::PARAMETER_MATERIAL => 'Материал',
                        ParameterGroup::PARAMETER_COVERTYPE => 'Тип покрытия',
                        ParameterGroup::PARAMETER_IZOLACIA => 'Изоляция'
                    )
                )
            );
    }

    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('title', null, array('label' => 'Название', 'required' => true))
            ->add('keyword', null, array('label' => 'Кейворд', 'required' => true))
            ->add('pattern', null, array('label' => 'Паттерн', 'required' => true))
            ->add('slug', null, array('label' => 'Слаг', 'required' => true))
            ->add(
                'typeId',
                'choice',
                array(
                    'label' => 'Тип группы',
                    'required' => true,
                    'choices' => array(
                        ParameterGroup::PARAMETER_MARKA => 'Марка',
                        ParameterGroup::PARAMETER_GOST => 'Гост',
                        ParameterGroup::PARAMETER_RAZMER => 'Размер',
                        ParameterGroup::PARAMETER_KLASS => 'Класс',
                        ParameterGroup::PARAMETER_TIP => 'Тип',
                        ParameterGroup::PARAMETER_VID => 'Вид',
                        ParameterGroup::PARAMETER_CONDITION => 'Состояние',
                        ParameterGroup::PARAMETER_PURPOSE => 'Назначение',
                        ParameterGroup::PARAMETER_SIDETYPE => 'Тип стенки',
                        ParameterGroup::PARAMETER_SHINETYPE => 'Блеск',
                        ParameterGroup::PARAMETER_MATERIAL => 'Материал',
                        ParameterGroup::PARAMETER_COVERTYPE => 'Тип покрытия',
                        ParameterGroup::PARAMETER_IZOLACIA => 'Изоляция'
                    )
                )
            );
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $categories = $this->em->getRepository('MetalCategoriesBundle:Category')->getCategoriesAsSimpleArray(true);

        $datagridMapper
            ->add('slug', null, array('label' => 'Слаг'))
            ->add(
                'typeId',
                'doctrine_orm_callback',
                array(
                    'label' => 'Принадлежность к группе',
                    'callback' => function ($queryBuilder, $alias, $field, $value) {
                        if (!isset($value['value'])) {
                            return;
                        }
                        $queryBuilder->andWhere(sprintf("%s.typeId = %s", $alias, $value['value']));


                        return true;
                    }
                ),
                'choice',
                array(
                    'choices' => array(
                        ParameterGroup::PARAMETER_MARKA => 'Марка',
                        ParameterGroup::PARAMETER_GOST => 'Гост',
                        ParameterGroup::PARAMETER_RAZMER => 'Размер',
                        ParameterGroup::PARAMETER_KLASS => 'Класс',
                        ParameterGroup::PARAMETER_TIP => 'Тип',
                        ParameterGroup::PARAMETER_VID => 'Вид',
                        ParameterGroup::PARAMETER_CONDITION => 'Состояние',
                        ParameterGroup::PARAMETER_PURPOSE => 'Назначение',
                        ParameterGroup::PARAMETER_SIDETYPE => 'Тип стенки',
                        ParameterGroup::PARAMETER_SHINETYPE => 'Блеск',
                        ParameterGroup::PARAMETER_MATERIAL => 'Материал',
                        ParameterGroup::PARAMETER_COVERTYPE => 'Тип покрытия',
                        ParameterGroup::PARAMETER_IZOLACIA => 'Изоляция'
                    )
                )
            )
            ->add(
                'category',
                'doctrine_orm_callback',
                array(
                    'label' => 'Категории',
                    'callback' => function ($queryBuilder, $alias, $field, $value) {
                        if (!isset($value['value'])) {
                            return;
                        }
                        $queryBuilder->join(
                            'MetalCategoriesBundle:Parameter',
                            'p',
                            'WITH',
                            'p.parameterOption = '.sprintf('%s.id', $alias)
                        );
                        $queryBuilder->join(
                            'MetalCategoriesBundle:ParameterGroup',
                            'pg',
                            'WITH',
                            'p.parameterGroup= pg.id'
                        );
                        $queryBuilder->andWhere(sprintf('pg.category = %s', $value['value']));

                        return true;
                    }
                ),
                'choice',
                array(
                    'choices' => $categories
                )
            );
    }

    public function toString($object)
    {
        return $object instanceof ParameterOption ? $object->getTitle() : '';
    }
}
