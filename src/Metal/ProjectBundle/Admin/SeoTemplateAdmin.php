<?php

namespace Metal\ProjectBundle\Admin;

use Doctrine\ORM\EntityManager;
use Metal\ProjectBundle\Entity\SeoTemplate;
use Metal\ProjectBundle\Entity\SeoTemplateAttribute;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;

class SeoTemplateAdmin extends AbstractAdmin
{
    protected $perPageOptions = [10, 25, 50, 100, 150, 200];

    /**
     * @var EntityManager
     */
    private $em;

    public function __construct(
        $code,
        $class,
        $baseControllerName,
        EntityManager $em
    ) {
        parent::__construct($code, $class, $baseControllerName);

        $this->em = $em;
    }

    protected function configureRoutes(RouteCollection $collection)
    {
        $collection
            ->add('import', 'import', ['_controller' => 'MetalProjectBundle:SeoTemplateAdmin:import'])
            ->remove('create');
    }

    public function configure()
    {
        $this->setTemplate('list', 'MetalProjectBundle:SeoTemplateAdmin:list.html.twig');
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id')
            ->add(
                'name',
                null,
                [
                    'label' => 'Название',
                ]
            )
            ->add(
                'category',
                null,
                [
                    'label' => 'Категория',
                ]
            )
            ->add(
                'seoTemplateAttributes',
                null,
                [
                    'label' => 'Атрибуты',
                    'associated_property' => function (SeoTemplateAttribute $seoTemplateAttribute) {
                        $parts = [$seoTemplateAttribute->getAttribute()->getTitle()];

                        if ($seoTemplateAttribute->getAttributeValue()) {
                            $parts[] = $seoTemplateAttribute->getAttributeValue()->getValue();
                        }

                        return implode(': ', $parts);
                    },
                ]
            )
            ->add('priority', null, ['label' => 'Приоритет']);
    }

    protected function configureShowFields(ShowMapper $show)
    {
        $show
            ->with('Правила применения шаблона')
                ->add('name', null, ['label' => 'Название'])
                ->add('category', null, ['label' => 'Категория'])
                ->add(
                    'seoTemplateAttributes',
                    null,
                    [
                        'label' => 'Атрибуты',
                        'associated_property' => function (SeoTemplateAttribute $seoTemplateAttribute) {
                            $parts = [$seoTemplateAttribute->getAttribute()->getTitle()];

                            if ($seoTemplateAttribute->getAttributeValue()) {
                                $parts[] = $seoTemplateAttribute->getAttributeValue()->getValue();
                            }

                            return implode(': ', $parts);
                        },
                    ]
                )
                ->add('priority', null, ['label' => 'Приоритет'])
            ->end()
            ->with('Шаблоны')
                ->add('metadata.title', null, ['label' => 'Meta Title'])
                ->add('metadata.description', null, ['label' => 'Meta Description'])
                ->add('metadata.h1Title', null, ['label' => 'Meta H1'])
                ->add('textBlock', null, ['label' => 'Текстовый блок'])
            ->end();
    }


    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('name', null, ['label' => 'Название'])
            ->add(
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

    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with('Правила применения шаблона')
            ->add('name', null, ['label' => 'Название'])
            ->add('category', null, ['label' => 'Категория'])

            ->end()
            ->add('metadata.title',
                'textarea',
                array('label' => 'Meta title'))
            ->add('metadata.description',
                'textarea',
                array('label' => 'Meta Description'))
            ->add('metadata.h1Title',
                'textarea',
                array('label' => 'Meta H1'))
            ->add('textBlock',
                'textarea',
                array('label' => 'Текстовый блок'));
    }

    public function toString($object)
    {
        return $object instanceof SeoTemplate ? $object->getName() : '';
    }
}
