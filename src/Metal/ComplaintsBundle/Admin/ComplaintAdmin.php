<?php

namespace Metal\ComplaintsBundle\Admin;

use Doctrine\ORM\EntityManager;
use Metal\ComplaintsBundle\Entity\AbstractComplaint;
use Metal\ComplaintsBundle\Entity\ValueObject\ComplaintTypeProvider;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;

class ComplaintAdmin extends AbstractAdmin
{
    protected $datagridValues = array(
        '_sort_order' => 'DESC',
        '_sort_by' => 'createdAt',
        'complaint_object_type' => array('value' => AbstractComplaint::DEMAND_TYPE),
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
     * @var EntityManager
     */
    private $em;

    public function __construct($code, $class, $baseControllerName, EntityManager $em)
    {
        parent::__construct($code, $class, $baseControllerName);

        $this->em = $em;
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id', null, array('label' => 'ID жалобы'))
            ->add(
                'processedAt',
                'doctrine_orm_callback',
                array(
                    'label' => 'Обработанные',
                    'callback' => function($queryBuilder, $alias, $field, $value) {
                        if (!isset($value['value'])) {
                            return null;
                        }

                        if ($value['value'] === 'y') {
                            $queryBuilder->andWhere(sprintf('%s.processedAt IS NOT NULL', $alias));
                        } else {
                            $queryBuilder->andWhere(sprintf('%s.processedAt IS NULL', $alias));
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
            ->add(
                'complaintTypeId',
                'doctrine_orm_choice',
                array(
                    'label' => 'Тип жалобы'
                ),
                'choice',
                array(
                    'required' => false,
                    'choices' => ComplaintTypeProvider::getAllTypesAsSimpleArrayWithoutKind()
                )
            )
            ->add(
                'complaint_object_type',
                'doctrine_orm_callback',
                array(
                    'label' => 'Жалоба на',
                    'callback' => function ($queryBuilder, $alias, $field, $value) {
                        if (!isset($value['value'])) {
                            return null;
                        }

                        if ($value['value'] == AbstractComplaint::PRODUCT_TYPE) {
                            $queryBuilder->andWhere(sprintf('%s INSTANCE OF Metal\ComplaintsBundle\Entity\ProductComplaint', $alias));
                        } elseif ($value['value'] == AbstractComplaint::DEMAND_TYPE) {
                            $queryBuilder->andWhere(sprintf('%s INSTANCE OF Metal\ComplaintsBundle\Entity\DemandComplaint', $alias));
                        } else {
                            $queryBuilder->andWhere(sprintf('%s INSTANCE OF Metal\ComplaintsBundle\Entity\CompanyComplaint', $alias));
                        }

                        return true;
                    }
                ),
                'choice',
                array(
                    'required' => false,
                    'choices' => array(
                        AbstractComplaint::PRODUCT_TYPE => 'Продукты',
                        AbstractComplaint::DEMAND_TYPE => 'Заявки',
                        AbstractComplaint::COMPANY_TYPE => 'Компании'
                    )
                )
            )
        ;
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id')
            ->add('complaintTypeTitle', null, array('label' => 'Тип жалобы'))
            ->add('author', null, array('label' => 'Имя пользователя', 'template' => 'MetalProjectBundle:Admin:author_topic_list.html.twig'))
            ->add('createdAt', null, array('label' => 'Дата создания'))
            ->add('processedAt', null, array('label' => 'Дата обработки'))
            ->add(
                'show',
                null,
                array(
                    'label' => 'Действия',
                    'template' => 'MetalComplaintsBundle:ComplaintAdmin:complaint.html.twig'
                )
            )
            ;
    }

    public function configure()
    {
        $this->setTemplate('show', 'MetalComplaintsBundle:ComplaintAdmin:show_complaint.html.twig');
    }

    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->remove('create');
        $collection->remove('delete');
        $collection->remove('edit');
        parent::configureRoutes($collection);

    }

    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('process', 'checkbox', array('label' => 'Решена', 'required' => false))
            ->add('reopen', 'checkbox', array('label' => 'Переоткрыть', 'required' => false))
        ;
    }

    protected function configureShowFields(ShowMapper $showMapper)
    {
        $complaint = $this->getSubject();
        /* @var $complaint AbstractComplaint */
        $showMapper
            ->add('complaintTypeTitle', null, array('label' => 'Тип жалобы'))
            ->add('body', null, array('label' => 'Жалоба'))
            ->add('userName', null, array('label' => 'Автор', 'template' => 'MetalComplaintsBundle:ComplaintAdmin:author_complaint_show.html.twig'))
            ->add('createdAt', null, array('label' => 'Дата создания'))
            ;
        if ($complaint->getViewedAt()) {
            $showMapper
                ->add('viewedAt', null, array('label' => 'Дата просмотра'))
                ->add('viewedByFullName', null, array('label' => 'Кем просмотрена', 'template' => 'MetalComplaintsBundle:ComplaintAdmin:viewed_by_complaint_show.html.twig'));
        }

        if ($complaint->isProcessed()) {
            $showMapper
                ->add('isProcessed', 'choice', array('label' => 'Решена', 'choices' => array('0' => 'Нет', '1' => 'Да')))
                ->add('processedAt', null, array('label' => 'Дата решения'))
            ;
        }

        if ($complaint->getObjectKind() == AbstractComplaint::DEMAND_TYPE && $user = $complaint->getDemand()->getUser()) {
            $this->em->getRepository('MetalUsersBundle:User')->attachComplaintDemandsIdsToUser($user, $complaint->getDemand()->getId());
            $this->em->getRepository('MetalUsersBundle:User')->attachIpsToUser($user);
        }
    }

    public function toString($object)
    {
        return $object instanceof AbstractComplaint ? sprintf('Жалоба %d', $object->getId()) : '';
    }
}
