<?php

namespace Metal\ProjectBundle\Admin;

use Metal\ProjectBundle\Entity\Site;
use Metal\ProjectBundle\Entity\ValueObject\SiteSourceTypeProvider;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;

class SiteAdmin extends AbstractAdmin
{
    protected $perPageOptions = array(10, 25, 50, 100, 150, 200);

    public function getBatchActions()
    {
        $actions['sendYandex'] = array(
            'label' => 'Отправить на Яндекс',
            'ask_confirmation' => false
        );
        $actions['sendGoogle'] = array(
            'label' => 'Отправить на Google',
            'ask_confirmation' => false
        );
        $actions = array_merge($actions, parent::getBatchActions());

        return $actions;
    }

    protected function configureRoutes(RouteCollection $collection)
    {
        $collection
            ->add('import', 'import', array('_controller' => 'MetalProjectBundle:SiteAdmin:importDomain'))
            ->add('take-token-yandex', 'take-token-yandex', array('_controller' => 'MetalProjectBundle:SiteAdmin:takeYandexToken'))
            ->add('take-token-google', 'take-token-google', array('_controller' => 'MetalProjectBundle:SiteAdmin:takeGoogleToken'))
            ->remove('create')
            ->remove('delete')
            ->remove('edit')
            ->remove('show')
        ;
    }

    public function configure()
    {
        $this->setTemplate('list', 'MetalProjectBundle:SiteAdmin:list.html.twig');
    }

    protected function configureShowField(ShowMapper $showMapper)
    {
        $showMapper
            ->add('id', null, array('label' => 'Идентификатор'))
            ->add('hostname', null, array('label' => 'Хост'))
            ->add('yandexSiteId', null, array('label' => 'ID сайта Yandex'))
            ->add('yandexCode', null, array('label' => 'Код Yandex'))
            ->add('googleSiteId', null, array('label' => 'ID сайта Google'))
            ->add('googleCode', null, array('label' => 'Код Google'))
        ;
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id')
            ->add('hostname', null,
                array(
                    'label' => 'Хост',
                    'template' => 'MetalProjectBundle:SiteAdmin:siteListHost.html.twig'
                )
            )
            ->add('yandexSiteId', null,
                array(
                    'label' => 'ID сайта Yandex',
                    'template' => 'MetalProjectBundle:SiteAdmin:yandexUrl.html.twig'
                )
            )
            ->add('yandexCode', null, array('label' => 'Код Yandex'))
            ->add('googleSiteId', null,
                array(
                    'label' => 'ID сайта Google',
                    'template' => 'MetalProjectBundle:SiteAdmin:googleUrl.html.twig'
                )
            )
            ->add('googleCode', null, array('label' => 'Код Google'))
        ;
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('hostname', null, array('label' => 'Хост'))
            ->add('yandexSiteId', null, array('label' => 'ID сайта Yandex'))
            ->add('yandexCode', null, array('label' => 'Код Yandex'))
            ->add('googleSiteId', null, array('label' => 'ID сайта Google'))
            ->add('googleCode', null, array('label' => 'Код Google'))
            ->add(
                'yandexId',
                'doctrine_orm_callback',
                array(
                    'label' => 'Присутствует на Yandex',
                    'callback' => function ($queryBuilder, $alias, $field, $value) {
                        if (!isset($value['value'])) {
                            return;
                        }
                        if ($value['value'] == 'y') {
                            $queryBuilder->andWhere(sprintf("(%s.yandexSiteId IS NOT NULL)", $alias));
                        } else {
                            $queryBuilder->andWhere(sprintf("(%s.yandexSiteId IS NULL)", $alias));
                        }

                        return true;
                    }
                ),
                'choice',
                array(
                    'choices' => array(
                        'y' => 'Да',
                        'n' => 'Нет'
                    )
                )
            )
            ->add(
                'googleId',
                'doctrine_orm_callback',
                array(
                    'label' => 'Присутствует на Google',
                    'callback' => function ($queryBuilder, $alias, $field, $value) {
                        if (!isset($value['value'])) {
                            return;
                        }
                        if ($value['value'] == 'y') {
                            $queryBuilder->andWhere(sprintf("(%s.googleSiteId IS NOT NULL)", $alias));
                        } else {
                            $queryBuilder->andWhere(sprintf("(%s.googleSiteId IS NULL)", $alias));
                        }

                        return true;
                    }
                ),
                'choice',
                array(
                    'choices' => array(
                        'y' => 'Да',
                        'n' => 'Нет'
                    )
                )
            )
            ->add(
                'sourceTypeId',
                'doctrine_orm_choice',
                array(
                    'label' => 'Базовый домен'
                ),
                'choice',
                array(
                    'choices' => SiteSourceTypeProvider::getAllTypesAsSimpleArray(),
                )
            )
        ;
    }

    public function toString($object)
    {
        return $object instanceof Site ? $object->getHostname() : '';
    }
}
