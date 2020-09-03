<?php

namespace Metal\UsersBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Constraints\NotBlank;

class UserPhotoType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'uploadedPhoto',
                'file',
                array(
                    'required' => true,
                    'attr' => array(
                        'accept' => 'image/*'
                    ),
                    'constraints' => array(
                        new NotBlank(),
                        new Image(),
                    )
                )
            );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'metal_usersbundle_usersavephoto';
    }
}
