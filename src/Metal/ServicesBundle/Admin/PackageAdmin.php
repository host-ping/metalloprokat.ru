<?php

namespace Metal\ServicesBundle\Admin;

use Metal\ServicesBundle\Entity\Package;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class PackageAdmin extends AbstractAdmin
{
    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    public function __construct($code, $class, $baseControllerName, TokenStorageInterface $tokenStorage)
    {
        parent::__construct($code, $class, $baseControllerName);

        $this->tokenStorage = $tokenStorage;
    }

    public function configureRoutes(RouteCollection $collection)
    {
        $collection->remove('delete');
    }

    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('title', 'text', array('label' => 'Заголовок'))
            ->add('priceForOneMonth', null, array('label' => 'На 1 мес'))
            ->add('priceForThreeMonth', null, array('label' => 'На квартал'))
            ->add('priceForSixMonth', null, array('label' => 'На полгода'))
            ->add('priceForTwelveMonth', null, array('label' => 'На год'))
            ->add('priority', 'text', array('label' => 'Очередность', 'required' => true))
            ->add(
                'checked',
                'choice',
                array(
                    'label' => 'Статус',
                    'choices' => array(
                        0 => 'Не отмечен',
                        1 => 'Отмечен'
                    )
                )
            );
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id')
            ->add('title', null, array('label' => 'Заголовок'))
            ->add('priceForOneMonth', null, array('label' => 'На месяц'))
            ->add('priceForThreeMonth', null, array('label' => 'На квартал'))
            ->add('priceForSixMonth', null, array('label' => 'На полгода'))
            ->add('priceForTwelveMonth', null, array('label' => 'На год'))
            ->add('priority', null, array('label' => 'Приоритет'))
            ->add('createdAt', null, array('label' => 'Дата создания'))
            ->add('updatedAt', null, array('label' => 'Дата изменения'))
        ;
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
//        $datagridMapper
//            ->add('title', null, array('label' => 'Заголовок'))
//        ;
    }

    public function toString($object)
    {
        return $object instanceof Package ? $object->getTitle() : '';
    }

    public function prePersist($object)
    {
        /* @var $object Package */
        $object->setUser($this->tokenStorage->getToken()->getUser());
    }
}
