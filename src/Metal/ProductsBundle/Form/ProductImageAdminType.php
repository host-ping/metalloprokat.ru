<?php

namespace Metal\ProductsBundle\Form;

use Doctrine\ORM\EntityManagerInterface;
use Metal\ProjectBundle\Validator\Constraints\Image;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints as Assert;

class ProductImageAdminType extends AbstractType
{
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver
            ->setDefaults(array('need_select_category' => false, 'entity_manager' => null))
            ->setRequired(array('entity_manager'));
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $needSelectCategory = $options['need_select_category'];
        $em = $options['entity_manager'];
        /* @var $em EntityManagerInterface */

        if ($needSelectCategory && $em) {
            $builder->add(
                'category',
                'entity',
                array(
                    'label' => 'Категория',
                    'required' => true,
                    'constraints' => array(
                        new Assert\NotBlank(),
                    ),
                    'class' => 'MetalCategoriesBundle:Category',
                    'property' => 'nestedTitle',
                    'placeholder' => '',
                    'choices' => $em->getRepository('MetalCategoriesBundle:Category')
                        ->buildCategoriesByLevels(null, true),
                )
            );
        }

        $builder
            ->add(
                'images',
                'file',
                array(
                    'label' => 'Выберите изображения',
                    'required' => true,
                    'multiple' => true,
                    'constraints' => new Assert\All(array(new Image())),
                )
            );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'metal_products_image_admin_create';
    }
}
