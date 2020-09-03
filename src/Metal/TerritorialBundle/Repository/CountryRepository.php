<?php

namespace Metal\TerritorialBundle\Repository;

use Metal\ProjectBundle\DataFetching\Sphinxy\FacetResultExtractor;
use Metal\ProjectBundle\Doctrine\EntityRepository;
use Metal\TerritorialBundle\Entity\Country;

class CountryRepository extends EntityRepository
{
    /**
     * @param FacetResultExtractor $facetResultExtractor
     *
     * @return Country[]
     */
    public function loadByFacetResult(FacetResultExtractor $facetResultExtractor)
    {
        return $this->findByIds($facetResultExtractor->getIds(), true);
    }

    /**
     * @return Country[]
     */
    public function getEnabledCountries()
    {
        return $this->findBy(array('id' => Country::getEnabledCountriesIds()));
    }
}
