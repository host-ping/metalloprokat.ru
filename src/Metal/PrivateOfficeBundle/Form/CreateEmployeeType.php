<?php

namespace Metal\PrivateOfficeBundle\Form;

use Metal\UsersBundle\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints as Assert;

class CreateEmployeeType extends AbstractType
{
    private $phoneRequired;

    public function __construct($phoneRequired = true)
    {
        $this->phoneRequired = $phoneRequired;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'fullName',
                TextType::class,
                array(
                    'required' => true,
                    'constraints' => array(new Assert\NotBlank()),
                )
            )
            ->add('job', TextType::class)
            ->add('phone', null, array('required' => $this->phoneRequired))
            ->add('email', EmailType::class, array('required' => true));

        if (!$options['is_new']) {
            $builder
                ->add(
                    'newPassword',
                    'repeated',
                    array(
                        'invalid_message' => 'Пароли должны совпадать',
                        'required' => false,
                        'first_options' => array('label' => 'Новый пароль'),
                        'second_options' => array('label' => '...еще раз'),
                        'constraints' => array(new Assert\Length(array('min' => 6, 'max' => 20))),
                    )
                );
        }

        $builder
            ->add(
                'hasEditPermission',
                CheckboxType::class,
                array('label' => 'Управление информацией')
            )
            ->add(
                'canUseService',
                CheckboxType::class,
                array(
                    'label' => 'Право на использование сервисов',
                )
            );

        if (!$options['is_new']) {
            $builder
                ->add(
                    'approved',
                    'checkbox',
                    array(
                        'label' => 'Право на вход в личный кабинет',
                    )
                );
        }

        $builder
            ->add(
                'displayInContacts',
                CheckboxType::class,
                array(
                    'label' => 'Отображать на минисайте',
                )
            )
            ->add(
                'displayPosition',
                IntegerType::class,
                array(
                    'label' => 'Позиция',
                )
            );
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $validationsGroups = array('registration_employees', Constraint::DEFAULT_GROUP);
        if ($this->phoneRequired) {
            $validationsGroups[] = 'phone';
        }

        $resolver->setDefaults(
            array(
                'data_class' => User::class,
                'validation_groups' => $validationsGroups,
                'is_new' => true
            )
        );
    }
}
