<?php

namespace Metal\ProjectBundle\EventListener;

use Sonata\AdminBundle\Event\ConfigureMenuEvent;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class SonataAdminMenuBuilderListener
{
    /**
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker;

    public function __construct(AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->authorizationChecker = $authorizationChecker;
    }

    public function addMenuItems(ConfigureMenuEvent $event)
    {
        if (!$this->authorizationChecker->isGranted('ROLE_SUPER_ADMIN')) {
            return;
        }

        $menu = $event->getMenu();

        $menu->addChild(
            'sonata_admin_all_statistic_view',
            array('route' => 'sonata_admin_all_statistic_view')
        )
            ->setAttribute('icon', '<i class="fa fa-bar-chart"></i>')
            ->setLabel('Статистика проекта');

        $menu->addChild(
            'sonata_admin_sphinx_log_statistic_view',
            array('route' => 'sonata_admin_sphinx_log_statistic_view')
        )
            ->setAttribute('icon', '<i class="fa fa-area-chart"></i>')
            ->setLabel('Статистика sphinx log');

        $menu->addChild(
            'sonata_admin_stats_bots_statistic_view',
            array('route' => 'sonata_admin_stats_bots_statistic_view')
        )
            ->setAttribute('icon', '<i class="fa fa-pie-chart"></i>')
            ->setLabel('Статистика ботов');
    }
}
