<?php

namespace Metal\PrivateOfficeBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints as Assert;

class MiniSiteAddressType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('slug', 'text', array(
                'required'  => true
            ));

        if ($options['minisite_domains']) {
            $builder
                ->add('domainId', 'choice', array(
                        'choices' => $options['minisite_domains'],
                    ));
        }
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
                'data_class' => 'Metal\CompaniesBundle\Entity\Company',
                'minisite_domains' => array()
            ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'metal_privateofficebundle_minisiteaddress';
    }
}
