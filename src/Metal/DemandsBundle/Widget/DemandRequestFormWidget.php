<?php

namespace Metal\DemandsBundle\Widget;

use Brouzie\Bundle\WidgetsBundle\Widget\WidgetAbstract;
use Metal\DemandsBundle\Entity\Demand;
use Metal\DemandsBundle\Entity\DemandFile;
use Metal\DemandsBundle\Entity\DemandItem;
use Metal\DemandsBundle\Entity\PrivateDemand;
use Metal\DemandsBundle\Form\DemandType;
use Metal\UsersBundle\Entity\User;

class DemandRequestFormWidget extends WidgetAbstract
{
    public function setDefaultOptions()
    {
        parent::setDefaultOptions();

        $this->optionsResolver
            ->setRequired(array('private_demand'))
            ->setDefaults(array('payment_company' => false, 'need_reload' => false));
        //need_reload - для перезагрузки страницы в личном кабинете
    }

    public function getParametersToRender()
    {
        $isPrivate = $this->options['private_demand'];
        $em = $this->container->get('doctrine');

        if ($isPrivate) {
            $demand = new PrivateDemand();
        } else {
            $demand = new Demand();
        }

        $user = null;
        if ($this->isGranted('ROLE_USER')) {
            $user = $this->getUser();
            /* @var $user User */
        }

        $demandItem = new DemandItem();
        $demand->addDemandItem($demandItem);

        $demandFile = new DemandFile();
        $demand->addDemandFile($demandFile);

        if ($user) {
            $demand->setPhone($user->getPhone());
            $demand->setPerson($user->getFullName());
            if ($user->getCity()) {
                $demand->setCity($user->getCity());
                $demand->cityTitle = $user->getCity()->getTitle();
            }
        }

        $options = array(
            'is_private' => $isPrivate,
            'is_authenticated' => $user !== null,
            'is_from_trader' => $user !== null && $user->getCompany() !== null,
            'data_class' => get_class($demand),
            'validation_groups' => array(
                $user !== null ? 'authenticated_prokat' : 'anonymous_prokat',
            ),
            'city_repository' => $em->getRepository('MetalTerritorialBundle:City'),
        );

        $form = $this->createForm(new DemandType(), $demand, $options);

        return array(
            'form' => $form->createView(),
        );
    }
}
