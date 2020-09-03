<?php

namespace Metal\CategoriesBundle\Admin;

use Behat\Transliterator\Transliterator;
use Cocur\Slugify\Slugify;
use Doctrine\ORM\EntityManager;
use Metal\CategoriesBundle\Entity\LandingPage;
use Metal\CategoriesBundle\Entity\LandingPageAttributeValue;
use Metal\CategoriesBundle\Entity\ValueObject\LandingPageTerritoryProvider;
use Metal\CompaniesBundle\Entity\ValueObject\CompanyServiceProvider;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\DoctrineORMAdminBundle\Datagrid\ProxyQuery;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class LandingPageAdmin extends AbstractAdmin
{
    protected $datagridValues = array(
        '_sort_order' => 'DESC',
        '_sort_by' => 'id',
    );

    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var Slugify
     */
    private $slugify;

    public function __construct($code, $class, $baseControllerName, EntityManager $em, Slugify $slugify)
    {
        parent::__construct($code, $class, $baseControllerName);

        $this->em = $em;
        $this->slugify = $slugify;
    }

    protected function configureRoutes(RouteCollection $collection)
    {
        $collection
            ->add(
                'show_results_count',
                $this->getRouterIdParameter().'/show_results_count',
                array('_controller' => 'MetalCategoriesBundle:LandingPageAdmin:showResultsCount')
            );
    }

    public function getExportFormats()
    {
        return array('csv');
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id')
            ->add(
                'title',
                null,
                array(
                    'label' => 'Название',
                    'template' => 'MetalCategoriesBundle:CategoryAdmin:titleWithCategoryAndAttributes.html.twig',
                )
            )
            ->add('slug', null, array('label' => 'Слаг'))
            ->add(
                '_territory',
                null,
                array(
                    'label' => 'Область показа',
                    'template' => 'MetalCategoriesBundle:LandingPageAdmin:territory_show.html.twig',
                )
            )
            ->add('enabled', null, array('label' => 'Включена'))
            ->add('resultsCountUpdatedAt', null, array('label' => 'Дата обновления количества результаов'))
            ->add(
                '_links',
                null,
                array(
                    'label' => 'Действия',
                    'template' => 'MetalCategoriesBundle:LandingPageAdmin:link_show.html.twig',
                )
            );
    }

    public function getDatagrid()
    {
        if ($this->datagrid) {
            return $this->datagrid;
        }

        $datagrid = parent::getDatagrid();
        $landingPages = $datagrid->getResults();

        $landingPageAttributeValueRepo = $this->em
            ->getRepository('MetalCategoriesBundle:LandingPageAttributeValue');
        $categoryRepo = $this->em->getRepository('MetalCategoriesBundle:Category');

        $landingPageAttributeValueRepo->attachAttributesForLandingPages($landingPages);

        $categoriesIds = array();
        /* @var $landingPages LandingPage[] */
        foreach ($landingPages as $landingPage) {
            if ($landingPage->getCategory()) {
                $categoriesIds[$landingPage->getCategory()->getId()] = true;
            }
        }

        if ($categoriesIds) {
            $categoryRepo->findBy(array('id' => array_keys($categoriesIds)));
        }

        return $this->datagrid;
    }


    protected function configureFormFields(FormMapper $formMapper)
    {
        $subject = $this->getSubject();
        /* @var $subject LandingPage */

        $formMapper
            ->with('main', array('name' => 'Основное', 'class' => 'col-md-6'))
            ->add('title', null, array('label' => 'Название'))
            ->add(
                'slug',
                null,
                array(
                    'label' => 'Слаг',
                    'required' => false,
                    'help' => 'Если не прописать вручную - слаг будет сгенерирован автоматически!'
                )
            )
            ->add(
                'enabled',
                'checkbox',
                array(
                    'label' => 'Включена',
                    'required' => false,
                )
            )
            ->end()
            ->with('territory', array('name' => 'Доступность по териториям', 'class' => 'col-md-6'))
            ->add(
                'landingPageTerritoryId',
                'choice',
                array('label' => 'Доступность', 'choices' => LandingPageTerritoryProvider::getAllTypesAsSimpleArray())
            )
            ->add(
                'city',
                null,
                array(
                    'label' => 'Город',
                    'property' => 'title',
                    'required' => false,
                    'placeholder' => 'Выберите город',
                )
            )
            ->add(
                'region',
                null,
                array(
                    'label' => 'Область',
                    'property' => 'title',
                    'required' => false,
                    'placeholder' => 'Выберите обасть',
                )
            )
            ->add(
                'country',
                null,
                array(
                    'label' => 'Страна',
                    'property' => 'title',
                    'required' => false,
                    'placeholder' => 'Выберите страну',
                )
            )
            ->end()
            ->with('criteria', array('name' => 'Критерии фильтрации', 'class' => 'col-md-6'))
            ->add(
                'category',
                'entity',
                array(
                    'label' => 'Категория',
                    'required' => false,
                    'placeholder' => '',
                    'class' => 'MetalCategoriesBundle:Category',
                    'property' => 'nestedTitle',
                    'choices' => $this->em->getRepository('MetalCategoriesBundle:Category')->buildCategoriesByLevels(),
                )
            )
            ->add(
                'landingPageAttributesValues',
                'textarea',
                array(
                    'label' => 'Список атрибутов',
                    'mapped' => false,
                    'required' => true,
                    'attr' => array('style' => 'width:100%; height: 250px;'),
                    'help' => 'Перечислять названия значений атрибутов, по одному на каждой строке',
                    'data' => $subject->getAttributesCollection()->toString("\n", "\n"),
                )
            )
            ->add('searchQuery', null, array('label' => 'Строка поиска'))
            ->add(
                'companyAttributes',
                'choice',
                array(
                    'choices' => CompanyServiceProvider::getAllServicesAsSimpleArray(),
                    'label' => 'Услуги',
                    'expanded' => true,
                    'multiple' => true,
                    'required' => false,
                )
            )
            ->end()
            ->with('metadata', array('name' => 'Метаданные', 'class' => 'col-md-6'))
            ->add(
                '_metadataH1Title',
                'text',
                array(
                    'label' => 'H1 заголовок',
                    'help' => '
                        <ul>
                            <li><b>Молоко коровье</b> 3,2% и 6% купить оптом</li>
                            <li><b>Молоко коровье</b> 3,2% и 6% купить оптом в Москве</li>
                        </ul>',
                    'property_path' => 'metadata.h1Title',
                )
            )
            ->add(
                '_metadataTitle',
                'textarea',
                array(
                    'label' => 'Meta заголовок',
                    'help' => '
                        <ul>
                            <li><b>Молоко коровье</b> 3,2% и 6% купить оптом — продажа, цены на <b>молоко коровье</b> 3,2% и 6% — Продукт.ру</li>
                            <li><b>Молоко коровье</b> 3,2% и 6% купить оптом в Москве — продажа, цены на <b>молоко коровье</b> 3,2% и 6% — Продукт.ру</li>
                        </ul>',
                    'property_path' => 'metadata.title',
                    'attr' => array('style' => 'width:100%; height: 80px;'),
                )
            )
            ->add(
                '_metadataDescription',
                'textarea',
                array(
                    'label' => 'Meta описание',
                    'help' => '
                        <ul>
                            <li><b>Молоко коровье</b> 3,2% 6% купить в России. <b>Молоко коровье</b> 3,2% 6% - каталог с фото, цены, описание, прайс-листы и технические характеристики. <b>Молоко коровье</b> 3,2% 6% - продажа оптом и в розницу с доставкой от компаний в России</li>
                            <li><b>Молоко коровье</b> 3,2% 6% купить в Москве. <b>Молоко коровье</b> 3,2% 6% - каталог с фото, цены, описание, прайс-листы и технические характеристики. <b>Молоко коровье</b> 3,2% 6% - продажа оптом и в розницу с доставкой от компаний в Москве</li>
                        </ul>',
                    'property_path' => 'metadata.description',
                    'attr' => array('style' => 'width:100%; height: 150px;'),
                )
            )
            ->add(
                'breadcrumbCategory',
                'entity',
                array(
                    'label' => 'Категория для хлебных крошек',
                    'required' => false,
                    'placeholder' => '',
                    'class' => 'MetalCategoriesBundle:Category',
                    'property' => 'nestedTitle',
                    'choices' => $this->em->getRepository('MetalCategoriesBundle:Category')->buildCategoriesByLevels(),
                )
            )
            ->end();
    }

    public function preUpdate($object)
    {
        /* @var $object LandingPage */
        $oldTitle = $this->em->getUnitOfWork()->getOriginalEntityData($object)['title'];
        $newTitle = $object->getTitle();
        if ($oldTitle !== $newTitle) {
            $object->setSlug($this->generateSlug($newTitle));
        }

        $this->changeAttributesValues($object);

        if (!$object->isModeSelectedTerritory()) {
            $object->setCity(null);
            $object->setRegion(null);
            $object->setCountry(null);
        } elseif ($object->getCity() || $object->getRegion() || $object->getCountry()) {
            $object->setLandingPageTerritoryId(LandingPageTerritoryProvider::SELECTED_TERRITORY);
        }
    }

    public function prePersist($object)
    {
        /* @var $object LandingPage */
        $this->changeAttributesValues($object);
    }

    public function postPersist($object)
    {
        /* @var $object LandingPage */
        $this->updateCounts($object);
        $this->getRequest()->getSession()->getFlashBag()->add(
            'sonata_flash_info',
            'Кол-во товаров по данному запросу обновилось!'
        );
    }

    public function postUpdate($object)
    {
        /* @var $object LandingPage */
        $this->updateCounts($object);
        $this->getRequest()->getSession()->getFlashBag()->add(
            'sonata_flash_info',
            'Кол-во товаров по данному запросу обновилось!'
        );
    }

    protected function updateCounts(LandingPage $landingPage)
    {
        $kernel = $this->getConfigurationPool()->getContainer()->get('kernel');
        $application = new Application($kernel);
        $application->setAutoExit(false);

        $input = new ArrayInput(
            array(
                'command' => 'metal:categories:update-landing-page',
                '--landing-id' => $landingPage->getId(),
            )
        );

        $output = new NullOutput();
        $application->run($input, $output);
    }

    protected function changeAttributesValues(LandingPage $landingPage)
    {
        $originalAttributesValues = $landingPage->getAttributesValues();

        $attributesValuesData = $this->getForm()->get('landingPageAttributesValues')->getData();
        $delimiter = '\r\n|\r|\n';
        $attributesValuesArray = preg_split('/'.$delimiter.'/', $attributesValuesData);
        $newAttributesValues = array();
        foreach ($attributesValuesArray as $attributeValueString) {
            $attributeValue = $this->em
                ->getRepository('MetalAttributesBundle:AttributeValue')
                ->findOneBy(array('value' => $attributeValueString));
            if ($attributeValue) {
                $newAttributesValues[$attributeValue->getId()] = $attributeValue;
            }
        }

        $addAttributesValuesIds = array_diff(array_keys($newAttributesValues), array_keys($originalAttributesValues));
        $deleteAttributesValuesIds = array_diff(
            array_keys($originalAttributesValues),
            array_keys($newAttributesValues)
        );

        foreach ($addAttributesValuesIds as $newAttributeValueId) {
            // создаем новую запись если нашли атрибут по входной строке и его еще не было в базе
            $landingPageAttributeValue = new LandingPageAttributeValue();
            $landingPageAttributeValue->setAttributeValue($newAttributesValues[$newAttributeValueId]);
            $landingPage->addLandingPageAttributeValue($landingPageAttributeValue);

            $this->em->persist($landingPageAttributeValue);
        }

        foreach ($deleteAttributesValuesIds as $deleteAttributeValueId) {
            // удаляем запись если не нашли атрибут по входной строке и он есть в базе
            $landingPageAttributeValue = $this->em
                ->getRepository('MetalCategoriesBundle:LandingPageAttributeValue')
                ->findOneBy(
                    array(
                        'attributeValue' => $originalAttributesValues[$deleteAttributeValueId],
                        'landingPage' => $landingPage,
                    )
                );

            $landingPage->removeLandingPageAttributeValue($landingPageAttributeValue);
        }
    }

    public function getFormBuilder()
    {
        $formBuilder = parent::getFormBuilder();

        $formBuilder->addEventListener(
            FormEvents::PRE_SUBMIT,
            function (FormEvent $event) {
                $data = $event->getData();

                if ($data['title'] && !$data['slug']) {
                    $data['slug'] = $this->generateSlug($data['title']);
                }

                $event->setData($data);
            }
        );

        return $formBuilder;
    }

    protected function generateSlug($title)
    {
        $subject = $this->getSubject();
        /* @var $subject LandingPage */

        $slug = $this->slugify->slugify($title);
        $slug = Transliterator::urlize($slug);
        $landingPageRepository = $this->em->getRepository('MetalCategoriesBundle:LandingPage');
        $existentLandingPage = $landingPageRepository->findOneBy(array('slug' => $slug));
        if ($existentLandingPage && (!$subject->getId() || $existentLandingPage->getId() != $subject->getId())) {
            $slug .= '-'.mt_rand(0, 10);
        }

        return $slug;
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $filterParameters = $this->getFilterParameters();

        $repository = $this->em->getRepository('MetalAttributesBundle:AttributeValueCategory');
        $categoryId = isset($filterParameters['category']) ? $filterParameters['category']['value'] : null;

        $datagridMapper
            ->add(
                'title',
                null,
                array(
                    'label' => 'Название страницы',
                )
            )
            ->add(
                'category',
                null,
                array(
                    'label' => 'Категория',
                    'required' => false,
                ),
                'entity',
                array(
                    'class' => 'MetalCategoriesBundle:Category',
                    'property' => 'nestedTitle',
                    'choices' => $this->em->getRepository('MetalCategoriesBundle:Category')->buildCategoriesByLevels(),
                )
            )
            ->add(
                'enabled',
                'doctrine_orm_boolean',
                array(
                    'label' => 'Включена',
                ),
                'sonata_type_boolean'
            )
            ->add(
                'landingPageTerritoryId',
                'doctrine_orm_choice',
                array(
                    'label' => 'Доступна',
                ),
                'choice',
                array(
                    'required' => false,
                    'choices' => LandingPageTerritoryProvider::getAllTypesAsSimpleArray(),
                )
            )
        ;

        if ($categoryId) {
            $datagridMapper
                ->add(
                    '_attribute_value',
                    'doctrine_orm_choice',
                    array(
                        'label' => 'Значение атрибута',
                    ),
                    'choice',
                    array(
                        'choices' => $repository->getAttributesOptionsWithoutCodeArray($categoryId),
                    )
                );
        };
    }

    public function toString($object)
    {
        return $object instanceof LandingPage ? $object->getTitle() : '';
    }

}
