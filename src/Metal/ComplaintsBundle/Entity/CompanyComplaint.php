<?php

namespace Metal\ComplaintsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class CompanyComplaint extends AbstractComplaint
{
    public function setObject($object)
    {
        $this->setCompany($object);
    }

    public function getObjectKind()
    {
        return 'company';
    }
}
