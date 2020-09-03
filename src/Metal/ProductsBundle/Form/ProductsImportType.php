<?php

namespace Metal\ProductsBundle\Form;

use Metal\CompaniesBundle\Entity\CompanyCity;
use Metal\CompaniesBundle\Repository\CompanyCityRepository;
use Metal\ProductsBundle\Service\ProductImportService;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints as Assert;

class ProductsImportType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if ($options['xls']) {
            $builder
                ->add(
                    'attachment',
                    'file',
                    array(
                        'label' => 'Файл XLS/XLSX для импорта',
                        'required' => true,
                        'constraints' => array(
                            new Assert\NotBlank(array('message' => 'Загрузите файл формата xls/xlsx')),
                            new Assert\File(
                                array(
                                    'maxSize' => '8M',
                                    'mimeTypesMessage' => 'Загрузите файл формата xls/xlsx',
                                )
                            )
                        )
                    )
                );
        }
        if ($options['yml'])  {
            $builder
                ->add(
                    'ymlUrl',
                    null,
                    array(
                        'label' => 'Ссылка для импорта',
                        'required' => true,
                        'constraints' => array(
                            new Assert\Url()
                        )
                    )
                );
        }

        $companyId = $options['company_id'];
        if ($companyId && !$options['is_private_office']) {
            $builder
                ->add(
                    'template',
                    'choice',
                    array('label' => 'Шаблон', 'choices' => array(
                        ProductImportService::MODE_ADMIN => 'Старый (админка)',
                        ProductImportService::MODE_PRIVATE_OFFICE => 'Расширенный (Личный кабинет)'
                    ))
                )
                ->add(
                    'branchOffice',
                    'entity',
                    array(
                        'label' => 'Филиал',
                        'required' => true,
                        'class' => 'MetalCompaniesBundle:CompanyCity',
                        'query_builder' => function (CompanyCityRepository $rep) use ($companyId) {
                            return $rep->createQueryBuilder('cc')
                                ->join('cc.city', 'c')
                                ->addSelect('c')
                                ->andWhere('cc.company = :company_id')
                                ->andWhere('cc.kind = :kind')
                                ->setParameter('company_id', $companyId)
                                ->setParameter('kind', CompanyCity :: KIND_BRANCH_OFFICE)
                                ->orderBy('cc.isMainOffice', 'DESC')
                                ->addOrderBy('c.title');
                        },
                        'property' => 'city.title'
                    )
                )
            ;
        }

        if ($options['is_private_office']) {
            $builder
                ->add('branchOffice', 'entity_id', array(
                        'class' => 'MetalCompaniesBundle:CompanyCity',
                        'hidden'  => false,
                        'required' => false,
                    ));
        }
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
                'is_private_office' => null,
                'company_id' => null,
                'yml' => false,
                'xls' => false
            ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'metal_productsbundle_product';
    }
}
