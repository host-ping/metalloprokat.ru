<?php

namespace Metal\CorpsiteBundle\Form;

use Metal\CompaniesBundle\Entity\ValueObject\CompanyPackageTypeProvider;
use Metal\ServicesBundle\Entity\Package;
use Metal\ServicesBundle\Entity\PackageOrder;
use Metal\ServicesBundle\Entity\ValueObject\ServicePeriodTypesProvider;
use Metal\ServicesBundle\Repository\PackageRepository;
use Metal\TerritorialBundle\Repository\CityRepository;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints as Assert;

class PackageOrderType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('package', 'entity', array(
                'class' => 'MetalServicesBundle:Package',
                'query_builder' => function (PackageRepository $r) {
                    return $r->createQueryBuilder('p')
                        ->where('p.id <> :base_package')
                        ->setParameter('base_package', Package::BASE_PACKAGE)
                        ->orderBy('p.priority', 'ASC');
                },
                'property' => 'title'
            ))
            ->add('packagePeriod', 'choice', array(
                'choices' => ServicePeriodTypesProvider::getAllTypesAsSimpleArray()
            ))
            ->add('startAt', 'date', array(
                'widget' => 'single_text',
                'format' => 'd MMMM yyyy'
            ))
            ->add('finishAt', 'date', array(
                    'widget' => 'single_text',
                    'format' => 'd MMMM yyyy',
                    'attr' => array(
                        'readonly' => true,
                    )
                ))
            ->add('company', 'entity_id', array(
                    'class' => 'MetalCompaniesBundle:Company',
                    'hidden'  => true
                ))
            ->add('companyTitle', 'text')
            ->add('cityTitle', null, array('mapped' => false))
            ->add('city', 'entity_id', array(
                'class' => 'MetalTerritorialBundle:City',
                'hidden' => false
            ))
            ->add('comment', 'textarea')
            ->add('phone')
            ->add('email')
            ->add('fullName')
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
        $resolver->setDefaults(array(
            'error_mapping' => array(
                'city' => 'cityTitle'
            ),
            'validation_groups' => array('corp'),
            'data_class' => PackageOrder::class,
        ));

        $resolver->setRequired(array('city_repository'));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'order_package';
    }
}
