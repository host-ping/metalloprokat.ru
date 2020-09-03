<?php

namespace Metal\AnnouncementsBundle\Admin;

use Doctrine\ORM\EntityRepository;
use Metal\AnnouncementsBundle\Entity\ZoneStatus;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\CoreBundle\Form\Type\BooleanType;

class ZoneStatusAdmin extends AbstractAdmin
{
    protected $parentAssociationMapping = 'zone';

    protected $datagridValues = array(
        '_sort_order' => 'DESC',
        '_sort_by' => 'startsAt',
        'deleted' => array(
            'value' => BooleanType::TYPE_NO,
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

    public function createQuery($context = 'list')
    {
        $query = parent::createQuery($context);
        $alias = $query->getRootAliases();
        $alias = reset($alias);
        $query->leftJoin(sprintf('%s.company', $alias), 'c')->addSelect('c');
        $query->leftJoin(sprintf('%s.zone', $alias), 'z')->addSelect('z');

        return $query;
    }

    protected function configureFormFields(FormMapper $form)
    {
        $subject = $this->getSubject();
        /* @var $subject ZoneStatus */
        $disabled =$subject->getAnnouncement() ? true : false;

        $form
            ->add('zone', 'entity', array(
                'class' => 'MetalAnnouncementsBundle:Zone',
                'property' => 'titleAndSize',
                'label' => 'Зона',
                'placeholder' => '',
                'disabled' => $disabled,
                'query_builder' => function (EntityRepository $repository) {
                    return $repository->createQueryBuilder('zone')
                        ->orderBy('zone.title', 'ASC');
                },
                'required' => true,
                'group_by' => 'section.title'

            ))
            ->add('company', 'entity_id', array(
                'class' => 'MetalCompaniesBundle:Company',
                'label' => 'ID Компании',
                'hidden' => false,
                'required' => true,
                'disabled' => $disabled,
            ))
            ->add('startsAt', 'sonata_type_date_picker', array('label' => 'Дата старта', 'format' => 'dd.MM.yyyy', 'disabled' => $disabled))
            ->add('endsAt', 'sonata_type_date_picker', array('label' => 'Дата окончания', 'format' => 'dd.MM.yyyy', 'disabled' => $disabled))
            ->add(
                'status',
                'choice',
                array(
                    'label' => 'Статус',
                    'placeholder' => '',
                    'required' => true,
                    'disabled' => $disabled,
                    'choices' => ZoneStatus::getStatuses(),
                    'attr' => array('style' => 'width: 200px;')
            ))
            ->add('comment', 'textarea', array('label' => 'Комментарии', 'required' => false))
            ->add('deleted', 'checkbox', array('label' => 'Удалена', 'required' => false))
        ;
    }

    protected function configureListFields(ListMapper $list)
    {
        $list
            ->addIdentifier('id')
            ->add('status', 'choice', array('label' => 'Статус', 'choices' => ZoneStatus::getStatuses()))
            ->add('startsAt', null, array('label' => 'Дата старта'))
            ->add('endsAt', null, array('label' => 'Дата окончания'))
            ->add('zone', null, array('label' => 'Зона', 'template' => 'MetalAnnouncementsBundle:AdminZone:section_type_list.html.twig'))
            ->add('company', null, array('label' => 'Компания', 'template' => 'MetalProjectBundle:Admin:company_with_id_list.html.twig'))
            ->add('deleted', null, array('label' => 'Удалена'))
        ;
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
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
            ->add(
                'is_active',
                'doctrine_orm_callback',
                array(
                    'label' => 'Время действия',
                    'callback' => function ($queryBuilder, $alias, $field, $value) {
                        if (!isset($value['value'])) {
                            return;
                        }

                        switch ($value['value']) {
                            case 'past': {
                                $queryBuilder->andWhere(sprintf("%s.endsAt < :now", $alias));
                                break;
                            }
                            case 'active': {
                                $queryBuilder->andWhere(sprintf(":now BETWEEN %s.startsAt AND %s.endsAt", $alias, $alias));
                                break;
                            }
                            case 'future': {
                                $queryBuilder->andWhere(sprintf("%s.startsAt > :now", $alias));

                                break;
                            }
                        }
                        $queryBuilder->setParameter('now', new \DateTime());

                        return true;
                    }
                ),
                'choice',
                array(
                    'choices' => array(
                        'past' => 'Отработвашие',
                        'active' => 'Действующие',
                        'future' => 'Будущие'
                    )
                )
            )
            ->add(
                'status',
                'doctrine_orm_choice',
                array(
                    'label' => 'Статус'
                ),
                'choice',
                array(
                    'required' => false,
                    'choices' => ZoneStatus::getStatuses()
                )
            )
            ->add(
                'deleted',
                null,
                array(
                    'label' => 'Удалена',
                )
            )
        ;
    }

    public function toString($object)
    {
        return $object instanceof ZoneStatus && $object->getCompany() ? $object->getCompany()->getTitle() : '';
    }
}
