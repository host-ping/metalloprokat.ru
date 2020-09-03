<?php

namespace Metal\CorpsiteBundle\Admin;

use Metal\CorpsiteBundle\Entity\ClientReview;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Vich\UploaderBundle\Form\Type\VichImageType;

class ClientReviewAdmin extends AbstractAdmin
{
    protected function configureRoutes(RouteCollection $collection)
    {
        $collection
            ->add('download_file', $this->getRouterIdParameter().'/download_file', array('_controller' => 'MetalCorpsiteBundle:ClientReviewAdmin:downloadFile'));
    }

    protected function configureListFields(ListMapper $list)
    {
        $list
            ->addIdentifier('id')
            ->add('company', null, array('label' => 'Компания', 'template' => 'MetalProjectBundle:Admin:company_with_id_list.html.twig'))
            ->add('name', null, array('label' => 'Имя'))
            ->add('position', null, array('label' => 'Должность'))
            ->add('shortComment', null, array('label' => 'Краткий отзыв'))
            ->add(
                'uploadedPhoto',
                null,
                array(
                    'label' => 'Фото',
                    'template' => '@MetalProject/Admin/display_image_in_list.html.twig',
                    'image_filter' => 'client_review_logo_sq40',
                )
            )
            ->add('createdAt', null, array('label' => 'Дата создания'))
            ->add('moderated', 'boolean', array('label' => 'Промодерирован'))
            ->add('deleted', 'boolean', array('label' => 'Удален'))
        ;
    }

    protected function configureFormFields(FormMapper $form)
    {
        $subject = $this->getSubject();
        $downloadUri = false;
        if ($subject && $subject->getId()) {
            $downloadUri = $this->generateUrl('download_file', array('id' => $subject->getId()));
        }

        $form
            ->add('company', 'entity_id', array(
                'class' => 'MetalCompaniesBundle:Company',
                'label' => 'ID компании',
                'hidden' => false,
                'required' => true,
                'attr' => array(
                    'ng-model' => 'company.id',
                    'initial-value' => '',
                ),
            ))
            ->add('name', null, array('label' => 'Имя'))
            ->add('position', null, array('label' => 'Должность'))
            ->add('uploadedPhoto', VichImageType::class,
                array(
                    'label' => 'Фото сотрудника',
                    'required' => false,
                    'imagine_pattern' => 'client_review_logo_sq136',
                    'download_uri' => $downloadUri,
                )
            )
            ->add('shortComment', 'textarea', array('label' => 'Текст краткого отзыва', 'attr' => array('rows' => 2)))
            ->add('comment', 'textarea', array('label' => 'Текст отзыва', 'attr' => array('rows' => 9)))
            ->add('moderated', 'checkbox', array('label' => 'Промодерирована', 'required' => false))
            ->add('deleted', 'checkbox', array('label' => 'Удален', 'required' => false))

        ;
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id', null, array('label' => 'ID отзыва'))
            ->add(
                'company',
                'doctrine_orm_number',
                array(
                    'label' => 'ID компании',
                )
            )
            ->add(
                'moderatedAt',
                'doctrine_orm_callback',
                array(
                    'label' => 'Модерация',
                    'callback' => function ($queryBuilder, $alias, $field, $value) {
                        if (!isset($value['value'])) {
                            return null;
                        }

                        if ($value['value'] === 'y') {
                            $queryBuilder->andWhere(sprintf('%s.moderatedAt IS NOT NULL', $alias));
                        } else {
                            $queryBuilder->andWhere(sprintf('%s.moderatedAt IS NULL', $alias));
                        }

                        return true;
                    }
                ),
                'choice',
                array(
                    'choices' => array(
                        'y' => 'Промодерирован',
                        'n' => 'Не промодерирован'
                    )
                )
            )
            ->add(
                'deletedAt',
                'doctrine_orm_callback',
                array(
                    'label' => 'Удаленность',
                    'callback' => function ($queryBuilder, $alias, $field, $value) {
                        if (!isset($value['value'])) {
                            return null;
                        }

                        if ($value['value'] === 'y') {
                            $queryBuilder->andWhere(sprintf('%s.deletedAt IS NOT NULL', $alias));
                        } else {
                            $queryBuilder->andWhere(sprintf('%s.deletedAt IS NULL', $alias));
                        }

                        return true;
                    }
                ),
                'choice',
                array(
                    'choices' => array(
                        'y' => 'Удален',
                        'n' => 'Не удален'
                    )
                )
            )
        ;
    }

    public function prePersist($object)
    {
        $this->checkDifferences($object);
    }

    public function preUpdate($object)
    {
        $this->checkDifferences($object);
    }

    protected function checkDifferences($object)
    {
        /* @var $object ClientReview */

        $tokenStorage = $this->getConfigurationPool()->getContainer()->get('security.token_storage');
        if ($object->getModeratedAt()) {
            $object->setModeratedAt(new \DateTime());
            $object->setModeratedBy($tokenStorage->getToken()->getUser());
        }

        if ($object->getDeletedAt()) {
            $object->setDeletedAt(new \DateTime());
            $object->setDeletedBy($tokenStorage->getToken()->getUser());
        }
    }

    public function toString($object)
    {
        return $object instanceof ClientReview ? $object->getName() : '';
    }
}
