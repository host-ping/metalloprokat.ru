<?php

namespace Metal\CorpsiteBundle\Menu;

use Doctrine\ORM\EntityManager;
use Knp\Menu\FactoryInterface;

class CorpMainMenuBuilder
{
    private $factory;

    /**
     * @var EntityManager
     */
    protected $em;

    public function __construct(FactoryInterface $factory, EntityManager $em)
    {
        $this->factory = $factory;
        $this->em = $em;
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
            'services',
            array(
                'label' => 'Услуги',
                'route' => 'MetalCorpsiteBundle:Default:services',
                'extras' => array(
                    'routes' => array(
                        array('pattern' => '#MetalCorpsiteBundle:Default:services#'),
                    ),
                ),
            )
        );

        if (count($this->em->getRepository('MetalCorpsiteBundle:Promotion')->getActivePromotions()) > 0) {
            $menu->addChild(
                'promotions',
                array(
                    'label' => 'Промо-акции',
                    'route' => 'MetalCorpsiteBundle:Default:promotions',
                    'extras' => array(
                        'routes' => array(
                            array('pattern' => '#MetalCorpsiteBundle:Default:promotions#'),
                        ),
                    ),
                )
            );
        }

        $menu->addChild(
            'clients',
            array(
                'label' => 'Клиенты',
                'route' => 'MetalCorpsiteBundle:Default:clients',
                'extras' => array(
                    'routes' => array(
                        array('pattern' => '#MetalCorpsiteBundle:Default:clients#'),
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
