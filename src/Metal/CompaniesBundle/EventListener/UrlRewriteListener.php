<?php

namespace Metal\CompaniesBundle\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Metal\CompaniesBundle\Entity\Company;
use Metal\ProjectBundle\Entity\UrlRewrite;

class UrlRewriteListener
{
    public function postPersist(Company $company, LifecycleEventArgs $args)
    {
        $this->handleCompanyChange($company, $args);
    }

    public function postUpdate(Company $company, LifecycleEventArgs $args)
    {
        $this->handleCompanyChange($company, $args);
    }

    private function handleCompanyChange(Company $company, LifecycleEventArgs $args)
    {
        $em = $args->getEntityManager();
        $urlRewrite = $em->getRepository('MetalProjectBundle:UrlRewrite')->findOneBy(array('company' => $company));

        if ($company->isDeleted()) {
            if ($urlRewrite) {
                $em->remove($urlRewrite);
                $em->flush($urlRewrite);
            }

            return;
        }

        if (!$company->getSlug()) {
            return;
        }

        if (!$urlRewrite) {
            $urlRewrite = new UrlRewrite();
            $urlRewrite->setCompany($company);
            $em->persist($urlRewrite);
        }

        if ($urlRewrite->getPathPrefix() != $company->getSlug()) {
            $urlRewrite->setPathPrefix($company->getSlug());
            $em->flush($urlRewrite);
        }
    }
}
