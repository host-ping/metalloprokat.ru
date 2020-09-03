<?php

namespace Metal\ContentBundle\Widget;

use Brouzie\Bundle\WidgetsBundle\Widget\WidgetAbstract;
use Doctrine\ORM\EntityManager;
use Metal\CategoriesBundle\Entity\PlainMenuItem;
use Metal\ContentBundle\Entity\Category;
use Metal\ContentBundle\Entity\CategoryClosure;

class BreadcrumbsWidget extends WidgetAbstract
{
    public function setDefaultOptions()
    {
        $this
            ->optionsResolver
            ->setRequired(array('route', 'homepage_route'))
            ->setDefined(array('category'))
            ->setAllowedTypes('category', array(Category::class, 'null'))
            ->setDefaults(
                array(
                    'route_params' => array(),
                    'homepage_route_params' => array(),
                    'append_items' => array(),
                    '_template' => '@MetalCategories/widgets/BreadcrumbsWidget.html.twig'
                )
            );
    }

    public function getParametersToRender()
    {
        $em = $this->getDoctrine()->getManager();
        /* @var $em EntityManager */
        if (!$this->options['category']) {
            $categories = $em->getRepository('MetalContentBundle:Category')->findAll();

            $rootItems = array_filter($categories, function (Category $menuItem) {
                return $menuItem->isRoot();
            });

            foreach ($categories as $category) {
                if ($category->getParent()) {
                    $category->getParent()->loadedSiblings[] = $category;
                }
            }

            return array(
                'branch' => $rootItems,
            );
        }

        $branchClosures = $em->createQueryBuilder()
            ->select('mic')
            ->from('MetalContentBundle:CategoryClosure', 'mic')
            ->join('mic.ancestor', 'anc')
            ->addSelect('anc')
            ->where('mic.descendant = :menu_item')
            ->setParameter('menu_item', $this->options['category'])
            ->orderBy('mic.depth', 'DESC')
            ->getQuery()
            ->getResult();
        /* @var $branchClosures CategoryClosure[] */

        $branch = array();
        $parentToMenuItem = array();
        foreach ($branchClosures as $branchClosure) {
            $category = $branchClosure->getAncestor();
            $branch[$category->getId()] = $category;
            $parentToMenuItem[$category->getParentId()] = $category;
        }

        $categoryRepository = $em->getRepository('MetalContentBundle:Category');

        $siblings = $categoryRepository->getSiblingsForBranch($branch);
        $rootItems = array_filter($siblings, function (Category $menuItem) {
            return $menuItem->isRoot();
        });
        $childItems = $categoryRepository->getChildrenForItems($rootItems);

        // 2. строим иерархию
        foreach ($siblings as $siblingMenuItem) {
            if (!isset($branch[$siblingMenuItem->getId()])) {
                $parentToMenuItem[$siblingMenuItem->getParentId()]->loadedSiblings[] = $siblingMenuItem;
            }
        }
        foreach ($childItems as $childItem) {
            $rootItems[$childItem->getParentId()]->loadedChildren[] = $childItem;
        }

        //TODO: продумать кеширование виджета

        // 3. удаляем соседей корневого элемента, у которых нет ни одного дочернего элемента
        $rootItem = reset($branch);
        $rootItem->loadedSiblings = array_filter(
            $rootItem->loadedSiblings,
            function (Category $menuItem) {
                return count($menuItem->loadedChildren) > 0;
            }
        );

//        clear_buffer();
//        doctrine_dump($parentToMenuItem);
//        exit;

        foreach ($this->options['append_items'] as $appendItemRaw) {
            $appendItem = new PlainMenuItem();
            $appendItem->setId($appendItemRaw['id']);
            $appendItem->setTitle($appendItemRaw['title']);
            $appendItem->setIsLabel($appendItemRaw['is_label']);
            if (!empty($appendItemRaw['slug_combined'])) {
                $appendItem->setSlugCombined($appendItemRaw['slug_combined']);
            }

            if (!empty($appendItemRaw['siblings'])) {
                foreach ($appendItemRaw['siblings'] as $siblingItemRaw) {
                    $siblingItem = new PlainMenuItem();
                    $siblingItem->setTitle($siblingItemRaw['title']);
                    $siblingItem->setIsLabel($siblingItemRaw['is_label']);
                    if (!empty($siblingItemRaw['slug_combined'])) {
                        $siblingItem->setSlugCombined($siblingItemRaw['slug_combined']);
                    }

                    $appendItem->loadedSiblings[] = $siblingItem;
                }
            }

            $branch[] = $appendItem;
        }

        return array(
            'branch' => $branch,
        );
    }
}
