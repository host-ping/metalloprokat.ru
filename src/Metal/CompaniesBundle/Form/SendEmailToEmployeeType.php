<?php

namespace Metal\CompaniesBundle\Form;

use Metal\UsersBundle\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints as Assert;

class SendEmailToEmployeeType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if (!$options['is_authenticated']) {
            $builder
                ->add(
                    'userName',
                    'text',
                    array(
                        'required' => true,
                        'constraints' => array(
                            new Assert\NotBlank(),
                            new Assert\Length(array('min' => 3)),
                        )
                    )
                )
                ->add(
                    'userEmail',
                    'email',
                    array(
                        'required' => true,
                        'constraints' => array(
                            new Assert\NotBlank(),
                            new Assert\Email(array('strict' => true)),
                        )
                    )
                );
        }

        $builder
            ->add(
                'emailText',
                'textarea',
                array(
                    'required' => true,
                    'constraints' => array(
                        new Assert\NotBlank(),
                        new Assert\Length(array('min' => 3)),
                    )
                )
            )
            ->add(
                'employee',
                'entity_id',
                array(
                    'class' => 'MetalUsersBundle:User'
                )
            );
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setRequired(array('is_authenticated'));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'metal_send_email';
    }
}
