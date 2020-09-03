<?php

namespace Metal\PrivateOfficeBundle\Controller;

use Doctrine\ORM\EntityManager;
use Metal\CompaniesBundle\Entity\ValueObject\ActionTypeProvider;
use Metal\PrivateOfficeBundle\Form\CreateEmployeeType;
use Metal\TerritorialBundle\Entity\Country;
use Metal\UsersBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class PrivateEmployeesController extends Controller
{
    /**
     * @Security("has_role('ROLE_SUPPLIER')")
     */
    public function listAction()
    {
        $em = $this->getDoctrine()->getManager();
        /* @var $em EntityManager */
        $user = $this->getUser();
        /* @var $user User */

        $employees = $em
            ->createQueryBuilder()
            ->select('u')
            ->from('MetalUsersBundle:User', 'u', 'u.id')
            ->andWhere('u.isEnabled = true')
            ->andWhere('u.company = :company')
            ->setParameter('company', $user->getCompany())
            ->orderBy('u.approvedAt', 'ASC')
            ->addOrderBy('u.createdAt', 'ASC')
            ->getQuery()
            ->getResult();
        /* @var $employees User[] */

        // прячем текущего пользователя
        unset($employees[$user->getId()]);

        return $this->render(
            'MetalPrivateOfficeBundle:PrivateEmployees:list.html.twig',
            array(
                'employees' => $employees,
            )
        );
    }

    /**
     * @ParamConverter("employee", class="MetalUsersBundle:User")
     * @Security("has_role('ROLE_MAIN_USER') and employee.getCompany().getId() == user.getCompany().getId() and employee.getId() != user.getId()")
     */
    public function loadAction(User $employee)
    {
        $dataCollectorWidgetManager = $this->container->get('brouzie_widgets.widget_manager');
        $widget = $dataCollectorWidgetManager
            ->createWidget('MetalPrivateOfficeBundle:RegistrationEmployee', array('id' => $employee->getId()));

        return JsonResponse::create(
            array(
                'status' => 'success',
                'html' => $dataCollectorWidgetManager->renderWidget($widget),
            )
        );
    }

    /**
     * @ParamConverter("employee", class="MetalUsersBundle:User")
     * @Security("has_role('ROLE_MAIN_USER') and employee.getCompany().getId() == user.getCompany().getId() and employee.getId() != user.getId()")
     */
    public function deleteAction(User $employee)
    {
        $em = $this->getDoctrine()->getManager();
        /* @var $em EntityManager */

        $this->get('metal.users.users_mailer')->notifyOfAccessionToCompany($employee, false);

        $employee->scheduleSynchronization();

        $employee->setCompany(null);
        $employee->setApproved(null);

        $em->flush();

        return JsonResponse::create(array(
            'status' => 'success',
        ));
    }

    /**
     * @ParamConverter("employee", class="MetalUsersBundle:User")
     * @Security("has_role('ROLE_MAIN_USER') and employee.getCompany().getId() == user.getCompany().getId() and employee.getId() != user.getId()")
     */
    public function saveAction(Request $request, User $employee)
    {
        $em = $this->getDoctrine()->getManager();
        /* @var $em EntityManager */

        $user = $this->getUser();
        /* @var $user User */

        $oldApproved = $employee->isApproved();

        $form = $this->createForm(
            new CreateEmployeeType($this->container->getParameter('project.require_user_phone')),
            $employee,
            array('is_new' => false)
        );

        $form->handleRequest($request);

        if (!$form->isValid()) {
            $errors = $this->get('metal.project.form_helper')->getFormErrorMessages($form);

            return JsonResponse::create(array(
                'errors' => $errors
            ));
        }

        if ($form->get('newPassword')->getData()) {
            $this->get('metal.users.user_service')->updatePassword($employee);
            $this->get('metal.users.users_mailer')->notifyOfChangePassword($employee);
        }

        $companyService = $this->container->get('metal.companies.company_service');

        $companyHistory = $companyService->addCompanyHistory($user->getCompany(), $user, ActionTypeProvider::EDIT_USER);
        $companyHistory->setUser($employee);

        $em->flush();

        if ($oldApproved !== $employee->isApproved()) {
            $this->get('metal.users.users_mailer')->notifyOfAccessionToCompany($employee, $employee->isApproved());

            if ($employee->isApproved()) {
                $companyHistory = $companyService->addCompanyHistory($user->getCompany(), $user, ActionTypeProvider::CONNECT_USER);
            } else {
                $companyHistory = $companyService->addCompanyHistory($user->getCompany(), $user, ActionTypeProvider::DISCONNECT_USER);
            }

            $user->scheduleSynchronization();

            $companyHistory->setUser($employee);

            $em->flush();
        }

        return JsonResponse::create(array(
            'status' => 'success',
        ));
    }

    /**
     * @ParamConverter("employee", class="MetalUsersBundle:User")
     * @Security("has_role('ROLE_MAIN_USER') and employee.getCompany().getId() == user.getCompany().getId()")
     */
    public function approvedAction(User $employee)
    {
        $employee->setApproved();

        $this->getDoctrine()->getManager()->flush();

        return JsonResponse::create(array(
            'status' => 'success',
        ));
    }

    /**
     * @Security("has_role('ROLE_MAIN_USER')")
     */
    public function createAction(Request $request, Country $country)
    {
        $em = $this->getDoctrine()->getManager();
        /* @var $em EntityManager */

        $user = $this->getUser();
        /* @var $user User */

        $employee = new User();
        $employee->setCountry($country);
        $form = $this->createForm(
            new CreateEmployeeType($this->container->getParameter('project.require_user_phone')),
            $employee
        );

        $form->handleRequest($request);

        if (!$form->isValid()) {
            $errors = $this->get('metal.project.form_helper')->getFormErrorMessages($form);

            return JsonResponse::create(array(
                'errors' => $errors
            ));
        }

        $employee->setReferer($request->headers->get('REFERER'));
        $this->get('metal.users.user_service')->registerUser($employee);
        $this->get('metal.users.registration_services')->connectCompany($user->getCompany(), $employee);
        $employee->setApproved(true);

        $companyService = $this->container->get('metal.companies.company_service');

        $companyHistory = $companyService->addCompanyHistory($user->getCompany(), $user, ActionTypeProvider::CONNECT_USER);
        $companyHistory->setUser($employee);

        $employee->scheduleSynchronization();

        $em->flush();

        return JsonResponse::create(array(
            'status' => 'success',
        ));
    }
}
