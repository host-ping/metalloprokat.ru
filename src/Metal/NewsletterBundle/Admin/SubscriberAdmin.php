<?php

namespace Metal\NewsletterBundle\Admin;

use Doctrine\ORM\EntityManager;
use Knp\Menu\ItemInterface as MenuItemInterface;
use Metal\NewsletterBundle\Entity\Subscriber;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;

class SubscriberAdmin extends AbstractAdmin
{
    protected $datagridValues = array(
        '_sort_order' => 'DESC',
        '_sort_by' => 'createdAt'
    );

    /**
     * @var EntityManager
     */
    private $em;

    public function __construct($code, $class, $baseControllerName, EntityManager $em)
    {
        parent::__construct($code, $class, $baseControllerName);

        $this->em = $em;
    }

    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->remove('delete');
        $collection
            ->add('import', 'import', array('_controller' => 'MetalNewsletterBundle:AdminSubscriber:importSubscribers'))
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

        if (!in_array($action, array('edit'))) {
            return;
        }

        if ($this->getSubject()->getUser()) {
            $menu->addChild('Редактировать пользователя', array('uri' => $this->getConfigurationPool()->getAdminByAdminCode('metal.users.admin.user')->generateUrl('edit', array('id' => $this->getSubject()->getUser()->getId())) ));
        }

    }

    public function configure()
    {
        parent::configure();

        $this->setTemplate('list', 'MetalNewsletterBundle:SubscriberAdmin:list.html.twig');
    }

    protected function configureFormFields(FormMapper $formMapper)
    {
        $subscriber = $this->getSubject();
        /* @var $subscriber Subscriber */
        $formMapper
            ->add('email', null, array('label' => 'Email пользователя', 'disabled' => null !== $subscriber->getUser()))
            ->add('subscribedOnNews', 'checkbox', array('label' => 'Подписка на новости', 'required' => false))
            ->add('subscribedOnIndex', 'checkbox', array('label' => 'Подписка на металлиндекс', 'required' => false))
            ->add('subscribedForDemands', 'choice', array('label' => 'Подписка на заявки', 'required' => false, 'choices' => Subscriber::getDemandSubscriptionStatusesAsArray()))
            ->add('subscribedOnRecallEmails', 'checkbox', array('label' => 'Подписка на еженедельное напоминание актуализации цены', 'required' => false))
            ->add('subscribedOnDemandRecallEmails', 'checkbox', array('label' => 'Подписка на еженедельное повторное письмо, оставившим заявку', 'required' => false))
            ->add('subscribedOnProductsUpdate', 'checkbox', array('label' => 'Подписка на ежедневное уведомление о модерации продуктов', 'required' => false))
            ->add('isBounced', 'checkbox', array('label' => 'Письма не доходят', 'required' => false))
            ->add('bounceLog', 'textarea', array('attr' => array('rows' => 10), 'label' => 'Лог', 'required' => false, 'read_only' => true))
            ->add('deleted', 'checkbox', array('label' => 'Удален', 'required' => false))
        ;
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id')
            ->add('email', null, array('label' => 'Email пользователя'))
            ->add('createdAt', null, array('label' => 'Дата подписки'))
            ->add('updatedAt', null, array('label' => 'Дата обновления'))
            ->add('subscribedOnNews', null, array('label' => 'Подписка на новости'))
            ->add('subscribedOnIndex', null, array('label' => 'Подписка на металлиндекс'))
            ->add('subscribedForDemands', 'choice', array('label' => 'Подписка на заявки', 'choices' => Subscriber::getDemandSubscriptionStatusesAsArray()))
            ->add('demandsDigestSentAt', null, array('label' => 'Последний раз получал дайджест заявок'))
            ->add('subscribedOnRecallEmails', null, array('label' => 'Подписка на еженедельное напоминание актуализации цены'))
            ->add('subscribedOnDemandRecallEmails', null, array('label' => 'Подписка на еженедельное повторное письмо, оставившим заявку'))
            ->add('subscribedOnProductsUpdate', null, array('label' => 'Подписка на ежедневное уведомление о модерации продуктов'))
            ->add('source', null, array('label' => 'Источник'))
            ->add('deleted', null, array('label' => 'Удален'))
            ->add('isInvalid', null, array('label' => 'Некорректная электронная почта'))
            ->add('bouncedAt', 'boolean', array('label' => 'Письма не доходят'))
        ;
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $sources = $this->em->createQueryBuilder()
            ->select('s.source')
            ->from('MetalNewsletterBundle:Subscriber', 's', 's.source')
            ->groupBy('s.source')
            ->orderBy('s.source', 'ASC')
            ->getQuery()
            ->getResult();

        $sources = array_map(function ($source) {
                return $source['source'];
            }, $sources);

        $datagridMapper
            ->add('email', null, array('label' => 'Электронная почта'))
            ->add(
                'subscribedForDemands',
                'doctrine_orm_choice',
                array(
                    'label' => 'Подписка на заявки'
                ),
                'choice',
                array(
                    'choices' => Subscriber::getDemandSubscriptionStatusesAsArray()
                )
            )
            ->add('source', 'doctrine_orm_choice', array('label' => 'Источник'), 'choice', array('choices' => $sources))
            ->add('subscribedOnNews', null, array('label' => 'Подписка на новости'))
            ->add('subscribedOnIndex', null, array('label' => 'Подписка на металлиндекс'))
            ->add(
                'subscribedOnRecallEmails',
                null,
                array('label' => 'Подписка на еженедельное напоминание актуализации цены')
            )
            ->add(
                'subscribedOnDemandRecallEmails',
                null,
                array('label' => 'Подписка на еженедельное повторное письмо, оставившим заявку')
            )
            ->add('deleted', null, array('label' => 'Удален'))
            ->add('isInvalid', null, array('label' => 'Некорректный имейл'))
            ->add(
                'isBounced',
                'doctrine_orm_callback',
                array(
                    'label' => 'Письма не доходят',
                    'callback' => function ($queryBuilder, $alias, $field, $value) {
                        if (!isset($value['value'])) {
                            return;
                        }

                        if ($value['value'] === 'y') {
                            $queryBuilder->andWhere(sprintf("%s.bouncedAt IS NULL", $alias));
                        } else {
                            $queryBuilder->andWhere(sprintf("%s.bouncedAt IS NOT NULL", $alias));
                        }

                        return true;
                    }
                ),
                'choice',
                array(
                    'choices' => array(
                        'y' => 'Доходят',
                        'n' => 'Не доходят'
                    )
                )
            )
        ;
    }

    public function toString($object)
    {
        return $object instanceof Subscriber ? $object->getEmail() : '';
    }
}
