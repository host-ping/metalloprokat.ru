<?php

namespace Metal\DemandsBundle\Admin;

use Doctrine\ORM\EntityManager;
use Metal\AttributesBundle\Entity\AttributeValue;
use Metal\CategoriesBundle\Repository\CategoryRepository;
use Metal\DemandsBundle\Entity\DemandItem;
use Metal\ProductsBundle\Entity\ValueObject\ProductMeasureProvider;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class DemandItemAdmin extends AbstractAdmin
{
    /**
     * @var EntityManager
     */
    private $em;

    private $productVolumeTitle;

    private $projectFamily;

    public function __construct($code, $class, $baseControllerName, EntityManager $em, $productVolumeTitle, $projectFamily)
    {
        parent::__construct($code, $class, $baseControllerName);

        $this->em = $em;
        $this->productVolumeTitle = $productVolumeTitle;
        $this->projectFamily = $projectFamily;
    }

    public function getNewInstance()
    {
        $object = new DemandItem();
        $object->setVolumeTypeId(ProductMeasureProvider::WITHOUT_VOLUME);

        return $object;
    }

    protected function configureFormFields(FormMapper $formMapper)
    {
        $subject = $this->getSubject();
        /* @var $subject DemandItem */

        $categoryId = null;
        if ($subject) {
            $categoryId = $subject->getCategoryId();
            $this->setPossibleAttributes($subject, $categoryId);
        }

        $builder = $formMapper->getFormBuilder();

        $builder->addEventListener(
            FormEvents::PRE_SUBMIT,
            function (FormEvent $event) use ($formMapper) {
                $data = $event->getData();
                $form = $event->getForm();

                // передобавляем заново поле со списком атрибутов, так как категория могла поменяться и для нее нужно подгрузить другие значения атрибутов
                $categoryId = $data['category'];
                $form->remove('attributeValues');
                $form->add(
                    'attributeValues',
                    'entity',
                    [
                        'class' => 'MetalAttributesBundle:AttributeValue',
                        'choice_value' => function (AttributeValue $attributeValue) {
                            return $attributeValue->getId();
                        },
                        'choice_label' => function (AttributeValue $attributeValue) {
                            return $attributeValue->getValue();
                        },
                        'group_by' => 'attribute.title',
                        'label' => 'Значения атрибутов',
                        'placeholder' => '',
                        'choices' => $this->em
                            ->getRepository('MetalAttributesBundle:AttributeValueCategory')
                            ->getAttributeValuesByCategory($categoryId),
                        'choices_as_values' => true,
                        'attr' => [
                            'style' => 'width: 225px;',
                            'class' => 'js-attribute-values',
                        ],
                        'required' => false,
                        'multiple' => true,
                        'sonata_field_description' => $this->formFieldDescriptions['attributeValues'],
                    ]
                );
            }
        );

        static $i = -1;
        $i++;

        $formMapper
            ->add('title', 'textarea', array(
                'label' => 'Наименовение',
                'attr' => array(
                    'cols' => 150,
                    'rows' => 5,
                    'class' => 'js-textarea-title',
                ),
            ));
        if ($this->projectFamily == 'metalloprokat') {
            $formMapper
                ->add(
                    'size',
                    null,
                    array(
                        // явно пробил размер, что б не путать менеджеров
                        'label' => 'Размер',
                        'required' => false,
                        'attr' => array(
                            'style' => 'width: 184px;',
                            'class' => 'js-textarea-size'
                        ),
                    )
                );
        }
        $formMapper
            ->add('categoryTitle', 'text', array(
                'label' => 'Категория',
                'required' => false,
                'attr' => array(
                    'style' => 'width: 240px',
                    'typeahead' => '',
                    'typeahead-prefetch-url' => $this->getRouteGenerator()->generate('MetalCategoriesBundle:Suggest:getCategories', array('allow_products' => true, 'full' => true)),
                    'typeahead-model' => "categories$i",
                    'typeahead-on-select' => 'reloadAttributeValues($event, $item)',
                    'typeahead-suggestion-template-url' => "'typeahead-suggestion-with-parent'",
                )
            ))
            ->add('category', 'entity_id', array(
                'required' => false,
                'class' => 'MetalCategoriesBundle:Category',
                'hidden' => false,
                'read_only' => true,
                'query_builder' => function(CategoryRepository $repo, $id) {
                    return $repo
                        ->createQueryBuilder('c')
                        ->select('PARTIAL c.{id, title, branchIds}')
                        ->andWhere('c.id = :id')
                        ->andWhere('c.allowProducts = true')
                        ->andWhere('c.virtual = false')
                        ->setParameter('id', $id)
                    ;
                },
                'attr' => array(
                    'style' => 'width: 240px',
                    'tabindex' => '-1',
                    'ng-model' => "categories$i.id",
                    'initial-value' => '',
                    'class' => 'js-category-id',
                ),
            ))
            ->add(
                'attributeValues',
                'entity',
                array(
                    'class' => 'MetalAttributesBundle:AttributeValue',
                    'choice_value' => function (AttributeValue $attributeValue) {
                        return $attributeValue->getId();
                    },
                    'choice_label' => function (AttributeValue $attributeValue) {
                        return $attributeValue->getValue();
                    },
                    'group_by' => 'attribute.title',
                    'label' => 'Значения атрибутов',
                    'placeholder' => '',
                    'choices' => $this->em
                        ->getRepository('MetalAttributesBundle:AttributeValueCategory')
                        ->getAttributeValuesByCategory($categoryId),
                    'choices_as_values' => true,
                    'attr' => array(
                        'style' => 'width: 225px;',
                        'class' => 'js-attribute-values',
                    ),
                    'required' => false,
                    'multiple' => true,
                )
            )
            ->add(
                'isLockedAttributeValues',
                null,
                array(
                    'label' => 'Отключить автоопределение атрибутов',
                    'required' => false,
                    'attr' => array(
                        'class' => 'js-locked-attribute-values',
                    ),
                ))
            ->add(
                'volume',
                null,
                array(
                    'label' => 'Объем закупки',
                    'required' => false,
                    'attr' => array(
                        'style' => 'width: 100px;'
                    ),
                )
            )
            ->add(
                'volumeTypeId',
                'choice',
                array(
                    'label' => 'Ед. изм.',
                    'placeholder' => '',
                    'choices' => ProductMeasureProvider::getAllTypesAsSimpleArray(),
                    'attr' => array(
                        'style' => 'width: 160px;'
                    ),
                )
            )
        ;
    }

    private function setPossibleAttributes(DemandItem $demandItem, $categoryId)
    {
        $productParameterValueRepository = $this->em->getRepository('MetalProductsBundle:ProductParameterValue');

        $attributesForTitle = $productParameterValueRepository
            ->matchAttributesForTitle($categoryId, $demandItem->getTitle(), $demandItem->getSize());
        $possibleAttributesValuesIds = array_column($attributesForTitle, 'parameterOptionId');
        $attibutesValues = $this->em
            ->getRepository('MetalAttributesBundle:AttributeValue')
            ->findBy(array('id' => $possibleAttributesValuesIds));

        foreach ($attibutesValues as $attributeValue) {
            $demandItem->addAttributeValue($attributeValue);
        }
    }

    public function toString($object)
    {
        return $object instanceof DemandItem ? $object->getTitle() : '';
    }
}
