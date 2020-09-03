<?php

namespace Metal\CompaniesBundle\Admin;

use Metal\CompaniesBundle\Entity\CompanyPhone;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Form\FormMapper;

class CompanyPhoneAdmin extends AbstractAdmin
{
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('phone', null, array('label' => 'Телефон'))
            ->add('additionalCode', null, array('label' => 'Добавочный', 'required' => false));
    }

    public function toString($object)
    {
        return $object instanceof CompanyPhone ? $object->getPhone() : '';
    }
}
