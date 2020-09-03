<?php

namespace Metal\CategoriesBundle\Controller;

use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class SuggestController extends Controller
{
    public function getCategoriesAction(Request $request)
    {
        $q = $request->query->get('q');
        $qb = $this->getDoctrine()->getManager()
            ->createQueryBuilder()
            ->select('c.id AS id, c.title AS title, pc.title AS parent_title')
            ->from('MetalCategoriesBundle:Category', 'c')
            ->leftJoin('c.parent', 'pc')
            ->andWhere('c.isEnabled = true')
            ->andWhere('c.virtual = false')
            ->addOrderBy('c.title', 'ASC');

        if ($request->query->get('allow_products')) {
            $qb->andWhere('c.allowProducts = :allowProducts')
                ->setParameter('allowProducts', true);
        }

        if ($q && !$request->query->get('full')) {
            $qb
                ->andWhere('c.title LIKE :q')
                ->setParameter('q', '%'.$q.'%');
        }

        $categories = $qb
            ->getQuery()
            ->getArrayResult();

        return JsonResponse::create($categories);
    }

    public function getCategoriesByLevelsAction()
    {
        $em = $this->getDoctrine()->getManager();
        /* @var $em EntityManager */

        $categoryRepository = $em->getRepository('MetalCategoriesBundle:Category');
        $categories = $categoryRepository->findBy(array('isEnabled' => true, 'virtual' => false));
        $categories = $categoryRepository->serializeAndFlattenCategories($categories);

        return JsonResponse::create($categories);
    }
}
