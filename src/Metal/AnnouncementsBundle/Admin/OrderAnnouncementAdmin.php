<?php

namespace Metal\AnnouncementsBundle\Admin;

use Doctrine\ORM\EntityRepository;
use Metal\AnnouncementsBundle\Entity\OrderAnnouncement;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class OrderAnnouncementAdmin extends AbstractAdmin
{
    protected $datagridValues = array(
        '_sort_order' => 'DESC',
        '_sort_by' => 'createdAt',
        'processedBy' => array(
            'value' => 'n',
        ),
        '_page' => 1,
        '_per_page' => 25,
    );

    /**
     * The number of result to display in the list.
     *
     * @var int
     */
    protected $maxPerPage = 25;

    /**
     * Predefined per page options.
     *
     * @var array
     */
    protected $perPageOptions = array(15, 25, 50, 100, 150, 200);

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    public function __construct($code, $class, $baseControllerName, TokenStorageInterface $tokenStorage)
    {
        parent::__construct($code, $class, $baseControllerName);

        $this->tokenStorage = $tokenStorage;
    }

    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->remove('delete');
        $collection->remove('create');
        parent::configureRoutes($collection);
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('id', null, array('label' => 'ID'))
            ->add('phone', null, array('label' => 'Номер телефона'))
            ->add(
                'zone',
                null,
                array(
                    'class' => 'MetalAnnouncementsBundle:Zone',
                    'label' => 'Зона',
                    'query_builder' => function (EntityRepository $repository) {
                        return $repository->createQueryBuilder('zone')
                            ->orderBy('zone.title', 'ASC');
                    },
                ),
                null,
                array('property' => 'title', 'group_by' => 'section.title')
            )
            ->add('createAnnouncement', null, array('label' => 'Создание баннера'))
            ->add(
                'processedBy',
                'doctrine_orm_callback',
                array(
                    'label' => 'Обработанные',
                    'callback' => function ($queryBuilder, $alias, $field, $value) {
                        if (!isset($value['value'])) {
                            return;
                        }
                        if ($value['value'] === 'y') {
                            $queryBuilder->andWhere(sprintf('%s.processedBy IS NOT NULL', $alias));
                        } else {
                            $queryBuilder->andWhere(sprintf('%s.processedBy IS NULL', $alias));
                        }

                        return true;
                    }
                ),
                'choice',
                array(
                    'choices' => array(
                        'y' => 'Обработанные',
                        'n' => 'Не обработанные'
                    )
                )
            )
        ;
    }

    protected function configureFormFields(FormMapper $form)
    {
        $form
            ->add('name', null, array('label' => 'Имя'))
            ->add('user', 'entity_id',
                array(
                    'class' => 'MetalUsersBundle:User',
                    'label' => 'ID пользователя',
                    'hidden' => false,
                    'read_only' => true,
                    'required' => false,
                )
            )
            ->add('phone', null, array('label' => 'Номер телефона'))
            ->add('email', null, array('label' => 'Email'))
            ->add('zone', 'entity', array(
                'class' => 'MetalAnnouncementsBundle:Zone',
                'property' => 'title',
                'label' => 'Зона',
                'placeholder' => '',
                'query_builder' => function (EntityRepository $repository) {
                    return $repository->createQueryBuilder('zone')
                        ->orderBy('zone.title', 'ASC');
                },
                'required' => false,
                'group_by' => 'section.title'

            ))
            ->add('createAnnouncement', null, array('label' => 'Создание баннера'))
            ->add('startsAt', null, array('label' => 'Дата старта'))
            ->add('processed', 'checkbox', array('label' => 'Обработан', 'required' => false))
            ->add('comment', 'textarea', array('label' => 'Комментарии', 'required' => false))
        ;
    }

    protected function configureListFields(ListMapper $list)
    {
        $list
            ->addIdentifier('id')
            ->add('name', null, array('label' => 'Имя'))
            ->add('user', null, array('label' => 'Пользователь', 'associated_property' => 'fullName'))
            ->add('phone', null, array('label' => 'Номер телефона'))
            ->add('email', null, array('label' => 'Email'))
            ->add('zone', null, array('label' => 'Зона', 'associated_property' => 'title'))
            ->add('createAnnouncement', null, array('label' => 'Создание баннера'))
            ->add('startsAt', null, array('label' => 'Дата старта'))
            ->add('createdAt', null, array('label' => 'Дата создания'))
            ->add('processedAt', null, array('label' => 'Дата обработки'))
            ->add(
                'processedBy',
                null,
                array('label' => 'Кем обработан', 'template' => 'MetalAnnouncementsBundle:OrderAnnouncementAdmin:userInfo.html.twig')
            )
        ;
    }

    public function toString($object)
    {
        return $object instanceof OrderAnnouncement ? sprintf('Заявка на баннер %d', $object->getId()) : '';
    }

    public function preUpdate($object)
    {
        /* @var $object OrderAnnouncement */
        if ($object->isProcessed()) {
            if (!$object->getProcessedBy()) {
                $object->setProcessedBy($this->tokenStorage->getToken()->getUser());
            }
        } else {
            $object->setProcessed(false);
        }
    }
}
