<?php

namespace Metal\ServicesBundle\Admin;

use Metal\ServicesBundle\Entity\Payment;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Symfony\Component\Form\FormInterface;

class PaymentAdmin extends AbstractAdmin
{
    protected $datagridValues = array(
        '_sort_order' => 'DESC',
        '_sort_by' => 'id',
    );

    public function getNewInstance()
    {
        $instance = parent::getNewInstance();
        $instance->setCreatedInCrm(new \DateTime());

        return $instance;
    }

    protected function configureRoutes(RouteCollection $collection)
    {
        $collection
            ->remove('delete');
    }

    protected function configureFormFields(FormMapper $formMapper)
    {
        $subject = $this->getSubject();
        /* @var $subject Payment */

        $formMapper
            ->add(
                'company',
                'entity_id',
                array(
                    'label' => 'ID Компании',
                    'class' => 'MetalCompaniesBundle:Company',
                    'hidden' => false,
                    'required' => false,
                )
            )
            ->add('orderNO', 'text', array('label' => 'Номер счета из CRM'))
            ->add('pSum', 'text', array('label' => 'Сумма'))
            ->add('servName', 'textarea', array('label' => 'Описание'))
            ->add(
                'createdInCrm',
                'sonata_type_date_picker',
                array('label' => 'Дата выставления', 'format' => 'dd.MM.yyyy')
            );

        if (!$subject->isPayed()) {
            $formMapper
                ->add('deleted', 'checkbox', array('label' => 'Удален', 'required' => false));
        }
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id')
            ->add('servName', null, array('label' => 'Описание'))
            ->add('orderNO', null, array('label' => 'Номер счета из CRM'))
            ->add(
                'company',
                null,
                array('label' => 'Компания', 'template' => 'MetalProjectBundle:Admin:company_with_id_list.html.twig')
            )
            ->add('pSum', null, array('label' => 'Сумма'))
            ->add('isPayed', 'boolean', array('label' => 'Оплачено'))
            ->add('createdInCrm', null, array('label' => 'Дата выставления'))
            ->add('payedAt', 'date', array('label' => 'Дата оплаты'))
            ->add('deleted', 'boolean', array('label' => 'Удален'));
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add(
                'status',
                'doctrine_orm_choice',
                array(
                    'label' => 'Оплачено'
                ),
                'choice',
                array(
                    'choices' => array(
                        1 => 'Да',
                        0 => 'Нет'
                    )
                )
            )
            ->add(
                'deleted',
                'doctrine_orm_boolean',
                array(
                    'label' => 'Удален'
                ),
                'sonata_type_boolean'
            )

        ;
    }

    public function configure()
    {
        $this->setTemplate('edit', '@MetalServices/Admin/edit_payment.html.twig');
    }

    public function getFormBuilder()
    {
        $this->formOptions['validation_groups'] = function(FormInterface $form) {
            $deleted = $form->get('deleted')->getData();

            // для удаленной заявки устанавливаем несуществующую группу что бы валидация не выполнялась
            return $deleted ? array('dummy') : array('Default', 'admin_panel');
        };

        return parent::getFormBuilder();
    }

    public function toString($object)
    {
        return $object instanceof Payment ? sprintf('Счет № %d', $object->getId()) : '';
    }
}
