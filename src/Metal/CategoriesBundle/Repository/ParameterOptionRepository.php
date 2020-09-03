<?php

namespace Metal\CategoriesBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Metal\CategoriesBundle\Entity\ParameterOption;

class ParameterOptionRepository extends EntityRepository
{
    public function refreshPriorityOrders()
    {
        $parameterOptions = $this->_em->getRepository('MetalCategoriesBundle:ParameterOption')
            ->findAll();

        usort(
            $parameterOptions,
            function (ParameterOption $b, ParameterOption $a) {
                return strnatcmp(mb_strtolower($a->getTitle()), mb_strtolower($b->getTitle())) * -1;
            }
        );

        foreach ($parameterOptions as $key => $parameterOption) {
            $parameterOption->setMinisitePriority($key);
        }

        $this->_em->flush();
    }
}
