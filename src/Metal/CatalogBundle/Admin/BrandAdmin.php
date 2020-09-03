<?php

namespace Metal\CatalogBundle\Admin;

use Metal\CatalogBundle\Entity\Brand;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Vich\UploaderBundle\Form\Type\VichImageType;

/**
 * @method Brand getSubject()
 */
class BrandAdmin extends AbstractAdmin
{
    protected function configureRoutes(RouteCollection $routeCollection)
    {
        $routeCollection
            ->remove('create')
            ->remove('show');
    }

    public function toString($object)
    {
        return $object instanceof Brand ? 'Бренд '.$object->getAttributeValue()->getValue() : '';
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id')
            ->add('title', null, array('label' => 'Название'))
            ->add('site', 'url', array('label' => 'Сайт'))
            ->add(
                'uploadedLogo',
                null,
                array(
                    'label' => 'Логотип',
                    'template' => '@MetalProject/Admin/display_image_in_list.html.twig',
                    'image_filter' => 'catalog_logo_sq40',
                )
            );
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id', null, array('label' => 'ID'))
            ->add(
                'title',
                null,
                array(
                    'label' => 'Название'
                )
            )
            ->add(
                'fileSize',
                'doctrine_orm_callback',
                array(
                    'label' => 'Наличие лого',
                    'callback' => function ($queryBuilder, $alias, $field, $value) {
                        if (!isset($value['value'])) {
                            return null;
                        }

                        if ($value['value'] === 'y') {
                            $queryBuilder->andWhere(sprintf('%s.logo.name IS NOT NULL', $alias));
                        } else {
                            $queryBuilder->andWhere(sprintf('%s.logo.name IS NULL', $alias));
                        }

                        return true;
                    }
                ),
                'choice',
                array(
                    'choices' => array(
                        'y' => 'С лого',
                        'n' => 'Без лого'
                    )
                )
            )
            ->add(
                '_hasProducts',
                'doctrine_orm_callback',
                array(
                    'label' => 'Добавлены продукты',
                    'callback' => function ($queryBuilder, $alias, $field, $value) {
                        if (!isset($value['value'])) {
                            return;
                        }

                        if ($value['value'] === 'y') {
                            $queryBuilder->andWhere(sprintf('(EXISTS (SELECT p.id FROM MetalCatalogBundle:Product p WHERE p.brand = %s.id))', $alias));
                        } else {
                            $queryBuilder->andWhere(sprintf('(NOT EXISTS (SELECT p.id FROM MetalCatalogBundle:Product p WHERE p.brand = %s.id))', $alias));
                        }

                        return true;
                    },
                    'field_type' => 'choice',
                ),
                null,
                array(
                    'choices' => array(
                        'y' => 'Да',
                        'n' => 'нет'
                    )
                )
            )
        ;
    }

    protected function configureFormFields(FormMapper $formMapper)
    {
        $brand = $this->getSubject();

        $formMapper
            ->add('description', null, array('label' => 'Описание'))
            ->add(
                'site',
                'url',
                array(
                    'label' => 'Сайт',
                    'required' => false
                )
            )
            ->add('uploadedLogo', VichImageType::class,
                array(
                    'label' => 'Логотип бренда',
                    'required' => !$brand->getLogo()->getName(),
                    'imagine_pattern' => 'catalog_logo_sq136',
                    'download_uri' => false,
                )
            );
    }
}
