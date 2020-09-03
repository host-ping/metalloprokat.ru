<?php

namespace Metal\CompaniesBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use Symfony\Component\Validator\Constraints as Assert;

class CompanyChangeCityType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $cityRepository = $options['city_repository'];

        $builder

            ->add('cityTitle', 'text')
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
        $resolver->setDefaults(
            array(
                'error_mapping' => array(
                    'city' => 'cityTitle',
                ),
            )
        );

        $resolver->setRequired(array('city_repository'));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'metal_companiesbundle_company_change_city';
    }
}