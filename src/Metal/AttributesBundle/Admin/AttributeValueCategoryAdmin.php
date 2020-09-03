<?php

namespace Metal\AttributesBundle\Admin;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;

class AttributeValueCategoryAdmin extends AbstractAdmin
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

    public function postUpdate($object)
    {
        $this->em->getRepository('MetalAttributesBundle:AttributeCategory')->refreshAttributeCategory();
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id')
            ->add('attributeValue.value', null, array('label' => 'Значение'))
            ->add('attributeValue.slug', null, array('label' => 'Слаг'))
            ->add('category', null, array('label' => 'Категория', 'associated_property' => 'titleWithParent'))
            ->add('attributeValue.attribute', null, array('label' => 'Атрибут', 'associated_property' => 'title'))
            ->add('matchingPriority', null, array('label' => 'Приоритет соответствия'));
    }

    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add(
                'attributeValue.attribute',
                'entity',
                array(
                    'class' => 'MetalAttributesBundle:Attribute',
                    'label' => 'Тип атрибута',
                    'required' => true,
                    'property' => 'title',
                )
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
            ->add('regexMatch', 'textarea', array('label' => 'Регулярка на совпадения', 'required' => false))
            ->add('regexExclude', 'textarea', array('label' => 'Регулярка на исключение', 'required' => false))
            ->add('matchingPriority', null, array('label' => 'Приоритет соответствия'));
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
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
                    'choices' => $this->em->getRepository('MetalCategoriesBundle:Category')->buildCategoriesByLevels(),
                )
            )
            ->add(
                'attributeValue.attribute',
                null,
                array(
                    'label' => 'Атрибут',
                ),
                null,
                array(
                    'property' => 'title',
                    'query_builder' => function (EntityRepository $repository) {
                        return $repository->createQueryBuilder('a')
                            ->orderBy('a.title');
                    },
                )
            )
            ->add(
                'attributeValue.value',
                null,
                array(
                    'label' => 'Значение атрибута',
                )
            );
    }
}
