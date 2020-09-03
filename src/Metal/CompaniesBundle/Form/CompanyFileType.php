<?php

namespace Metal\CompaniesBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints as Assert;

class CompanyFileType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'uploadedFile',
                'file',
                array(
                    'required' => true,
                    'constraints' => array(
                        new Assert\NotBlank(),
                        new Assert\File(
                            array(
                                'mimeTypes' => array(
                                    'image/jpeg',
                                    'image/png',
                                    'application/pdf',
                                    'application/vnd.ms-excel',
                                    'application/vnd.ms-office',
                                    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                                    'application/msword',
                                    'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                                ),
                                'mimeTypesMessage' => 'Поддерживаемые форматы: png, jpeg, pdf, xls, xlsx, doc, docx'
                            )
                        )
                    )
                )
            );
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => 'Metal\CompaniesBundle\Entity\CompanyFile',
            )
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'metal_companiesbundle_companyfile';
    }
}
