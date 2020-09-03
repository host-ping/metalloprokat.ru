<?php

namespace Metal\ProjectBundle\Routing;

use Doctrine\ORM\EntityManager;
use Metal\CategoriesBundle\Entity\Category;
use Metal\CompaniesBundle\Entity\Company;
use Metal\ContentBundle\Entity\Category as ContentCategory;
use Metal\ProjectBundle\Entity\UrlRewrite;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Symfony\Component\Routing\Matcher\RequestMatcherInterface;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\RouterInterface;

class AttributesLoadingRouter implements RouterInterface, RequestMatcherInterface
{
    protected $em;

    protected $hostnamePackage;

    protected $expressionLanguage;

    protected $request;

    protected $context;

    //TODO: передавать это из конфига
    private $portalHostnamePackages = array(
        'metalloprokat',
        'metalloprokat-ua',
        'metalloprokat-by',
        'metalloprokat-kz',
    );

    private $regexps;

    private $sections;

    public function __construct(EntityManager $em, $hostnamePackage, array $regexps, array $sections)
    {
        $this->em = $em;
        $this->hostnamePackage = $hostnamePackage;
        $this->regexps = $regexps;
        $this->regexps['optional_slash'] = '/?';
        $this->sections = $sections;
    }

    /**
     * {@inheritdoc}
     */
    public function setContext(RequestContext $context)
    {
        $this->context = $context;
    }

    /**
     * {@inheritdoc}
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * {@inheritdoc}
     */
    public function matchRequest(Request $request)
    {
        $this->request = $request;

        $regexps = $this->regexps;

        $catalogProductRepository = $this->em->getRepository('MetalCatalogBundle:Product');

        $routes = array(
            array(
                'pattern' => "#^/(?P<category_slug>{$regexps['category_slug']})/catalog{$regexps['optional_slash']}$#",
                'condition' => function (ParameterBag $matchedAttributes, ParameterBag $requestAttributes) {
                    return $matchedAttributes->has('category') && !$requestAttributes->get('company_on_subdomain', false);
                },
                'available_on' => $this->portalHostnamePackages,
                'section' => 'main',
                'routes' => array(
                    'MetalProductsBundle:Products:companies_list_category',
                    'MetalProductsBundle:Products:companies_list_category_country',
                    'MetalProductsBundle:Products:companies_list_category_subdomain'
                )
            ),
            array(
                'pattern' => "#^/demands/(?P<category_slug>{$regexps['category_slug']})/demand_\d+\.html$#",
                'condition' => function (ParameterBag $matchedAttributes, ParameterBag $requestAttributes) {
                    return $matchedAttributes->has('category') && !$requestAttributes->get('company_on_subdomain', false);
                },
                'available_on' => $this->portalHostnamePackages,
                'section' => 'main',
                'routes' => array('MetalDemandsBundle:Demand:view')
            ),
            array(
                'pattern' => "#^/demands/(?P<category_slug>{$regexps['category_slug']}){$regexps['optional_slash']}$#",
                'condition' => function (ParameterBag $matchedAttributes, ParameterBag $requestAttributes) {
                    return $matchedAttributes->has('category') && !$requestAttributes->get('company_on_subdomain', false);
                },
                'available_on' => $this->portalHostnamePackages,
                'section' => 'main',
                'routes' => array(
                    'MetalDemandsBundle:Demands:list_category',
                    'MetalDemandsBundle:Demands:list_subdomain_category'
                )
            ),
            // catalog start
            array(
                'pattern' => "#^/(?P<category_slug>{$regexps['category_slug']})/manufacturers{$regexps['optional_slash']}$#",
                'condition' => function (ParameterBag $matchedAttributes) {
                    return $matchedAttributes->has('category');
                },
                'available_on' => $this->portalHostnamePackages,
                'section' => 'catalog',
                'routes' => array(
                    'MetalCatalogBundle:Manufacturers:list_category_subdomain',
                )
            ),
            array(
                'pattern' => "#^/(?P<category_slug>{$regexps['category_slug']})/brands{$regexps['optional_slash']}$#",
                'condition' => function (ParameterBag $matchedAttributes) {
                    return $matchedAttributes->has('category');
                },
                'available_on' => $this->portalHostnamePackages,
                'section' => 'catalog',
                'routes' => array(
                    'MetalCatalogBundle:Brands:list_category_subdomain',
                )
            ),
            array(
                'pattern' => "#^/(?P<category_slug>{$regexps['category_slug']})/products{$regexps['optional_slash']}$#",
                'condition' => function (ParameterBag $matchedAttributes) {
                    return $matchedAttributes->has('category');
                },
                'available_on' => $this->portalHostnamePackages,
                'section' => 'catalog',
                'routes' => array(
                    'MetalCatalogBundle:Products:list_category_subdomain',
                )
            ),
            array(
                'pattern' => "#^/(?P<category_slug>{$regexps['category_slug']})/(?P<id>\d+)$#",
                'condition' => function (ParameterBag $matchedAttributes, ParameterBag $requestAttributes, array $matches) use ($catalogProductRepository) {
                    if ($matchedAttributes->has('category') && $catalogProductRepository->find($matches['id'])) {
                        return array('catalog_product_view' => true);
                    }
                },
                'available_on' => $this->portalHostnamePackages,
                'section' => 'catalog',
                'routes' => array(
                    'MetalCatalogBundle:Product:view',
                )
            ),
            // catalog end

            // content start
            // http://www.stroy.ru/apartment/parts-front/
            array(
                'pattern' => "#^/(?P<category_slug>{$regexps['category_slug']}){$regexps['optional_slash']}$#",
                'condition' => function (ParameterBag $matchedAttributes) {
                    return $matchedAttributes->has('content_category');
                },
                'available_on' => $this->portalHostnamePackages,
                'section' => 'content',
                'routes' => array('MetalContentBundle:Topics:list')
            ),
            // http://www.stroy.ru/apartment/com-electric/publications_2640.html
            array(
                'pattern' => "#^/(?P<category_slug>{$regexps['category_slug']})/publications_\d+\.html$#",
                'condition' => function (ParameterBag $matchedAttributes) {
                    return $matchedAttributes->has('content_category');
                },
                'available_on' => $this->portalHostnamePackages,
                'section' => 'content',
                'routes' => array('MetalContentBundle:Topic:view')
            ),
            // http://www.stroy.ru/apartment/parts-front/questions.html
            array(
                'pattern' => "#^/(?P<category_slug>{$regexps['category_slug']})/questions\.html$#",
                'condition' => function (ParameterBag $matchedAttributes) {
                    return $matchedAttributes->has('content_category');
                },
                'available_on' => $this->portalHostnamePackages,
                'section' => 'content',
                'routes' => array('MetalContentBundle:Question:view')
            ),
            // http://www.stroy.ru/cottage/build-foundation/questions_4967.html
            array(
                'pattern' => "#^/(?P<category_slug>{$regexps['category_slug']})/questions_\d+\.html$#",
                'condition' => function (ParameterBag $matchedAttributes) {
                    return $matchedAttributes->has('content_category');
                },
                'available_on' => $this->portalHostnamePackages,
                'section' => 'content',
                'routes' => array('MetalContentBundle:Questions:list')
            ),
            // content end

            array(
                'pattern' => "#^/(?P<company_slug>{$regexps['company_slug']})/(?P<category_slug>{$regexps['category_slug']}){$regexps['optional_slash']}$#",
                'condition' => function (ParameterBag $matchedAttributes, ParameterBag $requestAttributes) {
                    return $matchedAttributes->has('category') && $matchedAttributes->has('company') && !$requestAttributes->get('company_on_subdomain', false);
                },
                'available_on' => $this->portalHostnamePackages,
                'section' => 'main',
                'routes' => array(
                    'MetalCompaniesBundle:Company:products_category'
                )
            ),
            array(
                'pattern' => "#^/(?P<company_slug>{$regexps['company_slug']}){$regexps['optional_slash']}$#",
                'condition' => function (ParameterBag $matchedAttributes) {
                    return $matchedAttributes->has('company');
                },
                'available_on' => $this->portalHostnamePackages,
                'section' => 'main',
                'routes' => array(
                    'MetalCompaniesBundle:Company:products'
                )
            ),
            array(
                'pattern' => "#^/(?P<category_slug>{$regexps['category_slug']}){$regexps['optional_slash']}$#",
                'condition' => function (ParameterBag $matchedAttributes) {
                    return $matchedAttributes->has('category');
                },
                'available_on' => array_merge($this->portalHostnamePackages, array('metalspros', '8_800')),
                'section' => 'main',
                'routes' => array(
                    //TODO: минисайт сюда же?
                    'MetalProductsBundle:Products:list_category',
                    'MetalProductsBundle:Products:list_category_subdomain',
                    'SprosProjectBundle:Default:index_subdomain_category',
                    'SprosProjectBundle:Default:index_category'
                )
            ),
            array(
                'pattern' => "#^/category(?P<custom_company_category_id>\d+)(?P<product_attributes_slugs>{$regexps['category_slug']})?{$regexps['optional_slash']}$#",
                'condition' => function (ParameterBag $matchedAttributes) {
                    return $matchedAttributes->has('custom_company_category');
                },
                'available_on' => $this->portalHostnamePackages,
                'section' => 'main',
                'routes' => array(
                    'MetalMiniSiteBundle:MiniSite:products_custom_category'
                ),
            ),
        );

        $routes = array_filter(
            $routes,
            function ($route) {
                return in_array($this->hostnamePackage, $route['available_on']) && $this->sections[$route['section']];
            }
        );

        $partsToMatch = array();
        $matchedPatterns = array();
        foreach ($routes as $key => $route) {
            $matches = array();
            if (!preg_match($route['pattern'], $request->getPathInfo(), $matches)) {
                continue;
            }

            $matchedPatterns[$key] = $matches;
            if (isset($matches['category_slug'])) {
                foreach ($this->getCandidates($matches['category_slug']) as $candidate) {
                    $partsToMatch[$candidate] = true;
                }
            }

            if (isset($matches['company_slug'])) {
                foreach ($this->getCandidates($matches['company_slug']) as $candidate) {
                    $partsToMatch[$candidate] = true;
                }
            }
        }

        $slugRewrites = $this->loadUrlRewritesBySlugs(array_keys($partsToMatch));

        foreach ($matchedPatterns as $key => $matchedAttributes) {
            $databaseAttributes = $this->loadMatchedAttributes($matchedAttributes, $slugRewrites);
            $check = $routes[$key]['condition'];
            $conditionResult = $check(new ParameterBag($databaseAttributes), $request->attributes, $matchedAttributes);

            if ($conditionResult) {
                if (is_array($conditionResult)) {
                    $databaseAttributes = array_merge($databaseAttributes, $conditionResult);
                }

                $request->attributes->add($databaseAttributes);
                $this->request = null;

                // условие роута совпало, переходим к следующему роутеру
                throw new ResourceNotFoundException();
            }
        }
        $this->request = null;

        if ($url = $this->getUrlForRedirect($request)) {
            return array(
                '_controller' => 'FrameworkBundle:Redirect:urlRedirect',
                '_route' => 'dummy',
                'path' => $url,
                'permanent' => true
            );
        }

        throw new ResourceNotFoundException();
    }

    public function getUrlForRedirect(Request $request)
    {
        $requestUri = $request->getRequestUri();
        $redirectUrl = null;
        $urlParts = array_filter(explode('/', $requestUri));

        if ($urlParts) {
            $urlCandidates = array();
            $tmp = '';
            foreach ($urlParts as $el) {
                $tmp .= preg_quote('/'.$el);
                $urlCandidates[] = $tmp.'$';
            }
            $possibleUrl = $this->em
                ->getConnection()
                ->fetchColumn(
                    'SELECT c.slug_combined
                FROM Message73 AS c
                WHERE c.slug_combined REGEXP :uri
                ORDER BY LENGTH(c.slug_combined) DESC
                LIMIT 1',
                    array(
                        'uri' => implode('|', $urlCandidates),
                    )
                );

            if ($possibleUrl) {
                $pieces = array_filter(explode('/', $possibleUrl));
                $matchedUrl = implode('/', array_intersect($urlParts, $pieces));

                $position = strpos($requestUri, $matchedUrl) + strlen($matchedUrl);
                $urlSuffix = substr($requestUri, $position);

                $redirectUrl = '/'.$possibleUrl.$urlSuffix;
            }
        }

        return $redirectUrl;
    }

    /**
     * {@inheritdoc}
     */
    public function getRouteCollection()
    {
        return new RouteCollection();
    }

    /**
     * {@inheritdoc}
     */
    public function generate($name, $parameters = array(), $referenceType = self::ABSOLUTE_PATH)
    {
        throw new RouteNotFoundException();
    }

    /**
     * {@inheritdoc}
     */
    public function match($pathinfo)
    {
        throw new ResourceNotFoundException();
    }

    protected function loadMatchedAttributes($matchedAttributes, array $slugRewrites)
    {
        $databaseMatchedAttributes = array();

        if (isset($matchedAttributes['category_slug'])) {
            $databaseMatchedAttributes = array_replace($databaseMatchedAttributes, (array)$this->loadCategorySlugAttribute($matchedAttributes['category_slug'], $slugRewrites));
            $databaseMatchedAttributes = array_replace($databaseMatchedAttributes, (array)$this->loadContentCategorySlugAttribute($matchedAttributes['category_slug'], $slugRewrites));
        }

        if (isset($matchedAttributes['company_slug'])) {
            $databaseMatchedAttributes = array_replace($databaseMatchedAttributes, (array)$this->loadCompanySlugAttribute($matchedAttributes['company_slug'], $slugRewrites));
        }

        if (isset($matchedAttributes['custom_company_category_id'])) {
            //TODO: выполнять проверку на компанию
            $databaseMatchedAttributes = array_replace($databaseMatchedAttributes, (array)$this->loadCustomCompanyCategorySlugAttribute(
                $matchedAttributes['custom_company_category_id'],
                isset($matchedAttributes['product_attributes_slugs']) ? $matchedAttributes['product_attributes_slugs'] : ''
            ));
        }

        return $databaseMatchedAttributes;
    }

    public function loadCustomCompanyCategorySlugAttribute($customCompanyCategoryId, $productAttributesSlugs)
    {
        $customCompanyCategory = $this->em
            ->getRepository('MetalCompaniesBundle:CustomCompanyCategory')
            ->findOneBy(array('id' => $customCompanyCategoryId));

        if ($customCompanyCategory) {

            $databaseMatchedAttributes['custom_company_category'] = $customCompanyCategory;
            $databaseMatchedAttributes['category'] = $customCompanyCategory;

            if (!$productAttributesSlugs) {
                return $databaseMatchedAttributes;
            }

            $remainedSlug = trim($productAttributesSlugs, '/');
            if (!$remainedSlug) {
                return $databaseMatchedAttributes;
            }

            $parametersSlugs = preg_split('#[/_]#', $remainedSlug);

            $parameterRepository = $this->em->getRepository('MetalCategoriesBundle:Parameter');
            $databaseMatchedAttributes['products_parameters_slugs'] = $parametersSlugs;
            //TODO: deprecate products_parameters
            $databaseMatchedAttributes['products_parameters'] = $parameterRepository->getParametersBySlugs($parametersSlugs);

            return $databaseMatchedAttributes;
        }

        return null;
    }

    /**
     * @param string $categorySlug
     * @param UrlRewrite[] $slugRewrites
     *
     * @return array|null
     */
    protected function loadCategorySlugAttribute($categorySlug, array $slugRewrites)
    {
        $category = null;
        $candidates = $this->getCandidates($categorySlug);
        foreach ($candidates as $candidate) {
            $category = isset($slugRewrites[$candidate]) ? $slugRewrites[$candidate]->getCategory() : null;
            if ($category instanceof Category) {
                break;
            }
        }

        if (!$category) {
            return null;
        }

        $databaseMatchedAttributes = array('category' => $category);
        $remainedSlug = substr($categorySlug, strlen($category->getSlugCombined().'/'));

        if (!$remainedSlug) {
            return $databaseMatchedAttributes;
        }

        $parametersSlugs = preg_split('#[/_]#', $remainedSlug);

        $parameterRepository = $this->em->getRepository('MetalCategoriesBundle:Parameter');
        $databaseMatchedAttributes['products_parameters_slugs'] = $parametersSlugs;
        //TODO: deprecate products_parameters
        $databaseMatchedAttributes['products_parameters'] = $parameterRepository->getParametersByCategoryAndSlugs($category, $parametersSlugs);

        return $databaseMatchedAttributes;
    }

    /**
     * @param string $categorySlug
     * @param UrlRewrite[] $slugRewrites
     *
     * @return array|null
     */
    protected function loadContentCategorySlugAttribute($categorySlug, array $slugRewrites)
    {
        $category = null;
        $candidates = $this->getCandidates($categorySlug);
        foreach ($candidates as $candidate) {
            $category = isset($slugRewrites[$candidate]) ? $slugRewrites[$candidate]->getContentCategory() : null;
            if ($category instanceof ContentCategory) {
                break;
            }
        }

        if (!$category) {
            return null;
        }

        $databaseMatchedAttributes = array('content_category' => $category);
        $remainedSlug = substr($categorySlug, strlen($category->getSlugCombined().'/'));

        if (!$remainedSlug) {
            return $databaseMatchedAttributes;
        }
        //TODO: обрабатывать ситуацию, когда всякой фигни понаписывали

        return $databaseMatchedAttributes;
    }

    /**
     * @param string $companySlug
     * @param UrlRewrite[] $slugRewrites
     *
     * @return array|null
     */
    protected function loadCompanySlugAttribute($companySlug, array $slugRewrites)
    {
        $company = isset($slugRewrites[$companySlug]) ? $slugRewrites[$companySlug]->getCompany() : null;
        if ($company instanceof Company) {
            return array('company' => $company, 'country' => $company->getCountry());
        }

        return null;
    }

    protected function loadUrlRewritesBySlugs(array $slugs)
    {
        if (!$slugs) {
            return array();
        }

        return $this->em->createQueryBuilder()
            ->select('ur')
            ->from('MetalProjectBundle:UrlRewrite', 'ur', 'ur.pathPrefix')
            ->leftJoin('ur.category', 'cat')
            ->addSelect('cat')
            ->leftJoin('ur.company', 'comp')
            ->addSelect('comp')
            ->where('ur.pathPrefix IN (:slugs)')
            ->setParameter('slugs', $slugs)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param $url
     *
     * @return array
     */
    protected function getCandidates($url)
    {
        $candidates = array();
        if (!$url) {
            return $candidates;
        }

        do {
            $candidates[] = $url;

            $pos = strrpos($url, '/');
            $url = substr($url, 0, $pos);
        } while (false !== $pos);

        return $candidates;
    }
}
