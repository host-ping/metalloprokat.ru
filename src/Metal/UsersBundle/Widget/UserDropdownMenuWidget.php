<?php

namespace Metal\UsersBundle\Widget;

use Brouzie\Bundle\WidgetsBundle\Widget\WidgetAbstract;
use Doctrine\ORM\EntityManager;
use Metal\UsersBundle\Entity\User;
use Metal\UsersBundle\Entity\UserAutoLogin;
use Metal\UsersBundle\Helper\AutoLoginHelper;

class UserDropdownMenuWidget extends WidgetAbstract
{
    public function setDefaultOptions()
    {
        parent::setDefaultOptions();

        $this->optionsResolver->setRequired(array('place'));

    }

    protected function getParametersToRender()
    {
        $em = $this->getDoctrine()->getManager();
        /* @var $em EntityManager */
        $companyCounterRepository = $em->getRepository('MetalCompaniesBundle:CompanyCounter');


        $companyCounter = null;
        $menuItems = array();
        $newCallbacksCount = $newDemandsCount = $totalCount = $notificationFromModerator = 0;

        $user = null;
        if ($this->isGranted('ROLE_USER')) {
            $user = $this->getUser();
            /* @var $user User */
        }

        if ($user) {
            $companyCounter = $user->getCompany() ? $user->getCompany()->getCounter() : null;
            $notificationFromModerator = (int)$em->getRepository('MetalSupportBundle:Topic')->getUnviewedTopicsCount($user) + $user->getCounter()->getNewModeratorAnswersCount();

            if ($this->isGranted('ROLE_SUPPLIER') && $this->isGranted('ROLE_APPROVED_USER')) {
                $newCallbacksCount = $companyCounterRepository->getNewCallbacksCountForUser($user);
                $newDemandsCount = $companyCounterRepository->getNewDemandsCountForUser($user);
            }

            if ($companyCounter) {
                $totalCount = $companyCounter->getNewComplaintsCount() + $companyCounter->getNewCompanyReviewsCount() + $newCallbacksCount + $newDemandsCount;
            }
        }

        if (!$companyCounter || ($this->isGranted('ROLE_SUPPLIER') && $this->isGranted('ROLE_APPROVED_USER'))) {
            $menuItems = $this->container->getParameter('additional_menu_items');
            $autoLoginHelper = $this->container->get('brouzie.helper_factory')->get('MetalUsersBundle:AutoLogin');
            /* @var $autoLoginHelper AutoLoginHelper */

            if ($user) {
                foreach ($menuItems as $key => $item) {
                    $menuItems[$key]['url'] = $autoLoginHelper->filterUrl($item['url'], UserAutoLogin::TARGET_EXTERNAL_SITE);
                }
            }
        }

        return array(
            'menuItems' => $menuItems,
            'newCallbacksCount' => $newCallbacksCount,
            'newDemandsCount' => $newDemandsCount,
            'totalCount' => $totalCount,
            'notificationFromModerator' => $notificationFromModerator,
            'place' =>  $this->options['place']
        );
    }
}
