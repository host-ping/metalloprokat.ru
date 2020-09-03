<?php

namespace Metal\UsersBundle\Controller;

use Doctrine\DBAL\DBALException;
use Doctrine\ORM\EntityManager;

use Metal\CategoriesBundle\Entity\Category;
use Metal\CompaniesBundle\Entity\Company;
use Metal\CompaniesBundle\Entity\ValueObject\ActionTypeProvider;
use Metal\UsersBundle\Entity\UserRegisteredInviteProject;
use Metal\UsersBundle\Entity\ValueObject\ActionTypeProvider as UserActionTypeProvider;
use Metal\CompaniesBundle\Helper\DefaultHelper;
use Metal\ProjectBundle\Entity\LandingTemplate;
use Metal\TerritorialBundle\Entity\Country;
use Metal\UsersBundle\Entity\User;
use Metal\UsersBundle\Entity\UserHistory;
use Metal\UsersBundle\Form\UserType;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class RegistrationController extends Controller
{
    public function getUserInfoForInviteProjectAction(Request $request)
    {
        $userId = (int)$request->query->get('uid');
        $sig = (string)$request->query->get('sig');

        if (!$userId || !$sig || $sig !== sha1($userId.$this->container->getParameter('secret'))) {
            return JsonResponse::create(
                array(
                    'status' => 'error'
                )
            );
        }

        $em = $this->getDoctrine()->getManager();
        /* @var $em EntityManager */

        $userInfo = $em->createQueryBuilder()
            ->select('IDENTITY(user.city) AS city_id')
            ->addSelect('user.phone')
            ->addSelect('user.secondName')
            ->addSelect('user.firstName')
            ->addSelect('user.email')
            ->addSelect('company.title AS company_title')
            ->addSelect('company.companyTypeId AS company_type')
            ->from('MetalUsersBundle:User', 'user')
            ->join('user.company', 'company')
            ->where('user.id = :userId')
            ->setParameter('userId', $userId)
            ->getQuery()
            ->getOneOrNullResult()
        ;

        if (!$userInfo) {
            return JsonResponse::create(
                array(
                    'status' => 'error'
                )
            );
        }

        return JsonResponse::create(
            array(
                'status' => 'success',
                'user_info' => array(
                    'company_title' => $userInfo['company_title'],
                    'company_type' => $userInfo['company_type'],
                    'city_id' => $userInfo['city_id'],
                    'phone' => $userInfo['phone'],
                    'full_name' => $userInfo['firstName']. ' '. $userInfo['secondName'],
                    'email' => $userInfo['email'],
                )
            )
        );
    }

    /**
     * @ParamConverter("category", class="MetalCategoriesBundle:Category", options={"id" = "category_id"}, isOptional=true)
     */
    public function registerAction(Request $request, Country $country, Category $category = null)
    {
        if ($this->isGranted('ROLE_USER')) {
            return new RedirectResponse($request->getUriForPath('/'));
        }

        $em = $this->getDoctrine()->getManager();
        /* @var $em EntityManager */

        $user = new User();
        $user->setCountry($country);

        if (!$request->isMethod('POST')) {
            if ($cityId = $request->query->get('city')) {
                $city = $em->find('MetalTerritorialBundle:City', $cityId);
                if ($city) {
                    $user->cityTitle = $city->getTitle();
                    $user->setCity($city);
                }
            }
        }

        $form = $this->createForm(new UserType($this->container->getParameter('project.require_user_phone')), $user, array(
            'city_repository' => $em->getRepository('MetalTerritorialBundle:City'),
            'supplier_token' => $this->container->getParameter('tokens.registration_supplier_profile'),
            'consumer_token' => $this->container->getParameter('tokens.registration_consumer_profile'),
            'promocode_enabled' => $this->container->getParameter('project.promocode_enabled'),
            '_redirect_to' => $request->query->get('_redirect_to')
        ));

        $this->populateFormFromInviter($request, $form);

        $landingTemplate = null;
        if ($category) {
            $landingTemplate = $em->getRepository('MetalProjectBundle:LandingTemplate')->findOneBy(array('id' => $category->getId()));
        }

        if (!$request->isMethod('POST')) {
            $randomReview = $em->getRepository('MetalCorpsiteBundle:ClientReview')->getRandomClientReview();

            return $this->render(
                'MetalUsersBundle:Registration:register.html.twig',
                array(
                    'form' => $form->createView(),
                    'category' => $category,
                    'landingTemplate' => $landingTemplate,
                    'randomReview' => $randomReview
                )
            );
        }

        $form->handleRequest($request);

        $email = $user->getEmail();

        if (!$form->isValid()) {
            $errors = $this->get('metal.project.form_helper')->getFormErrorMessages($form);
            //TODO: возможно это разрулить более оптимально. задать в UniqueEntity в User какой-то параметр или еще как-то
            // @see https://github.com/symfony/symfony/issues/5525
            $errorType = 'metal_usersbundle_user[email]';

            if (isset($errors[$errorType])) {
                $errorText = $errors[$errorType][0];
                $defaultErrorMailText = 'Значение адреса электронной почты недопустимо.';
                if ($errorText != $defaultErrorMailText && $email != '') {
                    return JsonResponse::create(array(
                        'errors' => array(),
                        'html' => $this->renderView('MetalUsersBundle:Registration:registeredBefore.html.twig', array('email' => $email))));
                }
            }

            return JsonResponse::create(array(
                'errors' => $errors,
            ));
        }

        $companyName = $form->get('companyTitle')->getData();
        $selectedCompany = $form->get('company')->getData();
        $checkDuplication = $form->get('checkDuplication')->getData();
        $isTrader = $form->get('userType')->getData() == User::TRADER;

        $user->setReferer($request->getSession()->get('registration_referer') ?: $request->headers->get('REFERER'));

        $companyType = $form->get('companyType')->getData();
        $city = $form->get('city')->getData();

        if ($isTrader && $checkDuplication) {
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

        $promocodeEnabled = $this->container->getParameter('project.promocode_enabled');
        $company = null;
        if ($isTrader) {
            if ($selectedCompany) {
                $company = $selectedCompany;
            } else {
                $company = new Company();
            }

            $companyHelper = $this->container->get('brouzie.helper_factory')->get('MetalCompaniesBundle');
            /* @var $companyHelper DefaultHelper */
            if ($promocodeEnabled && $promocode = $form->get('promocode')->getData()) {
                $promocodeStatus = $companyHelper->validatePromocode($company, $promocode);
                if (isset($promocodeStatus['error_message'])) {
                    $errors = array($form->get('promocode')->createView()->vars['full_name'] => array($promocodeStatus['error_message']));

                    return JsonResponse::create(array('errors' => $errors));
                }
            }
        }

        $this->get('metal.users.user_service')->registerUser($user);

        // если поставщик, то создаем новую компанию
        if ($isTrader) {
            $registrationService = $this->container->get('metal.users.registration_services');

            if (!$selectedCompany instanceof Company) {
                $registrationService->createCompany($company, $user, $user->getPhone(), $companyName, $companyType, $city);
                $this->container->get('metal.companies.company_service')->addCompanyHistory($company, $user, ActionTypeProvider::COMPANY_CREATION);
            } else {
                $registrationService->connectCompany($company, $user);
            }

            if ($promocodeEnabled && $promocode = $form->get('promocode')->getData()) {
                $registrationService->applyPromocodeToCompany($company, $promocode);
            }
        }

        $em->flush();

        //TODO: использовать rememberme когда соответствующий тикет исправят https://github.com/symfony/symfony/issues/3137

        $this->get('security.token_storage')->setToken(
            new UsernamePasswordToken(
                $user,
                $user->getPassword(),
                $this->container->getParameter('security_main_firewall'),
                $user->getRoles()
            )
        );

        $redirectTo = $this->generateUrl('MetalPrivateOfficeBundle:Default:index');

        if ($user->getCompany()) {
            if ($category) {
                $redirectTo = $this->generateUrl(
                    'MetalUsersBundle:WizzardRegistration:registerSecondStep',
                    array('category_id' => $category->getId())
                );
            } else {
                $redirectTo = $this->generateUrl('MetalPrivateOfficeBundle:Company:edit');
            }
        }

        $redirectTo = $form->get('_redirect_to')->getData() ?: $redirectTo;

        if ($isTrader && $this->container->getParameter('project.family') === 'metalloprokat') {
            $inviteUrl = $this->generateUrl(
                'MetalUsersBundle:Registration:redirectToRegistrationInviteProject',
                array(
                    'uid' => $user->getId(),
                    'sig' => sha1($user->getId().$this->container->getParameter('secret'))
                )
            );

            return JsonResponse::create(
                array(
                    'errors' => array(),
                    'html' => $this->renderView(
                        'MetalUsersBundle:Registration:inviteRegistration.html.twig',
                        array(
                            'redirect_to_after_registration' => $redirectTo,
                            'invite_url' => $inviteUrl
                        )
                    )
                )
            );
        }

        if ($form->get('is_registered_invite_project')->getData() === '1') {
            $userRegisteredInviteProject = new UserRegisteredInviteProject();
            $userRegisteredInviteProject->setUser($user);
            $em->persist($userRegisteredInviteProject);
            $em->flush();
        }

        return JsonResponse::create(array('status' => 'success', 'redirect_to' => $redirectTo));
    }

    public function confirmationAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        /* @var $em EntityManager */
        $code = $request->query->get('code');
        $id = $request->query->get('id');

        if ($user = $em->getRepository('MetalUsersBundle:User')->findOneBy(array('id' => $id))) {
            /* @var $user User */
            if ($user->getRegistrationCode() == $code) {
                $user->setIsEmailConfirmed(true);
                $user->setRegistrationCode('');

                $subscriberService = $this->container->get('metal.newsletter.subscriber_service');
                $subscriberService->removeUnnecessarySubscriberForUser($user);
                $subscriberService->createOrUpdateSubscriberForUser($user);

                $em->flush();
            } else {
                die('Неверный код подтверждения');
            }
        } else {
            die('Пользователь не найден');
        }

        $redirectTo = $this->generateUrl('MetalPrivateOfficeBundle:Default:index');
        if ($user->getCompany() && $user->getHasEditPermission()) {
            $redirectTo = $this->generateUrl('MetalPrivateOfficeBundle:Company:edit');
        }

        return $this->redirect($redirectTo);
    }

    /**
     * @ParamConverter("user", class="MetalUsersBundle:User")
     */
    public function confirmNewEmailAction(User $user, $code)
    {
        $em = $this->getDoctrine()->getManager();
        /* @var $em EntityManager */
        if ($user->getChangeEmailCode() != $code) {
            die('Неверный код подтверждения');
        }

        $newEmail = $user->getNewEmail();
        $oldEmail = $user->getEmail();

        $user->setEmail($newEmail);
        $user->setIsEmailConfirmed(true);
        $user->setNewEmail(null);
        $user->setChangeEmailCode(null);

        try {
            $em->flush();
        } catch (DBALException $e) {
            die('Невозможно сменить email! Пользователь с таким email уже существует');
        }

        $subscriberService = $this->container->get('metal.newsletter.subscriber_service');
        $subscriberService->removeUnnecessarySubscriberForUser($user, $newEmail);
        $subscriberService->createOrUpdateSubscriberForUser($user);
        $em->flush();

        $userHistory = new UserHistory();
        $userHistory->setActionId(UserActionTypeProvider::CHANGE_EMAIL);
        $userHistory->setAuthor($user);
        $userHistory->setUser($user);
        $userHistory->setComment($oldEmail);

        $em->persist($userHistory);
        $em->flush($userHistory);

        return $this->redirect($this->generateUrl('MetalPrivateOfficeBundle:Account:view'));
    }

    private function populateFormFromInviter(Request $request, FormInterface $form)
    {
        $uid = $request->query->get('uid');
        $sig = $request->query->get('sig');

        if (!$request->isMethod('POST') && $uid && $sig) {
            $browser = $this->container->get('buzz')->getBrowser('verify_inviter');
            $resource = sprintf('?uid=%d&sig=%s&utm_source=%s', $uid, $sig, 'invite_metalloprokat');
            $browser->getClient()->setTimeout(10);
            $verifyResponse = $browser->get($resource)->getContent();

            $userInfo = json_decode($verifyResponse, true);
            if (isset($userInfo['status']) && 'success' === $userInfo['status']) {
                $city = $this->getDoctrine()->getManager()->find('MetalTerritorialBundle:City', $userInfo['user_info']['city_id']);
                $form->get('companyTitle')->setData($userInfo['user_info']['company_title']);
                $form->get('phone')->setData($userInfo['user_info']['phone']);
                $form->get('fullName')->setData($userInfo['user_info']['full_name']);
                $form->get('email')->setData($userInfo['user_info']['email']);
                $form->get('userType')->setData(User::TRADER);
                $form->get('city')->setData($city);
                $form->get('cityTitle')->setData($city->getTitle());
                $form->get('companyType')->setData($userInfo['user_info']['company_type']);
                $form->get('is_registered_invite_project')->setData(true);
            }
        }
    }
}
