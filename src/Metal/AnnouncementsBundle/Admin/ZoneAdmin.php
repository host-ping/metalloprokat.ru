<?php

namespace Metal\AnnouncementsBundle\Admin;

use Doctrine\ORM\EntityManager;
use Metal\AnnouncementsBundle\Entity\ValueObject\SectionProvider;
use Knp\Menu\ItemInterface as MenuItemInterface;
use Metal\AnnouncementsBundle\Entity\Zone;
use Metal\AnnouncementsBundle\Repository\ZoneStatusRepository;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;

class ZoneAdmin extends AbstractAdmin
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var ZoneStatusRepository
     */
    private $zoneStatusRepository;

    public function __construct($code, $class, $baseControllerName, EntityManager $em)
    {
        parent::__construct($code, $class, $baseControllerName);

        $this->em = $em;
        $this->zoneStatusRepository = $em->getRepository('MetalAnnouncementsBundle:ZoneStatus');
    }

    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->remove('delete');
        $collection->remove('create');
        parent::configureRoutes($collection);
    }

    protected function configureTabMenu(MenuItemInterface $menu, $action, AdminInterface $childAdmin = null)
    {
        if (null !== $childAdmin) {
            return;
        }

        if (!in_array($action, array('show', 'edit'))) {
            return;
        }

        $menu->addChild('Доступность баннеров', array('uri' => $this->getChild('metal.announcements.admin.zone_status')->generateUrl('list')));
      }

    protected function configureFormFields(FormMapper $form)
    {
        $form
            ->add('sectionId', 'choice', array('label' => 'Раздел', 'choices' => SectionProvider::getAllTypesAsSimpleArray(), 'disabled' => true))
            ->add('title', null, array('label' => 'Название зоны'))
            ->add('number', 'integer', array('label' => 'Номер', 'attr' => array('style' => 'width: 100px;')))
            ->add('slug', null, array('label' => 'Слаг', 'required' => true, 'read_only' => true))
            ->add('width', 'text', array('label' => 'Ширина', 'attr' => array('style' => 'width: 100px;')))
            ->add('height', 'text', array('label' => 'Высота', 'attr' => array('style' => 'width: 100px;')))
            ->add('cost', 'text', array('label' => 'Стоимость', 'attr' => array('style' => 'width: 100px;')))
            ->add('priority', null, array('label' => 'Приоритет', 'attr' => array('style' => 'width: 100px;')))
            ->add('isHidden', null, array('label' => 'Скрыта на корпсайте'))
            ->add('alwaysAvailable', null, array('label' => 'Всегда в наличии', 'required' => false))
        ;
    }

    protected function configureListFields(ListMapper $list)
    {
        $list
            ->addIdentifier('id')
            ->add('title', null, array('label' => 'Название зоны'))
            ->add('number', null, array('label' => 'Номер'))
            ->add('slug', null, array('label' => 'Слаг'))
            ->add('width', null, array('label' => 'Ширина'))
            ->add('height', null, array('label' => 'Высота'))
            ->add('cost', null, array('label' => 'Стоимость'))
            ->add('priority', null, array('label' => 'Приоритет'))
            ->add('alwaysAvailable', null, array('label' => 'Всегда в наличии'))
            ->add('isHidden', null, array('label' => 'Скрыта на корпсайте'))
            ->add('sectionId', 'choice', array('label' => 'Раздел', 'choices' => SectionProvider::getAllTypesAsSimpleArray()))
            ->add(
                'status',
                null,
                array(
                    'label' => 'Статус',
                    'template' => 'MetalAnnouncementsBundle:AdminZone:zone_status.html.twig'
                )
            )
        ;
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add(
                'sectionId',
                'doctrine_orm_choice',
                array(
                    'label' => 'Раздел'
                ),
                'choice',
                array(
                    'required' => false,
                    'choices' => SectionProvider::getAllTypesAsSimpleArray()
                )
            )
            ->add('alwaysAvailable', null, array('label' => 'Всегда в наличии'))
            ->add('isHidden', null, array('label' => 'Скрытые на корпсайте'))
        ;
    }

    public function getDatagrid()
    {
        if ($this->datagrid) {
            return $this->datagrid;
        }

        $datagrid = parent::getDatagrid();
        $zones = $datagrid->getResults();
        $this->zoneStatusRepository->attachZoneStatusesToZones($zones);

        return $this->datagrid;
    }

    public function toString($object)
    {
        return $object instanceof Zone ? $object->getTitle() : '';
    }
}
