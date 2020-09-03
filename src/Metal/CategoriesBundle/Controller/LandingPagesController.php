<?php

namespace Metal\CategoriesBundle\Controller;

use Doctrine\ORM\EntityManager;
use Metal\CategoriesBundle\Entity\LandingPage;
use Metal\TerritorialBundle\Entity\City;
use Metal\TerritorialBundle\Entity\Country;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class LandingPagesController extends Controller
{
    public function searchAction(Request $request, Country $country, City $city = null)
    {
        $em = $this->getDoctrine()->getManager();
        /* @var $em EntityManager */

        $perPage = 1000;

        $qb = $em->getRepository('MetalCategoriesBundle:LandingPage')->createQueryBuilder('lp')
            ->select('lp')
        ;

        if ($city) {
            $qb
                ->join('lp.landingPageCityCounts', 'lpcc', 'WITH', 'lp.id = lpcc.landingPage and lpcc.city = :city_id')
                ->setParameter('city_id', $city->getId())
                ->andWhere('lpcc.resultsCount >= :min_products_count')
                ->setParameter('min_products_count', LandingPage::MIN_PRODUCTS_COUNT);
        } elseif ($country) {
            $qb
                ->join('lp.landingPageCountryCounts', 'lpctc', 'WITH', 'lp.id = lpctc.landingPage and lpctc.country = :country_id')
                ->setParameter('country_id', $country->getId())
                ->andWhere('lpctc.resultsCount >= :min_products_count')
                ->setParameter('min_products_count', LandingPage::MIN_PRODUCTS_COUNT);
        }

        $pagerfanta = new Pagerfanta(new DoctrineORMAdapter($qb, false));
        $pagerfanta
            ->setMaxPerPage($perPage)
            ->setCurrentPage($request->query->get('page', 1));

        if ($request->isXmlHttpRequest()) {
            return $this->render(
                '@MetalCategories/partial/landings_in_list.html.twig',
                array(
                    'pagerfanta' => $pagerfanta,
                )
            );
        }

        return $this->render('@MetalCategories/LandingPages/landings_list.html.twig',
            array(
                'pagerfanta' => $pagerfanta
            )
        );

    }
}
