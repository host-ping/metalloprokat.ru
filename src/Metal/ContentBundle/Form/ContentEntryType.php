<?php

namespace Metal\ContentBundle\Form;

use Metal\ContentBundle\Entity\Topic;
use Metal\ContentBundle\Entity\ValueObject\SubjectTypeProvider;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\Image;

class ContentEntryType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if (!$options['is_authenticated']) {
            $builder
                ->add('name')
                ->add('email');
        }

        $builder
            ->add('title')
            ->add('category', 'entity', array(
                'class' => 'MetalContentBundle:Category',
                'property' => 'nestedTitle',
                'placeholder' => 'Категория',
            ))
            ->add('categorySecondary', 'entity', array(
                'class' => 'MetalContentBundle:Category',
                'property' => 'nestedTitle',
                'placeholder' => 'Доп. категория'
            ))
            ->add('subjectTypeId', 'choice', array(
                'choices' => SubjectTypeProvider::getAllTypesAsSimpleArray(),
                'placeholder' => 'Раздел'
            ))
            ->add('description')
            ->add('shortDescription')
            ->add('contentEntryTags', 'collection', array(
                'type' => new ContentEntryTagType(),
                'by_reference' => false,
                'allow_add' => true,
                'allow_delete' => true,
            ))
        ;

        if ($options['data_class'] == Topic::class) {
            $builder
                ->add('uploadedImage', 'file', array(
                    'required' => false,
                    'attr' => array(
                        'accept' => 'image/*'
                    ),
                ))
            ;
        }
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setRequired(array('is_authenticated'));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'metal_content_entry';
    }
}
