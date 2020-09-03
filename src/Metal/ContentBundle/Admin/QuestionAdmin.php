<?php

namespace Metal\ContentBundle\Admin;

use Brouzie\Sphinxy\IndexManager;
use Doctrine\ORM\EntityManager;
use Metal\ContentBundle\Entity\Question;
use Metal\ContentBundle\Entity\ValueObject\StatusTypeProvider;
use Metal\ContentBundle\Entity\ValueObject\SubjectTypeProvider;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;

class QuestionAdmin extends AbstractAdmin
{
    protected $datagridValues = array(
        '_sort_order' => 'DESC',
        '_sort_by' => 'id',
    );

    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var IndexManager
     */
    private $indexManager;

    public function __construct($code, $class, $baseControllerName, EntityManager $em, IndexManager $indexManager)
    {
        parent::__construct($code, $class, $baseControllerName);

        $this->em = $em;
        $this->indexManager = $indexManager;
    }

    public function toString($object)
    {
        return $object instanceof Question ? sprintf('Вопрос %d', $object->getId()) : '';
    }

    protected function configureRoutes(RouteCollection $collection)
    {
        $collection
            ->remove('delete');
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id')
            ->add('title', null, array('label' => 'Заголовок'))
            ->add('createdAt', null, array('label' => 'Дата добавления'))
            ->add(
                'statusTypeId',
                'choice',
                array(
                    'label' => 'Статус модерации',
                    'choices' => StatusTypeProvider::getAllTypesAsSimpleArray(),
                )
            )
            ->add(
                'branchTitle',
                null,
                array('label' => 'Полный путь')
            )
            ->add(
                'subjectTypeId',
                'choice',
                array(
                    'label' => 'Раздел',
                    'choices' => SubjectTypeProvider::getAllTypesAsSimpleArray(),
                )
            )
            ->add(
                'user',
                null,
                array(
                    'label' => 'Пользователь',
                    'template' => 'MetalContentBundle:Admin:user.html.twig',
                )
            );
    }

    protected function configureFormFields(FormMapper $formMapper)
    {
        $categories = $this->em->getRepository('MetalContentBundle:Category')->buildCategoriesByLevels();
        $formMapper
            ->add('title', null, array('label' => 'Заголовок'))
            ->add('pageTitle', null, array('label' => 'Мета заголовок'))
            ->add(
                'user',
                'entity_id',
                array( //FIXME: сделать простое поля для пользователя
                    'label' => 'ID пользователя',
                    'class' => 'MetalUsersBundle:User',
                    'required' => false,
                    'hidden' => false,
                )
            )
            ->add(
                'category',
                'entity',
                array(
                    'label' => 'Категория',
                    'required' => false,
                    'class' => 'MetalContentBundle:Category',
                    'property' => 'nestedTitle',
                    'choices' => $categories,
                )
            )
            ->add(
                'categorySecondary',
                'entity',
                array(
                    'label' => 'Дополнительная категория',
                    'required' => false,
                    'class' => 'MetalContentBundle:Category',
                    'property' => 'nestedTitle',
                    'choices' => $categories,
                )
            )
            ->add(
                'subjectTypeId',
                'choice',
                array(
                    'label' => 'Раздел',
                    'placeholder' => '',
                    'choices' => SubjectTypeProvider::getAllTypesAsSimpleArray(),
                )
            )
            ->add(
                'description',
                null,
                array(
                    'label' => 'Описание',
                    'attr' => array(
                        'data' => 'editable',
                    ),
                )
            )
            ->add(
                'shortDescription',
                null,
                array(
                    'label' => 'Краткое описание',
                    'attr' => array(
                        'data' => 'editable',
                    ),
                )
            )
            ->add(
                'statusTypeId',
                'choice',
                array(
                    'label' => 'Статус модерации',
                    'choices' => StatusTypeProvider::getAllTypesAsSimpleArray(),
                )
            )
            ->add(
                'tags',
                'entity',
                array(
                    'label' => 'Теги',
                    'placeholder' => '',
                    'property' => 'title',
                    'multiple' => true,
                    'required' => false,
                    'class' => 'MetalContentBundle:Tag',
                    'choices' => $this->em->getRepository('MetalContentBundle:Tag')->findAll()
                )
            )
        ;
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id', null, array('label' => 'ID'))
            ->add('title', null, array('label' => 'Заголовок'))
            ->add(
                'statusTypeId',
                'doctrine_orm_choice',
                array('label' => 'Статус модерации'),
                'choice',
                array(
                    'choices' => StatusTypeProvider::getAllTypesAsSimpleArray(),
                )
            )
            ->add(
                'subjectTypeId',
                'doctrine_orm_choice',
                array('label' => 'Раздел'),
                'choice',
                array(
                    'choices' => SubjectTypeProvider::getAllTypesAsSimpleArray(),
                )
            )
            ->add(
                'category',
                'doctrine_orm_choice',
                array(
                    'label' => 'Категория',
                ),
                'entity',
                array(
                    'class' => 'MetalContentBundle:Category',
                    'property' => 'nestedTitle',
                    'choices' => $this->em->getRepository('MetalContentBundle:Category')->buildCategoriesByLevels(),
                )

            );
    }

    public function postUpdate($object)
    {
        /* @var $object Question */
        if ($object->getStatusTypeId() === StatusTypeProvider::CHECKED) {
            $this->indexManager->reindexItems('content_entry', array($object->getId()));
        } else {
            $this->indexManager->removeItems('content_entry', array($object->getId()));
        }

    }

    public function postPersist($object)
    {
        /* @var $object Question */
        if ($object->getStatusTypeId() === StatusTypeProvider::CHECKED) {
            $this->indexManager->reindexItems('content_entry', array($object->getId()));
        }
    }
}
