<?php

namespace Metal\ProjectBundle\Menu;

use Knp\Menu\FactoryInterface;
use Metal\UsersBundle\Entity\User;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class MainMenuBuilder
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

        $suppliersMenuItem = $menu->addChild(
            'suppliers',
            array(
                'label' => 'Поставщики',
                'route' => 'MetalProjectBundle:Default:index_subdomain',
                'routeParameters' => array('subdomain' => $request->attributes->get('subdomain') ?: 'www'),
                'extras' => array(
                    'cls' => 'supplier icon-suppliers-color',
                    'routes' => array(
                        array('pattern' => '#MetalProjectBundle:Default:index#'),
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
                        array('pattern' => '#MetalCategoriesBundle:(LandingPages|LandingPage):#'),
                    ),
                ),
            )
        );

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

        $favoritesCount = null;
        $user = null;
        $openCompletePackagePopup = false;
        if ($this->tokenStorage->getToken() && $this->authorizationChecker->isGranted('ROLE_USER')) {
            $user = $this->tokenStorage->getToken()->getUser();
            /* @var $user User */
            $openCompletePackagePopup = !$user->isAllowedAddInFavorite();
            $userCounter = $user->getCounter();
            $favoritesCount = $userCounter->getFavoriteDemandsCount() + $userCounter->getFavoriteCompaniesCount();
        }

        $menu->addChild(
            'favorites',
            array(
                'label' => 'Избранное',
                'extras' => array(
                    'cls' => 'favorites icon-favorite-active',
                    'openLoginPopup' => null === $user,
                    'openCompletePackagePopup' => $openCompletePackagePopup,
                    'count' => $favoritesCount,
                    'rawRoutes' => array(
                        'suppliers' => 'MetalUsersBundle:Favorites:list',
                        'consumers' => 'MetalUsersBundle:Favorites:demandsList',
                        'favorites' => $request->attributes->get('_route'),
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
