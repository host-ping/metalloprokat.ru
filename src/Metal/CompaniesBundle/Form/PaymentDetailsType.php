<?php

namespace Metal\CompaniesBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class PaymentDetailsType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('displayOnMiniSite', 'checkbox', array(
                'label' => 'Отображать на мини сайте',
                'required' => false
            ))
            ->add(
                'nameOfLegalEntity',
                null,
                array(
                    'label' => 'Название юр. лица',
                    'required' => false
                )
            )
            ->add(
                'inn',
                null,
                array(
                    'label' => 'ИНН',
                    'required' => false
                )
            )
            ->add(
                'legalAddress',
                null,
                array(
                    'label' => 'Юридический адрес',
                    'required' => false
                )
            )
            ->add(
                'mailAddress',
                null,
                array(
                    'label' => 'Почтовый адрес',
                    'required' => false
                )
            )
            ->add(
                'kpp',
                null,
                array(
                    'label' => 'КПП',
                    'required' => false
                )
            )
            ->add(
                'orgn',
                null,
                array(
                    'label' => 'ОГРН',
                    'required' => false
                )
            )
            ->add(
                'directorFullName',
                null,
                array(
                    'label' => 'ФИО директора',
                    'required' => false
                )
            )
            ->add(
                'bankAccount',
                null,
                array(
                    'label' => 'Р/С',
                    'required' => false
                )
            )
            ->add(
                'bankCorrespondentAccount',
                null,
                array(
                    'label' => 'Номер корр. счета',
                    'required' => false
                )
            )
            ->add(
                'bankBik',
                'text',
                array(
                    'label' => 'БИК',
                    'required' => false
                )
            )
            ->add(
                'bankTitle',
                null,
                array(
                    'label' => 'Банк',
                    'required' => false
                )
            )
            ->add(
                'uploadedFile',
                'file',
                array(
                    'required' => false,
                    'attr' => array(
                        'accept' => 'image/*'
                    )
                )
            )
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => 'Metal\CompaniesBundle\Entity\PaymentDetails'
            )
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'metal_companiesbundle_paymentdetails';
    }
}
