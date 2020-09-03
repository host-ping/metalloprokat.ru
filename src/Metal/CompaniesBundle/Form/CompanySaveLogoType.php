<?php

namespace Metal\CompaniesBundle\Form;

use Metal\CompaniesBundle\Entity\Company;
use Metal\ProjectBundle\Validator\Constraints\Image;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class CompanySaveLogoType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $constraints = array(new Image());
        if (!$options['data']->getLogo()) {
            $constraints[] = new NotBlank();
        }

        $builder
            ->add(
                'uploadedLogo',
                'file',
                array(
                    'required' => !$options['data']->getLogo(),
                    'attr' => array(
                        'accept' => 'image/*',
                    ),
                    'constraints' => $constraints,
                )
            )
            ->add(
                'optimizeLogo',
                'checkbox',
                array(
                    'label' => 'Оптимизировать изображение',
                    'required' => false,
                )
            );
    }


    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => Company::class,
            )
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'metal_companiesbundle_companysavelogo';
    }
}
