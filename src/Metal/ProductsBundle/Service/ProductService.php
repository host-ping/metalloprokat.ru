<?php

namespace Metal\ProductsBundle\Service;

use Brouzie\Sphinxy\QueryBuilder;

class ProductService
{
    public static function applySimpleCriteriaToQueryBuilder(QueryBuilder $sqb, array $criteria, array $excluded = array())
    {
        foreach ($criteria as $option => $value) {
            if (in_array($option, $excluded)) {
                continue;
            }

            if (is_array($value)) {
                if (is_array(current($value))) {
                    foreach ($value as $val) {
                        $sqb->andWhere(sprintf('%s IN %s', $option, $sqb->createParameter($val, $option)));
                    }
                } else {
                    $sqb->andWhere(sprintf('%s IN %s', $option, $sqb->createParameter($value, $option)));
                }
            } else {
                $sqb->andWhere(sprintf('%s = :%s', $option, $option))
                    ->setParameter($option, $value);
            }
        }
    }
}
