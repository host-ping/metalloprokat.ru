<?php

namespace Metal\DemandsBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

use Symfony\Component\Validator\Constraints as Assert;
use Metal\TerritorialBundle\Repository\CityRepository;

class DemandAnswerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if (!$options['is_authenticated']) {
            $builder
                ->add('company')
                ->add('email')
                ->add('name')
                ->add('phone')
                ->add('city', 'entity_id', array(
                    'class' => 'MetalTerritorialBundle:City',
                    'hidden' => false,
                    'constraints' => array(
                        new Assert\NotBlank(array(
                            'message' => 'Нужно выбрать город из списка.',
                            'groups' => array(
                                'anonymous'
                            )
                        )),
                    ),
                ))
                ->add('cityTitle', 'text', array('mapped' => false))
            ;
        }

        $builder
            ->add('description')
        ;

        $cityRepository = $options['city_repository'];
        /* @var $cityRepository CityRepository  */
        $builder->addEventListener(
            FormEvents::PRE_SUBMIT,
            function(FormEvent $event) use (&$cityRepository) {
                $data = $event->getData();

                if (empty($data['city']) && !empty($data['cityTitle'])) {
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
        $resolver->setRequired(array('is_authenticated'));
        $resolver->setDefaults(array(
            'data_class' => 'Metal\DemandsBundle\Entity\DemandAnswer',
            'error_mapping' => array(
                'city' => 'cityTitle',
            ),

        ));

        $resolver->setRequired(array('city_repository'));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'metal_demandsbundle_demandanswertype';
    }
}
