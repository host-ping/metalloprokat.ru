<?php

namespace Metal\PrivateOfficeBundle\Widget;

use Brouzie\Bundle\WidgetsBundle\Widget\WidgetAbstract;
use Metal\PrivateOfficeBundle\Form\CreateEmployeeType;
use Metal\UsersBundle\Entity\User;

class RegistrationEmployeeWidget extends WidgetAbstract
{
    protected function setDefaultOptions()
    {
        parent::setDefaultOptions();

        $this->optionsResolver
            ->setDefaults(array(
                'id' => null
            ))
        ;
    }

    protected function getParametersToRender()
    {
        $employee = new User();
        $employee->setDisplayInContacts(false);

        if (isset($this->options['id'])) {
            $employee = $this->getDoctrine()->getManager()->find('MetalUsersBundle:User', $this->options['id']);
        }

        $form = $this->createForm(new CreateEmployeeType(), $employee, array('is_new' => !isset($this->options['id'])));

        return array(
            'form' => $form->createView(),
            'employee' => $this->formatEmployee($employee),
        );
    }

    private function formatEmployee(User $employee)
    {
        return array(
            'fullName' => $employee->getFullName(),
            'phone' => $employee->getPhone(),
            'email' => $employee->getEmail(),
            'hasEditPermission' => $employee->getHasEditPermission(),
            'canUseService' => $employee->getCanUseService(),
            'displayPosition' => $employee->getDisplayPosition(),
            'displayInContacts' => $employee->getDisplayInContacts(),
            'approved' => (bool)$employee->getApprovedAt(),
            'newPassword' => array(
                'first' => '',
                'second' => ''
            ),
            'job' => $employee->getJob()
        );
    }
}
