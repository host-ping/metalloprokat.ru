<?php

namespace Metal\CorpsiteBundle\Menu;

use Knp\Menu\FactoryInterface;

class CorpStroyMenuBuilder
{
    private $factory;

    public function __construct(FactoryInterface $factory)
    {
        $this->factory = $factory;
    }

    public function getMenu()
    {
        $menu = $this->factory->createItem('root');

        $menu->addChild(
            'index',
            array(
                'label' => 'О компании',
                'route' => 'MetalCorpsiteBundle:Default:index',
                'extras' => array(
                    'routes' => array(
                        array('pattern' => '#MetalCorpsiteBundle:Default:index#'),
                    ),
                ),
            )
        );

        $menu->addChild(
            'media',
            array(
                'label' => 'Медийная реклама',
                'route' => 'MetalCorpsiteBundle:Default:announcementOrder',
                'extras' => array(
                    'routes' => array(
                        array('pattern' => '#MetalCorpsiteBundle:Default:announcementOrder#'),
                    ),
                ),
            )
        );

        $menu->addChild(
            'contacts',
            array(
                'label' => 'Контакты',
                'route' => 'MetalCorpsiteBundle:Default:contacts',
                'extras' => array(
                    'routes' => array(
                        array('pattern' => '#MetalCorpsiteBundle:Default:contacts#'),
                    ),
                ),
            )
        );

        return $menu;
    }
}
