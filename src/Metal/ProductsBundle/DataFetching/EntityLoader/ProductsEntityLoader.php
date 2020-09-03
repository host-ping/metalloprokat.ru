<?php

namespace Metal\ProductsBundle\DataFetching\EntityLoader;

use Doctrine\ORM\EntityManagerInterface;
use Metal\ProductsBundle\DataFetching\Result\ProductItem;
use Metal\ProductsBundle\DataFetching\Spec\ProductsLoadingSpec;
use Metal\ProductsBundle\Entity\Product;
use Metal\ProjectBundle\DataFetching\ConcreteEntityLoader;
use Metal\ProjectBundle\DataFetching\Spec\LoadingSpec;
use Metal\ProjectBundle\DataFetching\UnsupportedSpecException;
use Metal\ProjectBundle\DataFetching\Util\ItemsUtil;
use Predis\Client;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class ProductsEntityLoader implements ConcreteEntityLoader
{
    const PRODUCTS_STATS_SHOW_KEY = 'products_stats_show';

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
        /** @var ProductItem[] $rows */
        if (null === $options) {
            $options = new ProductsLoadingSpec();
        } elseif (!$options instanceof ProductsLoadingSpec) {
            throw UnsupportedSpecException::create(ProductsLoadingSpec::class, $options);
        }

        $productsIds = ItemsUtil::getIdsOfItems($rows);

        if (!$productsIds) {
            return array();
        }

        $specification = array(
            'id' => $productsIds,
            'preload_image' => $options->preloadImages,
            'preload_company' => true,
            'preload_branch_office' => true,
            'index_by_id' => true,
        );

        $productRepository = $this->em->getRepository('MetalProductsBundle:Product');

        $products = $productRepository->getProductsQbBySpecification($specification)
            ->getQuery()
            ->getResult();
        /* @var $products Product[] */

        $loadedProducts = array();
        $loadedCompanies = array();
        $productsStatsData = array();
        foreach ($productsIds as $productId) {
            if (isset($products[$productId])) {
                $product = $products[$productId];
                $loadedProducts[] = $product;
                $loadedCompanies[$product->getCompany()->getId()] = $product->getCompany();

                $productsStatsData[] = array(
                    'id' => $product->getId(),
                    'cid' => $product->getCompany()->getId(),
                );
            }
        }

        if ($options->attachCategories) {
            $productRepository->attachCategoriesToProducts($loadedProducts);
            $this->em->getRepository('MetalCategoriesBundle:ParameterGroup')->attachAttributesForProducts($loadedProducts);
        }

        if ($options->normalizePrice) {
            $productRepository->attachNormalizedPrice($loadedProducts, $options->normalizePrice);
        }

        if ($options->attachFavorite) {
            $user = null;
            if ($this->authorizationChecker->isGranted('ROLE_USER')) {
                $user = $this->tokenStorage->getToken()->getUser();
            }
            $productRepository->attachFavoriteToProducts($loadedProducts, $user);
        }

        if ($options->preloadPhones) {
            $this->em->getRepository('MetalCompaniesBundle:CompanyPhone')
                ->attachPhonesToCompaniesForCurrentTerritory($loadedCompanies, $options->preloadPhones);
        }

        if ($options->preloadDelivery) {
            $this->em->getRepository('MetalCompaniesBundle:CompanyCity')->attachCompanyCities($loadedCompanies, $options->preloadDelivery);
        }

        $this->trackProductShow($productsStatsData, $options->trackShowing);

        foreach ($rows as $row) {
            if (null === $row->companyProductsCount) {
                break;
            }

            $productId = $row->getId();
            if (isset($products[$productId])) {
                $product = $products[$productId];

                $product->setAttribute('products_count', $row->companyProductsCount);
            }
        }

        return $loadedProducts;
    }

    protected function trackProductShow($productsStatsData, $sourceTypeId)
    {
        if (!$sourceTypeId || !$productsStatsData) {
            return;
        }

        $request = $this->container->get('request_stack')->getMasterRequest();

        $userId = null;
        if ($this->authorizationChecker->isGranted('ROLE_USER')) {
            $userId = $this->tokenStorage->getToken()->getUser()->getId();
        }

        $cityId = null;
        if ($request->attributes->get('city')) {
            $cityId = $request->attributes->get('city')->getId();
        } elseif ($request->query->get('city')) {
            $cityId = (int)$request->query->get('city');
        }

        $category = $request->attributes->get('category');

        $userAgent = $request->headers->get('USER_AGENT');
        if (!$userAgent) {
            return;
        }

        $data = array(
            'info' =>  array(
                'user' => $userId,
                'ip' => $request->getClientIp(),
                'user_agent' => $userAgent,
                'referer' => $request->headers->get('REFERER'),
                'session' => $request->getSession()->getId(),
                'category' => $category ? $category->getId() : null,
                'city' => $cityId,
                'source_type' => $sourceTypeId,
                'date_created_at' => (new \DateTime())->format('d.m.Y H:i:s'),
            ),
            'products' => $productsStatsData,
        );

        $this->redis->sadd(self::PRODUCTS_STATS_SHOW_KEY, json_encode($data));
    }
}
