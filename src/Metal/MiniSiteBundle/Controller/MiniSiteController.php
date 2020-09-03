<?php

namespace Metal\MiniSiteBundle\Controller;

use Doctrine\ORM\EntityManager;
use Metal\AttributesBundle\DataFetching\AttributesFacetResult;
use Metal\AttributesBundle\Entity\Attribute;
use Metal\AttributesBundle\Entity\AttributeValue;
use Metal\AttributesBundle\Entity\DTO\AttributesCollection;
use Metal\CategoriesBundle\Entity\Category;
use Metal\CategoriesBundle\Entity\CategoryAbstract;
use Metal\CompaniesBundle\Entity\Company;
use Metal\CompaniesBundle\Entity\CompanyCity;
use Metal\CompaniesBundle\Entity\CompanyFile;
use Metal\CompaniesBundle\Entity\CustomCompanyCategory;
use Metal\CompaniesBundle\Form\SendEmailToEmployeeType;
use Metal\MiniSiteBundle\Helper\DefaultHelper;
use Metal\ProductsBundle\DataFetching\Spec\ProductsFacetSpec;
use Metal\ProductsBundle\DataFetching\Spec\ProductsFilteringSpec;
use Metal\ProductsBundle\DataFetching\Spec\ProductsLoadingSpec;
use Metal\ProductsBundle\DataFetching\Spec\ProductsOrderingSpec;
use Metal\ProductsBundle\Entity\Product;
use Metal\ProductsBundle\Widget\ProductTabsWidget;
use Metal\ProjectBundle\DataFetching\Sphinxy\FacetResultExtractor;
use Metal\ProjectBundle\Entity\ValueObject\SourceTypeProvider;
use Metal\ProjectBundle\Helper\UrlHelper;
use Metal\TerritorialBundle\Entity\Country;
use Metal\UsersBundle\Entity\User;
use Metal\UsersBundle\Repository\UserRepository;
use Pagerfanta\Adapter\FixedAdapter;
use Pagerfanta\Pagerfanta;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Vich\UploaderBundle\Util\Transliterator;

class MiniSiteController extends Controller
{
    protected $productsPerPage = 20;
    protected $productsSpecialOffers = 15;

    public function indexAction(Request $request, Company $company)
    {
        // показываются все товары компании из всех городов, сбрасываем критерий showOnPortal
        $specification = (new ProductsFilteringSpec())
            ->company($company)
            ->showOnPortal(null);

        if ($q = $request->query->get('q')) {
            $specification->matchTitle($q);
        }

        $loaderOpts = (new ProductsLoadingSpec())
            ->trackShowing(SourceTypeProvider::MINISITE);

        $orderBy = (new ProductsOrderingSpec())
            ->specialOffer()
            ->position()
            ->updatedAt()
            ->iterateByCategory();

        $page = $request->query->get('page', 1);
        $pagerfanta = $this->get('metal.products.data_fetcher')
            ->getPagerfantaByCriteria($specification, $orderBy, $this->productsSpecialOffers, $page);
        $pagerfanta = $this->get('metal.products.products_entity_loader')
            ->getPagerfantaWithEntities($pagerfanta, $loaderOpts);

        if ($request->isXmlHttpRequest()) {
            return $this->render(
                '@MetalCompanies/partial/products_in_list_mini_special.html.twig',
                compact('pagerfanta')
            );
        }

        return $this->filterResponse(
            $request,
            $this->render(
                '@MetalCompanies/MiniSite/view.html.twig',
                compact('pagerfanta')
            )
        );
    }

    public function hotOfferProductsAction(Company $company, Request $request)
    {
        $page = $request->get('page', 1);

        $pagerfanta = new Pagerfanta(new FixedAdapter(0, array()));
        if ($company->getPackageChecker()->getShowHotOfferMenuItem()) {
            $loaderOpts = (new ProductsLoadingSpec())
                ->trackShowing(SourceTypeProvider::MINISITE);

            $specification = (new ProductsFilteringSpec())
                ->company($company)
                ->showOnPortal(null)
            ;

            $specification->isHotOffer(true);

            /* @var $specification ProductsFilteringSpec*/

            if ($q = $request->query->get('q')) {
                $specification->matchTitle($q);
            }

            $dataFetcher = $this->container->get('metal.products.data_fetcher');
            $entityLoader = $this->get('metal.products.products_entity_loader');

            $orderBy = new ProductsOrderingSpec();

            if (!$orderBy->applyFromRequest($request)) {
                $orderBy->hotOfferPosition();
            }

            $pagerfanta = $dataFetcher->getPagerfantaByCriteria($specification, $orderBy, $this->productsPerPage, $page);
            $pagerfanta = $entityLoader->getPagerfantaWithEntities($pagerfanta, $loaderOpts);
        }

        if ($request->isXmlHttpRequest()) {
            return $this->render(
                '@MetalCompanies/partial/products_in_list_mini.html.twig',
                array(
                    'company' => $company,
                    'pagerfanta' => $pagerfanta,
                )
            );
        }

        return $this->render('@MetalCompanies/MiniSite/special_offer_products.htnl.twig', array(
            'company' => $company,
            'pagerfanta' => $pagerfanta,
        ));
    }

    /**
     * @ParamConverter("product", converter="products_converter", class="Metal\ProductsBundle\Entity\Product")
     */
    public function productViewAction(Company $company, Product $product, Request $request)
    {
        $productRepository = $this->getDoctrine()->getRepository('MetalProductsBundle:Product');
        $enabledProduct = null;
        if ($product->isDeleted()) {
            $enabledProduct = $productRepository->getEnabledDuplicateForProduct($product);
        }

        // открыли продукт на чужом минисайте
        if ($company->getId() !== $product->getCompany()->getId()) {
            $urlHelper = $this->container->get('brouzie.helper_factory')->get('MetalProjectBundle:Url');
            /* @var $urlHelper UrlHelper */

            return $this->redirect(
                $urlHelper->generateUrl(
                    'MetalMiniSiteBundle:MiniSite:product_view',
                    array(
                        'domain' => $product->getCompany()->getDomain(),
                        'id' => $product->getId(),
                        '_secure' => $company->getPackageChecker()->isHttpsAvailable()
                    )
                ),
                301
            );
        }

        $this->get('brouzie.helper_factory')->get('MetalProductsBundle')->trackProductView($product, SourceTypeProvider::createBySlug('mini-site'));

        if ($request->isXmlHttpRequest()) {
            $productsWidget = $this->get('brouzie_widgets.widget_manager')
                ->createWidget(
                    'MetalProductsBundle:ProductTabs',
                    array(
                        'product' => $product,
                        'city' => $company->getCity(),
                        'page' => $request->query->get('page', 1),
                        'available_tabs' => array('similar-products-tab'),
                        'active_tab' => 'similar-products-tab',
                        'disabled_normalize_price' => true,
                        'product_view_url_mode' => 'minisite'
                    )
                );
            /* @var $productsWidget ProductTabsWidget */

            $products = $productsWidget->getProducts();

            return $this->render(
                'MetalProductsBundle:Product:products_more.html.twig',
                array(
                    'pagerfanta' => $products['products']->pagerfanta,
                    'id' => 'similar-products-tab-more',
                    'productViewUrlMode' => 'minisite',
                    'use_pagination' => false,
                )
            );
        }

        return $this->filterResponse(
            $request,
            $this->render(
                'MetalCompaniesBundle:MiniSite:product.html.twig',
                array(
                    'product' => $product,
                    'category' => $product->getCategory(),
                    'enabledProduct' => $enabledProduct
                )
            )
        );
    }

    public function reviewsAction(Request $request, Company $company)
    {
        $perPage = 10;
        $pagerfanta = $this->getDoctrine()->getRepository('MetalCompaniesBundle:CompanyReview')
            ->getPagerfantaForCompanyReview(array('company' => $company), array('created_at' => 'DESC'), $perPage, $request->query->get('page', 1));

        if ($request->isXmlHttpRequest()) {
            return $this->render(
                'MetalCompaniesBundle:partial:minisite_review_in_list.html.twig',
                array(
                    'pagerfanta' => $pagerfanta,
                    'company' => $company
                )
            );
        }

        $companyCounter = $this->getDoctrine()->getRepository('MetalCompaniesBundle:CompanyCounter')->findBy(array('company' => $company));

        return $this->render('MetalCompaniesBundle:MiniSite:review.html.twig',
            array('company' => $company, 'companyCounter' => $companyCounter, 'pagerfanta' => $pagerfanta));
    }

    public function aboutAction(Company $company)
    {
        $em = $this->getDoctrine()->getManager();
        /* @var $em EntityManager */

        $companyCategories = $em
            ->getRepository('MetalCompaniesBundle:CompanyCategory')
            ->createQueryBuilder('cc')
            ->join('cc.category', 'c')
            ->addSelect('c')
            ->where('cc.company = :company')
            ->andWhere('cc.enabled = true')
            ->andWhere('c.allowProducts = true')
            ->setParameter('company', $company)
            ->orderBy('cc.displayPosition', 'ASC')
            ->addOrderBy('c.title', 'ASC')
            ->getQuery()
            ->getResult()
        ;

        $companyCities = array();
        $companyDeliveryText = '';
        if ($company->getPackageChecker()->isAllowedSetDeliveryDescription() && $company->getDeliveryDescription()) {
            $companyDeliveryText = $company->getDeliveryDescription();
        } else {
            $companyCities = $em->getRepository('MetalCompaniesBundle:CompanyCity')
                ->createQueryBuilder('cc')
                ->addSelect('city')
                ->join('cc.city', 'city')
                ->andWhere('cc.company = :company')
                ->setParameter('company', $company)
                ->andWhere('cc.enabled = true')
                ->orderBy('cc.displayPosition', 'ASC')
                ->addOrderBy('city.title', 'ASC')
                ->getQuery()
                ->getResult();
            /* @var $companyCities CompanyCity[]  */
        }

        $companyCitiesSorted = array(
            'deliveries' => array(),
            'filials' => array(),
        );

        foreach ($companyCities as $companyCity) {
            if ($companyCity->isBranchOffice()) {
                $companyCitiesSorted['filials'][] = $companyCity;
            } else {
                $companyCitiesSorted['deliveries'][] = $companyCity;
            }
        }

        return $this->render(
            '@MetalCompanies/MiniSite/about.html.twig',
            array(
                'company' => $company,
                'companyCategories' => $companyCategories,
                'cities' => $companyCitiesSorted,
                'companyDeliveryText' => $companyDeliveryText
            )
        );
    }

    public function contactAction(Company $company)
    {
        $em = $this->getDoctrine()->getManager();
        /* @var $em EntityManager */

        $userRepository = $em->getRepository('MetalUsersBundle:User');
        /* @var $userRepository UserRepository */

        $companyDetails = $em->getRepository('MetalCompaniesBundle:PaymentDetails')
            ->findOneBy(array('company' => $company));

        $companyCities = $em->getRepository('MetalCompaniesBundle:CompanyCity')
            ->createQueryBuilder('companyCity')
            ->addSelect('city')
            ->join('companyCity.city', 'city')
            ->andWhere('companyCity.company = :company')
            ->setParameter('company', $company)
            ->andWhere('companyCity.address <> :empty')
            ->setParameter('empty', '')
            ->andWhere('companyCity.enabled = true')
            ->orderBy('companyCity.displayPosition', 'ASC')
            ->addOrderBy('city.population', 'DESC')
            ->addOrderBy('city.title', 'ASC')
            ->getQuery()
            ->getResult();
        /* @var $companyCities CompanyCity[]  */

        $minisiteHelper = $this->container->get('brouzie.helper_factory')->get('MetalMiniSiteBundle:Default');
        /* @var $minisiteHelper DefaultHelper */

        if ($currentCity = $minisiteHelper->getCurrentCity()) {
            foreach ($companyCities as $key => $companyCity) {
                if ($companyCity->getCity()->getId() == $currentCity->getId()) {
                    $currentBranchOffice = $companyCity;
                    unset($companyCities[$key]);
                    array_unshift($companyCities, $currentBranchOffice);
                    break;
                }
            }
        } else {
            $currentCity = $company->getCity();
        }

        $employees = $userRepository->getEmployeesForTerritory($company, $currentCity);

        return $this->render('MetalCompaniesBundle:MiniSite:contact.html.twig',
            array(
                'company' => $company,
                'employees' => $employees,
                'offices' => $companyCities,
                'companyDetails' => $companyDetails
            )
        );
    }

    public function productsAction(Request $request, Company $company, CategoryAbstract $category, AttributesCollection $attributes_collection)
    {
        $page = $request->get('page', 1);

        $em = $this->getDoctrine()->getManager();
        /* @var $em EntityManager */

        $loaderOpts = (new ProductsLoadingSpec())
            ->trackShowing(SourceTypeProvider::MINISITE);

        $specification = (new ProductsFilteringSpec())
            ->company($company)
            ->category($category)
            ->attributesCollection($attributes_collection)
            ->showOnPortal(null)
        ;

        /* @var $specification ProductsFilteringSpec*/

        if ($q = $request->query->get('q')) {
            $specification->matchTitle($q);
        }

        if ($cityId = $request->query->get('city')) {
            $specification->productCity($em->find('MetalTerritorialBundle:City', $cityId));
        }

        $dataFetcher = $this->container->get('metal.products.data_fetcher');
        $entityLoader = $this->get('metal.products.products_entity_loader');

        $orderBy = new ProductsOrderingSpec();
        if (!$orderBy->applyFromRequest($request)) {
            $orderBy->updatedAt();
        }

        $orderBy->specialOffer();

        $pagerfanta = $dataFetcher->getPagerfantaByCriteria($specification, $orderBy, $this->productsPerPage, $page);
        $pagerfanta = $entityLoader->getPagerfantaWithEntities($pagerfanta, $loaderOpts);

        if ($request->isXmlHttpRequest()) {
            return $this->render(
                '@MetalCompanies/partial/products_in_list_mini.html.twig',
                array(
                    'company' => $company,
                    'pagerfanta' => $pagerfanta,
                )
            );
        }

        $attributeValueRepository = $em->getRepository('MetalAttributesBundle:AttributeValue');

        $cities = array();
        $attributesCollection = null;
        $facetsResultSet = null;
        $facetSpec = new ProductsFacetSpec();
        $facetSpec->facetByProductCity($specification);

        $orders = array(Attribute::ORDER_OUTPUT_PRIORITY, AttributeValue::ORDER_OUTPUT_PRIORITY);

        if ($category instanceof Category && $category->getAllowProducts()) {
            $attributes = $em->getRepository('MetalAttributesBundle:AttributeCategory')
                ->getAttributesForCategory($category);

            foreach ($attributes as $attribute) {
                $facetSpec->facetByAttribute($attribute, $specification);
            }

            $facetsResultSet = $dataFetcher->getFacetedResultSetByCriteria($specification, $facetSpec);
            $attributesCollection = $attributeValueRepository->loadCollectionByFacetResult(
                new AttributesFacetResult($facetsResultSet, $attributes),
                $orders
            );
        } elseif ($category instanceof CustomCompanyCategory) {
            $facetSpec->facetByAttributesForEmptyAttributes($specification);

            $facetsResultSet = $dataFetcher->getFacetedResultSetByCriteria($specification, $facetSpec);
            $attributeValuesIds = (new FacetResultExtractor($facetsResultSet, ProductsFacetSpec::COLUMN_ATTRIBUTES_IDS))
                ->getIds();

            if ($attributeValuesIds && count($specification->productAttrsByGroup)) {
                $attributes = $attributeValueRepository->getAttributesForAttributesValues($attributeValuesIds);

                foreach ($attributes as $attribute) {
                    $facetSpec->facetByAttribute($attribute, $specification);
                }

                $facetsResultSet = $dataFetcher->getFacetedResultSetByCriteria($specification, $facetSpec);
                $attributesCollection = $attributeValueRepository->loadCollectionByFacetResult(
                    new AttributesFacetResult($facetsResultSet, $attributes),
                    $orders
                );
            } else {
                $attributesCollection = $attributeValueRepository->loadCollectionByAttributesValuesIds(
                    $attributeValuesIds,
                    $orders
                );
            }
        }

        if ($facetsResultSet) {
            $cities = $em->getRepository('MetalTerritorialBundle:City')
                ->loadByFacetResult(new FacetResultExtractor($facetsResultSet, ProductsFacetSpec::COLUMN_PRODUCT_CITY_ID));
        }

        return $this->render('@MetalCompanies/MiniSite/products.html.twig', array(
            'company' => $company,
            'pagerfanta' => $pagerfanta,
            'category' => $category,
            'branchOfficesCities'  => $cities,
            'currentAttributesCollection' => $attributes_collection,
            'attributesCollection' => $attributesCollection
        ));
    }

    public function sendEmailAction(Request $request, Country $country)
    {
        $options = array('is_authenticated' => $this->isGranted('ROLE_USER'));
        $form = $this->createForm(new SendEmailToEmployeeType(), null, $options);
        $form->handleRequest($request);

        if (!$form->isValid()) {
            $errors = $this->get('metal.project.form_helper')->getFormErrorMessages($form);

            return JsonResponse::create(array(
                'errors' => $errors
            ));
        }

        $employee = $form->get('employee')->getData();
        /* @var $employee User */

        $mailer = $this->get('metal.newsletter.mailer');

        $sender = array('company' => null);
        if ($this->isGranted('ROLE_USER')) {
            $user = $this->getUser();
            /* @var $user User */
            $sender['name'] = $user->getFullName();
            $sender['email'] = $user->getEmail();

            if ($user->getCompany()) {
                $sender['company'] = $user->getCompany();
            }
        } else {
            $sender['name'] = $form->get('userName')->getData();
            $sender['email'] = $form->get('userEmail')->getData();
        }

        $replyTo = array($sender['email'] => $sender['name']);

        try {
            $mailer->sendMessage(
                'MetalUsersBundle::emails/send_mail_to_employee.html.twig',
                $employee->getEmail(),
                array(
                    'user' => $employee,
                    'sender' => $sender,
                    'text' => $form->get('emailText')->getData(),
                    'country' => $country
                ),
                null,
                $replyTo
            );
        } catch (\Swift_RfcComplianceException $e) {
        }

        return JsonResponse::create(array(
            'status' => 'success',
        ));
    }

    public function documentsAction(Company $company)
    {
        $em = $this->getDoctrine()->getManager();
        /* @var $em EntityManager */

        $documents = $em->getRepository('MetalCompaniesBundle:CompanyFile')
            ->findBy(array('company' => $company));

        return $this->render('MetalCompaniesBundle:MiniSite:documents.html.twig', array(
            'documents' => $documents,
        ));
    }

    /**
     * @ParamConverter("companyFile", class="MetalCompaniesBundle:CompanyFile")
     */
    public function downloadDocumentAction(CompanyFile $companyFile, $action)
    {
       $dispositions = array(
           'view' => ResponseHeaderBag::DISPOSITION_INLINE,
           'download' => ResponseHeaderBag::DISPOSITION_ATTACHMENT
       );
        $response = $this->get('vich_uploader.download_handler')
            ->downloadObject($companyFile, 'uploadedFile', null, true);
        $disposition = $response->headers->makeDisposition(
            $dispositions[$action],
            Transliterator::transliterate($companyFile->getFile()->getOriginalName())
        );

        $response->headers->set('Content-Disposition', $disposition);
        $response->headers->set('Content-Type', $companyFile->getFile()->getMimeType());

        return $response;
    }

    private function filterResponse(Request $request, Response $response)
    {
        $cityId = $request->query->get('city');
        if ($cityId) {
            $cookie = new Cookie(DefaultHelper::MINISITE_CITY_COOKIE, $cityId);
            $response->headers->setCookie($cookie);
        }

        return $response;
    }
}
