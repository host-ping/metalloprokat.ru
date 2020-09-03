<?php

namespace Metal\CatalogBundle\Admin;

use Doctrine\ORM\EntityManager;
use Metal\CatalogBundle\Entity\ProductReview;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class ProductReviewAdmin extends AbstractAdmin
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    public function __construct($code, $class, $baseControllerName, EntityManager $em, TokenStorageInterface $tokenStorage)
    {
        parent::__construct($code, $class, $baseControllerName);

        $this->em = $em;
        $this->tokenStorage = $tokenStorage;
    }

    protected $datagridValues = array(
        '_sort_order' => 'DESC',
        '_sort_by' => 'createdAt',
    );

    protected function configureRoutes(RouteCollection $routeCollection)
    {
        $routeCollection
            ->remove('create');
    }

    public function toString($object)
    {
        return $object instanceof ProductReview ? sprintf('Отзыв о продукте - %s', $object->getProduct()->getTitle()) : '';
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id', null, array('template' => 'MetalDemandsBundle:DemandAdmin:colorId.html.twig'))
            ->add('type', null, array('label' => 'Положительный отзыв'))
            ->add('comment', null, array('label' => 'Комментарий'))
            ->add('createdAt', null, array('label' => 'Создан'))
            ->add('user', null, array('label' => 'Автор', 'template' => 'MetalCatalogBundle:AdminProductReview:author.html.twig'))
            ->add('city', null, array('label' => 'Город', 'associated_property' => 'title'))
            ->add('product', null, array('label' => 'Продукт', 'associated_property' => 'title'))
        ;
    }

    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id', null, array('label' => 'ID'))
            ->add(
                'moderatedAt',
                'doctrine_orm_callback',
                array(
                    'label' => 'Модерация',
                    'callback' => function ($queryBuilder, $alias, $field, $value) {
                        if (!isset($value['value'])) {
                            return;
                        }
                        if ($value['value'] == 'y') {
                            $queryBuilder->andWhere(sprintf('%s.moderatedAt IS NOT NULL', $alias));
                        } else {
                            $queryBuilder->andWhere(sprintf('%s.moderatedAt IS NULL', $alias));
                        }

                        return true;
                    }
                ),
                'choice',
                array(
                    'choices' => array(
                        'y' => 'Промодерированный',
                        'n' => 'Не промодерированный'
                    )
                )
            )
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
                    'choices' => $this->em->getRepository('MetalCategoriesBundle:Category')->buildCategoriesByLevels()
                )
            )
            ->add(
                'deletedAt',
                'doctrine_orm_callback',
                array(
                    'label' => 'Удаленность',
                    'callback' => function ($queryBuilder, $alias, $field, $value) {
                        if (!isset($value['value'])) {
                            return;
                        }
                        if ($value['value'] == 'y') {
                            $queryBuilder->andWhere(sprintf('%s.deletedAt IS NOT NULL', $alias));
                        } else {
                            $queryBuilder->andWhere(sprintf('%s.deletedAt IS NULL', $alias));
                        }

                        return true;
                    }
                ),
                'choice',
                array(
                    'choices' => array(
                        'y' => 'Удален',
                        'n' => 'Не удален'
                    )
                )
            )
        ;
    }

    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('comment', null, array('label' => 'Комментарий'))
            ->add('moderated', 'checkbox', array('label' => 'Промодерированный', 'required' => false))
            ->add('deleted', 'checkbox', array('label' => 'Удаленный', 'required' => false))
        ;
    }

    public function preUpdate($object)
    {
        /* @var $object ProductReview */
        $user = $this->tokenStorage->getToken()->getUser();

        if ($object->getModeratedAt()) {
            $object->setModeratedAt(new \DateTime());
            $object->setModeratedBy($user);
        }

        if ($object->getDeletedAt()) {
            $object->setDeletedAt(new \DateTime());
            $object->setDeletedBy($user);
        }
    }
}
