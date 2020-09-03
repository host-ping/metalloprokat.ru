<?php

namespace Metal\PrivateOfficeBundle\Widget;

use Brouzie\Bundle\WidgetsBundle\Widget\WidgetAbstract;
use Metal\CompaniesBundle\Form\CompanySaveLogoType;
use Metal\UsersBundle\Entity\User;

class UploadCompanyLogoWidget extends WidgetAbstract
{
    public function getParametersToRender()
    {
        $user = $this->getUser();
        /* @var $user User */

        $form = $this->createForm(new CompanySaveLogoType(), $user->getCompany());

        return array(
            'form' => $form->createView()
        );
    }
}
