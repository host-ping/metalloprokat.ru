<?php

namespace Metal\ContentBundle\Admin;

use Doctrine\ORM\EntityManager;
use Metal\ContentBundle\Entity\Category;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;

class CategoryAdmin extends AbstractAdmin
{
    /**
     * @var EntityManager
     */
    private $em;

    public function __construct($code, $class, $baseControllerName, EntityManager $em)
    {
        $this->em = $em;

        parent::__construct($code, $class, $baseControllerName);
    }

    public function configureRoutes(RouteCollection $collection)
    {
        $collection
            ->remove('delete');
    }

    public function configure()
    {
        $this->classnameLabel = 'Content category';
    }

    public function toString($object)
    {
        return $object instanceof Category ? $object->getTitle() : '';
    }

    public function configureFormFields(FormMapper $form)
    {
        $subject = $this->getSubject();
        $id = $subject->getId();

        $form
            ->tab('Основное')
                ->add('title', null, array('label' => 'Заголовок'))
                ->add(
                    'parent',
                    'entity',
                    array(
                        'label' => 'Родитель',
                        'required' => false,
                        'class' => 'MetalContentBundle:Category',
                        'property' => 'nestedTitle',
                        'placeholder' => 'Без родителя',
                        'choices' => $this->em->getRepository('MetalContentBundle:Category')->buildCategoriesByLevels($id),
                    )
                )
                ->add('slug', null, array('label' => 'Slug'))
                ->add('titleGenitive', null, array('label' => 'Родительный падеж', 'help' => 'Кого? Чего? - квартиры и таунхауса'))
                ->add('titleAccusative', null, array('label' => 'Винительный падеж', 'help' => 'Кого? Что? - квартиру и таунхаус'))
                ->add('titlePrepositional', null, array('label' => 'Предложнный падеж', 'help' => 'О ком? О чем? - квартире и таунхаусе'))
                ->add('titleAblative', null, array('label' => 'Творительный падеж', 'help' => 'Кем? Чем? - квартирой и таунхаусом'))
                ->add('isEnabled', 'checkbox', array('label' => 'Включена', 'required' => false))
                ->add('priority', null, array('label' => 'Приоритет'))
            ->end()
            ->end()
            ->tab('Seo')
                ->add('_metadataTitle', 'textarea', array('label' => 'Meta заголовок', 'required' => false, 'property_path' => 'metadata.title'))
                ->add('_metadataDescription', 'textarea', array('label' => 'Meta описание', 'required' => false, 'property_path' => 'metadata.description'))
                ->add('_metadataKeywords', 'textarea', array('label' => 'Ключевые слова', 'required' => false, 'property_path' => 'metadata.keywords'))
            ->end()
            ->end()
        ;
    }

    public function configureListFields(ListMapper $list)
    {
        $list
            ->addIdentifier('id')
            ->add('title', null, array('label' => 'Заголовок'))
            ->add('slug', null, array('label' => 'Slug'))
            ->add('parent', null, array('label' => 'Родительская категория', 'associated_property' => 'title'))
            ->add('priority', null, array('label' => 'Приоритет вывода'))
            ->add('createdAt', null, array('label' => 'Дата создания'));
    }

    public function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id', null, array('label' => 'ID'))
            ->add('title', null, array('label' => 'Заголовок'))
            ->add('slug', null, array('label' => 'Slug'))
            ->add(
                'parent',
                null,
                array(
                    'label' => 'Родитель',
                    'required' => false,
                ),
                'entity',
                array(
                    'class' => 'MetalContentBundle:Category',
                    'property' => 'nestedTitle',
                    'choices' => $this->em->getRepository('MetalContentBundle:Category')->buildCategoriesByLevels(),
                )
            );
    }
}
