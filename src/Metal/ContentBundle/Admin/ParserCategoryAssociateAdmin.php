<?php
namespace Metal\ContentBundle\Admin;

use Doctrine\ORM\EntityManager;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;

class ParserCategoryAssociateAdmin extends AbstractAdmin
{
    protected $datagridValues = array(
        '_sort_order' => 'DESC',
        '_sort_by' => 'priority',
    );

    /**
     * @var EntityManager
     */
    private $em;

    public function __construct($code, $class, $baseControllerName, EntityManager $em)
    {
        parent::__construct($code, $class, $baseControllerName);

        $this->em = $em;
    }

    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->remove('delete');
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('parserCategoryId')
            ->add(
                'title',
                null,
                array('label' => 'Спарсеная категория')
            )
            ->add(
                'category',
                null,
                array('label' => 'Настоящая категория', 'property' => 'title')
            )
            ->add(
                'priority',
                null,
                array('label' => 'Приоритет')
            )
            ->add(
                'createdAt',
                null,
                array('label' => 'Дата добавления')
            )
        ;
    }

    protected function configureFormFields(FormMapper $formMapper)
    {
        $categories = $this->em->getRepository('MetalCategoriesBundle:Category')->buildCategoriesByLevels();
        $formMapper
            ->add('title', null, array('label' => 'Заголовок', 'read_only' => true))
            ->add(
                'category',
                'entity',
                array(
                    'label' => 'Категория',
                    'required' => false,
                    'class' => 'MetalCategoriesBundle:Category',
                    'property' => 'nestedTitleAndIsAllowedAddProducts',
                    'choices' => $categories,
                )
            )
        ;
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add(
                '_category',
                'doctrine_orm_callback',
                array(
                    'label' => 'Категория определена',
                    'callback' => function ($queryBuilder, $alias, $field, $value) {
                        if (!isset($value['value'])) {
                            return;
                        }

                        if ($value['value'] === 'y') {
                            $queryBuilder->andWhere(sprintf('%s.category IS NOT NULL', $alias));
                        } else {
                            $queryBuilder->andWhere(sprintf('%s.category IS NULL', $alias));
                        }

                        return true;
                    }
                ),
                'choice',
                array(
                    'choices' => array(
                        'y' => 'Да',
                        'n' => 'Нет'
                    )
                )
            )
        ;
    }
}
