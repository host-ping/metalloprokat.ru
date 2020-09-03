<?php

namespace Metal\ProductsBundle\DataFetching\Elastica;

interface ProductIndex
{
    public const ID = 'id';

    public const COMPANY_ID = 'company_id';

    public const TITLE = 'title';

    public const COMPANY_TITLE = 'company_title';

    public const SCOPE = 'product';

    public const PRICE = 'price';

    public const IS_SPECIAL_OFFER = 'is_special_offer';

    public const IS_HOT_OFFER = 'is_hot_offer';

    public const DAY_UPDATED_AT = 'day_updated_at';

    public const CATEGORIES_IDS = 'categories_ids';

    public const COUNTRIES_IDS = 'countries_ids';

    public const CITIES_IDS = 'cities_ids';

    public const ATTRIBUTES = 'attributes';

    public const ATTRIBUTES_IDS = 'attributes_ids';

    public const COMPANY_LAST_VISITED_AT = 'company_last_visited_at';

    public const COMPANY_RATING = 'company_rating';

    public const COMPANY_ACCESS = 'company_access';

    public const COMPANY_CITY_ID = 'company_city_id';

    public const VISIBILITY_STATUS = 'visibility_status';

    public const IS_VIRTUAL = 'is_virtual';

    public const PRODUCT_POSITION = 'product_position';

    public const PRIORITY_SHOW = 'priority_show';
}
