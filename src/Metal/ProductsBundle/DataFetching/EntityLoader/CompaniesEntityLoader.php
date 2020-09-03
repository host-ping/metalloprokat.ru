<?php

namespace Metal\ProductsBundle\DataFetching\EntityLoader;

use Doctrine\ORM\EntityManagerInterface;
use Metal\AttributesBundle\Entity\AttributeValue;
use Metal\CompaniesBundle\Entity\Company;
use Metal\ProductsBundle\DataFetching\Spec\ProductsLoadingSpec;
use Metal\ProductsBundle\Entity\Product;
use Metal\ProjectBundle\DataFetching\ConcreteEntityLoader;
use Metal\ProjectBundle\DataFetching\Spec\LoadingSpec;
use Metal\ProjectBundle\DataFetching\UnsupportedSpecException;
use Predis\Client;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class CompaniesEntityLoader implements ConcreteEntityLoader
{
    private $em;

    /**
     * @var Client
     */
    private $redis;

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker;

    private $container;

    public function __construct(
        EntityManagerInterface $em,
        Client $redis,
        ContainerInterface $container,
        TokenStorageInterface $tokenStorage,
        AuthorizationCheckerInterface $authorizationChecker
    )
    {
        $this->em = $em;
        $this->redis = $redis;
        $this->container = $container;

        $this->tokenStorage = $tokenStorage;
        $this->authorizationChecker = $authorizationChecker;
    }

    public function getEntitiesByRows(\Traversable $rows, LoadingSpec $options = null)
    {
        if (null === $options) {
            $options = new ProductsLoadingSpec();
        } elseif (!$options instanceof ProductsLoadingSpec) {
            throw UnsupportedSpecException::create(ProductsLoadingSpec::class, $options);
        }

        $companyRepository = $this->em->getRepository('MetalCompaniesBundle:Company');
        $productRepository = $this->em->getRepository('MetalProductsBundle:Product');

        $companiesAttributes = array();
        foreach ($rows as $row) {
            $companiesAttributes[$row['company_id']] = array(
                'company_price' => $row['company_price'],
                'product_id' => $row['product_id'],
                'product_updated_at' => $row['product_updated_at'],
                'products_count_by_company' => $row['products_count_by_company']
            );
        }

        if (!count($companiesAttributes)) {
            return array();
        }

        $specification = array(
            'id' => array_keys($companiesAttributes),
            'index_by_id' => true
        );

        $companies = $companyRepository->getCompaniesQbBySpecification($specification)
            ->getQuery()
            ->getResult();
        /* @var $companies Company[] */

        $productsIds = array_column(iterator_to_array($rows), 'product_id');

        $specification = array(
            'id' => $productsIds,
            'preload_image' => $options->preloadImages,
            'preload_company' => true,
            'preload_branch_office' => true,
            'index_by_id' => true,
        );

        $products = $productRepository->getProductsQbBySpecification($specification)
            ->getQuery()
            ->getResult();
        /* @var $products Product[] */

        $loadedCompanies = array();
        /* @var $loadedCompanies Company[] */
        foreach ($companiesAttributes as $companyId => $companyAttrs) {
            if (!isset($companies[$companyId])) {
                continue;
            }

            $company = $companies[$companyId];
            $loadedCompanies[] = $company;
            $company->setAttribute('company_price', $companyAttrs['company_price']);
            $time = new \DateTime();
            $time->setTimestamp(strtotime($companyAttrs['product_updated_at']));
            $company->setAttribute('product_updated_at', $time);
            $company->setAttribute('products_count_by_company', $companyAttrs['products_count_by_company']);

            if (isset($products[$companyAttrs['product_id']]) && !$options->attachProductsAttr) {
                $company->setAttribute('product', $products[$companyAttrs['product_id']]);
            }
        }

        if ($options->attachFavorite) {
            $user = null;
            if ($this->authorizationChecker->isGranted('ROLE_USER')) {
                $user = $this->tokenStorage->getToken()->getUser();
            }
            $companyRepository->attachFavoriteCompanyToCompanies($loadedCompanies, $user);
        }

        if ($options->preloadDelivery) {
            $this->em->getRepository('MetalCompaniesBundle:CompanyCity')->attachCompanyCities($loadedCompanies, $options->preloadDelivery);
        }

        if ($options->preloadPhones) {
            $this->em->getRepository('MetalCompaniesBundle:CompanyPhone')->attachPhonesToCompaniesForCurrentTerritory($loadedCompanies, $options->preloadPhones);
        }

        if ($options->attachProductsAttr) {
            $sphinxy = $this->container->get('sphinxy.default_connection');
            $limit = 4;
            $count = $limit * count($companiesAttributes);
            $qb = $sphinxy
                ->createQueryBuilder()
                ->select('id')
                ->addSelect('company_id')
                ->from('products')
                ->andWhere('company_id IN :companies_ids')
                ->andWhere('is_virtual = 0')
                ->setParameter('companies_ids', array_keys($companiesAttributes))
                ->groupBy('company_id', $limit)
                ->withinGroupOrderBy('is_special_offer', 'DESC');

            if (isset($options->attachProductsAttr['query'])) {
                $match = AttributeValue::normalizeTitle(trim($options->attachProductsAttr['query']));
                $match = $sphinxy->getEscaper()->halfEscapeMatch($match);
                $qb
                    ->andWhere('MATCH (:match_title)')
                    ->setParameter('match_title', "@title $match");
            }

            $productsIdsForLastCompanies = $qb
                ->setMaxResults($count)
                ->getResult();

            $productsIds = array_column(iterator_to_array($productsIdsForLastCompanies), 'id');

            $specificationForProductsToLastCompanies = array(
                'id' => $productsIds,
                'preload_image' => true,
                'index_by_id' => true,
            );

            $products = $productRepository->getProductsQbBySpecification($specificationForProductsToLastCompanies)
                ->getQuery()
                ->getResult();
            /* @var $products Product[] */

            $companiesProducts = array();
            foreach ($products as $product) {
                $companiesProducts[$product->getCompany()->getId()][] = $product;
            }

            foreach ($loadedCompanies as $company) {
                if (isset($companiesProducts[$company->getId()])) {
                    $company->setAttribute('company_products', $companiesProducts[$company->getId()]);
                }
            }
        }

        return $loadedCompanies;
    }
}
