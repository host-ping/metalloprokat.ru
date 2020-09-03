<?php

namespace Metal\CompaniesBundle\Admin;

use Metal\CompaniesBundle\Entity\CompanyRegistration;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Route\RouteCollection;

class CompanyRegistrationAdmin extends AbstractAdmin
{
    protected $datagridValues = array(
        '_sort_order' => 'DESC',
        '_sort_by' => 'createdAt',
    );

    protected function configureRoutes(RouteCollection $collection)
    {
        $collection
            ->add(
                'download_file',
                $this->getRouterIdParameter().'/download_file',
                array('_controller' => 'MetalCompaniesBundle:CompanyRegistrationAdmin:downloadFile')
            )
            ->remove('delete')
            ->remove('create')
            ->remove('show')
            ->remove('edit');
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id')
            ->add(
                'company',
                null,
                array(
                    'label' => 'Компания',
                    'template' => 'MetalProjectBundle:Admin:company_with_id_list.html.twig'
                )
            )
            ->add(
                'category',
                null,
                array(
                    'label' => 'Категория',
                    'associated_property' => 'titleWithParent'
                )
            )
            ->add(
                'uploadedPrice',
                null,
                array(
                    'label' => 'Прайс',
                    'template' => 'MetalCompaniesBundle:CompanyRegistrationAdmin:registration_price.html.twig'
                )
            )
            ->add(
                'updater',
                'choice',
                array(
                    'choices' => CompanyRegistration::getAvailablePromotions(),
                    'label' => 'Способ продвижения'
                )
            )
            ->add(
                'termPackage',
                'choice',
                array(
                    'choices' => CompanyRegistration::getSimpleTermPackage(),
                    'label' => 'Период'
                )
            )
            ->add('createdAt');
    }
}
