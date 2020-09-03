<?php

namespace Metal\MiniSiteBundle\Admin;

use Metal\MiniSiteBundle\Entity\MiniSiteCover;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Vich\UploaderBundle\Form\Type\VichImageType;

/**
 * @method MiniSiteCover getSubject()
 */
class MiniSiteCoverAdmin extends AbstractAdmin
{
    protected $datagridValues = array(
        'company' => array(
            'value' => 'n',
        ),
        '_sort_order' => 'DESC',
        '_sort_by' => 'updatedAt',
    );

    protected $parentAssociationMapping = 'company';

    public function configureRoutes(RouteCollection $collection)
    {
        $collection
            ->add(
                'download_file',
                $this->getRouterIdParameter().'/download_file',
                array('_controller' => 'MetalMiniSiteBundle:MiniSiteCoverAdmin:downloadFile')
            )
            ->remove('delete');
    }

    public function setSubject($subject)
    {
        parent::setSubject($subject);
        /* @var $subject MiniSiteCover */

        if (!$subject->getId()) {
            $this->formOptions['validation_groups'] = array('Default', 'new_item');
        }
    }

    protected function configureFormFields(FormMapper $form)
    {
        $subject = $this->getSubject();
        $downloadUri = false;
        if ($subject && $subject->getId()) {
            $downloadUri = $this->generateUrl('download_file', array('id' => $subject->getId()));
        }
        $form
            ->add(
                'uploadedFile',
                VichImageType::class,
                array(
                    'label' => 'Фото',
                    'required' => !$subject->getFile()->getName(),
                    'imagine_pattern' => 'minisite_cover_big',
                    'download_uri' => $downloadUri,
                )
            );
    }

    protected function configureListFields(ListMapper $list)
    {
        $list
            ->addIdentifier('id')
            ->add(
                'uploadedFile',
                null,
                array(
                    'label' => 'Preview',
                    'template' => '@MetalProject/Admin/display_image_in_list.html.twig',
                    'image_filter' => 'minisite_cover_big',
                    'without_default_size' => true
                )
            )
            ->add(
                'company',
                null,
                array('label' => 'Компания', 'template' => 'MetalProjectBundle:Admin:company_with_id_list.html.twig')
            )
            ->add('createdAt', null, array('label' => 'Дата создания'))
            ->add('updatedAt', null, array('label' => 'Дата обновления'));
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add(
                'company',
                'doctrine_orm_callback',
                array(
                    'label' => 'Связана с компанией',
                    'callback' => function ($queryBuilder, $alias, $field, $value) {
                        if (!isset($value['value'])) {
                            return;
                        }
                        if ($value['value'] == 'y') {
                            $queryBuilder->andWhere(sprintf('%s.company IS NOT NULL', $alias));
                        } else {
                            $queryBuilder->andWhere(sprintf('%s.company IS NULL', $alias));
                        }

                        return true;
                    },
                ),
                'choice',
                array(
                    'choices' => array(
                        'y' => 'Связана',
                        'n' => 'Не связана',
                    ),
                )
            );
    }

    public function preUpdate($object)
    {
        /* @var $object MiniSiteCover */
        $object->setUpdatedAt(new \DateTime());
    }

    public function toString($object)
    {
        return $object instanceof MiniSiteCover ? $object->getFile()->getOriginalName() : '';
    }
}
