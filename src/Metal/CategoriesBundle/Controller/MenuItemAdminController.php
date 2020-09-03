<?php

namespace Metal\CategoriesBundle\Controller;

use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManager;
use Metal\CategoriesBundle\Entity\MenuItem;
use Sonata\AdminBundle\Controller\CRUDController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

class MenuItemAdminController extends CRUDController
{
    public function showTreeAction()
    {
        $em = $this->getDoctrine()->getManager();
        $menuItems = $em->getRepository('MetalCategoriesBundle:MenuItem')->findBy(array(), array('position' => 'ASC'));
        $treeMenuItems = array();
        foreach ($menuItems as $menuItem) {
            if ($menuItem->getParent()) {
                $treeMenuItems[$menuItem->getParent()->getId()][] = $menuItem;
            } else {
                $treeMenuItems[0][] = $menuItem;
            }
        }

        return $this->render(
            'MetalCategoriesBundle:MenuItemAdmin:showTree.html.twig',
            array(
                'treeMenuItems' => $treeMenuItems,
                'object' => null,
                'action' => 'show'
            )
        );
    }

    public function changePositionAction(Request $request, $action)
    {
        $id = $request->get($this->admin->getIdParameter());
        $menuItem = $this->admin->getObject($id);

        /* @var $em EntityManager */
        $em = $this->getDoctrine()->getManager();
        $menuItemRepository = $em->getRepository('MetalCategoriesBundle:MenuItem');

        $qb = $menuItemRepository->createQueryBuilder('mmi')
            ->select('mmi');
        if ($menuItem->getParent()) {
            $qb->where('mmi.parent = :parent')
                ->setParameter('parent', $menuItem->getParent());
        } else {
            $qb->where('mmi.parent is null');
        }

        if ($action == 'down') {
            $qb->andWhere('mmi.position > :position');
            $qb->orderBy('mmi.position', 'ASC');
        } elseif ($action == 'up') {
            $qb->andWhere('mmi.position < :position');
            $qb->orderBy('mmi.position', 'DESC');
        }

        $qb->setParameter('position', $menuItem->getPosition());
        $qb->setMaxResults(1);

        /* @var $siblingMenuItem MenuItem */
        $siblingMenuItem = $qb->getQuery()->getOneOrNullResult();

        if (!$siblingMenuItem) {
            return new RedirectResponse($request->headers->get('REFERER'));
        }

        $menuItemPosition = $menuItem->getPosition();
        $menuItem->setPosition($siblingMenuItem->getPosition());
        $siblingMenuItem->setPosition($menuItemPosition);

        $em->flush();

        return new RedirectResponse($request->headers->get('REFERER'));
    }

    public function validationAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        /* @var $em EntityManager */
        $conn = $em->getConnection();
        /* @var $conn Connection */
        $hasError = false;

        /*Вывод категорий у которых нет элементов меню*/
        $categoriesWithoutMenu = $conn->executeQuery(
            'SELECT cat.Message_ID
            FROM Message73 AS cat
              LEFT JOIN menu_item AS mi
                ON (cat.Message_ID = mi.category_id) AND mi.mode = :mode
            WHERE mi.id IS NULL',
            array(
                'mode' => MenuItem::MODE_REFERENCE
            )
        )->fetchAll();

        foreach ($categoriesWithoutMenu as $categoryWithoutMenu) {
            $hasError = true;
            $this->addFlash('sonata_flash_error', sprintf(
                    'Для категории %d нет ссылок.',
                    $categoryWithoutMenu['Message_ID']
                )
            );
        }

        /* Выводим айдишники категорий с дублями menuItems и mode = ссылка */
        $duplicatesMenuItem = $conn->executeQuery(
            "SELECT category_id, COUNT(*) AS el_count
            FROM menu_item
              WHERE mode = :mode AND category_id IS NOT NULL
            GROUP BY category_id HAVING el_count > 1",
            array(
                'mode' => MenuItem::MODE_REFERENCE
            )
        )->fetchAll();

        $categoriesIds = array_map(function ($categoryId) {
                return (int)$categoryId['category_id'];
            }, $duplicatesMenuItem );

        $menuItems = $conn->executeQuery(
            "SELECT id , category_id
            FROM menu_item
              WHERE mode = 1 AND category_id IN (:categoriesIds)",
            array(
                'mode' => MenuItem::MODE_REFERENCE,
                'categoriesIds' => $categoriesIds
            ),
            array(
                'categoriesIds' => Connection::PARAM_INT_ARRAY
            )
        )->fetchAll();

        $menuItemsToCategory = array();
        foreach ($menuItems as $menuItem) {
            $menuItemsToCategory[$menuItem['category_id']][] = $menuItem['id'];
        }

        foreach ($duplicatesMenuItem as $duplicateMenuItem) {
            $hasError = true;
            $this->addFlash('sonata_flash_error', sprintf(
                    'Для категории %d дублируются ссылки ids: %s.',
                    $duplicateMenuItem['category_id'], implode(', ', $menuItemsToCategory[$duplicateMenuItem['category_id']])
                )
            );
        }

        /*Элементы меню типа ссылка без категорий*/
        $menuItemsNotCategory = $conn->executeQuery(
            "SELECT id FROM menu_item WHERE mode = :mode AND category_id IS NULL",
            array(
                'mode' => MenuItem::MODE_REFERENCE
            )
        )->fetchAll();

        foreach ($menuItemsNotCategory as $menuItemNotCategory) {
            $hasError = true;
            $this->addFlash('sonata_flash_error', sprintf(
                    'Для ссылки %d не выбрана категория, возможно это надпись?',
                    $menuItemNotCategory['id']
                )
            );
        }
        //TODO  если меню айтем у нас имеет моде=лейбл и есть парент то полюбому у него должны быть depends_from_menu_items

        /*проверяем depends_from_menu_items и родителей*/
        $menuItemsDepends = $em
            ->createQueryBuilder()
            ->from('MetalCategoriesBundle:MenuItem', 'c', 'c.id')
            ->select("c.id")
            ->addSelect('IDENTITY(c.parent) as parent')
            ->addSelect('c.dependsFromMenuItems')
            ->getQuery()
            ->getResult();

        foreach ($menuItemsDepends as $menuItemDepends) {
            $explodeMenuItemDepends = array_map('intval', array_filter(explode(',', $menuItemDepends['dependsFromMenuItems'])));
            foreach ($explodeMenuItemDepends as $depend) {
                if (!isset($menuItemsDepends[$depend])) {
                    $hasError = true;
                    $this->addFlash('sonata_flash_error', sprintf(
                            'Для menuItem %d выбрана не существующая зависимость с id: %d',
                            $menuItemDepends['id'],
                            $depend
                        )
                    );
                } elseif ($menuItemsDepends[$depend]['parent'] != $menuItemDepends['parent']) {
                    $hasError = true;
                    $this->addFlash('sonata_flash_error', sprintf(
                            'У menuItem %d отличаются родители с зависимым menuItem с id: %d',
                            $menuItemDepends['id'],
                            $depend
                        )
                    );
                }
            }
        }

        if (!$hasError) {
            $this->addFlash('sonata_flash_success', 'Ошибок не найдено!');
        }

        return new RedirectResponse($request->headers->get('REFERER'));
    }
}
