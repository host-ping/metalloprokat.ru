<?php

namespace Metal\TerritorialBundle\Admin;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;

use Metal\ProjectBundle\Service\CloudflareService;
use Metal\TerritorialBundle\Entity\Country;
use Metal\TerritorialBundle\Entity\Region;
use Metal\TerritorialBundle\Repository\CityRepository;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBag;

class RegionAdmin extends AbstractAdmin
{
    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * CloudflareService
     */
    protected $cloudflareService;

    /**
     * FlashBag
     */
    protected $flashBag;

    private $countryId;

    public function __construct($code, $class, $baseControllerName, EntityManager $em, CloudflareService $cloudflareService, FlashBag $flashBag, $hostnamesMap, $baseHost)
    {
        parent::__construct($code, $class, $baseControllerName);

        $this->em = $em;
        $this->cloudflareService = $cloudflareService;
        $this->flashBag = $flashBag;
        $this->countryId = $hostnamesMap[$baseHost]['country_id'];
    }

    public function configureRoutes(RouteCollection $collection)
    {
        $collection->remove('delete');
    }

    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('title', null, array('label' => 'Название'))
            ->add('country', null, array(
                    'label' => 'Страна',
                    'property' => 'title',
                    'required' => true,
                    'placeholder' => 'Выберите страну',
                )
            )
            ->add('federalDistrict', null, array(
                    'label' => 'Федеральный округ',
                    'property' => 'title',
                    'required' => true,
                    'placeholder' => 'Выберите федеральный округ',
                )
            )
            //TODO: тут необходимо сделать queryBuilder, который бы возвращал только те города, которые внутри этой области
            ->add('administrativeCenter', null, array(
                    'label' => 'Административный центр',
                    'property' => 'title',
                    'required' => true,
                    'placeholder' => 'Выберите административный центр',
                )
            )
            ->add('titleLocative', null, array('label' => 'Местный падеж', 'help' => 'Где? - заявка на покупку арматуры композитной в Алтайском крае'))
            ->add('titleGenitive', null, array('label' => 'Родительный падеж', 'help' => 'Кого? Чего? - из Алтайского края'))
            ->add('titleAccusative', null, array('label' => 'Винительный падеж', 'help' => 'Кого? Что? Куда? - товары доствляемые в Алтайский край'))
        ;
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id')
            ->add('title', null, array('label' => 'Название'))
            ->add('country', null, array('label' => 'Страна', 'associated_property' => 'title'))
            ->add(
                'menu',
                null,
                array(
                    'label' => 'Меню',
                    'template' => 'MetalTerritorialBundle:AdminRegion:regionAdmin.html.twig'
                )
            )
        ;
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('title', null, array('label' => 'Название'))
            ->add('country', null, array('label' => 'Страна'), null, array(
                    'query_builder' => function (EntityRepository $repository) {
                            return $repository->createQueryBuilder('c')
                                ->orderBy('c.title');
                        },
                    'property' => 'title',
                ))
            ->add('administrativeCenter', null, array('label' => 'Административный центр'), null, array(
                    'query_builder' => function (CityRepository $repository) {
                            return $repository->createQueryBuilder('a')
                                ->orderBy('a.title');
                        },
                    'property' => 'title',
                ))
        ;
    }

    public function postUpdate($object)
    {
        $this->em->getRepository('MetalTerritorialBundle:TerritorialStructure')->populate();
    }

    public function postPersist($object)
    {
        /* @var $object Region */
        $this->em->getRepository('MetalTerritorialBundle:TerritorialStructure')->populate();

        if ($this->countryId == Country::COUNTRY_ID_RUSSIA) {
            $logger = function ($record, $success, $errorMsg) {
                if ($success) {
                    $this->flashBag->add('sonata_flash_info', sprintf('Область "%s" добавлена в cloudflare', $record));
                } else {
                    $this->flashBag->add('sonata_flash_error', sprintf('Record "%s" failed ("%s").', $record, $errorMsg));
                }
            };

            $this->cloudflareService->insertRecords(array($object->getId()), $logger);
        }
    }

    public function toString($object)
    {
        return $object instanceof Region ? $object->getTitle() : '';
    }
}
