<?php

namespace Metal\ProductsBundle\DataFetching\Spec;

use Metal\ProjectBundle\DataFetching\Spec\CacheableSpec;
use Metal\ProjectBundle\DataFetching\Spec\OrderingSpec;
use Metal\TerritorialBundle\Entity\City;
use Metal\TerritorialBundle\Entity\TerritoryInterface;
use Symfony\Component\HttpFoundation\Request;

class ProductsOrderingSpec extends OrderingSpec implements CacheableSpec
{
    public function createdAt()
    {
        return $this->pushOrder('createdAt');
    }

    public function payedCompanies(TerritoryInterface $territory = null)
    {
        return $this->pushOrder('payedCompanies', $territory ? [$territory->getKind() => $territory->getId()] : true);
    }

    public function weight()
    {
        return $this->pushOrder('weight');
    }

    public function position()
    {
        return $this->pushOrder('position');
    }

    public function companyCreatedAt()
    {
        return $this->pushOrder('companyCreatedAt');
    }

    public function companyLastVisitedAt()
    {
        return $this->pushOrder('companyLastVisitedAt');
    }

    public function specialOffer()
    {
        return $this->pushOrder('specialOffer');
    }

    public function hotOfferPosition()
    {
        return $this->pushOrder('hotOfferPosition');
    }

    public function iterateByCategory()
    {
        return $this->pushOrder('iterateByCategory');
    }

    public function iterateByCompany()
    {
        return $this->pushOrder('iterateByCompany');
    }

    public function updatedAt()
    {
        return $this->pushOrder('updatedAt');
    }

    public function price()
    {
        return $this->pushOrder('price');
    }

    public function rating()
    {
        return $this->pushOrder('rating');
    }

    public function title()
    {
        return $this->pushOrder('title');
    }

    public function random($seed = null)
    {
        return $this->pushOrder('random', $seed);
    }

    public function companyTitle()
    {
        return $this->pushOrder('companyTitle');
    }

    public function cityTitle()
    {
        return $this->pushOrder('cityTitle');
    }

    public function applyFromRequest(Request $request, $productsView = true)
    {
        $order = $request->query->get('order');

        if ('price' === $order) {
            $this->price();

            return true;
        }

        if ('title' === $order) {
            if ($productsView) {
                $this->title();
            } else {
                $this->companyTitle();
            }

            return true;
        }

        if ('date' === $order) {
            if ($productsView) {
                $this->updatedAt();
            } else {
                $this->companyCreatedAt();
            }

            return true;
        }

        if ('city' === $order) {
            $this->cityTitle();

            return true;
        }

        return false;
    }

    public function getCacheKey()
    {
        if (isset($this->orders['random'])) {
            return null;
        }

        return sha1(
            serialize(
                array(
                    'class' => __CLASS__,
                    'orders' => $this->orders,
                )
            )
        );
    }
}
