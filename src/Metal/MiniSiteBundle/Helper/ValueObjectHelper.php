<?php

namespace Metal\MiniSiteBundle\Helper;

use Brouzie\Bundle\HelpersBundle\Helper\HelperAbstract;
use Metal\MiniSiteBundle\Entity\ValueObject\BackgroundColorProvider;
use Metal\MiniSiteBundle\Entity\ValueObject\PrimaryColorProvider;
use Metal\MiniSiteBundle\Entity\ValueObject\SecondaryColorProvider;

class ValueObjectHelper extends HelperAbstract
{
    public function getAllBackgroundColors()
    {
        return BackgroundColorProvider::getAllTypes();
    }

    public function getAllPrimaryColors()
    {
        return PrimaryColorProvider::getAllTypes();
    }

    public function getAllSecondaryColors()
    {
        return SecondaryColorProvider::getAllTypes();
    }

    public function getDefaultColors()
    {
        return BackgroundColorProvider::getDefaultColors();
    }
}
