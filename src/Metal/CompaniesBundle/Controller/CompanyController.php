<?php

namespace Metal\CompaniesBundle\Controller;

use Doctrine\ORM\EntityManager;
use Metal\AttributesBundle\Entity\DTO\AttributesCollection;
use Metal\CategoriesBundle\Entity\Category;
use Metal\CompaniesBundle\Entity\Company;
use Metal\CompaniesBundle\Entity\CompanyReview;
use Metal\ProductsBundle\DataFetching\Elastica\ProductIndex;
use Metal\ProductsBundle\DataFetching\Spec\ProductsFilteringSpec;
use Metal\ProductsBundle\DataFetching\Spec\ProductsLoadingSpec;
use Metal\ProductsBundle\DataFetching\Spec\ProductsOrderingSpec;
use Metal\ProjectBundle\Entity\ValueObject\SourceTypeProvider;
use Metal\ProjectBundle\Form\ReviewType;
use Metal\ProjectBundle\Helper\UrlHelper;
use Metal\TerritorialBundle\Entity\City;
use Metal\TerritorialBundle\Entity\TerritoryInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class CompanyController extends Controller
{
    protected $productsPerPage = 20;

    public function productsAction(Request $request, TerritoryInterface $territory, Company $company, City $city = null, Category $category = null, AttributesCollection $attributes_collection = null)
    {
        // Открыли все продукты компании через несуществующий офис
        if ($city && $company->getVisibilityStatus() == Company::VISIBILITY_STATUS_NORMAL) {
            $companyCityRepository = $this->getDoctrine()->getRepository('MetalCompaniesBundle:CompanyCity');

            $companyCity = $companyCityRepository->findOneBy(array('company' => $company, 'city' => $city));

            if (!$companyCity && $city->isAdministrativeCenter()) {
                $companyCity = $companyCityRepository->getBranchOfficeInRegionWithoutSlug($company, $city);
            }

            if (!$companyCity) {
                $route = 'MetalCompaniesBundle:Company:products';
                if ($category) {
                    $route = 'MetalCompaniesBundle:Company:products_category';
                }

                $url = $this->generateUrl(
                    $route,
                    array(
                        'company_slug' => $company->getSlug(),
                        'category_slug' => $category ? $category->getSlugCombined() : null,
                        'subdomain' => $company->getCity()->getSlugWithFallback()
                    )
                );

                return $this->redirect($url, 301);
            }
        }

        $this->getDoctrine()
            ->getRepository('MetalCompaniesBundle:CompanyCity')
            ->attachCompanyCities(array($company), $territory);

        $specification = (new ProductsFilteringSpec())
            ->company($company)
            ->city($city ?: $company->getCity())
            ->price($request->query->get('price_from'), $request->query->get('price_to'))
            ->matchTitle($request->query->get('q'))
            ->maxMatches(25000);

        if ($category) {
            $specification
                ->category($category)
                ->attributesCollection($attributes_collection);
        }

        $orderBy = new ProductsOrderingSpec();
        if (!$orderBy->applyFromRequest($request)) {
            $orderBy->updatedAt();
        }

        $orderBy
            ->specialOffer()
            ->position();

        $page = $request->query->get('page', 1);
        $dataFetcher = $this->get('metal_products.data_fetcher_factory')
            ->getDataFetcher(ProductIndex::SCOPE);
        $pagerfanta = $dataFetcher->getPagerfantaByCriteria($specification, $orderBy, $this->productsPerPage, $page);
        $pagerfanta->getCurrentPageResults();

        // если в данной категории у компании нет товаров в каком-либо из филиалов
        // тогда пытаемся взять эти товары из главного офиса
        //TODO: подумать, нужна ли эта логика
        if (!count($pagerfanta) && $city && $city->getId() != $company->getCity()->getId()) {
            $specification->city($company->getCity());

            $pagerfanta = $dataFetcher
                ->getPagerfantaByCriteria($specification, $orderBy, $this->productsPerPage, $page);
        }

        $loaderOpts = (new ProductsLoadingSpec())
            ->trackShowing(SourceTypeProvider::ALL_COMPANY_PRODUCTS)
            ->normalizePrice($city->getCountry())
        ;

        $entityLoader = $this->get('metal.products.products_entity_loader');

        if ($request->isXmlHttpRequest()) {
            $response = JsonResponse::create(
                array(
                    'page.title' => $this->get('brouzie.helper_factory')->get('MetalProjectBundle:Seo')->getMetaTitleForAllProductsPage(),
                    'page.company_products_list' => $this->renderView(
                        '@MetalCompanies/partial/products_in_list.html.twig',
                        array(
                            'pagerfanta' => $entityLoader->getPagerfantaWithEntities($pagerfanta, $loaderOpts),
                            'current_city' => null
                        )
                    )
                )
            );

            $response->headers->addCacheControlDirective('no-store', true);

            return $response;
        }

        return $this->render(
            '@MetalCompanies/Company/products.html.twig',
            array(
                'productsViewModel' => $entityLoader->getListResultsViewModel($pagerfanta, $loaderOpts),
                'company' => $company,
                'category' => $category,
            )
        );
    }

    /**
     * @ParamConverter("company", class="MetalCompaniesBundle:Company", options={"repository_method"="loadCompany"})
     */
    public function redirectToMinisiteAction(Company $company)
    {
        //TODO: экшен не используется
        $urlHelper = $this->container->get('brouzie.helper_factory')->get('MetalProjectBundle:Url');
        /* @var $urlHelper UrlHelper */

        $url = $urlHelper->generateUrl(
            'MetalMiniSiteBundle:MiniSite:view',
            array('domain' => $company->getDomain(), '_secure' => $company->getPackageChecker()->isHttpsAvailable()),
            true
        );

        return $this->redirect($url, 301);
    }

    /**
     * @ParamConverter("company", class="MetalCompaniesBundle:Company", options={"repository_method"="loadCompany"})
     */
    public function redirectToCompanyProductsAction(Company $company)
    {
        return $this->redirect(
            $this->generateUrl('MetalCompaniesBundle:Company:products',
                array('company_slug' => $company->getSlug(), 'subdomain' => $company->getCity()->getSlugWithFallback()), true), 301);
    }

    /**
     * @ParamConverter("company", class="MetalCompaniesBundle:Company", options={"repository_method"="loadCompany"})
     */
    public function reviewAction(Request $request, Company $company, City $city = null)
    {
        $em = $this->getDoctrine()->getManager();
        /* @var $em EntityManager */

        $user = null;
        if ($this->isGranted('ROLE_USER')) {
            $user = $this->getUser();
        }

        $review = new CompanyReview();
        $form = $this->createForm(
            new ReviewType(),
            $review,
            array(
                'is_authenticated' => $user !== null,
                'validation_groups' => array(
                    $user !== null ? 'authenticated' : 'anonymous',
                ),
                'data_class' => CompanyReview::class,
            )
        );

        $form->handleRequest($request);

        if (!$form->isValid()) {
            $errors = $this->get('metal.project.form_helper')->getFormErrorMessages($form);

            return JsonResponse::create(array(
                'errors' => $errors,
            ));
        }

        $review->setCity($city ?: $company->getCity());
        $review->setCompany($company);
        $review->setUser($user);

        $categoryId = $request->query->get('category');
        if ($categoryId) {
            $category = $em->getRepository('MetalCategoriesBundle:Category')->find($categoryId);
            $review->setCategory($category);
        }

        $em->persist($review);
        $em->flush();

        $em->getRepository('MetalCompaniesBundle:CompanyCounter')->changeCounter($review->getCompany(), array('newCompanyReviewsCount'), true);

        $this->get('metal.companies.company_review_mailer')->notifyOfCompanyReview($review);

        return JsonResponse::create(array(
            'status' => 'success',
        ));
    }
}
