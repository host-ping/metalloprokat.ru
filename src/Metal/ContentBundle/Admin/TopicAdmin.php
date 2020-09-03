<?php

namespace Metal\ContentBundle\Admin;

use Brouzie\Sphinxy\IndexManager;
use Doctrine\ORM\EntityManager;
use Metal\ContentBundle\Entity\Topic;
use Metal\ContentBundle\Entity\ValueObject\StatusTypeProvider;
use Metal\ContentBundle\Entity\ValueObject\SubjectTypeProvider;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Vich\UploaderBundle\Form\Type\VichImageType;

class TopicAdmin extends AbstractAdmin
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

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    public function __construct(
        $code,
        $class,
        $baseControllerName,
        EntityManager $em,
        IndexManager $indexManager,
        TokenStorageInterface $tokenStorage
    ) {
        parent::__construct($code, $class, $baseControllerName);

        $this->em = $em;
        $this->indexManager = $indexManager;
        $this->tokenStorage = $tokenStorage;
    }

    public function configure()
    {
        $this->classnameLabel = 'Content Topic';
    }

    public function prePersist($object)
    {
        /* @var $object Topic */
        $object->setUser($this->tokenStorage->getToken()->getUser());
    }

    public function postPersist($object)
    {
        /* @var $object Topic */
        if ($object->getStatusTypeId() === StatusTypeProvider::CHECKED) {
            $this->indexManager->reindexItems('content_entry', array($object->getId()));
        }
    }

    public function postUpdate($object)
    {
        /* @var $object Topic */
        if ($object->getStatusTypeId() === StatusTypeProvider::CHECKED) {
            $this->indexManager->reindexItems('content_entry', array($object->getId()));
        } else {
            $this->indexManager->removeItems('content_entry', array($object->getId()));
        }
    }

    public function toString($object)
    {
        return $object instanceof Topic ? sprintf('Топик %d', $object->getId()) : '';
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
            )
            ->add(
                'uploadedImage',
                null,
                array(
                    'label' => 'Фото',
                    'template' => '@MetalProject/Admin/display_image_in_list.html.twig',
                    'image_filter' => 'topic_image_sq40',
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
            ->add('uploadedImage',VichImageType::class,
                array(
                    'label' => 'Фото топика',
                    'required' => false,
                    'imagine_pattern' => 'topics_sq136_non_optim',
                    'download_uri' => false,
                )
            );
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
                    'property' => 'titleWithString',
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
