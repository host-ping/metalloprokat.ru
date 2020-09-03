<?php

namespace Metal\UsersBundle\Form;

use Metal\CompaniesBundle\Entity\ValueObject\CompanyTypeProvider;
use Metal\UsersBundle\Entity\User;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints as Assert;

class UserType extends AbstractType
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
            ->add('fullName', 'text', array(
                'required'  => true,
                'constraints' => array(
                    new Assert\NotBlank(array(
                        'groups' => array(
                            'registration',
                        )
                    )),
                )
            ))
            ->add('phone', null, array(
                'required'  => true,
            ))
            ->add('companyTitle', 'text', array(
                'mapped' => false,
                'constraints' => array(
                    new Assert\NotBlank(array(
                            'groups' => array(
                                'new_trader',
                            )
                        )),
                )
            ));

        if ($options['promocode_enabled']) {
            $builder->add('promocode', 'text', array(
                'mapped' => false
            ));
        }

        $builder->add('cityTitle', 'text')
            ->add('city', 'entity_id', array(
                'class' => 'MetalTerritorialBundle:City',
                'hidden' => false,
                'constraints' => array(
                    new Assert\NotBlank(array(
                            'message' => 'Нужно выбрать город из списка.',
                            'groups' => array(
                                'registration'
                            )
                        )),
                ),
            ))
            ->add('agreeWithTerms', 'checkbox', array(
                'required'    => true,
                'mapped'      => false,
                'data'        => true,
                'constraints' => array(
                    new Assert\IsTrue(array(
                        'message' => 'Необходимо принять условия пользовательского соглашения.',
                        'groups' => array('registration')
                    )),
                )
            ))
            ->add('email', null, array(
                'required'    => true,
                'constraints' => array(
                    new Assert\NotBlank(array(
                        'groups' => array(
                            'registration'
                        )
                    )),
                )
            ))
            ->add('company', 'entity_id', array(
                'class' => 'MetalCompaniesBundle:Company'
            ))
            ->add('newPassword', 'repeated', array(
                'invalid_message' => 'Пароли должны совпадать.',
                'type' => 'password',
                'required' => true,
            ))
            ->add('userType', 'choice', array(
                'choices' => array(
                    User::TRADER => $options['supplier_token'],
                    User::CONSUMER => $options['consumer_token'],
                ),
                'mapped' => false,
                'data' => User::TRADER
            ))
            ->add('companyType', 'choice', array(
                'choices' => CompanyTypeProvider::getAllTypesAsSimpleArray(),
                'mapped' => false,
                'constraints' => array(
                    new Assert\NotBlank(array(
                        'groups' => array(
                            'new_trader'
                        )
                    ))
                )
            ))
            ->add('checkDuplication', 'hidden', array(
                'mapped' => false,
                'data' => true
            ))
            ->add('_redirect_to', 'hidden', array(
                'mapped' => false,
                'data' => $options['_redirect_to']
            ))
            ->add('is_registered_invite_project', 'hidden', array(
                'mapped' => false
            ))
        ;

        $cityRepository = $options['city_repository'];

        $builder->addEventListener(
            FormEvents::PRE_SUBMIT,
            function(FormEvent $event) use (&$cityRepository) {
                $data = $event->getData();

                if (empty($data['city']) && !empty($data['cityTitle'])) {
                    //TODO: грузить не тупо по тайтлу, а учитывать еще текущую активную страну
                    if ($city = $cityRepository->findOneBy(array('title' => $data['cityTitle']))) {
                        $data['city'] = $city->getId();
                    }
                }

                $event->setData($data);
            }
        );
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Metal\UsersBundle\Entity\User',
            'error_mapping' => array(
                'city' => 'cityTitle',
            ),
            'validation_groups' => function(FormInterface $form) {
                $userType = $form->get('userType')->getData();
                $validationsGroups = array('registration', Constraint::DEFAULT_GROUP);
                if ($userType == User::TRADER) {
                    $validationsGroups[] = 'new_trader';
                }

                if ($this->phoneRequired) {
                    $validationsGroups[] = 'phone';
                }

                return $validationsGroups;
            }
        ));

        $resolver->setRequired(array('city_repository', 'supplier_token', 'consumer_token', '_redirect_to', 'promocode_enabled'));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'metal_usersbundle_user';
    }
}
