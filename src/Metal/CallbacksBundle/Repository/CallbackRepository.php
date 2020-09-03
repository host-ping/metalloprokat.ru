<?php

namespace Metal\CallbacksBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Metal\CallbacksBundle\Entity\Callback as CallbackEntity;

class CallbackRepository extends EntityRepository
{
    /**
     * @param CallbackEntity[] $callbacks
     */
    public function attachCompaniesToCallbacks(array $callbacks)
    {
        $companiesIds = array();
        foreach ($callbacks as $callback) {
            if ($callback->getCompany()) {
                $companiesIds[$callback->getCompany()->getId()] = true;
            }
        }

        if (!$companiesIds) {
            return;
        }

        $this->_em->getRepository('MetalCompaniesBundle:Company')->findBy(array('id' => array_keys($companiesIds)));
    }

    /**
     * @param CallbackEntity[] $callbacks
     */
    public function attachCitiesToCallbacks(array $callbacks)
    {
        $citiesIds = array();
        foreach ($callbacks as $callback) {
            if ($callback->getCity()) {
                $citiesIds[$callback->getCity()->getId()] = true;
            }
        }

        if (!$citiesIds) {
            return;
        }

        $this->_em->getRepository('MetalTerritorialBundle:City')->findBy(array('id' => array_keys($citiesIds)));

    }

    /**
     * @param CallbackEntity[] $callbacks
     */
    public function attachCategoriesToCallbacks(array $callbacks)
    {
        $categoriesIds = array();
        foreach ($callbacks as $callback) {
            if ($callback->getCategory()) {
                $categoriesIds[$callback->getCategory()->getId()] = true;
            }
        }

        if (!$categoriesIds) {
            return;
        }

        $this->_em->getRepository('MetalCategoriesBundle:Category')->findBy(array('id' => array_keys($categoriesIds)));
    }

}
