<?php

namespace Metal\PrivateOfficeBundle\Widget;

use Brouzie\Bundle\WidgetsBundle\Widget\WidgetAbstract;
use Brouzie\WidgetsBundle\Widget\ConditionallyRenderedWidget;
use Metal\UsersBundle\Entity\User;

/**
 * Отвечает за показ попапа в ЛК тем компаниям, у которых по какой-то причине не выбран главный город
 */
class SelectCompanyCityWidget extends WidgetAbstract implements ConditionallyRenderedWidget
{
    public function shouldBeRendered()
    {
        $tokenStorage = $this->container->get('security.token_storage');

        $user = $tokenStorage->getToken()->getUser();

        /* @var $user User */

        return $user->getCompany()
        && !$user->getCompany()->getCity()
        && $this->getRequest()->get('_route') !== 'MetalPrivateOfficeBundle:Company:edit';
    }
}
