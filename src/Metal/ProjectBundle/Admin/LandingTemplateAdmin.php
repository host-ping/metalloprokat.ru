<?php

namespace Metal\ProjectBundle\Admin;

use Doctrine\ORM\EntityManagerInterface;
use Metal\ProjectBundle\Entity\LandingTemplate;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Vich\UploaderBundle\Form\Type\VichImageType;

/**
 * @method LandingTemplate getSubject()
 */
class LandingTemplateAdmin extends AbstractAdmin
{
    private $em;

    public function __construct($code, $class, $baseControllerName, EntityManagerInterface $em)
    {
        parent::__construct($code, $class, $baseControllerName);

        $this->em = $em;
    }

    public function setSubject($subject)
    {
        parent::setSubject($subject);
        /* @var $subject LandingTemplate */

        if (!$subject->getId()) {
            $this->formOptions['validation_groups'] = array('Default', 'new_item');
        }
    }

    protected function configureFormFields(FormMapper $formMapper)
    {
        $subject = $this->getSubject();

        $formMapper
            ->add(
                'category',
                'entity',
                array(
                    'label' => 'Категория',
                    'property' => 'nestedTitle',
                    'class' => 'MetalCategoriesBundle:Category',
                    'choices' => $this->em->getRepository('MetalCategoriesBundle:Category')->buildCategoriesByLevels(),
                )
            )
            ->add(
                'uploadedFile',
                VichImageType::class,
                array(
                    'label' => 'Фото',
                    'required' => !$subject->getFile()->getName(),
                    'imagine_pattern' => 'landing_template_big',
                    'download_uri' => false,
                )
            );
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id')
            ->add('category', null, array('label' => 'Категория', 'associated_property' => 'titleWithParent'))
            ->add(
                'uploadedFile',
                null,
                array(
                    'label' => 'Preview',
                    'template' => '@MetalProject/Admin/display_image_in_list.html.twig',
                    'image_filter' => 'landing_template_small',
                    'width' => 156,
                    'without_default_size' => true
                )
            )
            ->add(
                'landingWizzard',
                null,
                array(
                    'mapped' => false,
                    'label' => 'Лендинг с виззардом',
                    'template' => 'MetalProjectBundle:Admin:LandingTemplateAdmin/link_landing_page.html.twig',
                )
            )
            ->add(
                'landingSimple',
                null,
                array(
                    'mapped' => false,
                    'label' => 'Упрощенный лендинг',
                    'template' => 'MetalProjectBundle:Admin:LandingTemplateAdmin\link_landing_page.html.twig',
                )
            )
            ->add('createdAt', null, array('label' => 'Дата создания'))
            ->add('updatedAt', null, array('label' => 'Дата обновления'));
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add(
            'category',
            null,
            array(
                'label' => 'Категория',
                'required' => false,
            ),
            'entity',
            array(
                'class' => 'MetalCategoriesBundle:Category',
                'property' => 'nestedTitle',
                'choices' => $this->em->getRepository('MetalCategoriesBundle:Category')->buildCategoriesByLevels(),
            )
        );
    }

    public function toString($object)
    {
        return $object instanceof LandingTemplate && $object->getCategory() ? $object->getCategory()->getTitle() : '';
    }
}
