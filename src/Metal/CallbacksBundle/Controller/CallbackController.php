<?php

namespace Metal\CallbacksBundle\Controller;

use Doctrine\ORM\EntityManager;
use Metal\CallbacksBundle\Entity\Callback;
use Metal\CompaniesBundle\Entity\CompanyCity;
use Metal\ProjectBundle\Entity\ValueObject\SourceTypeProvider;
use Metal\CallbacksBundle\Form\CallbackType;
use Metal\ProjectBundle\Entity\ValueObject\SiteSourceTypeProvider;
use Metal\TerritorialBundle\Entity\City;
use Metal\UsersBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class CallbackController extends Controller
{
    public function saveAction(Request $request, $target_object, $id = null, $is_public = false, City $city = null)
    {
        $em = $this->getDoctrine()->getManager();
        /* @var $em EntityManager */

        $callback = new Callback();
        $demandItemTitle = null;

        switch ($target_object) {
            case 'category':
                $category = $em->getRepository('MetalCategoriesBundle:Category')->find($id);
                $callback->setCategory($category);
                $demandItemTitle = $category->getTitle();
                break;

            case 'demand':
                $demand = $em->getRepository('MetalDemandsBundle:Demand')->find($id);
                $callback->setCategory($demand->getCategory());
                $callback->setDemand($demand);
                if (!$city) {
                    $callback->setCity($demand->getCity());
                }
                break;

            case 'product':
                $product = $em->getRepository('MetalProductsBundle:Product')->find($id);
                $callback->setCategory($product->getCategory());
                $callback->setProduct($product);
                $demandItemTitle = $product->getTitle();
                if (!$city) {
                    $callback->setCity($product->getBranchOffice()->getCity());
                }
                break;

            case 'company':
                $company = $em->getRepository('MetalCompaniesBundle:Company')->find($id);
                $callback->setCompany($company);
                if (!$city) {
                    $callback->setCity($company->getCity());
                }
                break;
            case 'other':
                break;
            default:
                throw $this->createNotFoundException('Wrong kind');
        }

        $options = array(
           'for_product' => $request->query->get('for_product')
        );

        $user = null;
        if ($this->isGranted('ROLE_USER')) {
            $user = $this->getUser();
            /* @var $user User */
        }

        if ($user) {
            $callback->setPhone($user->getPhone());
        }

        $form = $this->createForm(new CallbackType(), $callback, $options);
        $form->handleRequest($request);

        if (!$form->isValid()) {
            return JsonResponse::create(array(
                'errors' => $this->get('metal.project.form_helper')->getFormErrorMessages($form)
            ));
        }

        if ($city) {
            $callback->setCity($city);
        }

        if ($user) {
            $user->setPhone($form->get('phone')->getData());
        }

        if ($categoryId = $request->query->get('category')) {
            $category = $em->getRepository('MetalCategoriesBundle:Category')->find($categoryId);
            $callback->setCategory($category);
            if (!$demandItemTitle) {
                $demandItemTitle = $category->getTitle();
            }
        }

        $callback->setKind($is_public ? Callback::CALLBACK_TO_MODERATOR : Callback::CALLBACK_TO_SUPPLIER);
        $callback->setCallFromType(SourceTypeProvider::createBySlug($request->query->get('from')));
        $callback->setUser($this->getUser());

        $em->persist($callback);
        $em->flush();

        $company = $callback->getCompany();

        if ($company && !$is_public) {
            $em->getRepository('MetalCompaniesBundle:CompanyCounter')->changeCounter($company, array('newCallbacksCount'), true);
        }

        $createDemand = $request->get('create_demand');
        if ($createDemand) {
            $demandData = array(
                'category' => $callback->getCategory(),
                'city' => $callback->getCity(),
                'phone' => $callback->getPhone(),
                'sourceTypeId' => SiteSourceTypeProvider::SOURCE_FROM_CALLBACK,
                'callback' => $callback
            );

            if ($demandItemTitle) {
                $demandData['demandItemTitle'] = $demandItemTitle;
            }

            $this->get('metal.demands.management_services')->createSimplePublicDemand($request, $demandData, $user);
        }

        if ($company && !$is_public) {
            $mainUser = $company->getCompanyLog()->getCreatedBy();
            $sendMessageToMainUser = $company->getMainUserAllSees();

            $companyConnectedUsers = $em->getRepository('MetalUsersBundle:User')->getEmployeesForTerritory($company, $request->attributes->get('territory'));

            $mailer = $this->get('metal.newsletter.mailer');

            if ($city) {
                $companyCity = $em->getRepository('MetalCompaniesBundle:CompanyCity')
                    ->findOneBy(array('company' => $company, 'city' => $city));

                /* @var $companyCity CompanyCity */
                if ($companyCity && $companyCity->getEmail()) {
                    $sendMessageToMainUser = $mainUser->getEmail() !== $companyCity->getEmail() && $company->getMainUserAllSees();
                    try {
                        $mailer->sendMessage(
                            'MetalCallbacksBundle::emails/callback_creation.html.twig',
                            array($companyCity->getEmail()),
                            array(
                                'callback' => $callback,
                                'user' => $user,
                                'employee' => null,
                                'company' => $companyCity->getCompany(),
                                'country' => $city->getCountry()
                            )
                        );

                    } catch (\Swift_RfcComplianceException $e) {

                    }
                }
            }

            foreach ($companyConnectedUsers as $companyConnectedUser) {
                if ($companyConnectedUser->isMainUserForCompany()) {
                    $sendMessageToMainUser = false;
                }
                try {
                    $mailer->sendMessage(
                        'MetalCallbacksBundle::emails/callback_creation.html.twig',
                        array($companyConnectedUser->getEmail() => $companyConnectedUser->getFullName()),
                        array(
                            'callback' => $callback,
                            'user' => $user,
                            'employee' => $companyConnectedUser,
                            'company' => $companyConnectedUser->getCompany(),
                            'country' => $companyConnectedUser->getCountry()
                        )
                    );
                } catch (\Swift_RfcComplianceException $e) {
                }
            }

            if ($sendMessageToMainUser) {
                try {
                    $mailer->sendMessage(
                        'MetalCallbacksBundle::emails/callback_creation.html.twig',
                        array($mainUser->getEmail() => $mainUser->getFullName()),
                        array(
                            'callback' => $callback,
                            'user' => $user,
                            'employee' => $mainUser,
                            'company' => $mainUser->getCompany(),
                            'country' => $mainUser->getCountry()
                        )
                    );
                } catch (\Swift_RfcComplianceException $e) {
                }
            }
        }

        return JsonResponse::create(array(
            'status' => 'success'
        ));
    }
}
