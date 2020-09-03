<?php

namespace Metal\ProductsBundle\Form;

use Metal\CompaniesBundle\Entity\CompanyCity;
use Metal\CompaniesBundle\Repository\CompanyCityRepository;
use Metal\ProductsBundle\Entity\Product;
use Metal\ProductsBundle\Entity\ValueObject\ProductMeasureProvider;

use Metal\ProductsBundle\Repository\ProductImageRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ProductsBatchEditType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $fields = array(
            'isSpecialOffer',
            'isHotOffer',
            'isPriceFrom',
            'category',
            'checked',
            'measureId',
        );

        $builder
            ->add(
                'isSpecialOffer',
                'checkbox',
                array(
                    'label' => 'Спецпредложение',
                    'required' => false,
                )
            )
            ->add(
                'isHotOffer',
                'checkbox',
                array(
                    'label' => 'Горячее предложение',
                    'required' => false,
                )
            )
            ->add(
                'isPriceFrom',
                'checkbox',
                array(
                    'label' => 'Цена от',
                    'required' => false,
                )
            )
            ->add(
                'categoryTitle',
                'text',
                array(
                    'label' => 'Категория',
                    'required' => false,
                    'mapped' => false
                )
            )
            ->add(
                'category',
                'entity_id',
                array(
                    'label' => 'ID категории',
                    'class' => 'MetalCategoriesBundle:Category',
                    'hidden' => false,
                    'read_only' => true
                )
            )
            ->add(
                'checked',
                'choice',
                array(
                    'label' => 'Статус',
                    'required' => false,
                    'choices' => Product::getAvailableStatusesForEdit()
                )
            )
            ->add(
                'measureId',
                'choice',
                array(
                    'label' => 'Ед. измерения',
                    'choices' => ProductMeasureProvider::getAllTypesAsSimpleArray()
                )
            );

        $companyId = $options['company_id'];
        if ($companyId) {
            $builder
                ->add(
                    'branchOffice',
                    'entity',
                    array(
                        'label' => 'Филиал',
                        'required' => false,
                        'class' => 'MetalCompaniesBundle:CompanyCity',
                        'query_builder' => function (CompanyCityRepository $rep) use ($companyId) {
                            return $rep->createQueryBuilder('cc')
                                ->join('cc.city', 'c')
                                ->addSelect('c')
                                ->andWhere('cc.company = :company_id')
                                ->andWhere('cc.kind = :kind')
                                ->setParameter('company_id', $companyId)
                                ->setParameter('kind', CompanyCity::KIND_BRANCH_OFFICE)
                                ->orderBy('cc.isMainOffice', 'DESC')
                                ->addOrderBy('c.title');
                        },
                        'property' => 'city.title'
                    )
                );

            $fields[] = 'branchOffice';
        }

        if ($options['show_images']) {
            $builder->add(
                'image',
                'entity',
                array(
                    'label' => 'Привязать фото',
                    'required' => true,
                    'class' => 'MetalProductsBundle:ProductImage',
                    'query_builder' => function (ProductImageRepository $repo) use ($companyId) {
                        return $repo->getProductsImages($companyId);
                    },
                    'property' => 'photo.name'
                )
            );

            $fields[] = 'image';
        }


        foreach ($fields as $field) {
            $builder
                ->add(
                    $field.'Editable',
                    'checkbox',
                    array(
                        'label' => false,
                        'required' => false,
                    )
                );
        }
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array('company_id' => null, 'show_images' => false));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'metal_productadmin';
    }
}
