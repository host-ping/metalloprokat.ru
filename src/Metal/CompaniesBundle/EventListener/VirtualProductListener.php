<?php

namespace Metal\CompaniesBundle\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Metal\CompaniesBundle\Entity\Company;
use Metal\ProductsBundle\Entity\Product;

class VirtualProductListener
{
    public function postUpdate(Company $company, LifecycleEventArgs $args)
    {
        $em = $args->getEntityManager();

        if (!$company->getMainOffice()) {
            // main office is not created yet
            return;
        }

        $virtualProduct = $em->getRepository('MetalProductsBundle:Product')->findOneBy(
            array('company' => $company, 'isVirtual' => true)
        );

        if ($virtualProduct) {
            // virtual product already created
            return;
        }

        $virtualProduct = Product::createVirtualProduct($company);
        $company->setVirtualProduct($virtualProduct);

        //TODO: merge with ProductRepository::createOrUpdateVirtualProduct

        $em->persist($virtualProduct);
        $em->flush(array($virtualProduct, $company));
    }
}
