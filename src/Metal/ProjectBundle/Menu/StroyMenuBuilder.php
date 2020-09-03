<?php

namespace Metal\ProjectBundle\Menu;

use Knp\Menu\FactoryInterface;
use Metal\UsersBundle\Entity\User;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class StroyMenuBuilder
{
    private $factory;

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker;

    public function __construct(FactoryInterface $factory, TokenStorageInterface $tokenStorage, AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->factory = $factory;
        $this->tokenStorage = $tokenStorage;
        $this->authorizationChecker = $authorizationChecker;
    }

    public function getMenu(RequestStack $requestStack)
    {
        $menu = $this->factory->createItem('root');
        $request = $requestStack->getMasterRequest();

        $ideasMenuItem = $menu->addChild(
            'ideas',
            array(
                'label' => 'Идеи',
                'route' => 'MetalProjectBundle:Default:index_subdomain',
                'routeParameters' => array('subdomain' => 'www'),
                'extras' => array(
                    'cls' => 'icon-idea',
                    'routes' => array(
                        array('pattern' => '#MetalProjectBundle:Default:index#'),
                    ),
                ),
            )
        );

        $ideasMenuItem->addChild(
            'ideas.fake_child',
            array(
                'extras' => array(
                    'cls' => 'icon-idea',
                    'routes' => array(
                        array('pattern' => '#MetalContentBundle:(Topics|Topic):#'),
                        array('pattern' => '#MetalContentBundle:(Tags|Tag):#'),
                        array('pattern' => '#MetalContentBundle:(Questions|Question):#'),
                        array('pattern' => '#MetalContentBundle:Instagram#'),
                        array('pattern' => '#MetalContentBundle:Search:search#'),
                    ),
                ),
            )
        );

        $suppliersMenuItem = $menu->addChild(
            'suppliers',
            array(
                'label' => 'Предложения',
                'route' => 'MetalProductsBundle:Products:products_index_subdomain',
                'routeParameters' => array('subdomain' => $request->attributes->get('subdomain') ?: 'www'),
                'extras' => array(
                    'cls' => 'supplier icon-suppliers-color',
                    'routes' => array(
                        array('pattern' => '#MetalProductsBundle:Products:products_index_subdomain#'),
                    ),
                ),
            )
        );

        $suppliersMenuItem->addChild(
            'suppliers.fake_child',
            array(
                'extras' => array(
                    'cls' => 'supplier icon-suppliers-color',
                    'routes' => array(
                        array('pattern' => '#MetalCompaniesBundle:(Companies|Company):#'),
                        array('pattern' => '#MetalProductsBundle:(Products|Product):#'),
                        array('pattern' => '#MetalCatalogBundle:(Products|Product):#'),
                        array('pattern' => '#MetalCatalogBundle:(Manufacturers|Manufacturer):#'),
                        array('pattern' => '#MetalCatalogBundle:(Brands|Brand):#'),
                    ),
                ),
            )
        );

        $favoritesCount = null;
        $user = null;
        if ($this->tokenStorage->getToken() && $this->authorizationChecker->isGranted('ROLE_USER')) {
            $user = $this->tokenStorage->getToken()->getUser();
            /* @var $user User */
            $userCounter = $user->getCounter();
            $favoritesCount = $userCounter->getFavoriteDemandsCount() + $userCounter->getFavoriteCompaniesCount();
        }

        $consumersMenuItem = $menu->addChild(
            'consumers',
            array(
                'label' => 'Потребители',
                'route' => 'MetalDemandsBundle:Default:index_subdomain',
                'routeParameters' => array('subdomain' => $request->attributes->get('subdomain') ?: 'www'),
                'extras' => array(
                    'cls' => 'consumer icon-consumers-color',
                    'routes' => array(
                        array('pattern' => '#MetalDemandsBundle:Default:index#'),
                    ),
                ),
            )
        );

        $consumersMenuItem->addChild(
            'consumers.fake_child',
            array(
                'extras' => array(
                    'cls' => 'supplier icon-suppliers-color',
                    'routes' => array(
                        array('pattern' => '#MetalDemandsBundle:(Demands|Demand):#'),
                    ),
                ),
            )
        );

        $menu->addChild(
            'favorites',
            array(
                'label' => 'Избранное',
                'extras' => array(
                    'cls' => 'favorites icon-favorite-active',
                    'openLoginPopup' => null === $user,
                    'count' => $favoritesCount,
                    'rawRoutes' => array(
                        'suppliers' => 'MetalUsersBundle:Favorites:list',
                        'ideas' => 'MetalUsersBundle:Favorites:list',
                        'favorites' => $request->attributes->get('_route'),
                        'consumers' => 'MetalUsersBundle:Favorites:demandsList',
                    ),
                    'routes' => array(
                        array('pattern' => '#MetalUsersBundle:Favorites#'),
                    ),
                ),
            )
        );

        return $menu;
    }
}
