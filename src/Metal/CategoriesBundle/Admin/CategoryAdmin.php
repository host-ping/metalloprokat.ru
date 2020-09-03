<?php

namespace Metal\CategoriesBundle\Admin;

use Behat\Transliterator\Transliterator;
use Cocur\Slugify\Slugify;
use Doctrine\ORM\EntityManager;
use Metal\CategoriesBundle\Entity\Category;
use Metal\CategoriesBundle\Service\ExpressionLanguage;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\CoreBundle\Validator\ErrorElement;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;
use Metal\ProductsBundle\Entity\ValueObject\ProductMeasureProvider;

class CategoryAdmin extends AbstractAdmin
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var Slugify
     */
    private $slugify;

    private $authorizationChecker;

    public function __construct($code, $class, $baseControllerName, EntityManager $em, Slugify $slugify, AuthorizationChecker $authorizationChecker)
    {
        parent::__construct($code, $class, $baseControllerName);

        $this->em = $em;
        $this->slugify = $slugify;
        $this->authorizationChecker = $authorizationChecker;
    }

    public function createQuery($context = 'list')
    {
        $query = parent::createQuery($context);
        $alias = $query->getRootAliases();
        $alias = reset($alias);
        $query->join(sprintf('%s.categoryExtended', $alias), 'ce')->addSelect('ce');

        return $query;
    }

    protected function configureFormFields(FormMapper $formMapper)
    {
        /* пока скрыто, мы их не используем
        $metaFieldsDescription = <<<HTML
<pre>шаблоны подстановок:
%category_title% - название категории,
%parameters_title% - дополнительные параметры для категории,
%category_title_accusative% - название категории в винительном падеже,
%category_title_genitive% - название категории в родительном падеже,
%city_title% - название города,
%city_title_locative% - название города в родительном падеже
</pre>
HTML;
*/
        $metaFieldsDescription = '';
        $subject = $this->getSubject();
        $id = $subject->getId();

        if ($this->allowFullEdit()) {
            $formMapper
            ->tab('Основное')
                ->add('title', null, array('label' => 'Название'))
                ->add(
                    'parent',
                    'entity',
                    array(
                        'label' => 'Родитель',
                        'required' => false,
                        'class' => 'MetalCategoriesBundle:Category',
                        'property' => 'nestedTitle',
                        'placeholder' => 'Без родителя',
                        'choices' => $this->em->getRepository('MetalCategoriesBundle:Category')->buildCategoriesByLevels($id),
                    )
                )
                ->add('slug', null, array('label' => 'Slug'))
                ->add('redirectSlug', null, array('label' => 'Слаг для Строй.ру'))
                ->add('allowProducts', 'checkbox', array('label' => 'Можно присоединять товары', 'required' => false))
                ->add('titleGenitive', null, array('label' => 'Родительный падеж', 'help' => 'Кого? Чего? - заявка на покупку арматуры композитной'))
                ->add('titleAccusative', null, array('label' => 'Винительный падеж', 'help' => 'Кого? Что? - заявка на арматуру композитную'))
                ->add('titlePrepositional', null, array('label' => 'Предложнный падеж', 'help' => 'О ком? О чем? - потребности в арматуре композитной'))
                ->add('titleAblative', null, array('label' => 'Творительный падеж', 'help' => 'Кем? Чем? - арматурой композитной'))
                ->add('isEnabled', 'checkbox', array('label' => 'Включена на портале', 'required' => false))
                ->add('isEnabledMetalspros', 'checkbox', array('label' => 'Включена на Металлспросе', 'required' => false))
                ->add('checkAge', 'checkbox', array('label' => 'От 18 лет', 'required' => false))
                ->add(
                    'volumeTypeId',
                    'choice',
                    array(
                        'label' => 'Основная ед. изм.',
                        'placeholder' => '',
                        'choices' => ProductMeasureProvider::getAllTypesAsSimpleArray(),
                        'attr' => array(
                            'style' => 'width: 160px;'
                        ),
                    )
                )
                ->end()
                ->end()

                ->tab('Автоопределение категории')
                ->add(
                    'extendedPatternEditor',
                    'textarea',
                    array(
                        'mapped' => false,
                        'label' => 'Расширеный шаблон',
                        'required' => false,
                        'attr' => array('id' => 'extended-pattern', 'rows' => 20)
                    ),
                    array('help' => '<input type="button" id="test-pattern-extended" value="Проверить"><ul id="bad-patterns-extended"></ul>')
                )

                ->add(
                    'categoryExtended.extendedPattern',
                    'textarea',
                    array(
                        'label' => ' ',
                        'required' => false,
                        'attr' => array('style' => 'display: none;', 'id' => 'extended-pattern', 'rows' => 20)
                    )
                )

                ->add('categoryExtended.pattern', 'textarea', array('label' => 'Шаблон', 'required' => false, 'attr' => array('rows' => 10, 'id' => 'pattern')),
                    array('help' => '<input type="button" id="test-pattern" value="Проверить"><ul id="bad-patterns"></ul>')
                )
                ->add('categoryExtended.testPattern', 'textarea', array('label' => 'Тест шаблона', 'required' => false, 'attr' => array('rows' => 10, 'id' => 'test-pattern')))
                ->add('categoryExtended.matchingPriority', null, array('label' => 'Приоритет', 'required' => false))
                ->end()
                ->end()

                ->tab('Особое поведение')
                ->add('realCategory', null, array('label' => 'Настоящая категория'))
                ->add('virtual', null, array('label' => 'Виртуальная категория', 'required' => false, 'help' => 'Виртуальная категория, не учитывается при выводе списка категорий.'))
                ->add('virtualParentsIds', 'textarea', array('label' => 'Виртуальные родители', 'required' => false, 'help' => 'Идентификаторы виртуальных родительских категорий через запятую'))
                ->end()
                ->end();
        }

        $formMapper
            ->tab('Seo', array('description' => $metaFieldsDescription))
                ->add('_h1Title', 'text', array('label' => 'H1 Заголовок', 'required' => false, 'property_path' => 'metadata.h1Title'))
                ->add('categoryExtended.description', 'textarea', array('label' => 'Описание', 'required' => false))
                ->add('priority', null, array('label' => 'Приоритет'))
                ->add('noindex', null, array('label' => 'Не индексировать', 'required' => false, 'help' => 'На списке товаров/заявок выводит мета-заголовки noindex.'))
                ->add('isVisibleForSeo', null, array('label' => 'Отображать ссылку на категорию', 'required' => false, 'help' => 'В просмотре конкретного товара из этой категории отображать ссылку на эту категорию в блоке "Товар содержится в категориях"'))
                ->add('titleForSeo', null, array('label' => 'Текст этой ссылки', 'required' => false))
                ->add('_metadataTitle', 'textarea', array('label' => 'Meta заголовок', 'required' => false, 'property_path' => 'metadata.title'))
                ->add('_metadataDescription', 'textarea', array('label' => 'Meta описание', 'required' => false, 'property_path' => 'metadata.description'))
            ->end()
            ->end()
        ;

        $formMapper
            ->tab('Метаданные по городам')
            ->add(
                'categoryCityMetadatas',
                'sonata_type_collection',
                array(
                    'label' => 'Метаданные по городам',
                    'by_reference' => false,
                    'required' => false
                ),
                array(
                    'edit' => 'inline',
                    'inline' => 'table',
                )
            )
            ->end()
            ->end();
    }

    public function getFormBuilder()
    {
        $formBuilder = parent::getFormBuilder();

        if (!$this->allowFullEdit()) {
            return $formBuilder;
        }

        $formBuilder->addEventListener(
            FormEvents::PRE_SUBMIT,
            function (FormEvent $event) {
                $data = $event->getData();

                if ($data['title'] && !$data['slug']) {
                    $slug = $this->slugify->slugify($data['title']);
                    $slug = Transliterator::urlize($slug);
                    $categoryRepository = $this->em->getRepository('MetalCategoriesBundle:Category');
                    $isDoubleSlug = $categoryRepository->findOneBy(array('slug' => $slug));
                    if ($isDoubleSlug) {
                        $slug .= '-'.mt_rand(0, 10);
                    }
                    $data['slug'] = $slug;
                }

                $event->setData($data);
            }
        );

        return $formBuilder;
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id')
            ->add('title', null, array('label' => 'Название'))
            ->add('slug', null, array('label' => 'Slug'))
            ->add('slugCombined', null, array('label' => 'SlugCombined'))
            ->add('allowProducts', null, array('label' => 'Можно присоединять товары'))
            ->add('priority', null, array('label' => 'Приоритет вывода'))
            ->add('categoryExtended.matchingPriority', null, array('label' => 'Приоритет автоопределения'))
            ->add('branchTitle', null, array('label' => 'Полный путь'))
            ->add('createdAt', null, array('label' => 'Дата добавления'))
        ;
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id')
            ->add('title', null, array('label' => 'Название'))
            ->add('slug', null, array('label' => 'Slug'))
            ->add('allowProducts', null, array('label' => 'Можно присоединять товары'))
            ->add('virtual', null, array('label' => 'Виртуальная'))
            ->add('noindex', null, array('label' => 'Не индексируются'))
            ->add(
                '_withoutPhoto',
                'doctrine_orm_callback',
                array(
                    'label' => 'Без фотографий',
                    'callback' => function ($queryBuilder, $alias, $field, $value) {
                        if (!isset($value['value'])) {
                            return;
                        }

                        if ($value['value'] === 'y') {
                            $queryBuilder->andWhere(sprintf('%s.allowProducts = true', $alias));
                            $queryBuilder->andWhere(sprintf('NOT EXISTS (SELECT pi.id FROM MetalProductsBundle:ProductImage pi WHERE pi.category = %s.id AND pi.company IS NULL)', $alias));
                        }

                        return true;
                    },
                    'field_type' => 'choice',
                ),
                null,
                array(
                    'choices' => array(
                        'y' => 'Да'
                    )
                )
            )
            ->add(
                '_extendedPatternIsEmpty',
                'doctrine_orm_callback',
                array(
                    'label' => 'Без расширенного шаблона',
                    'callback' => function ($queryBuilder, $alias, $field, $value) {
                        if (!isset($value['value'])) {
                            return;
                        }

                        $queryBuilder->join(sprintf('%s.categoryExtended', $alias), 'categoryExtended');

                        if ($value['value'] === 'y') {
                            $queryBuilder->andWhere('categoryExtended.extendedPattern = :emptyExtendedPattern');
                        } else {
                            $queryBuilder->andWhere('categoryExtended.extendedPattern <> :emptyExtendedPattern');
                        }

                        $queryBuilder->setParameter('emptyExtendedPattern', '');

                        return true;
                    },
                    'field_type' => 'choice',

                ),
                null,
                array(
                    'choices' => array(
                        'y' => 'Да',
                        'n' => 'Нет'
                    )
                )
            )
            ->add(
                'parent',
                null,
                array(
                    'label' => 'Родитель',
                    'required' => false,
                ),
                'entity',
                array(
                    'class' => 'MetalCategoriesBundle:Category',
                    'property' => 'nestedTitle',
                    'choices' => $this->em->getRepository('MetalCategoriesBundle:Category')->buildCategoriesByLevels()
                )
            )
        ;
    }

    public function postPersist($object)
    {
        $categoryRepository = $this->em->getRepository('MetalCategoriesBundle:Category');
        $categoryRepository->refreshCategoryData($object);
    }

    public function postUpdate($object)
    {
        $categoryRepository = $this->em->getRepository('MetalCategoriesBundle:Category');
        $categoryRepository->refreshCategoryData($object);
    }

    public function postRemove($object)
    {
        $categoryRepository = $this->em->getRepository('MetalCategoriesBundle:Category');
        $categoryRepository->onDeleteCategory();
    }

    public function toString($object)
    {
        return $object instanceof Category ? $object->getTitle() : 'Категории';
    }

    protected function configureRoutes(RouteCollection $collection)
    {
        $collection
            ->add('show_tree', 'showTree', array('_controller' => 'MetalCategoriesBundle:CategoryAdmin:showTree'))
            ->add('test_extended_pattern', 'testExtendedPattern', array('_controller' => 'MetalCategoriesBundle:CategoryAdmin:testExtendedPattern'))
            ->add(
                'change_position',
                $this->getRouterIdParameter().'/{action}',
                array('_controller' => 'MetalCategoriesBundle:MenuItemAdmin:changePosition'),
                array('action' => 'up|down')
            );
        parent::configureRoutes($collection);
    }

    public function getDashboardActions()
    {
        $actions = parent::getDashboardActions();

        $actions['show_tree'] = array(
            'label' => 'Просмотр в виде дерева',
            'url' => $this->generateUrl('show_tree'),
            'icon' => 'tree'
        );

        return $actions;
    }

    public function getBatchActions()
    {
        return array();
    }

    public function configure()
    {
        parent::configure();
        $this->setTemplate('list', 'MetalCategoriesBundle:CategoryAdmin:list.html.twig');
        $this->setTemplate('edit', 'MetalCategoriesBundle:CategoryAdmin:edit.html.twig');
    }

    public function validate(ErrorElement $errorElement, $object)
    {
        /* @var $object Category */
        if ($extendedPattern = $object->getCategoryExtended()->getExtendedPattern()) {
            $expressionLanguage = new ExpressionLanguage();

            //TODO: убрать это когда появится linter https://github.com/symfony/symfony/issues/16323
            $error = '';
            set_error_handler(function($errno, $errstr) use (&$error) {
                $error = $errstr;
            });

            $expressionLanguage->compile($extendedPattern, array('title'));

            restore_error_handler();

            if ($error) {
                $errorElement
                    ->with('categoryExtended.extendedPattern')
                    ->addViolation($error)
                    ->end();
            }
        }
    }

    private function allowFullEdit()
    {
        return $this->authorizationChecker->isGranted('ROLE_EXTENDED_MODERATOR');
    }
}
