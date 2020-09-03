<?php

namespace Metal\ProductsBundle\Form;

use Metal\CompaniesBundle\Entity\CompanyCity;
use Metal\CompaniesBundle\Repository\CompanyCityRepository;
use Metal\ProductsBundle\Entity\Product;
use Metal\ProductsBundle\Entity\ValueObject\ProductMeasureProvider;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints as Assert;

class ProductType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $companyId = $options['company_id'];
        $builder
            ->add('title')
            ->add(
                'size',
                null,
                array(
                    'required' => false,
                )
            )
            ->add(
                'price',
                null,
                array(
                    'required' => false,
                )
            )
            ->add(
                'category',
                'entity_id',
                array(
                    'class' => 'MetalCategoriesBundle:Category',
                    // ангуляр не подставляет значения в скрытые поля
                    'hidden' => false,
                    'required' => false,

                )
            )
            ->add(
                'description',
                'textarea',
                array(
                    'required' => false,
                    'property_path' => 'productDescription.description',
                )
            )
            ->add(
                'measureId',
                'choice',
                array(
                    'choices' => ProductMeasureProvider::getAllTypesAsSimpleArray(),
                    'placeholder' => '',
                )
            );

        if ($options['existing_product_editing']) {
            $builder
                ->add(
                    'isSpecialOffer',
                    null,
                    array(
                        'required' => false,
                    )
                )
                ->add(
                    'position',
                    null,
                    array(
                        'required' => false,
                    )
                )
                ->add(
                    'isPriceFrom',
                    null,
                    array(
                        'required' => false,
                    )
                )
                ->add(
                    'isHotOffer',
                    null,
                    array(
                        'required' => false,
                    )
                )
                ->add(
                    'hotOfferPosition',
                    null,
                    array(
                        'required' => false,
                    )
                )
            ;
        } else {
            $builder
                ->add('image', ProductImageType::class)
                ->add(
                    'branchOffice',
                    'entity_id',
                    array(
                        'hidden' => false,
                        'required' => true,
                        'class' => 'MetalCompaniesBundle:CompanyCity',
                        'query_builder' => function (CompanyCityRepository $rep, $data) use ($companyId) {
                            return $rep->createQueryBuilder('cc')
                                ->join('cc.city', 'c')
                                ->addSelect('c')
                                ->andWhere('cc.company = :company_id')
                                ->andWhere('cc.id = :branch_office_id')
                                ->setParameter('branch_office_id', $data)
                                ->setParameter('company_id', $companyId)
                                ->setMaxResults(1);
                        },
                    )
                );
        }
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => 'Metal\ProductsBundle\Entity\Product',
                'existing_product_editing' => false,
            )
        );

        $resolver->setRequired(array('company_id'));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'metal_productsbundle_product';
    }
}
