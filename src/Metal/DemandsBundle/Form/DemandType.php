<?php

namespace Metal\DemandsBundle\Form;

use Metal\DemandsBundle\Entity\Demand;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use Symfony\Component\Validator\Constraints as Assert;

class DemandType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('person')
            ->add('email', null, array('required' => false))
        ;

        if (!$options['is_from_trader']) {
            $builder
                ->add('companyTitle', null, array(
                        'required' => false,
                    ));
        }

        if ($options['is_private']) {
            $builder
            ->add('company', 'entity_id', array(
                    'class' => 'MetalCompaniesBundle:Company'
                ))
            ->add('product', 'entity_id', array(
                    'class' => 'MetalProductsBundle:Product'
                ))
            ->add('productCity', 'entity_id', array(
                    'class' => 'MetalTerritorialBundle:City'
                ));
        }

        $builder
            ->add('phone')
            ->add('info')
            ->add('category', 'entity_id', array(
                    'class' => 'MetalCategoriesBundle:Category',
                    'hidden' => false
                ))
            ->add('city', 'entity_id', array(
                    'class' => 'MetalTerritorialBundle:City',
                    'hidden' => false
                ))
            ->add('cityTitle', 'text')
            ->add('adminSourceTypeId')
        ;

        $builder->add('demandItems', 'collection', array(
                    'type' => DemandItemType::class,
                    'by_reference' => false,
                    'allow_add' => true,
                ));

        $builder->add('demandFiles', 'collection', array(
            'type' => new DemandFileType(),
            'by_reference' => false,
            'allow_add' => true,
        ));

//        $builder->addEventListener(FormEvents::PRE_SUBMIT,
//            function (FormEvent $event) {
//                $data = $event->getData();
//                if (empty($data['file'])) {
//                    $event->getForm()->add('demandItems', 'collection', array(
//                            'type' => new DemandItemType(),
//                            'by_reference' => false,
//                            'allow_add' => true,
//                        ));
//                }
//            }
//        );

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
            'data_class' => Demand::class,
            'is_private' => false,
            'is_authenticated' => false,
            'is_from_trader' => false,
            'cascade_validation' => true,
            'error_mapping' => array(
                'city' => 'cityTitle',
            ),
        ));
        $resolver->setRequired(array('city_repository'));
    }

    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['is_authenticated'] = $options['is_authenticated'];
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'demand';
    }
}
