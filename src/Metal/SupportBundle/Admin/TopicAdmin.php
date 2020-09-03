<?php

namespace Metal\SupportBundle\Admin;

use Metal\SupportBundle\Entity\Topic;

use Metal\UsersBundle\Repository\UserRepository;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @method Topic getSubject()
 */
class TopicAdmin extends AbstractAdmin
{
    protected $datagridValues = array(
        'resolvedAt' => array(
            'value' => 'n',
        ),
        '_sort_order' => 'DESC',
        '_sort_by' => 'lastAnswerAt',
        '_page' => 1,
        '_per_page' => 25,
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
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    public function __construct($code, $class, $baseControllerName, TokenStorageInterface $tokenStorage)
    {
        parent::__construct($code, $class, $baseControllerName);

        $this->tokenStorage = $tokenStorage;
    }

    public function configure()
    {
        $this->setTemplate('show', 'MetalSupportBundle:TopicAdmin:show_answers.html.twig');
    }

    protected function configureShowFields(ShowMapper $showMapper)
    {
        $topic = $this->getSubject();

        if ($topic->getTitle()) {
            $showMapper
                ->add('title', null, array('label' => 'Заголовок'));
        }

        $showMapper
            ->add('description', 'textarea', array('label' => 'Описание'));

        if ($topic->getResolvedBy()) {
            $showMapper
                ->add(
                    'resolvedBy',
                    null,
                    array(
                        'label' => 'Кем решена',
                        'template' => 'MetalProjectBundle:Admin:resolved_by_topic_show.html.twig'
                    )
                );
        }

        if ($topic->getResolvedAt()) {
            $showMapper
                ->add(
                'resolvedAt',
                null,
                array('label' => 'Дата решения')
            );
        }

        $showMapper->add('topicStatus', 'choice', array('label' => 'Состояние', 'choices' => Topic::getTopicStatusAliases()));

        $showMapper
            ->add(
                'sentFrom',
                'choice',
                array(
                    'label' => 'Отправлена с',
                    'choices' => Topic::getSentFromAliases()
                )
            );

        if ($topic->getCompany()) {
            $showMapper
                ->add(
                    'company',
                    'string',
                    array(
                        'label' => 'Компания',
                        'template' => 'MetalProjectBundle:Admin:company_with_id_show.html.twig'
                    )
                )
                ->add(
                    'company.manager',
                    null,
                    array('label' => 'Менеджер', 'template' => 'MetalProjectBundle:Admin:manager_topic_show.html.twig')
                )
            ;
        }

        $showMapper
            ->add(
                'author',
                null,
                array('label' => 'Автор', 'template' => 'MetalProjectBundle:Admin:author_topic_show.html.twig')
            )
            ->add(
                'email',
                null,
                array('label' => 'Email', 'template' => 'MetalProjectBundle:Admin:email_topic_show.html.twig')
            )
            ->add(
                'createdAt',
                null,
                array('label' => 'Дата создания')
            )
        ;
    }

    protected function configureFormFields(FormMapper $formMapper)
    {
        $company = $this->getSubject()->getCompany();

        $formMapper
            ->add('title', null, array('label' => 'Заголовок'))
            ->add(
                'description',
                'textarea',
                array('label' => 'Описание', 'required' => false, 'attr' => array('style' => 'width:100%; height: 250px;'))
            )
            ->add('receiver', 'entity', array(
                'label' => 'Какой сотрудник увидит обращение',
                'help' => 'Директор компании первый в списке.',
                'class' => 'MetalUsersBundle:User',
                'placeholder' => '',
                'property' => 'fullName',
                'query_builder' => function (UserRepository $repository) use ($company) {
                    return $repository->createQueryBuilder('u')
                        ->addSelect('(CASE WHEN u.id = d.id THEN 1 ELSE 0 END) AS HIDDEN equal')
                        ->join('u.company', 'c', 'u.company = company.id')
                        ->join('c.companyLog', 'cl')
                        ->join('cl.createdBy', 'd')
                        ->andWhere('u.company = :company')
                        ->andWhere('u.isEnabled = true')
                        ->orderBy('equal', 'DESC')
                        ->setParameter('company', $company);
                },
                'required' => true,
                'constraints' => array(new Assert\NotBlank())
            ))
        ->add('resolved', 'checkbox', array('label' => 'Решена', 'required' => false));
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id', null, array('route' => array('name' => 'show')))
            ->add('title', null, array('label' => 'Название темы'))
            ->add('company', null, array('label' => 'Компания', 'template' => 'MetalProjectBundle:Admin:company_with_id_list.html.twig'))
            ->add('author', null, array('label' => 'Пользователь', 'template' => 'MetalProjectBundle:Admin:author_topic_list.html.twig'))
            ->add(
                'sentFrom',
                'choice',
                array(
                    'label' => 'Отправлена с',
                    'choices' => Topic::getSentFromAliases()
                )
            )
            ->add('createdAt', null, array('label' => 'Дата создания'))
            ->add('lastAnswerAt', null, array('label' => 'Дата последнего ответа'))
            ->add('resolvedAt', null,array('label' => 'Дата решение проблемы'))
            ->add('resolvedBy', null,array('label' => 'Кем решена', 'template' => 'MetalSupportBundle:TopicAdmin:userInfo.html.twig'))
            ->add(
                'answers',
                null,
                array(
                    'label' => 'Действия',
                    'template' => 'MetalSupportBundle:TopicAdmin:answers.html.twig'
                )
            )
        ;
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('company', 'doctrine_orm_number', array('label' => 'Компания'))
            ->add(
                'sentFrom',
                'doctrine_orm_choice',
                array(
                    'label' => 'Отправлено с',
                ),
                'choice',
                array(
                    'choices' => Topic::getSentFromAliases()
                )
            )
            ->add(
                'lastAnswerId',
                'doctrine_orm_callback',
                array(
                    'label' => 'Темы с последним ответом',
                    'callback' => function ($queryBuilder, $alias, $field, $value) {
                        if (!isset($value['value'])) {
                            return;
                        }

                        if ($value['value'] == 'y') {
                            $queryBuilder->leftJoin(sprintf('%s.lastAnswer', $alias), 'a');
                            $queryBuilder->leftJoin(sprintf('%s.author', $alias), 'u');
                            $queryBuilder->andWhere('u.additionalRoleId = :role');
                            $queryBuilder->setParameter('role', 0);
                        } elseif ($value['value'] == 'm') {
                            $queryBuilder->leftJoin(sprintf('%s.lastAnswer', $alias), 'a');
                            $queryBuilder->leftJoin(sprintf('%s.author', $alias), 'u');
                            $queryBuilder->andWhere('u.additionalRoleId > :role');
                            $queryBuilder->setParameter('role', 0);
                        } else  {
                            $queryBuilder->andWhere(sprintf('%s.lastAnswer IS NULL', $alias));
                        }

                        return true;
                    }
                ),
                'choice',
                array(
                    'choices' => array(
                        'y' => 'От пользователя',
                        'm' => 'От модератора',
                        'n' => 'Без ответа'
                    )
                )
            )
            ->add(
                'resolvedAt',
                'doctrine_orm_callback',
                array(
                    'label' => 'Решенность',
                    'callback' => function ($queryBuilder, $alias, $field, $value) {
                        if (!isset($value['value'])) {
                            return;
                        }
                        if ($value['value'] == 'y') {
                            $queryBuilder->andWhere(sprintf('%s.resolvedAt IS NOT NULL', $alias));
                        } else {
                            $queryBuilder->andWhere(sprintf('%s.resolvedAt IS NULL', $alias));
                        }

                        return true;
                    }
                ),
                'choice',
                array(
                    'choices' => array(
                        'y' => 'Решена',
                        'n' => 'Не решена'
                    )
                )
            )
        ;
    }

    public function prePersist($object)
    {
        /* @var $object Topic */
        $object->setAuthor($this->tokenStorage->getToken()->getUser());
        $this->setAdditionalFields($object);
    }

    public function postPersist($object)
    {
        $this->notifyEmployee($object);
    }

    public function preUpdate($object)
    {
        /* @var $object Topic */
        if ($object->isResolved()) {
            if (!$object->getResolvedBy()) {
                $object->setResolvedBy($this->tokenStorage->getToken()->getUser());
            }
        } else {
            $object->setResolved(false);
        }
    }

    public function postUpdate($object)
    {
        $this->notifyEmployee($object);
    }

    private function setAdditionalFields(Topic $object)
    {
        $company = $this->getSubject()->getCompany();

        if (!$company) {
            return;
        }

        $object->setCompany($company);
        $object->setSentFrom(Topic::SOURCE_ADMIN);
    }

    private function notifyEmployee(Topic $object)
    {
        if ($object->getSentFrom() !== Topic::SOURCE_ADMIN) {
            return;
        }

        $mailer =$this->getConfigurationPool()->getContainer()->get('metal.newsletter.mailer');
        $company = $object->getCompany();
        try {
            $mailer->sendMessage(
                '@MetalSupport/emails/topic_from_admin_to_director.html.twig',
                array($object->getReceiver()->getEmail() => $object->getReceiver()->getFullName()),
                array(
                    'senderUserName' => $object->getAuthor()->getFullName(),
                    'senderEmail' => $object->getAuthor()->getEmail(),
                    'textMessage' => $object->getDescription(),
                    'topicId' => $object->getId(),
                    'country' => $company->getCountry(),
                    'isPrivate' => true
                )
            );
        } catch (\Swift_RfcComplianceException $e) {
        }
    }

    public function toString($object)
    {
        return $object instanceof Topic ? $object->getTitle() : '';
    }
}
