<?php

namespace Metal\CompaniesBundle\Helper;

class AttributesHelper
{
    public function buildAttributesCombination(array $sourceSets)
    {
//        $sourceSets = array(
//            //0 => array(1, 2, 3, 4),
//            //1 => array('b', 'c', 'd', 'e', 'f'),
//            //2 => array('-', '_'),
//            //3 => array('!', '&', '?'),
//            0 => array('a', 'b', 'c'),
//            1 => array('-', '+'),
//            2 => array(1, 2, 3, 4),
//        );

        $result = array();
        foreach ($sourceSets as $key => $sourceSet) {
            $variants = array();
            foreach ($sourceSet as $value) {
                $variants[] = array($value);
            }

            foreach ($sourceSets as $nestedKey => $nestedSourceSet) {
                if ($nestedKey <= $key) {
                    continue;
                }
                foreach ($variants as $existingVariant) {
                    foreach ($nestedSourceSet as $newVariantEnding) {
                        $newVariant = $existingVariant;
                        $newVariant[] = $newVariantEnding;
                        $variants[] = $newVariant;
                    }
                }
            }

            $result = array_merge($result, $variants);
        }

        return $result;
    }
}
