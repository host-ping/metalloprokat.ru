<?php

namespace Metal\PrivateOfficeBundle\Menu;

use Brouzie\Bundle\HelpersBundle\Helper\HelperFactory;
use Doctrine\ORM\EntityManager;
use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;

use Metal\CompaniesBundle\Entity\CompanyCounter;
use Metal\UsersBundle\Entity\User;
use Metal\UsersBundle\Entity\UserAutoLogin;
use Metal\UsersBundle\Entity\UserCounter;

use Metal\UsersBundle\Helper\AutoLoginHelper;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class PrivateOfficeMenuBuilder
{
    private $factory;
    private $helperFactory;
    private $em;
    private $additionalMenuItems;

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker;

    public function __construct(
        FactoryInterface $factoryInterface,
        TokenStorageInterface $tokenStorage, AuthorizationCheckerInterface $authorizationChecker,
        HelperFactory $helperFactory,
        EntityManager $entityManagerInterface,
        $additionalMenuItems
    ) {
        $this->factory = $factoryInterface;
        $this->em = $entityManagerInterface;
        $this->helperFactory = $helperFactory;
        $this->additionalMenuItems = $additionalMenuItems;

        $this->tokenStorage = $tokenStorage;
        $this->authorizationChecker = $authorizationChecker;
    }

    //TODO: не строить все меню сразу. Строить детей только для активной ноды
    public function getMenu()
    {
        $menu = $this->factory->createItem('root');

        $user = $this->tokenStorage->getToken()->getUser();
        /* @var $user User */

        $companyCounter = $user->getCompany() ? $user->getCompany()->getCounter() : null;
        $userCounter = $user->getCounter();

        $isSupplier = $this->authorizationChecker->isGranted('ROLE_SUPPLIER');
        $isApproved = $this->authorizationChecker->isGranted('ROLE_APPROVED_USER');

        if ($isSupplier && $isApproved) {
            $menu->addChild(
                'homepage',
                array(
                    'label' => 'Сводка',
                    'route' => 'MetalPrivateOfficeBundle:Default:index',
                )
            );
        }

        if (!$companyCounter || ($isSupplier && $isApproved)) {
            $autoLoginHelper = $this->helperFactory->get('MetalUsersBundle:AutoLogin');
            /* @var $autoLoginHelper AutoLoginHelper */
            foreach ($this->additionalMenuItems as $key => $item) {
                $menu->addChild(
                    $key,
                    array(
                        'label' => $item['title'],
                        'uri' => $autoLoginHelper->filterUrl($item['url'], UserAutoLogin::TARGET_EXTERNAL_SITE),
                        'extras' => array(
                            'target' => $item['target'],
                        ),
                    )
                );
            }
        }

        $this->buildManagement($menu, $user, $companyCounter);

        if ($companyCounter) {
            $this->buildClients($menu, $user, $companyCounter, $isApproved);
        }

        if ($companyCounter && $isApproved) {
            $this->buildSubscriptions($menu);
        }

        $this->buildArchive($menu);

        if ($isSupplier && $isApproved) {
            if ($user->getHasEditPermission()) {
                $this->buildStatistic($menu, $user);
                $this->buildServices($menu, $user);
                $this->buildMinisite($menu);
            }
        }

        $this->buildSupport($menu, $userCounter);

        $urlHelper = $this->helperFactory->get('MetalProjectBundle:Url');

        $menu->addChild(
            'quit',
            array(
                'label' => 'Выход',
                'uri' => $urlHelper->getLogoutUrl(),
                'extras' => array(
                    'cssClass' => 'quit',
                ),
            )
        );

        return $menu;
    }

    protected function buildSupport(ItemInterface $menu, UserCounter $userCounter)
    {
        $unviewedTopicsCount = (int)$this->em->getRepository('MetalSupportBundle:Topic')->getUnviewedTopicsCount($userCounter->getUser());

        $menu->addChild(
            'support',
            array(
                'label' => 'Техническая поддержка',
                'route' => 'MetalPrivateOfficeBundle:Support:list',
                'extras' => array('count' => $userCounter->getNewModeratorAnswersCount() + $unviewedTopicsCount),
            )
        );
    }

    protected function buildServices(ItemInterface $menu, User $user)
    {
        $countPendingPayments = $this->em->getRepository('MetalServicesBundle:Payment')->getUnpaidPaymentsCount($user);
        $menuItem = $menu->addChild(
            'services',
            array(
                'label' => 'Реквизиты и счета',
                'route' => 'MetalPrivateOfficeBundle:Details:edit',
                'extras' => array('count' => $countPendingPayments)
            )
        );

        $menuItem->addChild(
            'services.details',
            array(
                'label' => 'Реквизиты',
                'route' => 'MetalPrivateOfficeBundle:Details:edit',
            )
        );

        $menuItem->addChild(
            'services.payment',
            array(
                'label' => 'Счета',
                'route' => 'MetalPrivateOfficeBundle:Details:payment',
                'extras' => array('count' => $countPendingPayments)
            )
        );

//        $menuItem->addChild(
//            'services.services',
//            array(
//                'label' => 'Услуги',
//                'route' => 'MetalPrivateOfficeBundle:Services:edit',
//            )
//        );
//
//        $menuItem->addChild(
//            'services.accounts',
//            array(
//                'label' => 'Счета',
//                #      'route' => 'MetalPrivateOfficeBundle:Company:edit',
//            )
//        );
//        $menuItem->addChild(
//            'services.contract',
//            array(
//                'label' => 'Договор',
//                'route' => 'MetalPrivateOfficeBundle:Agreement:show',
//            )
//        );
    }

    protected function buildStatistic(ItemInterface $menu, User $user)
    {
        $menuItem = $menu->addChild(
            'statistic',
            array(
                'label' => 'Статистика',
                'route' => 'MetalPrivateOfficeBundle:Statistic:income',
            )
        );

        $menuItem->addChild(
            'statistic.income',
            array(
                'label' => 'Входящие клиенты',
                'route' => 'MetalPrivateOfficeBundle:Statistic:income',
                'extras' => array(
                    'routes' => array(
                        array('pattern' => '#MetalPrivateOfficeBundle:Statistic:income(Category|Region)#'),
                    ),
                ),
            )
        );

        $menuItem->addChild(
            'statistic.demand',
            array(
                'label' => 'Работа с потребностями',
                'route' => 'MetalPrivateOfficeBundle:Statistic:demand',
                'extras' => array(
                    'routes' => array(
                        array('pattern' => '#MetalPrivateOfficeBundle:Statistic:demand(Category|Region)#'),
                    ),
                ),
            )
        );

        $announcementsCount = $this->em->getRepository('MetalAnnouncementsBundle:Announcement')
            ->getAnnouncementsCount($user->getCompany());

        if ($announcementsCount) {
            $menuItem->addChild(
                'statistic.media',
                array(
                    'label' => 'Медийная реклама',
                    'route' => 'MetalPrivateOfficeBundle:Statistic:media',
                )
            );
        }

        $menuItem->addChild(
            'statistic.management',
            array(
                'label' => 'Управление информацией',
                'route' => 'MetalPrivateOfficeBundle:Statistic:management',
            )
        );
    }

    protected function buildManagement(ItemInterface $menu, User $user, CompanyCounter $companyCounter = null)
    {
        $menuItem = $menu->addChild(
            'management',
            array(
                'label' => 'Управление информацией',
                'route' => $companyCounter && $user->getHasEditPermission() ? 'MetalPrivateOfficeBundle:Products:list' : 'MetalPrivateOfficeBundle:Account:view',
            )
        );

        if ($companyCounter) {
            $counterRepository = $this->em->getRepository('MetalCompaniesBundle:CompanyCounter');
            $employeesCount = $counterRepository->getEmployeeCounters($user->getCompany());
            $menuItem->setExtra('count', $employeesCount['new_employees_count']);

            $companyCityRepository = $this->em->getRepository('MetalCompaniesBundle:CompanyCity');

            if ($user->getHasEditPermission()) {
                $menuItem->addChild(
                    'management.products',
                    array(
                        'label' => 'Товары',
                        'route' => 'MetalPrivateOfficeBundle:Products:list',
                        'extras' => array('total_count' => $companyCounter->getAllProductsCount()),
                    )
                );

                $menuItem->addChild(
                    'management.company',
                    array(
                        'label' => 'Компания',
                        'route' => 'MetalPrivateOfficeBundle:Company:edit',
                    )
                );

                $menuItem->addChild(
                    'management.cities',
                    array(
                        'label' => 'Города',
                        'route' => 'MetalPrivateOfficeBundle:Cities:view',
                        'extras' => array('total_count' => $companyCityRepository->getBranchesCountForCompany($user->getCompany())),
                    )
                );
            }

            $menuItem->addChild(
                'management.employees',
                array(
                    'label' => 'Сотрудники',
                    'route' => 'MetalPrivateOfficeBundle:Employees:list',
                    'extras' => array(
                        // отнимаем 1 чтобы исключить из кол-ва сотрудников текущего пользователя
                        'total_count' => $employeesCount['employees_count'] - 1,
                        'count' => $employeesCount['new_employees_count'],
                    ),
                )
            );
        }

        $menuItem->addChild(
            'management.me',
            array(
                'label' => 'Я',
                'route' => 'MetalPrivateOfficeBundle:Account:view',
            )
        );

        if (!$companyCounter) {
            $menuItem->addChild(
                'management.company_creation',
                array(
                    'label' => 'Компания',
                    'route' => 'MetalPrivateOfficeBundle:CompanyCreation:createCompany',
                )
            );
        }
    }

    protected function buildClients(ItemInterface $menu, User $user, CompanyCounter $companyCounter, $isApproved)
    {
        $newCallbacksCount = $newDemandsCount = 0;
        if ($isApproved) {
            $counterRepository = $this->em->getRepository('MetalCompaniesBundle:CompanyCounter');
            $newCallbacksCount = $counterRepository->getNewCallbacksCountForUser($user);
            $newDemandsCount = $counterRepository->getNewDemandsCountForUser($user);
        }

        $totalCount = $companyCounter->getNewComplaintsCount() + $companyCounter->getNewCompanyReviewsCount() + $newCallbacksCount + $newDemandsCount;
        $menuItem = $menu->addChild(
            'clients',
            array(
                'label' => 'Клиенты',
                'route' => 'MetalPrivateOfficeBundle:Demands:list',
                'extras' => array('count' => $totalCount),
            )
        );

        $menuItem->addChild(
            'clients.demands',
            array(
                'label' => 'Заявки',
                'route' => 'MetalPrivateOfficeBundle:Demands:list',
                'extras' => array('count' => $newDemandsCount),
            )
        );

        $menuItem->addChild(
            'clients.callbacks',
            array(
                'label' => 'Обратные звонки',
                'route' => 'MetalPrivateOfficeBundle:Callbacks:list',
                'extras' => array('count' => $newCallbacksCount),
            )
        );

        $menuItem->addChild(
            'clients.reviews',
            array(
                'label' => 'Отзывы',
                'route' => 'MetalPrivateOfficeBundle:Reviews:list',
                'extras' => array('count' => $companyCounter->getNewCompanyReviewsCount()),
            )
        );

        $menuItem->addChild(
            'clients.complaints',
            array(
                'label' => 'Жалобы',
                'route' => 'MetalPrivateOfficeBundle:Complaint:list',
                'extras' => array('count' => $companyCounter->getNewComplaintsCount()),
            )
        );
    }

    protected function buildSubscriptions(ItemInterface $menu)
    {
        $menuItem = $menu->addChild(
            'subscriptions',
            array(
                'label' => 'Подписки',
                'route' => 'MetalPrivateOfficeBundle:Subscriptions:demands',
            )
        );

        $menuItem->addChild(
            'subscriptions.demands',
            array(
                'label' => 'Потребности',
                'route' => 'MetalPrivateOfficeBundle:Subscriptions:demands',
            )
        );
    }

    protected function buildArchive(ItemInterface $menu)
    {
        $menuItem = $menu->addChild(
            'archive',
            array(
                'label' => 'Мои заявки',
                'route' => 'MetalPrivateOfficeBundle:Archive:demands',
            )
        );

        $menuItem->addChild(
            'archive.demands',
            array(
                'label' => 'Заявки',
                'route' => 'MetalPrivateOfficeBundle:Archive:demands',
            )
        );

        $menuItem->addChild(
            'archive.callbacks',
            array(
                'label' => 'Обратные звонки',
                'route' => 'MetalPrivateOfficeBundle:Archive:callbacks',
            )
        );
    }

    protected function buildMinisite(ItemInterface $menu)
    {
        $menuItem = $menu->addChild(
            'minisite',
            array(
                'label' => 'Мини-сайт',
                'route' => 'MetalPrivateOfficeBundle:MiniSite:address',
            )
        );

        $menuItem->addChild(
            'minisite.address',
            array(
                'label' => 'Адрес',
                'route' => 'MetalPrivateOfficeBundle:MiniSite:address',
            )
        );

        $menuItem->addChild(
            'minisite.header',
            array(
                'label' => 'Шапка',
                'route' => 'MetalPrivateOfficeBundle:MiniSite:header',
            )
        );

        $menuItem->addChild(
            'minisite.colors',
            array(
                'label' => 'Цвета',
                'route' => 'MetalPrivateOfficeBundle:MiniSite:colors',
            )
        );

        $menuItem->addChild(
            'minisite.share',
            array(
                'label' => 'Кнопка',
                'route' => 'MetalPrivateOfficeBundle:MiniSite:share',
            )
        );

        $menuItem->addChild(
            'minisite.documents',
            array(
                'label' => 'Документы',
                'route' => 'MetalPrivateOfficeBundle:MiniSite:documents',
            )
        );

        $menuItem->addChild(
            'minisite.analitics',
            array(
                'label' => 'Внешняя статистика',
                'route' => 'MetalPrivateOfficeBundle:MiniSite:analytics',
            )
        );

        $menuItem->addChild(
            'minisite.products',
            array(
                'label' => 'Товары',
                'route' => 'MetalPrivateOfficeBundle:PrivateCustomCategories:products',
            )
        );

        $menuItem->addChild(
            'minisite.custom_categories',
            array(
                'label' => 'Категории',
                'route' => 'MetalPrivateOfficeBundle:PrivateCustomCategories:categories',
            )
        );
    }
}
