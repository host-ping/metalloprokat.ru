<?php

namespace Metal\AttributesBundle\Admin;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Metal\AttributesBundle\Entity\AttributeValue;
use Metal\CategoriesBundle\Entity\ParameterOption;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;

class AttributeValueAdmin extends AbstractAdmin
{
    /**
     * @var EntityManager
     */
    private $em;

    protected $parentAssociationMapping = 'attribute';

    public function __construct($code, $class, $baseControllerName, EntityManager $em)
    {
        parent::__construct($code, $class, $baseControllerName);

        $this->em = $em;
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id')
            ->add('value', null, array('label' => 'Название'))
            ->add('valueAccusative', null, array('label' => 'Винительный падеж'))
            ->add('attribute', null, array('label' => 'Атрибут', 'associated_property' => 'title'))
            ->add('slug', null, array('label' => 'Слаг'))
            ->add('urlPriority', null, array('label' => 'Приоритет в URL'))
            ->add('outputPriority', null, array('label' => 'Приоритет вывода'))
            ->add('regexMatch', null, array('label' => 'Регулярка на совпадения'))
            ->add('regexExclude', null, array('label' => 'Регулярка на исключение'))
            ->add('additionalInfo', null, array('label' => 'Добавочная информация'));
    }

    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('value', null, array('label' => 'Название'))
            ->add(
                'valueAccusative',
                null,
                array(
                    'label' => 'Винительный падеж',
                    'help' => 'Кого? Что? - заявка на арматуру композитную'
                )
            )
            ->add(
                'attribute',
                'entity',
                array(
                    'class' => 'MetalAttributesBundle:Attribute',
                    'label' => 'Тип атрибута',
                    'required' => true,
                    'property' => 'title',
                    'placeholder' => '',
                )
            )
            ->add('urlPriority', null, array('label' => 'Приоритет в URL'))
            ->add('outputPriority', null, array('label' => 'Приоритет вывода'))
            ->add('slug', null, array('label' => 'Слаг'))
            ->add('regexMatch', 'textarea', array('label' => 'Регулярка на совпадения', 'required' => false))
            ->add('regexExclude', 'textarea', array('label' => 'Регулярка на исключение', 'required' => false))
            ->add('additionalInfo', null, array('label' => 'Добавочная информация'));
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('value', null, array('label' => 'Название'))
            ->add('slug', null, array('label' => 'Слаг'))
            ->add(
                'attribute',
                null,
                array(
                    'label' => 'Атрибут'
                ),
                null,
                array(
                    'property' => 'title',
                    'query_builder' => function (EntityRepository $repository) {
                        return $repository->createQueryBuilder('a')
                            ->orderBy('a.title');
                    },
                )
            );
    }

    public function prePersist($object)
    {
        /* @var $object AttributeValue */
        $parameterOption = new ParameterOption();
        $type = $this->em->getRepository('MetalCategoriesBundle:ParameterTypes')->findOneBy(array('id' => $object->getAttribute()->getId()));
        $parameterOption->setTitleAccusative($object->getValueAccusative());
        $parameterOption->setTitle($object->getValue());
        $parameterOption->setSlug($object->getSlug());
        $parameterOption->setKeyword($object->getSlug());
        $parameterOption->setType($type);

        $this->em->persist($parameterOption);
    }

    public function preUpdate($object)
    {
        /* @var $object AttributeValue */
        $parameterOption = $this->em->getRepository('MetalCategoriesBundle:ParameterOption')->findOneBy(array('id' => $object->getId()));
        if ($object->getValueAccusative()) {
            $parameterOption->setTitleAccusative($object->getValueAccusative());
        }
    }

    public function toString($object)
    {
        return $object instanceof AttributeValue ? $object->getValue() : '';
    }
}
