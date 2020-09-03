<?php

namespace Metal\PrivateOfficeBundle\Controller;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Metal\PrivateOfficeBundle\Form\ChangePasswordType;
use Metal\ProjectBundle\Helper\ImageHelper;
use Metal\ProjectBundle\Util\RandomGenerator;
use Metal\UsersBundle\Entity\User;
use Metal\UsersBundle\Entity\UserTelegram;
use Metal\UsersBundle\Form\ChangeEmailType;
use Metal\UsersBundle\Form\PrivateUserType;
use Metal\UsersBundle\Form\UserPhotoType;
use Metal\UsersBundle\Telegram\Exception\ExpiredException;
use Metal\UsersBundle\Telegram\Exception\InvalidHashException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraint;

class PrivateAccountController extends Controller
{
    /**
     * @Security("has_role('ROLE_USER')")
     */
    public function viewAccountAction()
    {
        /** @var User $user */
        $user = $this->getUser();
        $form = $this->createForm(new PrivateUserType(), $user);
        $formUserPhoto = $this->createForm(new UserPhotoType(), $user);

        /** @var EntityManagerInterface $em */
        $em = $this->getDoctrine()->getManager();
        $userTelegramRepository = $em->getRepository(UserTelegram::class);
        $userTelegram = $userTelegramRepository->findOneBy(['user' => $user]);

        return $this->render(
            '@MetalPrivateOffice/PrivateAccount/view.html.twig',
            array(
                'form' => $form->createView(),
                'formUserPhoto' => $formUserPhoto->createView(),
                'user' => $user,
                'userTelegram' => $userTelegram,
            )
        );
    }

    /**
     * @Security("has_role('ROLE_USER') and has_role('ROLE_CONFIRMED_EMAIL')")
     */
    public function saveAction(Request $request)
    {
        /** @var EntityManagerInterface $em */
        $em = $this->getDoctrine()->getManager();

        /** @var User $user */
        $user = $this->getUser();

        $options['validation_groups'] = array('edit_profile', Constraint::DEFAULT_GROUP);

        if ($this->container->getParameter('project.require_user_phone')) {
            $options['validation_groups'][] = 'phone';
        }

        $form = $this->createForm(new PrivateUserType(), $user, $options);

        $form->handleRequest($request);

        if (!$form->isValid()) {
            $errors = $this->get('metal.project.form_helper')->getFormErrorMessages($form);

            return JsonResponse::create(
                array(
                    'errors' => $errors,
                )
            );
        }

        $user->scheduleSynchronization();

        $em->flush();

        return JsonResponse::create(
            array(
                'status' => 'success',
            )
        );
    }

    /**
     * @Security("has_role('ROLE_USER') and has_role('ROLE_CONFIRMED_EMAIL')")
     */
    public function changePasswordAction(Request $request)
    {
        /** @var EntityManagerInterface $em */
        $em = $this->getDoctrine()->getManager();

        /** @var User $user */
        $user = $this->getUser();
        $form = $this->createForm(
            new ChangePasswordType(),
            $user,
            array('validation_groups' => array('change_password'))
        );
        $form->handleRequest($request);

        if (!$form->isValid()) {
            $errors = $this->get('metal.project.form_helper')->getFormErrorMessages($form);

            return JsonResponse::create(
                array(
                    'errors' => $errors,
                )
            );
        }

        $this->get('metal.users.user_service')->updatePassword($user);

        $em->flush();

        return JsonResponse::create(
            array(
                'status' => 'success',
            )
        );
    }

    /**
     * @Security("has_role('ROLE_USER')")
     */
    public function changeEmailAction(Request $request)
    {
        /** @var EntityManagerInterface $em */
        $em = $this->getDoctrine()->getManager();

        /** @var User $user */
        $user = $this->getUser();

        $form = $this->createForm(new ChangeEmailType(), $user, array('validation_groups' => array('change_email')));
        $form->handleRequest($request);

        if (!$form->isValid()) {
            $errors = $this->get('metal.project.form_helper')->getFormErrorMessages($form);

            return JsonResponse::create(
                array(
                    'errors' => $errors,
                )
            );
        }

        if (!$user->getChangeEmailCode()) {
            $user->setChangeEmailCode(RandomGenerator::generateRandomCode());
        }

        $em->flush();

        $mailer = $this->get('metal.newsletter.mailer');
        try {
            $mailer->sendMessage(
                'MetalUsersBundle::emails/change_email.html.twig',
                $user->getNewEmail(),
                array(
                    'user' => $user,
                    'country' => $user->getCountry(),
                ),
                $this->container->getParameter('mailer_from_account')
            );
        } catch (\Swift_RfcComplianceException $e) {

        }

        return JsonResponse::create(
            array(
                'status' => 'success',
            )
        );
    }

    /**
     * @Security("has_role('ROLE_USER') and not has_role('ROLE_CONFIRMED_EMAIL')")
     */
    public function resendConfirmationMailAction()
    {
        /** @var User $user */
        $user = $this->getUser();

        $this->get('metal.users.users_mailer')->notifyOnRegistration($user);

        return JsonResponse::create(
            array(
                'status' => 'success',
            )
        );
    }

    /**
     * @Security("has_role('ROLE_USER') and has_role('ROLE_CONFIRMED_EMAIL')")
     */
    public function saveUserPhotoAction(Request $request)
    {
        /** @var EntityManagerInterface $em */
        $em = $this->getDoctrine()->getManager();

        $imageHelper = $this->container->get('brouzie.helper_factory')->get('MetalProjectBundle:Image');
        /* @var $imageHelper ImageHelper */

        /** @var User $user */
        $user = $this->getUser();

        $form = $this->createForm(new UserPhotoType(), $user);
        $form->handleRequest($request);

        if (!$form->isValid()) {
            return JsonResponse::create(
                array(
                    'errors' => $this->get('metal.project.form_helper')->getFormErrorMessages($form),
                )
            );
        }

        $user->setAvatarName(
            $user->getPhoto()->getOriginalName().':'.$user->getPhoto()->getMimeType().':'.$user->getPhoto()->getSize()
        );

        $em->flush();

        $avatarUrl = $imageHelper->getAvatarUrl($user, 'sq168');

        return JsonResponse::create(
            array(
                'status' => 'success',
                'avatar' =>
                    array(
                        'url' => $avatarUrl,
                    ),
            ),
            200,
            // set Content-Type header for IE
            array('Content-Type' => 'text/plain')
        );
    }

    /**
     * @Security("has_role('ROLE_USER') and has_role('ROLE_CONFIRMED_EMAIL')")
     */
    public function deleteAvatarAction()
    {
        //TODO: handle csrf
        /** @var EntityManagerInterface $em */
        $em = $this->getDoctrine()->getManager();

        $user = $this->getUser();
        /* @var $user User */

        if ($user->getPhoto()->getName()) {
            $this->get('vich_uploader.upload_handler')->remove($user, 'uploadedPhoto');

            $em->flush();
        }

        return JsonResponse::create(
            [
                'status' => 'success',
            ]
        );
    }

    /**
     * @Security("has_role('ROLE_USER')")
     */
    public function connectTelegramAction(Request $request)
    {
        $connector = $this->get('metal.users.telegram.connector');

        try {
            $connectTelegram = $connector->checkTelegramAuthorization($request->query->all());
        } catch (InvalidHashException $e) {
            return JsonResponse::create(
                [
                    'status' => 'error',
                    'message' => 'Некорректный хеш.',
                ]
            );
        } catch (ExpiredException $e) {
            return JsonResponse::create(
                [
                    'status' => 'error',
                    'message' => 'Сессия истекла.',
                ]
            );
        }

        /** @var EntityManagerInterface $em */
        $em = $this->get('doctrine')->getManager();
        $userTelegramRepository = $em->getRepository(UserTelegram::class);

        $existentTgUser = $userTelegramRepository->findOneBy(['telegramUserId' => $connectTelegram->getId()]);

        if ($existentTgUser) {
            return JsonResponse::create(
                [
                    'status' => 'error',
                    'message' => sprintf(
                        'Данный Телеграм-аккаунт уже привязан к другому пользователю ("%s").',
                        $existentTgUser->getUser()->getEmail()
                    ),
                ]
            );
        }

        /** @var User $user */
        $user = $this->getUser();

        $alreadyConnectedTgUser = $userTelegramRepository->findOneBy(['user' => $user]);
        if ($alreadyConnectedTgUser) {
            $em->remove($alreadyConnectedTgUser);
        }

        $tgUser = new UserTelegram($user, $connectTelegram);

        $em->persist($tgUser);
        $em->flush();

        return JsonResponse::create(
            [
                'status' => 'succcess',
                'message' => 'Телеграм-аккаунт успешно ассоциирован с Вашей ученой записью.',
            ]
        );
    }

    //TODO: сделать возможность отвязывать учетку телеграма
}
