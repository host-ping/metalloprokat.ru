<?php

namespace Metal\UsersBundle\Admin;

use Doctrine\ORM\EntityManager;
use Knp\Menu\ItemInterface as MenuItemInterface;
use Metal\CompaniesBundle\Entity\Company;
use Metal\CompaniesBundle\Entity\ValueObject\ActionTypeProvider;
use Metal\UsersBundle\Entity\User;
use Metal\UsersBundle\Entity\UserHistory;
use Metal\UsersBundle\Entity\ValueObject\ActionTypeProvider as UserActionTypeProvider;
use Metal\UsersBundle\Repository\UserRepository;
use Metal\UsersBundle\Service\UserMailer;
use Metal\UsersBundle\Service\UserService;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\CoreBundle\Validator\ErrorElement;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Form\Type\VichImageType;

class UserAdmin extends AbstractAdmin
{
    protected $datagridValues = array(
        '_sort_order' => 'DESC',
        '_sort_by' => 'createdAt',
        '_page' => 1,
        '_per_page' => 25,
    );

    protected $formOptions = array(
        'validation_groups' => array(
            'Default',
            'change_email_admin'
        )
    );

    /**
     * The number of result to display in the list.
     *
     * @var int
     */
    protected $maxPerPage = 25;

    /**
     * Predefined per page options.
     *
     * @var array
     */
    protected $perPageOptions = array(15, 25, 50, 100, 150, 200);

    protected $parentAssociationMapping = 'company';

    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @var UserMailer
     */
    private $mailer;

    /**
     * @var EncoderFactoryInterface
     */
    private $encoderFactory;

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker;

    /**
     * @var UserService
     */
    private $userService;

    private $oldEmail = '';

    private $oldApproved;

    /**
     * @var Company
     */
    private $oldCompany;

    public function __construct(
        $code,
        $class,
        $baseControllerName,
        EntityManager $em,
        EncoderFactoryInterface $encoderFactory, UserMailer $mailer,
        TokenStorageInterface $tokenStorage,
        AuthorizationCheckerInterface $authorizationChecker,
        UserService $userService
    )
    {
        parent::__construct($code, $class, $baseControllerName);
        $this->em = $em;
        $this->userRepository = $em->getRepository('MetalUsersBundle:User');
        $this->encoderFactory = $encoderFactory;
        $this->mailer= $mailer;
        $this->userService = $userService;

        $this->tokenStorage = $tokenStorage;
        $this->authorizationChecker = $authorizationChecker;
    }

    public function getDatagrid()
    {
        if ($this->datagrid) {
            return $this->datagrid;
        }

        $datagrid = parent::getDatagrid();
        $users = $datagrid->getResults();
        /* @var $users User[] */
        $this->userRepository->attachSubscriberToUsers($users);
        $companiesIds = array();
        foreach ($users as $user) {
            if ($user->getCompany()) {
                $companiesIds[$user->getCompany()->getId()] = true;
            }
        }

        $this->em->getRepository('MetalCompaniesBundle:PaymentDetails')->synchronizePaymentDetails($companiesIds);

        return $this->datagrid;

    }

    protected function configureRoutes(RouteCollection $collection)
    {
        $collection
            ->remove('create')
            ->remove('delete')
            ->add('send_registration_email', $this->getRouterIdParameter().'/send_registration',
                array('_controller' => 'MetalUsersBundle:UserAdmin:sendRegistrationEmail')
            )
            ->add('send_recovery_password_email', $this->getRouterIdParameter().'/send_recovery_password',
                array('_controller' => 'MetalUsersBundle:UserAdmin:sendRecoveryPasswordEmail')
            )
            ->add('confirm_user_email', $this->getRouterIdParameter().'/confirm_user',
                array('_controller' => 'MetalUsersBundle:UserAdmin:confirmUserEmail')
            )
        ;
        parent::configureRoutes($collection);
    }

    protected function configureTabMenu(MenuItemInterface $menu, $action, AdminInterface $childAdmin = null)
    {
        if (null !== $childAdmin) {
            return;
        }

        if (!$this->getSubject()) {
            return;
        }

        if (!in_array($action, array('show', 'edit'))) {
            return;
        }

        $subscriberWithUser = $this->em->getRepository('MetalNewsletterBundle:Subscriber')->findOneBy(array('user' => $this->getSubject()));

        if ($subscriberWithUser) {
            $menu->addChild('Подписки', array('uri' => $this->getConfigurationPool()->getAdminByAdminCode('metal.newsletter.admin.subscriber')->generateUrl('edit', array('id' => $subscriberWithUser->getId()))));
        }

        if ($this->getSubject() && $this->getSubject()->getCompany()) {
            $menu->addChild('Редактировать компанию', array('uri' => $this->getConfigurationPool()->getAdminByAdminCode('metal.companies.admin.company')->generateUrl('edit', array('id' => $this->getSubject()->getCompany()->getId()))));
        }


        if ($this->authorizationChecker->isGranted('ROLE_ALLOWED_TO_SWITCH')) {
            $menu->addChild('Войти под', array(
                'uri' => $this->routeGenerator->generate('MetalPrivateOfficeBundle:Default:index',
                        array('_switch_user' => $this->getSubject()->getEmail()))));
        }

        if (!$this->getSubject()->getIsEmailConfirmed()) {
            $menu->addChild('Отправить письмо', array('uri' => $this->generateUrl('send_registration_email', array('id' => $this->getSubject()->getId()))));
        }

        $menu->addChild('Выслать письмо сброса пароля', array('uri' => $this->generateUrl('send_recovery_password_email', array('id' => $this->getSubject()->getId()))));

        $menu->addChild('Заявки', array(
            'uri' => $this->routeGenerator->generate('admin_metal_demands_abstractdemand_list', array('filter' => array('user' => array('value' => $this->getSubject()->getId()))))));
    }

    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->tab('Основное');

        if (!$this->getSubject()->getId()) {
            $formMapper
                ->add('fullName', 'text', array(
                        'label' => 'Имя и фамилия',
                        'required'  => true,
                        'constraints' => array(
                            new Assert\NotBlank(),
                        )
                    ));
        } else {
            $formMapper
                ->add('firstName', 'text', array(
                        'label' => 'Имя',
                        'required'  => false,
                    ))
                ->add('secondName', 'text', array(
                        'label' => 'Фамилия',
                        'required'  => false,
                    ));
        }

        $formMapper->add('city', null, array(
                'label' => 'Город',
                'class' => 'MetalTerritorialBundle:City',
                'property'=>'title',
                'required' => false,
                'placeholder' => '',
            ))
            ->add('email', null, array(
                'label' => 'Email (логин)',
                'required' => true,
            ))
            ->add('uploadedPhoto', VichImageType::class,
                array(
                    'label' => 'Фото пользователя',
                    'required' => false,
                    'imagine_pattern' => 'users_sq168',
                    'download_uri' => false,
                )
            )
            ->add('company', 'entity_id', array(
                'label' => 'ID Компании',
                'class' => 'MetalCompaniesBundle:Company',
                'required'  => false,
                'hidden'  => false,
            ))
            ->add('job', 'text', array(
                    'label' => 'Должность',
                    'required'  => false,
                ));

        if (!$this->getSubject()->getId()) {
            $formMapper->add('phone', null, array(
                'label' => 'Телефон',
                'required'  => true,
            ));
        } else {
            $formMapper->add('phone', null, array(
                'label' => 'Телефон',
                'required'  => false,
            ));
        }

        if ($this->getSubject()->getId()) {
            $formMapper->add('newPassword', 'repeated', array(
                    'invalid_message' => 'Пароли должны совпадать',
                    'required' => false,
                    'first_options'  => array('label' => 'Новый пароль (Не заполнять, если не хотите менять.)'),
                    'second_options' => array('label' => 'Повторить пароль'),
                    'constraints' => array(
                        new Assert\Length(array('min' => 6, 'max' => 20))
                    )
                ))
            ;

            $this->oldApproved = $this->getSubject()->isApproved();
        } else {
            $formMapper->add('newPassword', 'repeated', array(
                    'invalid_message' => 'Пароли должны совпадать',
                    'required' => true,
                    'first_options'  => array('label' => 'Новый пароль'),
                    'second_options' => array('label' => 'Повторить пароль'),
                    'constraints' => array(
                        new Assert\NotBlank(),
                        new Assert\Length(array('min' => 6, 'max' => 20))
                    )
                ))
            ;
        }

        $formMapper
            ->add('skype', 'text', array('label' => 'Skype', 'required' => false))
            ->add('icq', 'text', array('label' => 'Icq', 'required' => false))
        ;

        $roleEditable = $this->authorizationChecker->isGranted('ROLE_SUPER_ADMIN');
        $formMapper
            ->add(
                'additionalRoleId',
                'choice',
                array(
                    'label' => 'Роль',
                    'placeholder' => '',
                    'required' => false,
                    'choices' => User::getAvailableUserRoles(),
                    'disabled' => !$roleEditable,
                    'help' => 'Доступно для редактирования только пользователям с ролью "ROLE_SUPER_ADMIN"',
                )
            )
        ;

        if ($this->getSubject()->getCompany()) {
            $formMapper
                ->add('hasEditPermission', null, array('label' => 'Управление информацией в личном кабинете', 'required' => false))
                ->add('canUseService', null, array('label' => 'Право на использование сервисов', 'required' => false))
                ->add('approved', 'checkbox', array('label' => 'Право на вход в личный кабинет', 'required' => false))
                ->add('displayInContacts', 'checkbox', array('label' => 'Отображать на минисайте', 'required' => false))
                ->add('displayPosition', null, array('label' => 'Порядок вывода', 'required' => false, 'help' => 'Отображает порядок вывода сотрудников в контактах минисайта'))
            ;

            $this->oldCompany = $this->getSubject()->getCompany();
        }

        $formMapper
            ->end()
            ->end();

        $formMapper
            ->tab('Привязка к городам')
            ->add('displayOnlyInSpecifiedCities', null, array('label' => 'Отображать пользователя только в тех городах, в которых он указан', 'required' => false))
            ->add('userCities', 'sonata_type_collection',
                array(
                    'label' => 'Города',
                    'by_reference' => false,
                    'required' => false,
                    'constraints' => array(new Assert\Valid()),
                ),
                array(
                    'edit' => 'inline',
                    'inline' => 'table',
                ))
            ->end()
            ->end();

        if ($this->getSubject()->getId()) {
            if ($this->getSubject()->getIsEnabled()) {
                $tabDescription = 'Отключение пользователя';
                $fieldDescription = 'Причина отключения';
            } else {
                $tabDescription = 'Включение пользователя';
                $fieldDescription = 'Причина включения';
            }
            $formMapper
                ->tab($tabDescription)
                ->add('isEnabled',
                    null,
                    array(
                        'label' => 'Включен',
                        'required' => false
                    )
                )
                ->add(
                    'commentForIsEnabled',
                    'textarea',
                    array('label' => $fieldDescription, 'mapped' => false, 'required' => false)
                )
                ->end()
                ->end();
        }
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id')
            ->add(
                'fullName',
                null,
                array(
                    'label' => 'Полное имя'
                )
            )
            ->add('job', null, array('label' => 'Должность'))
            ->add('phone', null, array('label' => 'Телефон'))
            ->add('email', null, array('label' => 'Email'))
            ->add(
                'isEmailConfirmed',
                null,
                array('label' => 'Email подтвержден',
                'template' => 'MetalUsersBundle:UserAdmin:userEmailStatus.html.twig')
            )
            ->add('isEnabled', null, array('label' => 'Включен'))
            ->add('skype', null, array('label' => 'Skype'))
            ->add('icq', null, array('label' => 'Icq'))
            ->add(
                'company',
                null,
                array(
                    'label' => 'Компания',
                    'template' => 'MetalProjectBundle:Admin:company_with_id_list.html.twig'
                )
            )
            ->add('referer', null, array('label' => 'Реферер регистрации'))
            ->add(
                'actions',
                null,
                array(
                    'label' => 'Действия',
                    'template' => 'MetalUsersBundle:UserAdmin:userActions.html.twig'
                )
            )
        ;
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id', null, array('label' => 'ID'))
            ->add('firstName', null, array('label' => 'Имя'))
            ->add('secondName', null, array('label' => 'Фамилия'))
            ->add('email', null, array('label' => 'Email'))
            ->add('isEmailConfirmed', null, array('label' => 'Email подтвержден'))
            ->add(
                'approvedAt',
                'doctrine_orm_callback',
                array(
                    'label' => 'Право на вход в личный кабинет',
                    'callback' => function ($queryBuilder, $alias, $field, $value) {
                        if (!isset($value['value'])) {
                            return;
                        }
                        if ($value['value'] === 'y') {
                            $queryBuilder->andWhere(sprintf('%s.approvedAt IS NOT NULL', $alias));
                        } else {
                            $queryBuilder->andWhere(sprintf('%s.approvedAt IS NULL', $alias));
                        }

                        return true;
                    }
                ),
                'choice',
                array(
                    'choices' => array(
                        'y' => 'Да',
                        'n' => 'Нет'
                    )
                )
            )
            ->add('additionalRoleId',
                'doctrine_orm_choice',
                array(
                    'label' => 'Роль'
                ),
                'choice',
                array(
                    'required' => false,
                    'choices' => User::getAvailableUserRoles()
                )
            )
            ->add('isEnabled',
                null,
                array(
                    'label' => 'Включен'
                )
            )
        ;
    }

    public function preUpdate($user)
    {
        /* @var $user User */
        $original = $this->em->getUnitOfWork()->getOriginalEntityData($user);
        $author = $this->tokenStorage->getToken()->getUser();

        $companyService = $this->getConfigurationPool()->getContainer()->get('metal.companies.company_service');
        $companyHistory = null;
        if ($original['company'] && !$user->getCompany()) {
            $this->mailer->notifyOfAccessionToCompany($user, false, true, $original['company']);
            $companyHistory = $companyService->addCompanyHistory($original['company'], $author, ActionTypeProvider::DISCONNECT_USER);
            $companyHistory->setUser($user);
        } else if ($original['company'] && $user->getCompany() && $original['company']->getId() != $user->getCompany()->getId()) {
            $this->mailer->notifyOfConnectingToCompany($user, $original['company']);
            $companyHistory = $companyService->addCompanyHistory($user->getCompany(), $author, ActionTypeProvider::CONNECT_USER);
            $companyHistory->setUser($user);

            $user->setApproved(true);
        } else if (!$original['company'] && $user->getCompany()) {
            $this->mailer->notifyOfConnectingToCompany($user);
            $companyHistory = $companyService->addCompanyHistory($user->getCompany(), $author, ActionTypeProvider::CONNECT_USER);
            $companyHistory->setUser($user);

            $user->setApproved(true);
        }

        if ($companyHistory) {
            $this->em->flush($companyHistory);
        }

        // какие бы изменения не происходили с пользователем - обновляем статус его репликации
        $user->scheduleSynchronization();

        if ($original['company']) {
            // если у пользователя уже была компания - обновляем у нее статус репликации
            // даже если компания не менялась - ничего страшного, если мы у одной и той же компании дважды поставим true
            $original['company']->scheduleSynchronization();
        }

        if ($user->newPassword) {
            $encoder = $this->encoderFactory->getEncoder($user);
            $password = $encoder->encodePassword($user->newPassword, $user->getSalt());
            $user->setPassword($password);
            $this->mailer->notifyOfChangePassword($user);
        }

        if ($original['approvedAt'] !== $user->getApprovedAt()) {
            try {
                $this->mailer->notifyOfAccessionToCompany($user, $user->isApproved(), true);
            } catch (\Swift_RfcComplianceException $e) {
            }
        }

        if ($original['isEnabled'] != $user->getIsEnabled()) {

            $userHistory = new UserHistory();
            $userHistory->setAuthor($author);
            $userHistory->setUser($user);
            $userHistory->setComment($this->getForm()->get('commentForIsEnabled')->getData());
            $userHistory->setActionId($user->getIsEnabled() ? UserActionTypeProvider::ENABLED_STATUS : UserActionTypeProvider::DISABLED_STATUS);
            $this->em->persist($userHistory);
            $this->em->flush($userHistory);
        }

        $this->em->getConnection()->executeUpdate('
            UPDATE UserSend AS us
            SET us.deleted = :status
            WHERE us.user_id = :user_id',
            array('status' => !$user->getIsEnabled(), 'user_id' => $user->getId())
        );

        $this->oldEmail = $original['email'];
    }

    public function postUpdate($user)
    {
        /* @var $user User */

        if ($this->oldEmail !== $user->getEmail()) {
            // если подписчик с новым имейлом уже существует - удаляем его
            $subscriberService = $this->getConfigurationPool()->getContainer()->get('metal.newsletter.subscriber_service');
            $subscriberService->removeUnnecessarySubscriberForUser($user);
            $subscriberService->createOrUpdateSubscriberForUser($user);

            // если поменялся email добавляем это в userHistory
            $userHistory = new UserHistory();
            $userHistory->setActionId(UserActionTypeProvider::CHANGE_EMAIL);
            $userHistory->setAuthor($this->tokenStorage->getToken()->getUser());
            $userHistory->setUser($user);
            $userHistory->setComment($this->oldEmail);

            $this->em->persist($userHistory);
            $this->em->flush($userHistory);
        }

        // если отсоединяем пользователя от компании
        if ($this->oldCompany && !$user->getCompany()) {
            $user->setApproved(null);
        }

        $this->em->flush();
    }

    public function prePersist($object)
    {
        /* @var $object User */
        $object->setCountry($object->getCity()->getCountry());
        $object->cityTitle = $object->getCity()->getTitle();
        $this->userService->registerUser($object);
    }

    public function getSubject()
    {
        $user = parent::getSubject();
        /* @var $user User */

        if ($user && null === $user->getAttribute('author_history') && null === $user->getAttribute('user_history')) {
            $this->userRepository->attachHistoryToUser($user);
        }

        return $user;
    }

    public function getFormBuilder()
    {
        $this->formOptions['validation_groups'][] = 'admin_panel';

        return parent::getFormBuilder();
    }

    public function configure()
    {
        $this->setTemplate('edit', 'MetalUsersBundle:UserAdmin:edit.html.twig');
        $this->setTemplate('send_registration_email_view', 'MetalUsersBundle:UserAdmin:send_registration_email.html.twig');
    }

    public function validate(ErrorElement $errorElement, $object)
    {
        /* @var $object User */
        $original = $this->em->getUnitOfWork()->getOriginalEntityData($object);

        if (!$object->getCity() && !$object->getCompany()) {
            $errorElement
                ->with('city')
                ->addViolation('Город обязательный, потому что у пользователя нет компании')
                ->end();
        }

//        if ($original['company'] && $original['company']->getCompanyLog()->getCreatedBy()->getId() == $object->getId()) {
//            $errorElement
//                ->with('company')
//                ->addViolation('Перед удалением компании у пользователя нужно назначить другого пользователя главным для компании')
//                ->end();
//        }
    }

    public function toString($object)
    {
        return $object instanceof User ? sprintf('Пользователь %d', $object->getId()) : '';
    }
}
