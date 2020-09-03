<?php

namespace Metal\UsersBundle\Controller;

use Doctrine\ORM\EntityManager;
use Metal\CompaniesBundle\Entity\CompanyCategory;
use Metal\CompaniesBundle\Entity\CompanyRegistration;
use Metal\ServicesBundle\Entity\Package;
use Metal\ServicesBundle\Entity\PackageOrder;
use Metal\ServicesBundle\Entity\Payment;
use Metal\UsersBundle\Entity\User;
use Metal\UsersBundle\Form\RegistrationSecondType;
use Metal\UsersBundle\Form\RegistrationThirdType;
use Metal\CategoriesBundle\Entity\Category;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class WizzardRegistrationController extends Controller
{
    /**
     * @Security("has_role('ROLE_SUPPLIER')")
     * @ParamConverter("category", class="MetalCategoriesBundle:Category", options={"id" = "category_id"})
     */
    public function secondStepAction(Request $request, Category $category)
    {
        $em = $this->getDoctrine()->getManager();
        /* @var $em EntityManager*/
        $user = $this->getUser();
        /* @var $user User */
        $company = $user->getCompany();

        $companyRegistration = $em->getRepository('MetalCompaniesBundle:CompanyRegistration')->findOneBy(array('company' => $company));

        if (!$companyRegistration) {
            $companyRegistration = new CompanyRegistration();
            $companyRegistration->setCompany($company);
            $companyRegistration->setCategory($category);

            $em->persist($companyRegistration);
            $em->flush();
        }

        $companyCategory = new CompanyCategory();
        $companyCategory->setCategory($category);
        $companyRegistration->addCompanyCategories($companyCategory);

        $url = $this->generateUrl(
            'MetalUsersBundle:WizzardRegistration:registerThirdStep',
            array('category_id' => $category->getId())
        );
        if ($companyRegistration->getIsSecondStepDone()) {
            return $this->redirect($url, 301);
        }

        $form = $this->createForm(new RegistrationSecondType(), $companyRegistration);

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);

            $companyRegistration->setIsSecondStepDone(true);
            $companyRegistration->setUpdatedAt(new \DateTime());
            $em->flush();

            return $this->redirect($url, 301);
        }

        $formView = $form->createView();

        return $this->render(
            'MetalUsersBundle:WizzardRegistration:registerSecondStep.html.twig',
            array(
                'category' => $category,
                'form' => $formView
            )
        );
    }

    /**
     * @Security("has_role('ROLE_SUPPLIER')")
     * @ParamConverter("category", class="MetalCategoriesBundle:Category", options={"id" = "category_id"})
     */
    public function thirdStepAction(Request $request, Category $category)
    {
        $em = $this->getDoctrine()->getManager();
        /* @var $em EntityManager */
        $user = $this->getUser();
        /* @var $user User */
        $company = $user->getCompany();

        $companyRegistration = $em->getRepository('MetalCompaniesBundle:CompanyRegistration')->findOneBy(array('company' => $company));

        if ($companyRegistration->getIsThirdStepDone()) {
            return $this->redirect($this->generateUrl('MetalProjectBundle:Default:index'), 301);
        }

        $form = $this->createForm(new RegistrationThirdType(), $companyRegistration);

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);

            if (!$form->isValid()) {
                $errors = $this->get('metal.project.form_helper')->getFormErrorMessages($form);
                return JsonResponse::create(array(
                    'errors' => $errors,
                ));
            }

            $companyRegistration->setIsThirdStepDone(true);
            $companyRegistration->setUpdatedAt(new \DateTime());
            $em->flush();

            if ($companyRegistration->getPackage()->getId() > Package::BASE_PACKAGE) {
                $packageOrder = new PackageOrder();
                $packageOrder->setCompany($company);
                $packageOrder->setPackage($companyRegistration->getPackage());
                $packageOrder->setPackagePeriod($companyRegistration->getTermPackage());
                $packageOrder->setCompanyTitle($company->getTitle());
                $packageOrder->setCity($company->getCity());
                $packageOrder->setEmail($company->getCompanyLog()->getCreatedBy()->getEmail());
                $packageOrder->setFullName($company->getCompanyLog()->getCreatedBy()->getFullName());
                $packageOrder->setUser($company->getCompanyLog()->getCreatedBy());

                $em->persist($packageOrder);

                $payment = Payment::createFromPackageOrder($packageOrder);
                $payment->setServName(sprintf(
                    'Оплата информационных услуг на сайте %s, срок подключения - %s',
                    $this->container->getParameter('project.title'),
                    $packageOrder->getPackagePeriodName()
                ));

                $em->persist($payment);

                $em->flush();

                return $this->redirect($this->generateUrl('MetalPrivateOfficeBundle:Details:payment'));
            }

            return $this->redirect($this->generateUrl('MetalPrivateOfficeBundle:Default:index'));
        }

        //TODO: может быть от этого как-то можно отказаться тоже
        $packages = $em->getConnection()->fetchAll('SELECT Message_ID AS id, Price3 AS to_quarter, Price6 AS to_half_year, Price12 AS to_year FROM Message180 ORDER BY Priority');
        $priceToPackage = array();
        foreach ($packages as $package) {
            $priceToPackage[$package['id']] = $package;
            unset($priceToPackage[$package['id']]['id']);
        }

        return $this->render(
            'MetalUsersBundle:WizzardRegistration:registerThirdStep.html.twig',
            array(
                'form' => $form->createView(),
                'category' => $category,
                'priceToPackage' => $priceToPackage
            )
        );
    }
}
