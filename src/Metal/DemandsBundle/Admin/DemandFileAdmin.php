<?php

namespace Metal\DemandsBundle\Admin;

use Metal\DemandsBundle\Entity\DemandFile;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Vich\UploaderBundle\Form\Type\VichFileType;

class DemandFileAdmin extends AbstractAdmin
{
    protected function configureRoutes(RouteCollection $collection)
    {
        $collection
            ->add(
                'download_file',
                '{file_id}/download',
                array('_controller' => 'MetalDemandsBundle:DemandFile:downloadFileFromAdmin')
            );
    }

    protected function configureFormFields(FormMapper $formMapper)
    {
        $subject = $this->getSubject();
        /* @var $subject DemandFile */

        $downloadUri = false;
        if ($subject && $subject->getId()) {
            $downloadUri = $this->generateUrl('download_file', array('file_id' => $subject->getId()));
        }

        $formMapper
            ->add(
                'uploadedFile',
                VichFileType::class,
                array(
                    'label' => 'Файл',
                    'required' => false,
                    'allow_delete' => false,
                    'download_uri' => $downloadUri,
                )
            );
    }

    public function toString($object)
    {
        return $object instanceof DemandFile ? $object->getFile()->getOriginalName() : '';
    }
}
