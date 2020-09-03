<?php

namespace Metal\ProductsBundle\Widget;

use Brouzie\WidgetsBundle\Cache\CacheKeyGenerator;
use Brouzie\WidgetsBundle\Cache\CacheProfile;
use Brouzie\WidgetsBundle\Widget\CacheableWidget;
use Brouzie\WidgetsBundle\Widget\TwigWidget;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Metal\AttributesBundle\Entity\DTO\AttributesCollection;
use Metal\CategoriesBundle\Entity\Category;
use Metal\ProductsBundle\DataFetching\Elastica\ProductIndex;
use Metal\ProductsBundle\DataFetching\Result\ProductItem;
use Metal\ProductsBundle\DataFetching\Spec\ProductsFilteringSpec;
use Metal\ProductsBundle\DataFetching\Spec\ProductsOrderingSpec;
use Metal\ProductsBundle\Entity\Product;
use Metal\ProjectBundle\DataFetching\DataFetcher;
use Metal\ProjectBundle\DataFetching\DataFetcherFactory;
use Metal\TerritorialBundle\Entity\TerritoryInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class HotLineWidget extends TwigWidget implements CacheableWidget
{
    private $doctrine;

    private $dataFetcherFactory;

    public function __construct(Registry $doctrine, DataFetcherFactory $dataFetcherFactory)
    {
        $this->doctrine = $doctrine;
        $this->dataFetcherFactory = $dataFetcherFactory;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver
            ->setRequired(['category', 'territory', 'attributes_collection'])
            ->setAllowedTypes('category', Category::class)
            ->setAllowedTypes('territory', TerritoryInterface::class)
            ->setAllowedTypes('attributes_collection', AttributesCollection::class);
    }

    public function getCacheProfile()
    {
        return new CacheProfile(new CacheKeyGenerator($this), DataFetcher::TTL_5DAYS);
    }

    public function getContext()
    {
        /** @var Category $category */
        $category = $this->options['category'];

        $price = 0;
        $unit = '';
        $currency = '';
        $image = null;

        $criteria = (new ProductsFilteringSpec())
            ->category($category)
            ->territory($this->options['territory'])
            ->attributesCollection($this->options['attributes_collection'])
            ->price(10000, 999998);

        $orderBy = (new ProductsOrderingSpec())
            ->price();

        $dataFetcher = $this->dataFetcherFactory->getDataFetcher(ProductIndex::SCOPE);

        $productId = null;
        $pagerfanta = $dataFetcher
            ->getPagerfantaByCriteria($criteria, $orderBy, 1);
        /** @var ProductItem[] $productItems */
        $productItems = iterator_to_array($pagerfanta);
        if ($productItems) {
            $productId = reset($productItems)->getId();
        }

        $product = $this->doctrine
            ->getRepository('MetalProductsBundle:Product')
            ->findOneBy(array('id' => $productId));

        $productCategory = $category;
        if ($product instanceof Product) {
            $productCategory = $product->getCategory()->getId();
            $price = $product->getNormalizedPrice();
            $unit = $product->getMeasure()->getTokenPrice();
            $currency = $product->getCurrency()->getSymbolClass();
        }

        $images = $this->doctrine
            ->getRepository('MetalProductsBundle:ProductImage')
            ->findBy(array('category' => $productCategory, 'company' => null));

        if ($images) {
            $image = $images[array_rand($images)];
        }

        return array(
            'price' => $price,
            'unit' => $unit,
            'currencyClass' => $currency,
            'image' => $image
        );
    }
}
