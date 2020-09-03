<?php

namespace Metal\CallbacksBundle\Widget;

use Brouzie\Bundle\WidgetsBundle\Widget\WidgetAbstract;
use Metal\CallbacksBundle\Entity\Callback;
use Metal\CallbacksBundle\Form\CallbackType;
use Metal\UsersBundle\Entity\User;

class CallbackFormWidget extends WidgetAbstract
{
    public function setDefaultOptions()
    {
        $this
            ->optionsResolver
            ->setRequired(array('for_moderator'))
            ->setDefaults(
                array(
                    'payment_company' => false,
                    'for_product' => false,
                )
            );
    }

    public function getParametersToRender()
    {
        $callback = new Callback();

        $user = null;
        if ($this->isGranted('ROLE_USER')) {
            $user = $this->getUser();
            /* @var $user User */
            $callback->setPhone($user->getPhone());
        }

        $options = array(
            'for_product' => $this->options['for_product'],
        );

        $form = $this->createForm(new CallbackType(), $callback, $options);

        return array(
            'form' => $form->createView(),
        );
    }
}
