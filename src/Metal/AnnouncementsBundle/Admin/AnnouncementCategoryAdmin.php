<?php

namespace Metal\AnnouncementsBundle\Admin;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Metal\AnnouncementsBundle\Entity\AnnouncementCategory;
use Metal\CategoriesBundle\Repository\CategoryRepository;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Form\FormMapper;

class AnnouncementCategoryAdmin extends AbstractAdmin
{
    /**
     * @var EntityManager
     */
    private $em;

    public function __construct($code, $class, $baseControllerName, EntityManagerInterface $em)
    {
        parent::__construct($code, $class, $baseControllerName);

        $this->em = $em;
    }

    protected function configureFormFields(FormMapper $form)
    {
        static $i = -1;
        $i++;

        $form
            ->add('categoryTitle', 'text', array(
                'label' => 'Категория',
                'required' => false,
                'attr' => array(
                    'style' => 'width: 300px',
                    'typeahead' => '',
                    'typeahead-prefetch-url' => $this->getRouteGenerator()->generate('MetalCategoriesBundle:Suggest:getCategories', array('full' => true)),
                    'typeahead-model' => "categories$i",
                    'typeahead-suggestion-template-url' => "'typeahead-suggestion-with-parent'",
                )
            ))
            ->add('category', 'entity_id', array(
                'required' => false,
                'class' => 'MetalCategoriesBundle:Category',
                'hidden' => false,
                'read_only' => true,
                'query_builder' => function(CategoryRepository $repo, $id) {
                    return $repo
                        ->createQueryBuilder('c')
                        ->select('PARTIAL c.{id, title, branchIds}')
                        ->andWhere('c.id = :id')
                        ->setParameter('id', $id);
                },
                'attr' => array(
                    'ng-model' => "categories$i.id",
                    'initial-value' => '',
                ),
            ))
        ;
    }

    public function toString($object)
    {
        return $object instanceof AnnouncementCategory ? $object->getCategory()->getTitle() : '';
    }
}
