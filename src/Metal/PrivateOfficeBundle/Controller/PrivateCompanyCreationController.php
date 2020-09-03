<?php

namespace Metal\PrivateOfficeBundle\Controller;

use Metal\CompaniesBundle\Entity\Company;
use Metal\CompaniesBundle\Entity\ValueObject\ActionTypeProvider;
use Metal\CompaniesBundle\Form\CompanyCreationType;

use Metal\UsersBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class PrivateCompanyCreationController  extends Controller
{
    /**
     * @Security("has_role('ROLE_USER') and (not request.isMethod('POST') or has_role('ROLE_CONFIRMED_EMAIL')) and not has_role('ROLE_SUPPLIER')")
     */
    public function createCompanyAction(Request $request)
    {
        $user = $this->getUser();
        /* @var $user User */
        $em = $this->container->get('doctrine.orm.entity_manager');

        $form = $this->createForm(new CompanyCreationType(), null, array(
                'city_repository' => $em->getRepository('MetalTerritorialBundle:City'),
                'user_repository' => $em->getRepository('MetalUsersBundle:User'),
                'is_admin_panel' => false
            ));

        if (!$request->isMethod('POST')) {
            return $this->render('MetalPrivateOfficeBundle:PrivateCompanyCreation:company_creation.html.twig', array(
                    'form' => $form->createView()
                ));
        }

        $form->handleRequest($request);

        if (!$form->isValid()) {
            $errors = $this->get('metal.project.form_helper')->getFormErrorMessages($form);
            return JsonResponse::create(array(
                    'errors' => $errors,
                ));
        }

        $companyName = $form->get('companyTitle')->getData();
        $selectedCompany = $form->get('company')->getData();
        $companyType = $form->get('companyType')->getData();
        $phone = $form->get('phone')->getData();

        $city = $form->get('city')->getData();

        if ($form->get('checkDuplication')->getData()) {
            if ($possibleCompanies = $em->getRepository('MetalCompaniesBundle:Company')->getPossibleCompanies($city, $companyType, $companyName)) {
                return JsonResponse::create(
                    array(
                        'errors' => array(),
                        'html' => $this->renderView(
                            'MetalUsersBundle:Registration:foundCompany.html.twig',
                            array('companiesList' => $possibleCompanies)
                        )
                    )
                );
            }
        }

        if ($selectedCompany) {
            $company = $selectedCompany;
        } else {
            $company = new Company();
        }

        $registrationService = $this->container->get('metal.users.registration_services');
        $registrationService->createCompany($company, $user, $phone, $companyName, $companyType, $city);

        if (!$selectedCompany) {
            $this->container->get('metal.companies.company_service')->addCompanyHistory($company, $user, ActionTypeProvider::COMPANY_CREATION);
        }

        $subscriberService = $this->container->get('metal.newsletter.subscriber_service');
        $subscriberService->removeUnnecessarySubscriberForUser($user);
        $subscriberService->createOrUpdateSubscriberForUser($user);
        $em->flush();

        return JsonResponse::create(array('status' => 'success', 'redirect_to' => $this->generateUrl('MetalPrivateOfficeBundle:Company:edit')));
    }
}
