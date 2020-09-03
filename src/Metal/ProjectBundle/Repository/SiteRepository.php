<?php

namespace Metal\ProjectBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Metal\ProjectBundle\Doctrine\Utils;

class SiteRepository extends EntityRepository
{
    public function disableLogging()
    {
        Utils::disableLogger($this->_em);
    }

    public function restoreLogging()
    {
        Utils::restoreLogging($this->_em);
    }
}
