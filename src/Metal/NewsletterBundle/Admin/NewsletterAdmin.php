<?php

namespace Metal\NewsletterBundle\Admin;

use Doctrine\ORM\EntityManager;
use Metal\NewsletterBundle\Entity\Newsletter;
use Metal\NewsletterBundle\Repository\NewsletterRepository;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;

class NewsletterAdmin extends AbstractAdmin
{
    protected $datagridValues = array(
        '_sort_order' => 'DESC',
        '_sort_by' => 'createdAt'
    );

    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var NewsletterRepository
     */
    private $newsletterRepository;

    public function __construct($code, $class, $baseControllerName, EntityManager $em)
    {
        parent::__construct($code, $class, $baseControllerName);

        $this->em = $em;
        $this->newsletterRepository = $em->getRepository('MetalNewsletterBundle:Newsletter');
    }

    public function configureRoutes(RouteCollection $collection)
    {
        $collection->remove('delete');
    }

    public function getBatchActions()
    {
        $actions['importSubscribers'] = array(
            'label'            => 'Импортировать получателей',
            'ask_confirmation' => true
        );
        $actions = array_merge($actions, parent::getBatchActions());

        return $actions;
    }

    public function preUpdate($object)
    {
        $object->setUpdatedAt(new \DateTime());
    }

    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('title', null, array('label' => 'Заголовок'))
            ->add('startAt', 'sonata_type_datetime_picker', array('label' => 'Стартует', 'format' => 'dd.MM.yyyy HH:mm:ss'))
            ->add('template', null, array('label' => 'Путь к файлу'))
        ;
    }

    public function getDatagrid()
    {
        if ($this->datagrid) {
            return $this->datagrid;
        }

        $datagrid = parent::getDatagrid();
        $newsletters = $datagrid->getResults();
        $this->newsletterRepository->attachStatisticsToNewsletter($newsletters);

        return $this->datagrid;
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id')
            ->add('title', null, array('label' => 'Заголовок'))
            ->add('createdAt', null, array('label' => 'Создана'))
            ->add('updatedAt', null, array('label' => 'Обновлена'))
            ->add('startAt', null, array('label' => 'Стартует'))
            ->add('processedAt', null, array('label' => 'Обработана'))
            ->add(
                'statistic',
                'string',
                array(
                    'label' => 'Статистика',
                    'template' => 'MetalNewsletterBundle:NewsletterAdmin:statistics.html.twig',
                )
            )
        ;
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('title', null, array('label' => 'Заголовок'))
        ;
    }

    public function toString($object)
    {
        return $object instanceof Newsletter ? $object->getTitle() : '';
    }

}
