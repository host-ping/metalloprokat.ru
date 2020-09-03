<?php

namespace Metal\AttributesBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\Count;
use Symfony\Component\Validator\Constraints\NotBlank;

use Metal\CategoriesBundle\Repository\CategoryRepository;

class AttributeValueCategoryAdminType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $categoryRepository = $options['category_repository'];
        /* @var $categoryRepository CategoryRepository */
        $builder
            ->add('attribute', 'entity', array(
                'class' => 'MetalAttributesBundle:Attribute',
                'label' => 'Тип атрибута',
                'required' => true,
                'property' => 'title',
                'placeholder' => '',
                'constraints' => array(new NotBlank())
            ))
            ->add(
                'categories',
                'entity',
                array(
                    'label' => 'Категории',
                    'required' => true,
                    'class' => 'MetalCategoriesBundle:Category',
                    'property' => 'nestedTitle',
                    'placeholder' => '',
                    'multiple' => true,
                    'choices' => $categoryRepository->buildCategoriesByLevels(),
                    'constraints' => array(new NotBlank(), new Count(array('min' => 1)))
                )
            )
            ->add('possibleValue', 'textarea', array(
                'label' => 'Возможные значения',
                'required' => true,
                'attr' => array('style' => 'width:100%; height: 200px;'),
                'constraints' => array(new NotBlank())
            ))
        ;

    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setRequired(array('category_repository'));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'metal_attribute_value_category_admin_create';
    }
}
