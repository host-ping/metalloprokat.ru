<?php

namespace Metal\ServicesBundle\Admin;

use Metal\ServicesBundle\Entity\ServiceItem;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class ServiceItemAdmin extends AbstractAdmin
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

    protected function configureFormFields(FormMapper $formMapper)
    {
        $subject = $this->getSubject();
        $id = $subject->getId();

        $formMapper
            ->add('title', 'text', array('label' => 'Заголовок'))
            ->add('keyword', null, array('label' => 'Сокращение'))
            ->add('type1', 'checkbox', array('label' => 'Доступно в Базовом пакете', 'required' => false))
            ->add('type1Val', 'text', array('label' => 'Текст для БП', 'required' => false))
            ->add('type4', 'checkbox', array('label' => 'Доступно в Стандартном пакете', 'required' => false))
            ->add('type4Val', 'text', array('label' => 'Текст для CП', 'required' => false))
            ->add('type2', 'checkbox', array('label' => 'Доступно в Расширенном пакете', 'required' => false))
            ->add('type2Val', 'text', array('label' => 'Текст для РП', 'required' => false))
            ->add('type3', 'checkbox', array('label' => 'Достуно в Полном пакете', 'required' => false))
            ->add('type3Val', 'text', array('label' => 'Текст для ПП', 'required' => false))
            ->add('additionalPayment', 'checkbox', array('label' => 'Оплачивается дополнительно', 'required' => false))
            ->add('priority', 'text', array('label' => 'Очередность', 'required' => true))
            ->add('description', null, array('label' => 'Описание'))
            ->add(
                'parent',
                null,
                array(
                    'label' => 'Родитель',
                    'required' => false,
                    'placeholder' => 'Без родителя',
                    'query_builder' => function ($er) use ($id) {
                        $qb = $er->createQueryBuilder('i')
                            ->andWhere('(i.parent = 0 OR i.parent IS NULL)');

                        if ($id) {
                            $qb->andWhere('i.id <> :id')->setParameter('id', $id);
                        }

                        return $qb;
                    },
                )
            );
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id')
            ->add('title', null, array('label' => 'Заголовок'))
            ->add('type1', 'boolean', array('label' => 'Базовый пакет'))
            ->add('type4', 'boolean', array('label' => 'Стартовый пакет'))
            ->add('type2', 'boolean', array('label' => 'Расширенный пакет'))
            ->add('type3', 'boolean', array('label' => 'Полный пакет'))
            ->add('additionalPayment', 'boolean', array('label' => 'Оплачивается дополнительно'))
            ->add('description', null, array('label' => 'Описание'))
            ->add('createdAt', null, array('label' => 'Дата создания'))
            ->add('updatedAt', null, array('label' => 'Дата изменения'))
            ->add('parent', null, array('label' => 'Родитель'));
    }

    public function prePersist($object)
    {
        /* @var $object ServiceItem */
        $object->setUser($this->tokenStorage->getToken()->getUser());
        $object->setUpdatedAt(new \DateTime());
    }

    public function preUpdate($object)
    {
        /* @var $object ServiceItem */
        $object->setUpdatedAt(new \DateTime());
    }

    public function toString($object)
    {
        return $object instanceof ServiceItem ? $object->getTitle() : '';
    }
}
