<?php

namespace Metal\TerritorialBundle\Entity\ValueObject;

class MinisiteDomainsProvider
{
    protected static $domains = array();

    public static function initialize($domains)
    {
        foreach ($domains as $countryId => $countryDomains) {
            foreach ($countryDomains as $id => $domain) {
                self::$domains[$id] = array('id' => $id, 'title' => $domain, 'country_id' =>$countryId);
            }
        }
    }

    public static function create($id)
    {
        return new MinisiteDomain(self::$domains[$id]);
    }

    public static function getAllDomainsAsSimpleArray($countryId = null)
    {
        $domains = self::$domains;
        $resultDomains = array();
        if (null !== $countryId) {
            foreach ($domains as $i => $domainData) {
                if ($domainData['country_id'] == $countryId) {
                    $resultDomains[$i] = $domainData;
                }
            }
        }

        return array_map(
            function ($type) {
                return $type['title'];
            },
            $resultDomains
        );
    }
}
