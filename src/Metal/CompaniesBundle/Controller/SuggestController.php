<?php

namespace Metal\CompaniesBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class SuggestController extends Controller
{
    public function getCompaniesAction(Request $request)
    {
        $q = $request->query->get('q');

        $companiesRepository = $this->getDoctrine()->getManager()->getRepository('MetalCompaniesBundle:Company');
        $qb = $companiesRepository->createQueryBuilder('c');
        $qb->andWhere('c.title LIKE :q')
            ->setParameter('q', '%'.$q.'%');

        $companies = $qb->addOrderBy('c.title', 'ASC')
            ->getQuery()
            ->getArrayResult();

        $companiesResponse = array();
        foreach ($companies as $comp) {
            $companiesResponse[] = array(
                'id' => $comp['id'],
                'title' => $comp['title'],
            );
        }

        return JsonResponse::create($companiesResponse);
    }
}
