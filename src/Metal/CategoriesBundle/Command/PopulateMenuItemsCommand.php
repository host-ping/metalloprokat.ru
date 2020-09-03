<?php

namespace Metal\CategoriesBundle\Command;

use Doctrine\ORM\EntityManager;
use Metal\CategoriesBundle\Entity\Category;
use Metal\CategoriesBundle\Entity\MenuItem;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PopulateMenuItemsCommand extends ContainerAwareCommand
{
    public function configure()
    {
        $this->setName('metal:categories:populate-menu-items');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln(sprintf('Start command %s at %s', $this->getName(), date('Y-m-d H:i')));

        $em = $this->getContainer()->get('doctrine.orm.default_entity_manager');
        /* @var $em EntityManager */
        $em->getConfiguration()->setSQLLogger(null);

        $categoryRepository = $em->getRepository('MetalCategoriesBundle:Category');
        $menuItemRepository = $em->getRepository('MetalCategoriesBundle:MenuItem');
        $categories = $categoryRepository
            ->createQueryBuilder('c')
            ->orderBy('c.priority', 'ASC')
            ->addOrderBy('c.title', 'ASC')
            ->getQuery()
            ->getResult();
        /* @var $categories Category[] */

        $output->writeln(sprintf('Transfer categories at %s', date('Y-m-d H:i')));
        foreach ($categories as $position => $category) {
            $hasReference = false;
            $existingMenuItems = $menuItemRepository->findBy(array('category' => $category));
            foreach ($existingMenuItems as $existingMenuItem) {
                $existingMenuItem->setTitle($category->getTitle());
                $existingMenuItem->setSlugCombined($category->getSlugCombined());
                if ($existingMenuItem->isReference()) {
                    $hasReference = true;
                }
            }

            if (!$hasReference) {
                $menuItem = new MenuItem();
                $menuItem->setCategory($category);
                $menuItem->setPosition($position + 1);
                $menuItem->setTitle($category->getTitle());
                $menuItem->setSlugCombined($category->getSlugCombined());

                $em->persist($menuItem);
            }

            $em->flush();
        }

        $output->writeln(sprintf('Transfer parent category at %s', date('Y-m-d H:i')));
        foreach ($categories as $category) {
            $parentCategory = $category->getParent();
            if (!$parentCategory) {
                continue;
            }

            $menuItem = $menuItemRepository->findOneBy(array('category' => $category, 'mode' => MenuItem::MODE_REFERENCE));
            /* @var $menuItem menuItem */
            $menuItemParent = $menuItemRepository->findOneBy(array('category' => $parentCategory, 'mode' => MenuItem::MODE_REFERENCE));
            /* @var $menuItemParent menuItem */

            $menuItem->setParent($menuItemParent);

            $em->flush();
        }

        $output->writeln(sprintf('End command %s at %s', $this->getName(), date('Y-m-d H:i')));
    }
}
