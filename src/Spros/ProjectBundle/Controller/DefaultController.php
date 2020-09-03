<?php

namespace Spros\ProjectBundle\Controller;

use Doctrine\ORM\EntityManager;

use Metal\DemandsBundle\Entity\DemandFile;
use Metal\ProductsBundle\DataFetching\Spec\ProductsFilteringSpec;
use Metal\ProductsBundle\Entity\ValueObject\ProductMeasureProvider;
use Metal\ProjectBundle\DataFetching\DataFetcher;
use Metal\ProjectBundle\Entity\ValueObject\SiteSourceTypeProvider;
use Metal\CategoriesBundle\Entity\Category;
use Metal\CategoriesBundle\Repository\CategoryRepository;
use Metal\TerritorialBundle\Entity\City;
use Metal\DemandsBundle\Entity\Demand;
use Metal\DemandsBundle\Form\DemandType;
use Metal\DemandsBundle\Entity\DemandItem;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Geocoder\Exception\NoResultException;

class DefaultController extends Controller
{
    public function parseAction(Request $request)
    {
        $routeBack = $request->query->get('_returnTo');

        $em = $this->getDoctrine()->getManager();
        $categoryRepository = $em->getRepository('MetalCategoriesBundle:Category');

        $categories = $categoryRepository
            ->createQueryBuilder('c')
            ->addSelect('categoryExtended')
            ->join('c.categoryExtended', 'categoryExtended')
            ->andWhere('c.isEnabledMetalspros = true')
            ->andWhere('c.virtual = false')
            ->andWhere('c.pattern IS NOT NULL')
            ->andWhere("c.pattern != '' ")
            ->getQuery()
            ->getResult();
        /* @var $categories Category[] */

        $str = $request->request->get('demands_data');
        $str = preg_replace("/[\t]+/u","\t",$str);
        $str = preg_split('/\r\n|\n|\r/', $str);

        $demandItems = array();
        $badDemandItems = array();

        foreach ($str as $line) {
            $cols = str_getcsv($line, "\t");
            $cols = array_filter(array_map('trim', $cols));
            $demandItem = array();

            foreach ($cols as $i => $col) {
                if ($measure = ProductMeasureProvider::createByPattern($col)) {
                    $demandItem['volumeType'] = $measure;
                    unset($cols[$i]);
                }
            }

            if (!isset($demandItem['volumeType'])){
                $badDemandItems[] = $line;
                continue;
            }

            foreach ($cols as $i => $col) {
                $col = str_replace(",", ".", $col);
                if (is_numeric($col) ){
                    $demandItem['volume'] = $col;
                    unset($cols[$i]);
                }
            }
            if (!isset($demandItem['volume'])){
                $badDemandItems[] = $line;
                continue;
            }

            foreach ($cols as $col) {
                foreach ($categories as $category) {
                    if (@preg_match('#'.$category->getCategoryExtended()->getPattern().'#iu', $col)) {
                        $demandItem['categoryId'] = $category->getId();
                        $demandItem['title'] = $col;
                    }
                }
            }
            if (!isset($demandItem['categoryId'])){
                $badDemandItems[] = $line;
                continue;
            }

            $demandItems[] = $demandItem;
        }

        $demandItems = array_map(
            function ($demandItemRaw) use ($em) {
                $demandItem = new DemandItem();
                $demandItem->setTitle($demandItemRaw['title']);
                $demandItem->setVolume($demandItemRaw['volume']);
                $demandItem->setVolumeType($demandItemRaw['volumeType']);

                $category = $em->getRepository('MetalCategoriesBundle:Category')->find($demandItemRaw['categoryId']);

                $demandItem->setCategory($category);

                return $demandItem;
            },
            $demandItems
        );

        $demand = new Demand();
        $demand->setInfo(implode($badDemandItems, "\r\n"));

        if ($demandItems) {
            foreach ($demandItems as $demandItem) {
                $demand->addDemandItem($demandItem);
            }
        } else {
            $demand->addDemandItem(new DemandItem());
        }

        $form = $this->createForm(new DemandType(), $demand);

        if (count($badDemandItems)) {
            $this->get('session')->getFlashBag()->set(
                'error',
                'Не удалось распознать некоторые строки'
            );
        }

        return $this->render(
            'SprosProjectBundle:Default:form.html.twig',
            array(
                'form' => $form->createView(),
                'demand' => $demand,
                '_returnTo' => $routeBack,
            )
        );
    }

    public function indexAction(Request $request, $subdomain = null, $category_slug = null)
    {
        $city = $request->attributes->get('city');

        $category = $request->attributes->get('category');
        if ($category_slug && !$category) {
            throw $this->createNotFoundException('Category not found');
        }

        $data = $this->getDataForIndex($request, $city, $category);

        $template = 'SprosProjectBundle:Default:index.html.twig'; // для спроса index.html.twig
        if ($request->query->get('light')) {
            $template = 'SprosProjectBundle:Default:afterSendForm.html.twig';
        }

        $countries = $this->getDoctrine()->getRepository('MetalTerritorialBundle:Country')->getEnabledCountries();

        $orderedCountries = array();
        foreach ($countries as $country) {
            $orderedCountries[$country->getTitle()] = $country;
        }

        return $this->render(
            $template, $data + array(
                'city' => $city,
                'category' => $category,
                'countries' => $orderedCountries
            ));
    }

    public function lightIndexAction(Request $request, $category_slug = null)
    {
        $em = $this->getDoctrine()->getManager();
        /* @var $em EntityManager */

        $utmTerm = $request->query->get('utm_term');
        if ($utmTerm) {
            $categoryRepository = $em->getRepository('MetalCategoriesBundle:Category');
            /* @var $categoryRepository  CategoryRepository */

            $closest = $categoryRepository->getStringWithoutIn($utmTerm);
            $closest = $categoryRepository->getNormalizedString($closest);
        }

        $city = $request->attributes->get('city');
        if (!$city) {
            $ip = $request->server->get('REMOTE_ADDR');
            $cityByIp = null;
            try {
                $cityByIp = $this->get('bazinga_geocoder.geocoder')
                    ->using('sypexgeo')
                    ->geocode($ip);
            } catch (NoResultException $e) {

            }
            if ($cityByIp) {
                $city = $em->getRepository('MetalTerritorialBundle:City')->findOneBy(array('title' => $cityByIp->getCity()));
            }
        }

        $category = null;
        $categoryTitle = null;
        if ($utmTerm && !empty($closest)) {
            $categoryTitle = $closest;
        } else {
            $category = $request->attributes->get('category');
            if ($category_slug && !$category) {
                throw $this->createNotFoundException('Category not found');
            }
        }

        $data = $this->getDataForIndex($request, $city, $category);
        $template = 'SprosProjectBundle:Default:8-800.html.twig';
        if ($request->query->get('light')) {
            $template = 'SprosProjectBundle:Default:afterSendForm.html.twig';
        }

        $countries = $this->getDoctrine()->getRepository('MetalTerritorialBundle:Country')->findBy(array('id' => array(165, 209, 19, 83)));

        $orderedCounties = array();
        foreach ($countries as $country) {
            $orderedCounties[$country->getTitle()] = $country;
        }

        return $this->render(
            $template, $data + array(
                'city' => $city,
                'category' => $category,
                'categoryTitle' => $categoryTitle,
                'countries' => $orderedCounties
            ));
    }

    public function formAction(Request $request, $embedded = null)
    {
        $routeBack = $request->query->get('_returnTo');
        $categoryTitle = $request->query->get('category_title');

        $em = $this->getDoctrine()->getManager();
        $demand = new Demand();

        $demandItem = new DemandItem();
        $demand->addDemandItem($demandItem);

        $demandFile = new DemandFile();
        $demand->addDemandFile($demandFile);

        if (!$request->isMethod('POST')) {
            if ($cityId = $request->query->get('city')) {
                $city = $em->find('MetalTerritorialBundle:City', $cityId);
                if ($city) {
                    $demand->cityTitle = $city->getTitle();
                    $demand->setCity($city);
                }
            }

            if ($categoryId = $request->query->get('category')) {
                $category = $em->find('MetalCategoriesBundle:Category', $categoryId);
                if ($category) {
                    $demand->setCategory($category);
                    $demandItem->setCategory($category);
                    $demandItem->setTitle($category->getTitle().$request->query->get('parameters_title'));
                }
            } elseif ($categoryTitle) {
                $demandItem->setTitle($categoryTitle);
            }
        }

        $options = array(
            'validation_groups' => array('anonymous'),
            'city_repository' => $em->getRepository('MetalTerritorialBundle:City'),
        );

        $form = $this->createForm(new DemandType(), $demand, $options);

        $template = 'SprosProjectBundle:Default:form.html.twig';
        $source = SiteSourceTypeProvider::getSourceByHost($request->getHost());
        if ($source->getId() == SiteSourceTypeProvider::SOURCE_8_800) {
            $template = 'SprosProjectBundle:Default:form8-800.html.twig';
            if ($embedded) {
                $template = 'SprosProjectBundle:Default:form8-800_embedded.html.twig';
            }
        }

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);

            if (!$form->isValid()) {
                return $this->render($template, array(
                        'form' => $form->createView(),
                        'demand' => $demand,
                        '_returnTo' => $routeBack,
                        'categoryTitle' => $categoryTitle,
                    ));
            }

            $demand->populateDataFromRequest($request);

            $em->persist($demand);
            $em->flush();

            $this->get('session')->getFlashBag()->set(
                'success_demand',
                'Заявка успешно отправлена'
            );

            return $this->redirect($this->generateUrl('SprosProjectBundle:Default:index', array(
                 'light' => 1
                //'_returnTo' => $routeBack,
            )).'#begin_content');
        }

        return $this->render($template, array(
            'form' => $form->createView(),
            'demand' => $demand,
            '_returnTo' => $routeBack,
            'categoryTitle' => $categoryTitle,
        ));
    }

    public function oldUrlAction(Request $request, $subdomain)
    {
        $section = $request->query->get('section');
        if ($section){
            $section = mb_convert_encoding($section, 'UTF-8', 'WINDOWS-1251');
            $em = $this->getDoctrine()->getManager();

            $categoryRepository = $em->getRepository('MetalCategoriesBundle:Category');
            /* @var $categoryRepository \Metal\CategoriesBundle\Repository\CategoryRepository */

            $category = $categoryRepository->createQueryBuilder('c')
                ->andWhere('c.isEnabledMetalspros = true')
                ->andWhere('c.virtual = false')
                ->andWhere('c.title LIKE :section')
                ->setParameter('section', '%' . $section . '%')
                ->setMaxResults(1)
                ->getQuery()
                ->getOneOrNullResult();

            if ($category) {
                return $this->redirect($this->generateUrl('SprosProjectBundle:Default:index_category', array(
                    'category_slug' => $category->getSlugCombined(),
                )), 301);
            }
        }

        return $this->redirect($this->generateUrl('SprosProjectBundle:Default:index'), 301);
    }

    protected function getDataForIndex(Request $request, City $city = null, Category $category = null)
    {
        $em = $this->getDoctrine()->getManager();
        $country = $request->get('country');

        $regionRepository = $em->getRepository('MetalTerritorialBundle:Region');
        /* @var $regionRepository \Metal\TerritorialBundle\Repository\RegionRepository */

        $categoryRepository = $em->getRepository('MetalCategoriesBundle:Category');
        /* @var $categoryRepository \Metal\CategoriesBundle\Repository\CategoryRepository */

        $regionsData = $regionRepository->getRegionsWithCities();

        $categories = $categoryRepository
            ->createQueryBuilder('c')
            ->andWhere('c.isEnabledMetalspros = true')
            ->andWhere('c.virtual = false')
            ->leftJoin('c.parent', 'p')
            ->addSelect('p')
            ->getQuery()
            ->getResult();

        $categories = $categoryRepository->buildCategoriesHierarchy($categories);

        $dataFetcher = $this->container->get('metal.products.data_fetcher');
        $criteria = (new ProductsFilteringSpec())
            ->category($category)
            ->country($country)
            ->city($city)
            ->allowVirtual(true)
            ->loadCompanies(true);

        $companiesCount = $dataFetcher->getItemsCountByCriteria($criteria, DataFetcher::TTL_5DAYS);

        return compact('regionsData', 'categories', 'companiesCount');
    }
}
