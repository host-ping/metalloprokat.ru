<?php

namespace Metal\CompaniesBundle\Form;

use Metal\CompaniesBundle\Entity\ValueObject\CompanyTypeProvider;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints as Assert;

class CompanyCreationType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $cityRepository = $options['city_repository'];
        $userRepository = $options['user_repository'];
        $isAdminPanel = $options['is_admin_panel'];

        $builder
            ->add(
                'companyTitle',
                'text',
                array(
                    'mapped' => false,
                    'constraints' => array(
                        new Assert\NotBlank(),
                    )
                )
            )
            ->add(
                'companyType',
                'choice',
                array(
                    'choices' => CompanyTypeProvider::getAllTypesAsSimpleArray(),
                    'mapped' => false,
                    'constraints' => array(
                        new Assert\NotBlank()
                    )
                )
            )
            ->add('cityTitle', 'text', array('mapped' => false))
            ->add(
                'city',
                'entity_id',
                array(
                    'class' => 'Metal\TerritorialBundle\Entity\City',
                    'hidden' => false,
                    'constraints' => array(
                        new Assert\NotBlank(),
                    )
                )
            )
        ;

        if ($isAdminPanel) {
            $builder
                ->add(
                    'user',
                    'entity_id',
                    array(
                        'mapped' => false,
                        'hidden' => false,
                        'class' => 'Metal\UsersBundle\Entity\User',
                        'constraints' => array(
                            new Assert\NotBlank()
                        ),
                    )
                );
        } else {
            $builder
                ->add(
                    'checkDuplication',
                    'hidden',
                    array(
                        'mapped' => false,
                        'data' => true
                    )
                )
                ->add(
                    'company',
                    'entity_id',
                    array(
                        'class' => 'MetalCompaniesBundle:Company'
                    )
                )
                ->add(
                    'phone',
                    null,
                    array(
                        'required' => true,
                        'constraints' => array(
                            new Assert\NotBlank(),
                        )
                    )
                );
        }

        $builder->addEventListener(
            FormEvents::PRE_SUBMIT,
            function(FormEvent $event) use (&$cityRepository, &$userRepository, $isAdminPanel) {
                $data = $event->getData();

                if (!$isAdminPanel && empty($data['city']) && !empty($data['cityTitle'])) {
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
        $resolver->setDefaults(
            array(
                'error_mapping' => array(
                    'city' => 'cityTitle',
                ),
            )
        );

        $resolver->setRequired(array('city_repository'));
        $resolver->setRequired(array('user_repository'));
        $resolver->setRequired(array('is_admin_panel'));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'metal_companiesbundle_companycreation';
    }
}
