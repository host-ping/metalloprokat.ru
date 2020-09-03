<?php

namespace Metal\ProductsBundle\DataFetching\Spec\Aggregation;

use Metal\ProjectBundle\DataFetching\Spec\Aggregation\AbstractAggregation;

class ProductsPerCompanyAggregation extends AbstractAggregation
{
    public const ORDER_MODE_RATING = 'rating';

    public const ORDER_MODE_PRICE = 'price';

    public const ORDER_MODE_DATE = 'updatedAt';

    public const ORDER_MODE_NONE = 'none';

    private const DEFAULT_PRODUCTS_LIMIT = 50;

    public const AVAILABLE_ORDER_MODES = [
        self::ORDER_MODE_RATING,
        self::ORDER_MODE_PRICE,
        self::ORDER_MODE_DATE,
        self::ORDER_MODE_NONE,
    ];

    private $limitProductsPerCompany;

    private $orderMode;

    public static function createFromInnerOrders(
        string $name,
        array $innerOrders,
        int $limitProductsPerCompany = self::DEFAULT_PRODUCTS_LIMIT
    ): self {
        if (!count($innerOrders)) {
            throw new \InvalidArgumentException('Expected not empty inner orders.');
        }
        //TODO: thow ex if count($innerOrders) > 1?

        return new self($name, key($innerOrders), $limitProductsPerCompany);
    }

    public function __construct(
        string $name,
        string $orderMode = self::ORDER_MODE_NONE,
        int $limitProductsPerCompany = self::DEFAULT_PRODUCTS_LIMIT
    ) {
        parent::__construct($name);

        if (!in_array($orderMode, self::AVAILABLE_ORDER_MODES)) {
            throw new \InvalidArgumentException(sprintf('Unsupported order mode "%s".', $orderMode));
        }

        $this->orderMode = $orderMode;
        $this->limitProductsPerCompany = $limitProductsPerCompany;
    }

    public function getLimitProductsPerCompany(): int
    {
        return $this->limitProductsPerCompany;
    }

    public function getOrderMode(): string
    {
        return $this->orderMode;
    }
}
