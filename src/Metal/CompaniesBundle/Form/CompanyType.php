<?php

namespace Metal\CompaniesBundle\Form;

use Metal\CompaniesBundle\Entity\Company;
use Metal\CompaniesBundle\Entity\ValueObject\CompanyServiceProvider;
use Metal\CompaniesBundle\Entity\ValueObject\CompanyTypeProvider;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\Url;

class CompanyType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $company = $options['data'];
        /* @var $company Company */
        $cityTitleOptions = array(
            'required' => false,
            'disabled' => true,
        );
        $cityOptions = array('class' => 'MetalTerritorialBundle:City');
        if (!$company->getCity()) {
            $cityTitleOptions = array();

            $cityOptions = array(
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
            );
        }

        $builder
            ->add('title', 'text', array(
                'disabled' => $company->getTitle() !== ''
            ))
            ->add('cityTitle', 'text', $cityTitleOptions)
            ->add('city', 'entity_id', $cityOptions)

            ->add('address', null, array(
                'required'  => false,
            ))
            ->add('companyTypeId', 'choice', array(
                'choices' => CompanyTypeProvider::getAllTypesAsSimpleArray(),
                'placeholder' => '',
            ))
            ->add('sites', 'collection', array(
                'type' => 'url',
                'required' => false,
                'allow_add' => true,
                'allow_delete' => true,
                'options' => array(
                    'constraints' => array(
                        new Url(array('groups' => array('company_edit'))),
                    )
                ),
            ))
            ->add('slogan', null, array(
                'required'  => false,
            ))
            ->add('deliveryDescription', null, array(
                'required'  => false,
                'disabled' => !$company->getPackageChecker()->isAllowedSetDeliveryDescription()
            ))
            ->add('companyDescription', new CompanyDescriptionType())
            ->add('domain', null, array('disabled' => true))
            ->add('phones', 'collection', array(
                'type' => new CompanyPhoneType(),
                'by_reference' => false,
                'allow_add' => true,
                'allow_delete' => true,
            ))
            ->add('companyAttributesTypesIds', 'choice', array(
                'choices' => CompanyServiceProvider::getAllServicesAsSimpleArray(),
                'expanded' => true,
                'multiple' => true,
            ))
            ->add('enabledCompanyCategories', 'collection', array(
                'type' => new CompanyCategoryType(),
                'by_reference' => false,
                'allow_add' => true,
                'allow_delete' => true,
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
        $resolver->setRequired(array('city_repository'));
        $resolver->setDefaults(
            array(
                'data_class' => 'Metal\CompaniesBundle\Entity\Company',
                'cascade_validation' => true,
                'validation_groups' => 'company_edit',
                'error_mapping' => array(
                    'city' => 'cityTitle',
                ),
            )
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'metal_companiesbundle_company';
    }
}
