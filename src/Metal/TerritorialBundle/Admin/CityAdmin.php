<?php

namespace Metal\TerritorialBundle\Admin;

use Doctrine\ORM\EntityManager;
use Metal\ProjectBundle\Service\CloudflareService;
use Metal\TerritorialBundle\Entity\City;
use Metal\TerritorialBundle\Entity\Country;
use Metal\TerritorialBundle\Form\CityInlineEditType;
use Metal\TerritorialBundle\Repository\RegionRepository;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBag;

class CityAdmin extends AbstractAdmin
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

    private $original;

    private $countryId;

    protected $datagridValues = array(
        '_sort_order' => 'DESC',
        '_sort_by' => 'population'
    );

    public function __construct($code, $class, $baseControllerName, EntityManager $em, CloudflareService $cloudflareService, FlashBag $flashBag, $hostnamesMap, $baseHost)
    {
        parent::__construct($code, $class, $baseControllerName);

        $this->em = $em;
        $this->cloudflareService = $cloudflareService;
        $this->flashBag = $flashBag;
        $this->countryId = $hostnamesMap[$baseHost]['country_id'];
    }

    protected function configureRoutes(RouteCollection $collection)
    {
        // Отключаем ссылку на редактирование из списочных страниц других админок
        if ($this->hasParentFieldDescription()) {
            $collection->remove('edit');
        }
        $collection
            ->remove('delete')
            ->add(
                'inline_case_editing',
                $this->getRouterIdParameter().'/edit_inline',
                array('_controller' => 'MetalTerritorialBundle:CityAdmin:editInline')
            );
    }

    public function hasRoute($name)
    {
        //TODO: remove this method override when https://github.com/sonata-project/SonataAdminBundle/pull/3024 will be merged
        $filterParameters = $this->getFilterParameters();

        if ($name === 'batch' && isset($filterParameters['_editingMode']) && $filterParameters['_editingMode']['value'] === 'inline') {
            return false;
        }

        return parent::hasRoute($name);
    }

    public function prePersist($object)
    {
        /* @var $object City */
        $object->setAdministrativeCenter($object->getRegion()->getAdministrativeCenter());
    }

    public function postUpdate($object)
    {
        /* @var $object City */
        $this->setDisplayInCountry($object);

        $this->em->getRepository('MetalTerritorialBundle:TerritorialStructure')->populate();

        $currentSlug = $object->getSlug();

        if (($this->original['country']->getId() != $object->getCountry()->getId()) && $this->countryId != Country::COUNTRY_ID_RUSSIA) {
            $logger = function ($record, $success, $errorMsg) {
                if ($success) {
                    $this->flashBag->add('sonata_flash_info', sprintf('Город "%s" удален с cloudflare', $record));
                } else {
                    $this->flashBag->add('sonata_flash_error', sprintf('Record "%s" failed ("%s").', $record, $errorMsg));
                }
            };
            $this->cloudflareService->removeRecords(array($this->original['slug']), $logger);
        }

        if ($currentSlug != $this->original['slug']) {
            $word = 'обновлен в';
            if ($currentSlug == null) {
                $word = 'удален с';
            }

            $logger = function ($record, $success, $errorMsg) use ($word) {
                if ($success) {
                    $this->flashBag->add('sonata_flash_info', sprintf('Город "%s" %s cloudflare', $record, $word));
                } else {
                    $this->flashBag->add('sonata_flash_error', sprintf('Record "%s" failed ("%s").', $record, $errorMsg));
                }
            };

            if ($currentSlug == null) {
                $this->cloudflareService->removeRecords(array($this->original['slug']), $logger);
            } else {
                $this->cloudflareService->renameRecord($this->original['slug'], $object->getSlug(), $logger);
            }
        }
    }

    public function postPersist($object)
    {
        /* @var $object City */
        $this->setDisplayInCountry($object);

        $this->em->getRepository('MetalTerritorialBundle:TerritorialStructure')->populate();

        $slug = $object->getSlug();
        if ($slug && ($this->countryId == Country::COUNTRY_ID_RUSSIA)) {
            $logger = function ($record, $success, $errorMsg) {
                if ($success) {
                    $this->flashBag->add('sonata_flash_info', sprintf('Город "%s" добавлен в cloudflare', $record));
                } else {
                    $this->flashBag->add('sonata_flash_error', sprintf('Record "%s" failed ("%s").', $record, $errorMsg));
                }
            };

            $this->cloudflareService->insertRecords(array($slug), $logger);
        }
    }

    protected function setDisplayInCountry($object)
    {
        /* @var $object City */

        if (in_array($object->getCountry()->getId(), Country::getEnabledCountriesIds())) {
            $object->setDisplayInCountry($object->getCountry());
        } else {
            $object->setDisplayInCountry($this->em->getReference('MetalTerritorialBundle:Country', Country::COUNTRY_ID_RUSSIA));
        }

        $this->em->flush();
    }

    public function preUpdate($object)
    {
        /* @var $object City */
        $object->setAdministrativeCenter($object->getRegion()->getAdministrativeCenter());

        $this->original = $this->em->getUnitOfWork()->getOriginalEntityData($object);
    }

    public function setRequest(Request $request)
    {
        parent::setRequest($request);

        $filterParameters = $this->getFilterParameters();

        if (isset($filterParameters['_editingMode']) && $filterParameters['_editingMode']['value'] === 'inline') {
            $this->setTemplate('list', 'MetalTerritorialBundle:AdminCity:list.html.twig');
            $this->setTemplate('inner_list_row', 'MetalTerritorialBundle:AdminCity:list_inner_row.html.twig');
        }
    }

    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('title', null, array('label' => 'Название'))
            ->add('slug', null, array('label' => 'Слаг', 'required' => false))
            ->add('population', null, array('label' => 'Население'))
            ->add('titleLocative', null, array('label' => 'Местный падеж', 'help' => 'Где? - заявка на покупку арматуры композитной в Москве'))
            ->add('titleGenitive', null, array('label' => 'Родительный падеж', 'help' => 'Кого? Чего? - из Москвы'))
            ->add('titleAccusative', null, array('label' => 'Винительный падеж', 'help' => 'Кого? Что? Куда? - товары доствляемые в Москву'))
            ->add('region', null, array(
                    'label' => 'Область',
                    'property' => 'title',
                    'required' => true,
                    'placeholder' => 'Выберите обасть',
                )
            )
            ->add('country', null, array(
                    'label' => 'Страна',
                    'property' => 'title',
                    'required' => true,
                    'placeholder' => 'Выберите страну',
                )
            )
            ->add('robotsText', null, array('label' => 'Robots.txt'))
            ->add('onesignalCode', null, array('label' => 'OneSignal code'))
        ;
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id')
            ->add('title', null, array('label' => 'Название'))
            ->add('slug', null, array('label' => 'Слаг'))
            ->add('is_capital', 'boolean', array('label' => 'Столица'))
            ->add('population', null, array('label' => 'Население'))
            ->add('region', null, array('label' => 'Область', 'associated_property' => 'title'))
            ->add('country', null, array('label' => 'Страна', 'associated_property' => 'title'));
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('title', null, array('label' => 'Название'))
            ->add(
                'country',
                null,
                array('label' => 'Страна'),
                null,
                array(
                    'query_builder' => function ($repository) {
                        return $repository->createQueryBuilder('c')
                            ->orderBy('c.title');
                    },
                    'property' => 'title',
                )
            )
            ->add(
                'region',
                null,
                array('label' => 'Область'),
                null,
                array(
                    'query_builder' => function (RegionRepository $repository) {
                        return $repository->createQueryBuilder('r')
                            ->orderBy('r.title');
                    },
                    'property' => 'title',
                )
            )
            ->add(
                'slug',
                'doctrine_orm_callback',
                array(
                    'label' => 'Наличие слага',
                    'callback' => function ($queryBuilder, $alias, $field, $value) {
                        if (!isset($value['value'])) {
                            return;
                        }
                        if ($value['value'] == 'y') {
                            $queryBuilder->andWhere(sprintf("(%s.slug IS NOT NULL AND %s.slug <> '')", $alias, $alias));
                        } else {
                            $queryBuilder->andWhere(sprintf("(%s.slug IS NULL OR %s.slug = '')", $alias, $alias));
                        }

                        return true;
                    }
                ),
                'choice',
                array(
                    'choices' => array(
                        'y' => 'Есть слаг',
                        'n' => 'Нет слага'
                    )
                )
            )
            ->add(
                '_editingMode',
                'doctrine_orm_callback',
                array(
                    'label' => 'Режим редактирования',
                    'mapped' => false,
                    'callback' => function () {
                    }
                ),
                'choice',
                array('choices' => array('batch' => 'Пакетный', 'inline' => 'Построчный'))
            )
            ->add(
                '_isIncomplete',
                'doctrine_orm_callback',
                array(
                    'label' => 'Наличие склонения',
                    'callback' => function ($queryBuilder, $alias, $field, $value) {
                        if (!isset($value['value'])) {
                            return null;
                        }

                        if ($value['value'] === 'y') {
                            $queryBuilder->andWhere(sprintf("(%s.titleLocative IS NULL OR %s.titleGenitive IS NULL OR %s.titleAccusative IS NULL)", $alias, $alias, $alias));
                        }

                        return true;
                    }
                ),
                'choice',
                array(
                    'choices' => array(
                        'y' => 'Без склонения'
                    )
                )
            );
    }

    public function getFormForObject(City $city)
    {
        return $this->getFormContractor()
            ->getFormBuilder('form')
            ->getFormFactory()
            ->createNamed(CityInlineEditType::getNameForCity($city), new CityInlineEditType(), $city)
            ->createView();
    }

    public function toString($object)
    {
        return $object instanceof City ? $object->getTitle() : '';
    }
}
