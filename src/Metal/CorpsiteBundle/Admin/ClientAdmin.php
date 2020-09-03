<?php

namespace Metal\CorpsiteBundle\Admin;

use Metal\CorpsiteBundle\Entity\Client;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Route\RouteCollection;

class ClientAdmin extends AbstractAdmin
{
    protected $fileUploadPath;

    public function __construct($code, $class, $baseControllerName, $path)
    {
        parent::__construct($code, $class, $baseControllerName);

        $this->fileUploadPath = $path;
    }

    public function configureRoutes(RouteCollection $collection)
    {
        $collection
            ->remove('create')
            ->remove('delete')
            ->remove('edit');
    }

    protected function configureListFields(ListMapper $list)
    {
        $list
            ->addIdentifier('id')
            ->add('title')
            ->add('logo', null, array(
                'template' => 'MetalCorpsiteBundle:AdminClient:client_logo_item.html.twig'
            ))
            ->add('link', 'url', array());
    }

    public function toString($object)
    {
        return $object instanceof Client ? $object->getTitle() : '';
    }

}
