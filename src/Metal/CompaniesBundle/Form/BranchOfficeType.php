<?php

namespace Metal\CompaniesBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints as Assert;

class BranchOfficeType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $cityDisabled = !empty($options['data']) && $options['data']->getId();

        $builder
            ->add(
                'address',
                null,
                array(
                    'required' => false,
                )
            )
            ->add(
                'city',
                'entity_id',
                array(
                    'class' => 'MetalTerritorialBundle:City',
                    'hidden' => false,
                    'constraints' => array(
                        new Assert\NotBlank(
                            array(
                                'message' => 'Нужно выбрать город из списка.',
                            )
                        ),
                    ),
                )
            )
            ->add(
                'cityTitle',
                'text',
                array(
                    'required' => false,
                    'disabled' => $cityDisabled,
                )
            )
            ->add('description')
            ->add('displayPosition')
            ->add(
                'email',
                null,
                array(
                    'required' => false
                )
            )
            ->add(
                'site',
                'url',
                array(
                    'required' => false
                )
            )
            ->add(
                'phones',
                'collection',
                array(
                    'type' => new CompanyPhoneType(),
                    'required' => false,
                    'by_reference' => false,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'delete_empty' => true,
                )
            );

        $cityRepository = $options['city_repository'];

        $builder->addEventListener(
            FormEvents::PRE_SUBMIT,
            function (FormEvent $event) use (&$cityRepository) {
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
        $resolver->setDefaults(
            array(
                'data_class' => 'Metal\CompaniesBundle\Entity\CompanyCity',
                'cascade_validation' => true,
                'error_mapping' => array(
                    'city' => 'cityTitle',
                ),
                'validation_groups' => array('Default', 'branch_office')
            )
        );
        $resolver->setRequired(array('city_repository'));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'metal_companiesbundle_companycity';
    }
}
