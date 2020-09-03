<?php

namespace Metal\CatalogBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints as Assert;

class ProductsBatchEditType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $fields = array(
            'productCities',
            'category',
        );

        $builder
            ->add(
                'productCities',
                'entity_id',
                array(
                    'label' => 'ID города',
                    'class' => 'Metal\TerritorialBundle\Entity\City',
                    'hidden' => false,
                    'read_only' => true,
                    'required' => false,
                    'mapped' => false,
                    'multiple' => true,
                    'constraints' => array(
                        new Assert\NotBlank(array('groups' => 'productCities')),
                        new Assert\Count(array('min' => 1, 'groups' => 'productCities')),
                    ),
                )
            )
            ->add(
                'productCitiesTitle',
                'text',
                array(
                    'label' => 'Название города(ов)',
                    'required' => false,
                )
            )
            ->add(
                'categoryTitle',
                'text',
                array(
                    'label' => 'Категория',
                    'required' => false,
                    'mapped' => false,
                )
            )
            ->add(
                'category',
                'entity_id',
                array(
                    'label' => 'ID категории',
                    'class' => 'MetalCategoriesBundle:Category',
                    'hidden' => false,
                    'mapped' => false,
                    'read_only' => true,
                    'required' => false,
                )
            );

        foreach ($fields as $field) {
            $builder->add($field.'Editable', 'checkbox', array('label' => false, 'required' => false));
        }
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'validation_groups' => function(FormInterface $form) {
                $data = $form->getData();

                $groups = array('Default');

                if (isset($data['productCitiesEditable']) && true === $data['productCitiesEditable']) {
                    $groups[] = 'productCities';
                }

                return $groups;
            },
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'metal_productadmin';
    }
}
