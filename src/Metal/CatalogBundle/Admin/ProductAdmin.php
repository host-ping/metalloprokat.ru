<?php

namespace Metal\CatalogBundle\Admin;

use Cocur\Slugify\Slugify;
use Doctrine\ORM\EntityManager;
use Metal\CatalogBundle\Entity\Product;
use Metal\CatalogBundle\Entity\ProductCity;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\DoctrineORMAdminBundle\Datagrid\ProxyQuery;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Vich\UploaderBundle\Form\Type\VichImageType;

/**
 * @method Product getSubject()
 */
class ProductAdmin extends AbstractAdmin
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
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * @var Slugify
     */
    private $slugify;

    public function __construct(
        $code,
        $class,
        $baseControllerName,
        EntityManager $em,
        TokenStorageInterface $tokenStorage,
        Slugify $slugify
    ) {
        parent::__construct($code, $class, $baseControllerName);

        $this->em = $em;
        $this->tokenStorage = $tokenStorage;
        $this->slugify = $slugify;
    }

    public function configure()
    {
        $this->setTemplate('edit', 'MetalCatalogBundle:AdminProduct:edit.html.twig');
        $this->setTemplate('list', 'MetalCatalogBundle:AdminProduct:list.html.twig');
    }

    public function toString($object)
    {
        return $object instanceof Product ? $object->getTitle() : '';
    }

    public function preUpdate($object)
    {
        /* @var $object Product */
        $object->setUpdated();
    }

    public function prePersist($object)
    {
        /* @var $object Product */
        $object->setCreatedBy($this->tokenStorage->getToken()->getUser());
    }

    public function postPersist($object)
    {
        $this->em->getRepository('MetalCatalogBundle:Product')->checkProductUniqueTitle($object);
    }

    public function postUpdate($object)
    {
        $this->em->getRepository('MetalCatalogBundle:Product')->checkProductUniqueTitle($object);
    }

    public function getBatchActions()
    {
        $actions['edit'] = array(
            'label' => 'Редактировать',
            'ask_confirmation' => false,
        );

        return array_merge($actions, parent::getBatchActions());
    }

    public function setSubject($subject)
    {
        parent::setSubject($subject);

        /* @var $subject Product */
        $subject->initializeAdditionalAttributeValues();
    }

    protected function configureRoutes(RouteCollection $collection)
    {
        $collection
            ->add(
                'load_attributes_by_category',
                'loadAttributesByCategory',
                array('_controller' => 'MetalCatalogBundle:ProductAdmin:loadAttributesByCategory')
            )
            ->add(
                'load_additional_attributes_by_category',
                'loadAdditionalAttributesByCategory',
                array('_controller' => 'MetalCatalogBundle:ProductAdmin:loadAdditionalAttributesByCategory')
            );
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
                    'template' => 'MetalCatalogBundle:AdminProduct:colorModeratedText.html.twig'
                )
            )
            ->add('createdBy', null, array('label' => 'Автор', 'template' => 'MetalProjectBundle:Admin:author_topic_list.html.twig'))
            ->add('size', null, array('label' => 'Объем'))
            ->add(
                'uploadedPhoto',
                null,
                array(
                    'label' => 'Фото',
                    'template' => '@MetalProject/Admin/display_image_in_list.html.twig',
                    'image_filter' => 'products_sq40_non_optim',
                    'image_filter_big' => 'products_sq250_non_optim',

                )
            )
            ->add(
                'productCities',
                null,
                array(
                    'label' => 'Города',
                    'template' => 'MetalCatalogBundle:AdminProduct:product_cities.html.twig'
                )
            )
            ->add(
                'description',
                null,
                array(
                    'label' => 'Описание',
                    'template' => 'MetalCatalogBundle:AdminProduct:formatDescription.html.twig'
                )
            )
            ->add('createdAt', null, array('label' => 'Создан'))
            ->add('updatedAt', null, array('label' => 'Обновлен'));
    }

    public function getNewInstance()
    {
        $object = parent::getNewInstance();
        /* @var $object Product */

        $id = $this->getRequest()->query->get('previous_product_id');
        if (!$id) {
            return $object;
        }

        $previousProduct = $this->em->getRepository('MetalCatalogBundle:Product')->find($id);

        $object->setCategory($previousProduct->getCategory());
        $object->setManufacturer($previousProduct->getManufacturer());
        $object->setBrand($previousProduct->getBrand());
        $object->getProductCities()->clear();

        foreach ($previousProduct->getProductCities() as $productCity) {
            $newProductCity = new ProductCity();
            $newProductCity->setCity($productCity->getCity());
            $object->addProductCity($newProductCity);
        }

        return $object;
    }

    public function getDatagrid()
    {
        if ($this->datagrid) {
            return $this->datagrid;
        }

        $datagrid = parent::getDatagrid();
        $products = $datagrid->getResults();

        $productAttributeValueRepository = $this->em->getRepository('MetalCatalogBundle:ProductAttributeValue');
        $productRepository = $this->em->getRepository('MetalCatalogBundle:Product');
        $categoryRepository = $this->em->getRepository('MetalCategoriesBundle:Category');

        $productAttributeValueRepository->attachAttributesForProducts($products);
        $productRepository->attachCitiesToProducts($products);

        $categoriesIds = array();
        /* @var $products Product[] */
        foreach ($products as $product) {
            if ($product->getCategory()) {
                $categoriesIds[$product->getCategory()->getId()] = true;
            }
        }

        if ($categoriesIds) {
            $categoryRepository->findBy(array('id' => array_keys($categoriesIds)));
        }

        return $this->datagrid;
    }

    protected function configureFormFields(FormMapper $formMapper)
    {
        $product = $this->getSubject();

        $attrValRepo = $this->em->getRepository('MetalAttributesBundle:AttributeValue');

        $categoryId = $this->request->request->get(sprintf('%s[category]', $this->getUniqid()), null, true);
        if (null === $categoryId && $product->getCategory()) {
            $categoryId = $product->getCategory()->getId();
        }

        $formMapper
            ->with('products', array('name' => 'Дополнительные атрибуты', 'class' => 'box'))
                ->add(
                    'category',
                    'entity',
                    array(
                        'label' => 'Категория',
                        'required' => false,
                        'placeholder' => '',
                        'class' => 'MetalCategoriesBundle:Category',
                        'property' => 'nestedTitle',
                        'choices' => $this->em->getRepository('MetalCategoriesBundle:Category')->buildCategoriesByLevels()
                    )
                )
        ;

        $manufacturerOptions = array('label' => 'Производитель', 'property' => 'value', 'placeholder' => '');
        $brandOptions = array('label' => 'Бренд', 'property' => 'value', 'placeholder' => '');

        if ($categoryId) {
            $manufacturerOptions['query_builder'] = $attrValRepo->createQueryBuilderForOptionsList($categoryId, Product::ATTR_CODE_MANUFACTURER);
            $brandOptions['query_builder'] = $attrValRepo->createQueryBuilderForOptionsList($categoryId, Product::ATTR_CODE_BRAND);
        } else {
            $manufacturerOptions['choices'] = array();
            $brandOptions['choices'] = array();
        }

        $formMapper
            ->add('manufacturer', null, $manufacturerOptions)
            ->add('brand', null, $brandOptions)
            ->add(
                'productCities',
                'sonata_type_collection',
                array(
                    'label' => 'Города',
                    'by_reference' => false,
                ),
                array(
                    'edit' => 'inline',
                    'inline' => 'table',
                )
            )
            ->add('title', null, array('label' => 'Название'))
            ->add('size', null, array('label' => 'Объем', 'required' => false))
            ->add('uploadedPhoto', VichImageType::class,
                array(
                    'label' => 'Фото продукта',
                    'required' => !$product->getPhoto()->getName(),
                    'imagine_pattern' => 'catalog_logo_sq136',
                    'download_uri' => false,
                )
            )
            ->add(
                'description',
                'textarea',
                array('label' => 'Описание', 'required' => false, 'attr' => array('style' => 'width:100%; height: 250px;'))
            )
            ->end()
        ;

        $formMapper
            ->with('AdditionalAttributes', array('name' => 'Дополнительные атрибуты', 'class' => 'js-additional-attributes'));

        if ($categoryId) {
            $attributes = $this->em->getRepository('MetalAttributesBundle:AttributeCategory')->getAdditionalAttributesForCategory($categoryId);

            foreach ($attributes as $attrCode => $attrTitle) {
                $attrName = 'attributeValues_' . $attrCode;
                $formMapper->add(
                    $attrName,
                    'entity',
                    array(
                        'label' => $attrTitle,
                        'class' => 'MetalAttributesBundle:AttributeValue',
                        'property' => 'value',
                        'query_builder' => $attrValRepo->createQueryBuilderForOptionsList($categoryId, $attrCode),
                        'property_path' => sprintf('attributeValues[%s]', $attrCode),
                        'required' => false,
                        'placeholder' => '',
                    )
                )
                ->add(
                    $attrName.'_custom',
                    'text',
                    array(
                        'label' => 'Произвольное значение атрибута: '.$attrTitle,
                        'mapped' => false,
                        'required' => false,
                    )
                );
            }
        }

        $formMapper->end()->end();
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
                    'label' => 'Название продукта'
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
                'manufacturer',
                'doctrine_orm_choice',
                array(
                    'label' => 'Производитель',
                ),
                'choice',
                array(
                    'choices' => $repository->getAttributesOptionsArray(Product::ATTR_CODE_MANUFACTURER, $categoryId),
                )
            )
            ->add(
                'brand',
                'doctrine_orm_choice',
                array(
                    'label' => 'Бренд',
                ),
                'choice',
                array(
                    'choices' => $repository->getAttributesOptionsArray(Product::ATTR_CODE_BRAND, $categoryId),
                )
            )
            ->add(
                'fileSize',
                'doctrine_orm_callback',
                array(
                    'label' => 'Наличие фото',
                    'callback' => function (ProxyQuery $queryBuilder, $alias, $field, $value) {

                        if (!isset($value['value'])) {
                            return;
                        }

                        if ($value['value'] === 'y') {
                            $queryBuilder->andWhere(sprintf('%s.photo.name IS NOT NULL', $alias));
                        } else {
                            $queryBuilder->andWhere(sprintf('%s.photo.name IS NULL', $alias));
                        }

                        return true;
                    }
                ),
                'choice',
                array(
                    'choices' => array(
                        'y' => 'С фото',
                        'n' => 'Без фото'
                    )
                )
            )
            ->add(
                'user',
                'doctrine_orm_callback',
                array(
                    'label' => 'Идентификатор пользователя',
                    'callback' => function ($queryBuilder, $alias, $field, $value) {
                        if (!isset($value['value'])) {
                            return null;
                        }

                        $queryBuilder->andWhere(sprintf('%s.createdBy = :userId', $alias));
                        $queryBuilder->setParameter('userId', $value['value']);

                        return true;
                    }
                )
            )
            ->add(
                'createdBy',
                'doctrine_orm_choice',
                array('label' => 'Автор'),
                'choice',
                array(
                    'choices' => $this->em->getRepository('MetalCatalogBundle:Product')->getSimpleAuthors()
                )
            )
            ->add(
                'createdAt',
                'doctrine_orm_date_range',
                array('label' => 'Дата создания'),
                'sonata_type_date_range_picker',
                array(
                    'field_options_start' => array(
                        'format' => 'dd.MM.yyyy',
                        'label' => 'Дата от',
                    ),
                    'field_options_end' => array(
                        'format' => 'dd.MM.yyyy',
                        'label' => 'Дата до',
                    ),
                    'attr' => array(
                        'class' => 'js-sonata-datepicker',
                    ),
                )
            );
    }

    public function getFormBuilder()
    {
        $builder = parent::getFormBuilder();

        $builder->addEventListener(
            FormEvents::PRE_SUBMIT,
            function (FormEvent $event) {
                $data = $event->getData();
                $category = $this->em->getRepository('MetalCategoriesBundle:Category')->find($data['category']);
                $customAttrs = array();
                foreach ($data as $field => $value) {
                    if (preg_match('/attributeValues_(.+?)_custom/', $field, $matches) && $value) {
                        $customAttrs[$matches[1]] = $value;
                    }
                }

                $attributes = $this->em->getRepository('MetalAttributesBundle:Attribute')->findBy(array('code' => array_keys($customAttrs)));
                $attributeValueRepository = $this->em->getRepository('MetalAttributesBundle:AttributeValue');
                $attributeValueCategoryRepository = $this->em->getRepository('MetalAttributesBundle:AttributeValueCategory');
                foreach ($attributes as $attribute) {
                    $attributeValue = $attributeValueRepository->findOrCreateAttributeValue($attribute, $customAttrs[$attribute->getCode()], $this->slugify);
                    $attrValueCategory = $attributeValueCategoryRepository->findOrCreateAttributeValueCategory($attributeValue, $category);

                    $this->em->flush($attributeValue);
                    $this->em->flush($attrValueCategory);

                    $data['attributeValues_'.$attribute->getCode()] = $attributeValue->getId();
                }

                $event->setData($data);
            }
        );

        $builder->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event) {
                $subject = $event->getData();
                if ($subject instanceof Product) {
                    $subject->handleAdditionalAttributeValues();
                }
            });

        return $builder;
    }
}
