<?php

namespace Metal\ProductsBundle\Admin;

use Brouzie\Bundle\HelpersBundle\Helper\HelperFactory;
use Doctrine\ORM\EntityManager;
use Metal\ProductsBundle\Entity\ProductImage;
use Metal\ProjectBundle\Helper\ImageHelper;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\CoreBundle\Validator\ErrorElement;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Vich\UploaderBundle\Form\Type\VichImageType;

class ProductImageAdmin extends AbstractAdmin
{
    protected $datagridValues = array(
        '_sort_order' => 'DESC',
        '_sort_by' => 'updatedAt',
    );

    /** @var ImageHelper */
    protected $imageHelper;

    /** @var EntityManager */
    protected $em;

    protected $parentAssociationMapping = 'company';

    public function __construct($code, $class, $baseControllerName, HelperFactory $helperFactory, EntityManager $em)
    {
        parent::__construct($code, $class, $baseControllerName);

        $this->imageHelper = $helperFactory->get('MetalProjectBundle:Image');
        $this->em = $em;
    }

    public function getFilterParameters()
    {
        if (!$this->isChild()) {
            $this->datagridValues['companyId'] = array(
                'value' => 'n',
            );
        }

        return parent::getFilterParameters();
    }

    public function configure()
    {
        $this->setTemplate('list', 'MetalProductsBundle:ProductImageAdmin:list.html.twig');
    }

    public function configureRoutes(RouteCollection $collection)
    {
        $collection
            ->add(
                'create_multiple',
                'createMultiple',
                array('_controller' => 'MetalProductsBundle:ProductImageAdmin:createMultiple')
            )
            ->add(
                'download_file',
                $this->getRouterIdParameter().'/download_file',
                array('_controller' => 'MetalProductsBundle:ProductImageAdmin:downloadFile')
            );
    }

    public function setSubject($subject)
    {
        parent::setSubject($subject);
        /* @var $subject ProductImage */

        if (!$subject->getId()) {
            $this->formOptions['validation_groups'] = array('Default', 'new_item');
        }
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $subject = $this->getSubject();
        /* @var $subject ProductImage */

        $fileOptions = array(
            'label' => 'Ссылка',
            'required' => !$subject->getPhoto()->getName(),
            'imagine_pattern' => 'products_sq600',
        );
        if ($subject->getId() && ($subject->getDownloaded() || !$subject->getUrl())) {
            $originalFileUrl = $this->getRouteGenerator()->generateUrl(
                $this,
                'download_file',
                array('id' => $subject->getId()),
                true
            );
            $fileOptions['download_uri'] = $originalFileUrl;
//            $fileOptions['download_label'] = 'скачать';
        }

        $formMapper
            ->add(
                'description',
                null,
                array(
                    'label' => 'Название',
                    'required' => false,
                )
            )
            ->add('uploadedPhoto', VichImageType::class, $fileOptions)
            ->add(
                'optimized',
                null,
                array(
                    'label' => 'Оптимизировать изображение',
                    'required' => false,
                    'help' => 'При отключении оптимизации фото продукта будет на белом фоне.',
                )
            )
            ->add('url', null, array('label' => 'url', 'read_only' => true))
            ->add(
                'category',
                'entity',
                array(
                    'label' => 'Категория',
                    'required' => false,
                    'class' => 'MetalCategoriesBundle:Category',
                    'property' => 'nestedTitle',
                    'placeholder' => '',
                    'choices' => $this->em->getRepository('MetalCategoriesBundle:Category')->buildCategoriesByLevels(
                        null,
                        true
                    ),
                )
            );
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper->addIdentifier('id');

        if (!$this->isChild()) {
            $listMapper
                ->add(
                    'company',
                    null,
                    array(
                        'label' => 'Компания',
                        'template' => 'MetalProjectBundle:Admin:company_with_id_list.html.twig',
                    )
                );
        }

        $listMapper
            ->add('category', null, array('label' => 'Категория', 'associated_property' => 'titleWithParent'))
            ->add('description', null, array('label' => 'Описание'))
            ->add(
                'photo',
                null,
                array(
                    'label' => 'Предварительный просмотр',
                    'template' => 'MetalProductsBundle:ProductImageAdmin:product_image.html.twig',
                    'sortable' => false,
                )
            )
            ->add('createdAt', null, array('label' => 'Дата создания'))
            ->add('updatedAt', null, array('label' => 'Дата обновления'))
            ->add('retriesCount', null, array('label' => 'Кол-во попыток загрузки'));
    }

    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id')
            ->add('description', null, array('label' => 'Заголовок'))
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
                'companyId',
                'doctrine_orm_callback',
                array(
                    'label' => 'Связь с компанией',
                    'callback' => function ($queryBuilder, $alias, $field, $value) {
                        if (!isset($value['value'])) {
                            return null;
                        }

                        if ($value['value'] === 'y') {
                            $queryBuilder->andWhere(sprintf('%s.company IS NOT NULL', $alias));
                        } else {
                            $queryBuilder->andWhere(sprintf('%s.company IS NULL', $alias));
                        }

                        return true;
                    },
                ),
                'choice',
                array(
                    'choices' => array(
                        'y' => 'Связано',
                        'n' => 'Не связано',
                    ),
                )
            )
            ->add(
                'hasUrl',
                'doctrine_orm_callback',
                array(
                    'label' => 'Наличие ссылки',
                    'callback' => function ($queryBuilder, $alias, $field, $value) {
                        if (!isset($value['value'])) {
                            return;
                        }

                        if ($value['value'] == 'y') {
                            $queryBuilder->andWhere(sprintf('%s.url IS NOT NULL', $alias));
                        } else {
                            $queryBuilder->andWhere(sprintf('%s.url IS NULL', $alias));
                        }

                        return true;
                    },
                ),
                'choice',
                array(
                    'choices' => array(
                        'y' => 'Есть',
                        'n' => 'Нет',
                    ),
                )
            )
            ->add('url', null, array('label' => 'Ссылка'))
            ->add(
                'retriesCount',
                'doctrine_orm_callback',
                array(
                    'label' => 'Превышен лимит загрузок по url',
                    'callback' => function ($queryBuilder, $alias, $field, $value) {
                        if (!isset($value['value'])) {
                            return;
                        }

                        if ($value['value'] == 'y') {
                            $queryBuilder->andWhere(sprintf('%s.retriesCount >= 5', $alias));
                        } else {
                            $queryBuilder->andWhere(sprintf('%s.retriesCount < 5', $alias));
                        }

                        return true;
                    },
                ),
                'choice',
                array(
                    'choices' => array(
                        'y' => 'Да',
                        'n' => 'Нет',
                    ),
                )
            )
            ->add(
                'downloaded',
                'doctrine_orm_boolean',
                array(
                    'label' => 'Загружена по ссылке',
                ),
                'sonata_type_boolean'
            );
    }

    public function getBaseControllerName()
    {
        return 'MetalProductsBundle:ProductImageAdmin';
    }

    public function toString($object)
    {
        return $object instanceof ProductImage ? $object->getPhoto()->getOriginalName() : '';
    }

    public function validate(ErrorElement $errorElement, $object)
    {
        if ($object->getCategory() && !$object->getCategory()->getAllowProducts()) {
            $errorElement
                ->with('category')
                ->addViolation(
                    sprintf('К категории %s нельзя прикреплять фотографии.', $object->getCategory()->getTitle())
                )
                ->end();
        }
    }
}
